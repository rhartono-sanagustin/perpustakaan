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

<flux:card class="mb-4">
    <form method="GET" action="{{ $action }}" class="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @if ($includeDate)
            <flux:field>
                <flux:label for="date_from">Dari tanggal</flux:label>
                <flux:input type="date" id="date_from" name="date_from" value="{{ $filters->from->toDateString() }}" />
            </flux:field>

            <flux:field>
                <flux:label for="date_to">Sampai tanggal</flux:label>
                <flux:input type="date" id="date_to" name="date_to" value="{{ $filters->to->toDateString() }}" />
            </flux:field>
        @endif

        <flux:field class="{{ $includeDate ? 'xl:col-span-2' : 'xl:col-span-3' }}">
            <flux:label for="q">Cari</flux:label>
            <flux:input type="search" id="q" name="q" value="{{ $filters->search }}" icon="magnifying-glass" placeholder="{{ $searchPlaceholder }}" />
        </flux:field>

        @if ($includeCategory)
            <flux:field>
                <flux:label for="category_id">Kategori</flux:label>
                <flux:select id="category_id" name="category_id">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters->categoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        @if ($includeRole)
            <flux:field>
                <flux:label for="role">Peran</flux:label>
                <flux:select id="role" name="role">
                    <option value="">Semua peran</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected($filters->role === $role)>{{ $role->label() }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        @if (! empty($statusOptions))
            <flux:field>
                <flux:label for="status">{{ $statusLabel }}</flux:label>
                <flux:select id="status" name="status">
                    <option value="">Semua {{ strtolower($statusLabel) }}</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters->status === (string) $value)>{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        @if (! empty($conditionOptions))
            <flux:field>
                <flux:label for="condition">{{ $conditionLabel }}</flux:label>
                <flux:select id="condition" name="condition">
                    <option value="">Semua {{ strtolower($conditionLabel) }}</option>
                    @foreach ($conditionOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters->condition === (string) $value)>{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        @if ($includeLimit)
            <flux:field>
                <flux:label for="limit">Jumlah data</flux:label>
                <flux:select id="limit" name="limit">
                    @foreach ($limitOptions as $option)
                        <option value="{{ $option }}" @selected($filters->limit === $option)>{{ $option }} teratas</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif

        <div class="flex gap-2">
            <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
            <flux:button variant="outline" href="{{ $resetUrl }}" icon="arrow-path" aria-label="Reset filter" />
        </div>
    </form>
</flux:card>
