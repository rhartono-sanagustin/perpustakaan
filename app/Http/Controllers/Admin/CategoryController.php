<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $this->stringQuery($request, 'q', 100);
        $status = $this->stringQuery($request, 'status', 20);

        if (! in_array($status, ['aktif', 'nonaktif'], true)) {
            $status = '';
        }

        $categories = Category::query()
            ->withCount('books')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($status === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$category->name.'" berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$category->name.'" berhasil diperbarui.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Status kategori "'.$category->name.'" berhasil diubah.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            $category->update(['is_active' => false]);

            return redirect()
                ->route('admin.categories.index')
                ->with('warning', 'Kategori "'.$category->name.'" masih digunakan oleh buku, sehingga dinonaktifkan.');
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$name.'" berhasil dihapus.');
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
