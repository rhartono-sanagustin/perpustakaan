<?php

namespace App\Models;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use Database\Factories\BookCopyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookCopy extends Model
{
    /** @use HasFactory<BookCopyFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'inventory_code',
        'shelf_location',
        'condition',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'condition' => BookCondition::class,
            'status' => BookCopyStatus::class,
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->where('status', 'dipinjam');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', BookCopyStatus::Tersedia);
    }

    public function isAvailable(): bool
    {
        return $this->status === BookCopyStatus::Tersedia;
    }

    public function hasActiveLoan(): bool
    {
        if ($this->relationLoaded('activeLoan')) {
            return $this->activeLoan !== null;
        }

        return $this->activeLoan()->exists();
    }
}
