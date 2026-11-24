<?php

namespace App\Services;

use App\Support\SpreadsheetData;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Shuchkin\SimpleXLSX;

class SpreadsheetReader
{
    public function read(UploadedFile $file): SpreadsheetData
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($path === false) {
            throw new InvalidArgumentException('Berkas tidak dapat dibaca.');
        }

        $rawRows = match ($extension) {
            'csv' => $this->readCsv($path),
            'xlsx' => $this->readXlsx($path),
            default => throw new InvalidArgumentException('Format berkas harus CSV atau XLSX.'),
        };

        return $this->toSpreadsheet($rawRows);
    }

    /**
     * @return list<array<int, mixed>>
     */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('Berkas CSV tidak dapat dibuka.');
        }

        try {
            $firstLine = fgets($handle);

            if ($firstLine === false) {
                throw new InvalidArgumentException('Berkas tidak berisi data.');
            }

            $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';
            rewind($handle);

            $rows = [];

            while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
                $rows[] = $row;
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return list<array<int, mixed>>
     */
    private function readXlsx(string $path): array
    {
        $xlsx = SimpleXLSX::parse($path);

        if ($xlsx === false) {
            $error = SimpleXLSX::parseError();

            throw new InvalidArgumentException(
                'Berkas Excel tidak dapat dibaca.'.($error ? ' '.$error : ' Pastikan berkas .xlsx valid.')
            );
        }

        return $xlsx->rows(0);
    }

    /**
     * @param  list<array<int, mixed>>  $rawRows
     */
    private function toSpreadsheet(array $rawRows): SpreadsheetData
    {
        $headers = null;
        $rows = [];

        foreach ($rawRows as $index => $row) {
            if (! $this->hasContent($row)) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(
                    fn (mixed $value): string => $this->normalizeCell($value),
                    $row
                );

                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];

                while ($headers !== [] && end($headers) === '') {
                    array_pop($headers);
                }

                $this->assertUniqueHeaders($headers);

                continue;
            }

            $values = array_map(fn (mixed $value): string => $this->normalizeCell($value), $row);
            $values = array_pad($values, count($headers), '');
            $values = array_slice($values, 0, count($headers));

            if (! $this->hasContent($values)) {
                continue;
            }

            $rows[] = [
                'line' => $index + 1,
                'values' => $values,
            ];
        }

        if ($headers === null) {
            throw new InvalidArgumentException('Berkas tidak berisi data.');
        }

        $maxRows = max(1, (int) config('library.imports.max_rows', 1000));

        if (count($rows) > $maxRows) {
            throw new InvalidArgumentException(
                'Berkas berisi '.count($rows).' baris data. Maksimal '.$maxRows.' baris per impor.'
            );
        }

        if ($rows === []) {
            throw new InvalidArgumentException('Berkas tidak memiliki baris data.');
        }

        return new SpreadsheetData($headers, $rows);
    }

    /**
     * @param  list<string>  $headers
     */
    private function assertUniqueHeaders(array $headers): void
    {
        if ($headers === [] || implode('', $headers) === '') {
            throw new InvalidArgumentException('Baris pertama harus berisi nama kolom.');
        }

        $seen = [];

        foreach ($headers as $header) {
            $key = SpreadsheetData::normalize($header);

            if ($key === '') {
                continue;
            }

            if (array_key_exists($key, $seen)) {
                throw new InvalidArgumentException('Kolom "'.$header.'" muncul lebih dari sekali pada baris judul.');
            }

            $seen[$key] = true;
        }
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function hasContent(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    private function normalizeCell(mixed $value): string
    {
        $value = str_replace("\0", '', (string) $value);

        if ($value !== '' && ! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        }

        return trim($value);
    }
}
