<?php

namespace App\Services;

use Shuchkin\SimpleXLSXGen;
use Symfony\Component\HttpFoundation\Response;

class XlsxExporter
{
    /**
     * @param  list<string>  $headers
     * @param  iterable<int, array<int, mixed>>  $rows
     * @param  list<int>  $widths
     */
    public function download(string $filename, string $sheetName, array $headers, iterable $rows, array $widths = []): Response
    {
        $data = [$this->headerRow($headers)];

        foreach ($rows as $row) {
            $data[] = array_map(fn (mixed $value): string => $this->text($value), $row);
        }

        $xlsx = SimpleXLSXGen::fromArray($data, $sheetName);
        $xlsx->freezePanes('A2');

        foreach (array_values($headers) as $index => $header) {
            $xlsx->setColWidth(
                $this->columnLetter($index),
                $widths[$index] ?? max(12, min(40, mb_strlen($header) + 6))
            );
        }

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$this->safeFilename($filename).'"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * @param  list<string>  $headers
     * @return list<string>
     */
    private function headerRow(array $headers): array
    {
        return array_map(
            fn (string $header): string => '<b><style bgcolor="#DCE6F1">'.e($header).'</style></b>',
            $headers
        );
    }

    private function text(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = str_replace(["\r\n", "\r", "\n"], ' ', (string) $value);

        // Awalan NUL memaksa SimpleXLSXGen menulis nilai sebagai teks murni,
        // sehingga nol di depan nomor induk/ISBN tidak hilang dan tidak
        // berubah menjadi rumus atau hyperlink.
        return "\0".$value;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $remainder = ($index - 1) % 26;
            $letter = chr(65 + $remainder).$letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private function safeFilename(string $filename): string
    {
        return str_replace(["\0", "\r", "\n", "\t", '"'], '', $filename);
    }
}
