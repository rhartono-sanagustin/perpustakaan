<?php

namespace App\Models;

use App\Enums\BookCopyStatus;
use App\Models\Concerns\HasSlug;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'category_id',
        'publisher_id',
        'title',
        'slug',
        'isbn',
        'publication_year',
        'edition',
        'language',
        'summary',
        'cover_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function slugSource(): string
    {
        return 'title';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function availableCopies(): HasMany
    {
        return $this->copies()->where('status', BookCopyStatus::Tersedia);
    }

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class);
    }

    public function loans(): HasManyThrough
    {
        return $this->hasManyThrough(Loan::class, BookCopy::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like) {
            $query->where('title', 'like', $like)
                ->orWhere('isbn', 'like', $like)
                ->orWhere('edition', 'like', $like)
                ->orWhere('summary', 'like', $like)
                ->orWhereHas('category', fn (Builder $query) => $query->where('name', 'like', $like))
                ->orWhereHas('publisher', fn (Builder $query) => $query->where('name', 'like', $like))
                ->orWhereHas('authors', fn (Builder $query) => $query->where('name', 'like', $like));
        });
    }

    public function scopeCategorySlug(Builder $query, ?string $slug): Builder
    {
        if (blank($slug)) {
            return $query;
        }

        return $query->whereHas('category', fn (Builder $query) => $query->where('slug', $slug));
    }

    public function scopeAvailability(Builder $query, ?string $availability): Builder
    {
        return match ($availability) {
            'tersedia' => $query->whereHas('availableCopies'),
            'habis' => $query->whereDoesntHave('availableCopies'),
            default => $query,
        };
    }

    public function scopeWithCopyCounts(Builder $query): Builder
    {
        return $query->withCount([
            'copies',
            'availableCopies',
            'copies as borrowed_copies_count' => fn (Builder $query) => $query->where('status', BookCopyStatus::Dipinjam),
            'copies as damaged_copies_count' => fn (Builder $query) => $query->where('status', BookCopyStatus::Rusak),
            'copies as lost_copies_count' => fn (Builder $query) => $query->where('status', BookCopyStatus::Hilang),
            'copies as inactive_copies_count' => fn (Builder $query) => $query->where('status', BookCopyStatus::Nonaktif),
        ]);
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_path) {
            return Storage::disk('public')->url($this->cover_path);
        }

        return asset(config('library.default_cover'));
    }

    public function getAuthorsLabelAttribute(): string
    {
        return $this->authors->pluck('name')->implode(', ');
    }

    public function hasAvailableCopies(): bool
    {
        if ($this->relationLoaded('availableCopies')) {
            return $this->availableCopies->isNotEmpty();
        }

        if (array_key_exists('available_copies_count', $this->attributes)) {
            return (int) $this->attributes['available_copies_count'] > 0;
        }

        return $this->availableCopies()->exists();
    }

    /**
     * @return array<string, int>
     */
    public function copyStatusCounts(): array
    {
        $counts = $this->copies()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(BookCopyStatus::cases())
            ->mapWithKeys(fn (BookCopyStatus $status) => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }
}
