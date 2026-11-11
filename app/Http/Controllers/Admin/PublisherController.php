<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublisherRequest;
use App\Models\Publisher;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublisherController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Publisher::class);

        $search = $this->stringQuery($request, 'q', 100);

        $publishers = Publisher::query()
            ->withCount('books')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.publishers.index', [
            'publishers' => $publishers,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Publisher::class);

        return view('admin.publishers.form', [
            'publisher' => new Publisher,
        ]);
    }

    public function store(PublisherRequest $request): RedirectResponse
    {
        $this->authorize('create', Publisher::class);

        $publisher = Publisher::create($request->validated());
        $this->auditLogger->record('publisher.created', $publisher, null, $this->auditSnapshot($publisher));

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$publisher->name.'" berhasil ditambahkan.');
    }

    public function edit(Publisher $publisher): View
    {
        $this->authorize('update', $publisher);

        return view('admin.publishers.form', [
            'publisher' => $publisher,
        ]);
    }

    public function update(PublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $this->authorize('update', $publisher);

        $before = $this->auditSnapshot($publisher);
        $publisher->update($request->validated());
        $this->auditLogger->recordChanges('publisher.updated', $publisher, $before, $this->auditSnapshot($publisher->refresh()));

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$publisher->name.'" berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        $this->authorize('delete', $publisher);

        if ($publisher->books()->exists()) {
            return redirect()
                ->route('admin.publishers.index')
                ->with('error', 'Penerbit "'.$publisher->name.'" masih digunakan oleh buku dan belum dapat dihapus.');
        }

        $name = $publisher->name;
        $before = $this->auditSnapshot($publisher);
        $publisher->delete();
        $this->auditLogger->record('publisher.deleted', $publisher, $before, null);

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$name.'" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function auditSnapshot(Publisher $publisher): array
    {
        return [
            'name' => $publisher->name,
            'slug' => $publisher->slug,
            'address' => $publisher->address,
            'phone' => $publisher->phone,
            'email' => $publisher->email,
        ];
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
