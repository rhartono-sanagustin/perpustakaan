<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookFilters
{
    public function __construct(
        public readonly string $search = '',
        public readonly string $status = '',
        public readonly ?int $categoryId = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $status = self::stringQuery($request, 'status', 20);

        return new self(
            self::stringQuery($request, 'q', 100),
            in_array($status, ['aktif', 'arsip'], true) ? $status : '',
            filter_var($request->query('category_id'), FILTER_VALIDATE_INT) ?: null,
        );
    }

    /**
     * @param  Builder<object>  $query
     * @return Builder<object>
     */
    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->search !== '', fn (Builder $query) => $query->search($this->search))
            ->when($this->categoryId, fn (Builder $query) => $query->where('category_id', $this->categoryId))
            ->when($this->status === 'aktif', fn (Builder $query) => $query->where('is_active', true))
            ->when($this->status === 'arsip', fn (Builder $query) => $query->where('is_active', false));
    }

    /**
     * @return array<string, string|int>
     */
    public function toQuery(): array
    {
        return array_filter([
            'q' => $this->search,
            'status' => $this->status,
            'category_id' => $this->categoryId,
        ], fn (string|int|null $value): bool => $value !== null && $value !== '');
    }

    private static function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
