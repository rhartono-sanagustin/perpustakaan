<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

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
        $this->authorize('create', Category::class);

        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $category = Category::create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->auditLogger->record('category.created', $category, null, $this->auditSnapshot($category));

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$category->name.'" berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.form', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $before = $this->auditSnapshot($category);

        $category->update([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->auditLogger->recordChanges('category.updated', $category, $before, $this->auditSnapshot($category->refresh()));

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$category->name.'" berhasil diperbarui.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $before = $this->auditSnapshot($category);
        $category->update(['is_active' => ! $category->is_active]);
        $this->auditLogger->recordChanges('category.updated', $category, $before, $this->auditSnapshot($category->refresh()));

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Status kategori "'.$category->name.'" berhasil diubah.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $before = $this->auditSnapshot($category);

        if ($category->books()->exists()) {
            $category->update(['is_active' => false]);
            $this->auditLogger->recordChanges('category.deactivated', $category, $before, $this->auditSnapshot($category));

            return redirect()
                ->route('admin.categories.index')
                ->with('warning', 'Kategori "'.$category->name.'" masih digunakan oleh buku, sehingga dinonaktifkan.');
        }

        $name = $category->name;
        $category->delete();
        $this->auditLogger->record('category.deleted', $category, $before, null);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori "'.$name.'" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function auditSnapshot(Category $category): array
    {
        return [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'is_active' => $category->is_active,
        ];
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
