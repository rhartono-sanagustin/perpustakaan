<?php

namespace App\Models;

use Database\Factories\CarouselSlideFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarouselSlide extends Model
{
    /** @use HasFactory<CarouselSlideFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'caption',
        'alt_text',
        'image_path',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): string
    {
        return Storage::disk(config('library.carousel.disk', 'public'))->url($this->image_path);
    }

    public function isExternalLink(): bool
    {
        return is_string($this->link_url)
            && preg_match('/^https?:\/\//i', $this->link_url) === 1;
    }
}
