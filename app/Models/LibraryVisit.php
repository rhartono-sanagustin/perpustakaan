<?php

namespace App\Models;

use Database\Factories\LibraryVisitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryVisit extends Model
{
    /** @use HasFactory<LibraryVisitFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visit_date',
        'visited_at',
        'recorded_by',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'visited_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('visit_date', now()->toDateString());
    }

    public function scopeBetweenDates(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('visit_date', [$start, $end]);
    }
}
