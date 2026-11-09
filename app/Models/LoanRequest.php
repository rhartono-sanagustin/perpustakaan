<?php

namespace App\Models;

use App\Enums\LoanRequestStatus;
use Database\Factories\LoanRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanRequest extends Model
{
    /** @use HasFactory<LoanRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'processed_by',
        'status',
        'requested_at',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'expires_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => LoanRequestStatus::class,
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', LoanRequestStatus::Menunggu);
    }

    public function scopeStatus(Builder $query, ?LoanRequestStatus $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function isPending(): bool
    {
        return $this->status === LoanRequestStatus::Menunggu;
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }

    public function statusVariant(): string
    {
        return $this->status->badgeVariant();
    }
}
