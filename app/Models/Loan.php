<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\ReturnCondition;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_request_id',
        'user_id',
        'book_copy_id',
        'processed_by',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'return_condition',
        'return_notes',
        'duration_days',
        'quota_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
            'status' => LoanStatus::class,
            'return_condition' => ReturnCondition::class,
            'duration_days' => 'integer',
            'quota_snapshot' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_request_id');
    }

    public function getReferenceAttribute(): string
    {
        return 'LN-'.str_pad((string) $this->getKey(), 5, '0', STR_PAD_LEFT);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::Dipinjam);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::Dipinjam)
            ->whereDate('due_at', '<', today());
    }

    public function isActive(): bool
    {
        return $this->status === LoanStatus::Dipinjam;
    }

    public function isReturned(): bool
    {
        return $this->status === LoanStatus::Dikembalikan;
    }

    public function isOverdue(): bool
    {
        return $this->isActive() && $this->due_at->lt(today());
    }

    public function daysOverdue(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return (int) $this->due_at->startOfDay()->diffInDays(today()->startOfDay());
    }

    public function daysUntilDue(): int
    {
        if (! $this->isActive()) {
            return 0;
        }

        return (int) today()->startOfDay()->diffInDays($this->due_at->startOfDay());
    }

    public function statusLabel(): string
    {
        return $this->isOverdue() ? 'Terlambat' : $this->status->label();
    }

    public function statusVariant(): string
    {
        return $this->isOverdue() ? 'danger' : $this->status->badgeVariant();
    }
}
