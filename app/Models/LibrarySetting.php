<?php

namespace App\Models;

use Database\Factories\LibrarySettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibrarySetting extends Model
{
    /** @use HasFactory<LibrarySettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group_name',
        'label',
        'description',
        'updated_by',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode((string) $setting->value, true),
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value, ?int $updatedBy = null): self
    {
        $setting = static::query()->firstOrNew(['key' => $key]);

        $setting->fill([
            'value' => is_array($value) ? json_encode($value) : (string) $value,
            'updated_by' => $updatedBy,
        ]);

        if (! $setting->exists) {
            $setting->fill([
                'type' => is_int($value) ? 'integer' : (is_bool($value) ? 'boolean' : 'string'),
                'label' => str($key)->replace('_', ' ')->title()->toString(),
            ]);
        }

        $setting->save();

        return $setting;
    }
}
