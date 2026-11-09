<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'bio',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }
}
