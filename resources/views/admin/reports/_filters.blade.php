@php
    $includeDate = $includeDate ?? true;
    $includeCategory = $includeCategory ?? true;
    $includeRole = $includeRole ?? true;
    $includeLimit = $includeLimit ?? false;
    $statusOptions = $statusOptions ?? [];
    $conditionOptions = $conditionOptions ?? [];
    $statusLabel = $statusLabel ?? 'Status';
    $conditionLabel = $conditionLabel ?? 'Kondisi';
    $searchPlaceholder = $searchPlaceholder ?? 'Cari data laporan';
    $resetUrl = $resetUrl ?? $action;
@endphp

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ $action }}" class="row g-2 align-items-end">
            @if ($includeDate)
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="date_from" class="form-label">Dari tanggal</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters->from->toDateString() }}" class="form-control">
                </div>

                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="date_to" class="form-label">Sampai tanggal</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters->to->toDateString() }}" class="form-control">
                </div>
            @endif

            <div class="col-12 col-lg-6 col-xl-4">
                <label for="q" class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" id="q" name="q" value="{{ $filters->search }}" class="form-control" placeholder="{{ $searchPlaceholder }}">
                </div>
            </div>

            @if ($includeCategory)
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($filters->categoryId === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($includeRole)
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="role" class="form-label">Peran</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Semua peran</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected($filters->role === $role)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (! empty($statusOptions))
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="status" class="form-label">{{ $statusLabel }}</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua {{ strtolower($statusLabel) }}</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters->status === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (! empty($conditionOptions))
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="condition" class="form-label">{{ $conditionLabel }}</label>
                    <select id="condition" name="condition" class="form-select">
                        <option value="">Semua {{ strtolower($conditionLabel) }}</option>
                        @foreach ($conditionOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters->condition === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($includeLimit)
                <div class="col-6 col-lg-3 col-xl-2">
                    <label for="limit" class="form-label">Jumlah data</label>
                    <select id="limit" name="limit" class="form-select">
                        @foreach ($limitOptions as $option)
                            <option value="{{ $option }}" @selected($filters->limit === $option)>{{ $option }} teratas</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-12 col-lg-3 col-xl-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                <a href="{{ $resetUrl }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                </a>
            </div>
        </form>
    </div>
</div>
