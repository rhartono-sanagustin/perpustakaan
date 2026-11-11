<?php

namespace App\Support;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class ReportFilters
{
    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to,
        public readonly string $search = '',
        public readonly ?int $categoryId = null,
        public readonly ?UserRole $role = null,
        public readonly string $status = '',
        public readonly string $condition = '',
        public readonly int $limit = 10,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $from = self::date($request->query('date_from')) ?? today()->startOfMonth();
        $to = self::date($request->query('date_to')) ?? today();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        $search = $request->query('q');
        $status = $request->query('status');
        $condition = $request->query('condition');
        $categoryId = filter_var($request->query('category_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $limit = filter_var($request->query('limit'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $role = $request->query('role');

        return new self(
            from: $from,
            to: $to,
            search: is_string($search) ? mb_substr(trim($search), 0, 100) : '',
            categoryId: is_int($categoryId) ? $categoryId : null,
            role: is_string($role) ? UserRole::tryFrom($role) : null,
            status: is_string($status) ? mb_substr(trim($status), 0, 20) : '',
            condition: is_string($condition) ? mb_substr(trim($condition), 0, 20) : '',
            limit: in_array($limit, [10, 25, 50, 100], true) ? $limit : 10,
        );
    }

    public function periodLabel(): string
    {
        $from = $this->from->copy()->locale('id');
        $to = $this->to->copy()->locale('id');

        if ($from->isSameDay($to)) {
            return $from->translatedFormat('d F Y');
        }

        if ($from->isSameMonth($to) && $from->isSameYear($to)) {
            return $from->translatedFormat('d').' – '.$to->translatedFormat('d F Y');
        }

        return $from->translatedFormat('d F Y').' – '.$to->translatedFormat('d F Y');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function query(array $overrides = []): array
    {
        return array_filter(array_merge([
            'date_from' => $this->from->toDateString(),
            'date_to' => $this->to->toDateString(),
            'q' => $this->search !== '' ? $this->search : null,
            'category_id' => $this->categoryId,
            'role' => $this->role?->value,
            'status' => $this->status !== '' ? $this->status : null,
            'condition' => $this->condition !== '' ? $this->condition : null,
            'limit' => $this->limit,
        ], $overrides), fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private static function date(mixed $value): ?Carbon
    {
        if (! is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        return $date->format('Y-m-d') === $value ? $date : null;
    }
}
