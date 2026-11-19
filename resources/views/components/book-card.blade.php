@props(['book'])

<article class="group flex h-full flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <a href="{{ route('catalog.show', $book) }}" aria-label="Detail {{ $book->title }}" class="block overflow-hidden bg-zinc-100 dark:bg-zinc-800">
        <img
            src="{{ $book->cover_url }}"
            alt="Sampul {{ $book->title }}"
            class="aspect-[3/4] w-full object-cover transition duration-200 group-hover:scale-[1.02]"
            loading="lazy"
        >
    </a>

    <div class="flex flex-1 flex-col p-4">
        <div class="mb-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $book->category->name }}</div>

        <h3 class="line-clamp-2 min-h-[2.5rem] text-sm font-semibold">
            <a href="{{ route('catalog.show', $book) }}" class="hover:text-[var(--color-accent-content)]">{{ $book->title }}</a>
        </h3>

        <p class="mt-1 line-clamp-1 text-xs text-zinc-500 dark:text-zinc-400">
            {{ $book->authors_label ?: 'Penulis tidak diketahui' }}
        </p>

        <div class="mt-auto pt-3">
            @if ($book->hasAvailableCopies())
                <flux:badge color="green" size="sm" icon="check-circle">Tersedia {{ $book->available_copies_count }} eksemplar</flux:badge>
            @else
                <flux:badge color="zinc" size="sm" icon="x-circle">Sedang tidak tersedia</flux:badge>
            @endif
        </div>

        <flux:button href="{{ route('catalog.show', $book) }}" variant="outline" size="sm" class="mt-3 w-full">
            Detail Buku
        </flux:button>
    </div>
</article>
