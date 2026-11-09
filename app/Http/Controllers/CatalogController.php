<?php

namespace App\Http\Controllers;

use App\Enums\BookCopyStatus;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $this->stringQuery($request, 'q', 100);
        $categorySlug = $this->stringQuery($request, 'category', 120);
        $availability = $this->stringQuery($request, 'availability', 20);

        if (! in_array($availability, ['tersedia', 'habis'], true)) {
            $availability = '';
        }

        $categories = Category::query()
            ->active()
            ->withCount(['books' => fn ($query) => $query->active()])
            ->orderBy('name')
            ->get();

        $selectedCategory = $categorySlug !== ''
            ? $categories->firstWhere('slug', $categorySlug)
            : null;

        if ($categorySlug !== '' && ! $selectedCategory) {
            $categorySlug = '';
        }

        $books = Book::query()
            ->active()
            ->with(['category', 'authors', 'publisher'])
            ->withCopyCounts()
            ->search($search)
            ->categorySlug($categorySlug)
            ->availability($availability)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', [
            'books' => $books,
            'categories' => $categories,
            'search' => $search,
            'categorySlug' => $categorySlug,
            'availability' => $availability,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(Book $book): View
    {
        abort_unless($book->is_active, 404);

        $book->load(['category', 'publisher', 'authors'])
            ->loadCount([
                'copies',
                'copies as borrowed_copies_count' => fn ($query) => $query->where('status', BookCopyStatus::Dipinjam),
                'copies as damaged_copies_count' => fn ($query) => $query->whereIn('status', [
                    BookCopyStatus::Rusak,
                    BookCopyStatus::Hilang,
                ]),
                'availableCopies',
            ]);

        $shelfLocations = $book->copies()
            ->where('status', BookCopyStatus::Tersedia)
            ->whereNotNull('shelf_location')
            ->distinct()
            ->orderBy('shelf_location')
            ->pluck('shelf_location');

        $relatedBooks = Book::query()
            ->active()
            ->where('category_id', $book->category_id)
            ->whereKeyNot($book->getKey())
            ->with(['category', 'authors'])
            ->withCopyCounts()
            ->latest()
            ->take(4)
            ->get();

        return view('catalog.show', [
            'book' => $book,
            'shelfLocations' => $shelfLocations,
            'relatedBooks' => $relatedBooks,
        ]);
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
