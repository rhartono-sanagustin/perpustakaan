<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\PublisherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    /** @use HasFactory<PublisherFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
