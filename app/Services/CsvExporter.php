<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * @param  list<string>  $headers
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    public function stream(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers, ';', '"', '\\', "\n");

            foreach ($rows as $row) {
                fputcsv(
                    $handle,
                    array_map(fn (mixed $value): string => $this->sanitize($value), $row),
                    ';',
                    '"',
                    '\\',
                    "\n"
                );
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function sanitize(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = str_replace(["\r\n", "\r", "\n"], ' ', (string) $value);

        if (preg_match('/^[=+\-@\t]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
