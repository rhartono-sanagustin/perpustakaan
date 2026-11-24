<?php

namespace App\Support;

class SpreadsheetData
{
    /**
     * @var array<string, int>
     */
    private array $indexes = [];

    /**
     * @param  list<string>  $headers
     * @param  list<array{line: int, values: list<string>}>  $rows
     */
    public function __construct(
        public readonly array $headers,
        public readonly array $rows,
    ) {
        foreach ($this->headers as $index => $header) {
            $key = self::normalize($header);

            if ($key !== '' && ! array_key_exists($key, $this->indexes)) {
                $this->indexes[$key] = $index;
            }
        }
    }

    /**
     * Samakan nama kolom agar "Nomor Induk", "nomor induk", dan "No. Induk"
     * dapat dibaca dengan toleransi tanda baca serta huruf besar/kecil.
     */
    public static function normalize(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower(trim($value))) ?? '';
    }

    /**
     * @param  list<string>  $headers
     */
    public function hasAny(array $headers): bool
    {
        foreach ($headers as $header) {
            if (array_key_exists(self::normalize($header), $this->indexes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $headers
     */
    public function missing(array $headers): array
    {
        return array_values(array_filter(
            $headers,
            fn (string $header): bool => ! array_key_exists(self::normalize($header), $this->indexes)
        ));
    }

    /**
     * Ambil nilai sel berdasarkan nama kolom. Nama alternatif dapat diberikan
     * dalam bentuk array agar template lama tetap dapat diimpor.
     *
     * @param  list<string>  $values
     * @param  string|list<string>  $headers
     */
    public function value(array $values, string|array $headers): string
    {
        foreach ((array) $headers as $header) {
            $index = $this->indexes[self::normalize($header)] ?? null;

            if ($index === null) {
                continue;
            }

            return $this->clean($values[$index] ?? '');
        }

        return '';
    }

    /**
     * Nilai yang diekspor CSV diberi awalan apostrof agar tidak dieksekusi
     * sebagai formula. Awalan tersebut dilepas kembali saat impor.
     */
    private function clean(string $value): string
    {
        $value = trim($value);

        if (preg_match("/^'[=+\\-@\\t]/", $value) === 1) {
            $value = substr($value, 1);
        }

        return trim($value);
    }
}
