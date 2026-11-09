<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorRequest;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $this->stringQuery($request, 'q', 100);

        $authors = Author::query()
            ->withCount('books')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.authors.index', [
            'authors' => $authors,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.authors.form', [
            'author' => new Author,
        ]);
    }

    public function store(AuthorRequest $request): RedirectResponse
    {
        $author = Author::create($request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$author->name.'" berhasil ditambahkan.');
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.form', [
            'author' => $author,
        ]);
    }

    public function update(AuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$author->name.'" berhasil diperbarui.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return redirect()
                ->route('admin.authors.index')
                ->with('error', 'Penulis "'.$author->name.'" masih digunakan oleh buku dan belum dapat dihapus.');
        }

        $name = $author->name;
        $author->delete();

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$name.'" berhasil dihapus.');
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
