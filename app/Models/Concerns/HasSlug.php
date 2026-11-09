<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (self $model) {
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->{$model->slugSource()});
            }
        });
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    public function generateUniqueSlug(?string $value): string
    {
        $base = Str::slug((string) $value);

        if ($base === '') {
            $base = Str::lower(Str::random(8));
        }

        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
