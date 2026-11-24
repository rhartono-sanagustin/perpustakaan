<?php

namespace App\Support;

class ImportReport
{
    public int $created = 0;

    public int $updated = 0;

    public int $failed = 0;

    /**
     * @var list<array{line: int, message: string}>
     */
    private array $errors = [];

    private int $errorLimit;

    public function __construct(public readonly string $filename, ?int $errorLimit = null)
    {
        $this->errorLimit = $errorLimit ?? (int) config('library.imports.error_limit', 100);
    }

    public function markCreated(): void
    {
        $this->created++;
    }

    public function markUpdated(): void
    {
        $this->updated++;
    }

    public function markFailed(int $line, string $message): void
    {
        $this->failed++;

        if (count($this->errors) < $this->errorLimit) {
            $this->errors[] = [
                'line' => $line,
                'message' => $message,
            ];
        }
    }

    public function total(): int
    {
        return $this->created + $this->updated + $this->failed;
    }

    public function hasFailures(): bool
    {
        return $this->failed > 0;
    }

    public function truncated(): bool
    {
        return $this->failed > count($this->errors);
    }

    public function summary(): string
    {
        return sprintf(
            'Impor selesai: %d dibuat, %d diperbarui, %d gagal.',
            $this->created,
            $this->updated,
            $this->failed
        );
    }

    /**
     * @return array{
     *     filename: string,
     *     created: int,
     *     updated: int,
     *     failed: int,
     *     total: int,
     *     errors: list<array{line: int, message: string}>,
     *     truncated: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'created' => $this->created,
            'updated' => $this->updated,
            'failed' => $this->failed,
            'total' => $this->total(),
            'errors' => $this->errors,
            'truncated' => $this->truncated(),
        ];
    }
}
