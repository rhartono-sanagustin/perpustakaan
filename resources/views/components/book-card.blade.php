@props(['book'])

<div class="col">
    <article class="card h-100">
        <a href="{{ route('catalog.show', $book) }}" class="text-decoration-none" aria-label="Detail {{ $book->title }}">
            <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="card-img-top book-cover" loading="lazy">
        </a>

        <div class="card-body d-flex flex-column pb-2">
            <div class="small text-muted mb-1">{{ $book->category->name }}</div>
            <h3 class="h6 book-title-clamp mb-1">
                <a href="{{ route('catalog.show', $book) }}" class="text-reset">{{ $book->title }}</a>
            </h3>
            <p class="small text-muted book-author-clamp mb-2">
                {{ $book->authors_label ?: 'Penulis tidak diketahui' }}
            </p>

            <div class="mt-auto">
                @if ($book->hasAvailableCopies())
                    <span class="badge text-bg-success">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Tersedia {{ $book->available_copies_count }} eksemplar
                    </span>
                @else
                    <span class="badge text-bg-secondary">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Sedang tidak tersedia
                    </span>
                @endif
            </div>
        </div>

        <div class="card-footer border-top-0 bg-transparent pt-0">
            <a href="{{ route('catalog.show', $book) }}" class="btn btn-outline-primary btn-sm w-100">Detail Buku</a>
        </div>
    </article>
</div>
