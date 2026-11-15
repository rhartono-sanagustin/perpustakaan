<?php

namespace App\Services;

use App\Models\LibrarySetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class LibraryIdentity
{
    /**
     * @var array<string, string|null>|null
     */
    private ?array $values = null;

    public function schoolName(): string
    {
        return $this->stringValue('school_name', config('library.school_name'));
    }

    public function libraryName(): string
    {
        return $this->stringValue('library_name', config('library.name'));
    }

    public function tagline(): string
    {
        $values = $this->values();

        if (array_key_exists('tagline', $values)) {
            return (string) ($values['tagline'] ?? '');
        }

        return (string) config('library.tagline');
    }

    public function logoPath(): ?string
    {
        $path = $this->values()['logo_path'] ?? null;

        return is_string($path) && $path !== '' ? $path : null;
    }

    public function hasCustomLogo(): bool
    {
        return $this->logoPath() !== null;
    }

    public function logoUrl(): string
    {
        $path = $this->logoPath();

        if ($path !== null) {
            return Storage::disk(config('library.branding.disk', 'public'))->url($path);
        }

        return asset(config('library.logo'));
    }

    public function forget(): void
    {
        $this->values = null;
    }

    /**
     * @return array<string, string|null>
     */
    private function values(): array
    {
        if ($this->values !== null) {
            return $this->values;
        }

        try {
            $this->values = LibrarySetting::query()
                ->whereIn('key', array_keys(config('library.identity_definitions', [])))
                ->pluck('value', 'key')
                ->all();
        } catch (QueryException) {
            $this->values = [];
        }

        return $this->values;
    }

    private function stringValue(string $key, mixed $default): string
    {
        $value = $this->values()[$key] ?? null;

        if (is_string($value) && $value !== '') {
            return $value;
        }

        return (string) $default;
    }
}
