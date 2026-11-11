<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorRequest;
use App\Models\Author;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Author::class);

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
        $this->authorize('create', Author::class);

        return view('admin.authors.form', [
            'author' => new Author,
        ]);
    }

    public function store(AuthorRequest $request): RedirectResponse
    {
        $this->authorize('create', Author::class);

        $author = Author::create($request->validated());
        $this->auditLogger->record('author.created', $author, null, $this->auditSnapshot($author));

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$author->name.'" berhasil ditambahkan.');
    }

    public function edit(Author $author): View
    {
        $this->authorize('update', $author);

        return view('admin.authors.form', [
            'author' => $author,
        ]);
    }

    public function update(AuthorRequest $request, Author $author): RedirectResponse
    {
        $this->authorize('update', $author);

        $before = $this->auditSnapshot($author);
        $author->update($request->validated());
        $this->auditLogger->recordChanges('author.updated', $author, $before, $this->auditSnapshot($author->refresh()));

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$author->name.'" berhasil diperbarui.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        $this->authorize('delete', $author);

        if ($author->books()->exists()) {
            return redirect()
                ->route('admin.authors.index')
                ->with('error', 'Penulis "'.$author->name.'" masih digunakan oleh buku dan belum dapat dihapus.');
        }

        $name = $author->name;
        $before = $this->auditSnapshot($author);
        $author->delete();
        $this->auditLogger->record('author.deleted', $author, $before, null);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Penulis "'.$name.'" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function auditSnapshot(Author $author): array
    {
        return [
            'name' => $author->name,
            'slug' => $author->slug,
            'bio' => $author->bio,
        ];
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
