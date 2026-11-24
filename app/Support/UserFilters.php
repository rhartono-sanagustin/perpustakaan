<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilters
{
    public function __construct(
        public readonly string $search = '',
        public readonly ?UserRole $role = null,
        public readonly ?UserStatus $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            self::stringQuery($request, 'q', 100),
            UserRole::tryFrom((string) $request->query('role')),
            UserStatus::tryFrom((string) $request->query('status')),
        );
    }

    /**
     * @param  Builder<object>  $query
     * @return Builder<object>
     */
    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->search !== '', function (Builder $query): void {
                $like = '%'.$this->search.'%';

                $query->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('nomor_induk', 'like', $like)
                        ->orWhere('kelas', 'like', $like)
                        ->orWhere('unit_kerja', 'like', $like);
                });
            })
            ->when($this->role !== null, fn (Builder $query) => $query->where('role', $this->role->value))
            ->when($this->status !== null, fn (Builder $query) => $query->where('status', $this->status->value));
    }

    /**
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        return array_filter([
            'q' => $this->search,
            'role' => $this->role?->value,
            'status' => $this->status?->value,
        ], fn (?string $value): bool => $value !== null && $value !== '');
    }

    private static function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
