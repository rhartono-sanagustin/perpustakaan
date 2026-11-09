<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublisherRequest;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublisherController extends Controller
{
    public function index(Request $request): View
    {
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
        return view('admin.publishers.form', [
            'publisher' => new Publisher,
        ]);
    }

    public function store(PublisherRequest $request): RedirectResponse
    {
        $publisher = Publisher::create($request->validated());

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$publisher->name.'" berhasil ditambahkan.');
    }

    public function edit(Publisher $publisher): View
    {
        return view('admin.publishers.form', [
            'publisher' => $publisher,
        ]);
    }

    public function update(PublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $publisher->update($request->validated());

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$publisher->name.'" berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return redirect()
                ->route('admin.publishers.index')
                ->with('error', 'Penerbit "'.$publisher->name.'" masih digunakan oleh buku dan belum dapat dihapus.');
        }

        $name = $publisher->name;
        $publisher->delete();

        return redirect()
            ->route('admin.publishers.index')
            ->with('success', 'Penerbit "'.$name.'" berhasil dihapus.');
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
