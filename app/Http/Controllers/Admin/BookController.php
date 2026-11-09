<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $this->stringQuery($request, 'q', 100);
        $status = $this->stringQuery($request, 'status', 20);
        $categoryId = filter_var($request->query('category_id'), FILTER_VALIDATE_INT) ?: null;

        if (! in_array($status, ['aktif', 'arsip'], true)) {
            $status = '';
        }

        $books = Book::query()
            ->with(['category', 'authors'])
            ->withCopyCounts()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($status === 'arsip', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        return view('admin.books.form', [
            'book' => new Book(['language' => 'Indonesia', 'is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'selectedAuthors' => [],
        ]);
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $authorIds = $data['authors'];
        unset($data['authors'], $data['cover'], $data['remove_cover']);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        $book = DB::transaction(function () use ($data, $authorIds) {
            $book = Book::create($data);
            $book->authors()->sync($authorIds);

            return $book;
        });

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku "'.$book->title.'" berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        $book->load('authors');

        return view('admin.books.form', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'selectedAuthors' => old('authors', $book->authors->pluck('id')->all()),
        ]);
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();
        $authorIds = $data['authors'];
        unset($data['authors'], $data['cover'], $data['remove_cover']);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover')) {
            $this->deleteCover($book);
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteCover($book);
            $data['cover_path'] = null;
        }

        DB::transaction(function () use ($book, $data, $authorIds) {
            $book->update($data);
            $book->authors()->sync($authorIds);
        });

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku "'.$book->title.'" berhasil diperbarui.');
    }

    public function archive(Book $book): RedirectResponse
    {
        $book->update(['is_active' => false]);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku "'.$book->title.'" diarsipkan dari katalog publik.');
    }

    public function activate(Book $book): RedirectResponse
    {
        $book->update(['is_active' => true]);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku "'.$book->title.'" diaktifkan kembali di katalog.');
    }

    private function deleteCover(Book $book): void
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
