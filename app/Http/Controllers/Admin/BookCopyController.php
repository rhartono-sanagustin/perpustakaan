<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookCopyRequest;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCopyController extends Controller
{
    public function all(Request $request): View
    {
        $search = $this->stringQuery($request, 'q', 100);
        $status = $this->stringQuery($request, 'status', 30);

        if (! in_array($status, array_column(BookCopyStatus::cases(), 'value'), true)) {
            $status = '';
        }

        $copies = BookCopy::query()
            ->with(['book.category', 'activeLoan.user'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';
                $query->where(function ($query) use ($like) {
                    $query->where('inventory_code', 'like', $like)
                        ->orWhere('shelf_location', 'like', $like)
                        ->orWhereHas('book', fn ($query) => $query->where('title', 'like', $like));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('inventory_code')
            ->paginate(15)
            ->withQueryString();

        return view('admin.copies.index', [
            'copies' => $copies,
            'search' => $search,
            'status' => $status,
            'statuses' => BookCopyStatus::cases(),
            'summary' => $this->summary(),
        ]);
    }

    public function index(Book $book): View
    {
        $search = $this->stringQuery(request(), 'q', 100);
        $status = $this->stringQuery(request(), 'status', 30);

        if (! in_array($status, array_column(BookCopyStatus::cases(), 'value'), true)) {
            $status = '';
        }

        $copies = $book->copies()
            ->with('activeLoan.user')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';
                $query->where(function ($query) use ($like) {
                    $query->where('inventory_code', 'like', $like)
                        ->orWhere('shelf_location', 'like', $like);
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('inventory_code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.books.copies.index', [
            'book' => $book->load(['category', 'authors']),
            'copies' => $copies,
            'search' => $search,
            'status' => $status,
            'statuses' => BookCopyStatus::cases(),
            'summary' => $book->copyStatusCounts(),
        ]);
    }

    public function create(Book $book): View
    {
        return view('admin.books.copies.form', [
            'book' => $book,
            'copy' => new BookCopy([
                'condition' => BookCondition::Baik,
                'status' => BookCopyStatus::Tersedia,
            ]),
            'conditions' => BookCondition::cases(),
            'statuses' => BookCopyStatus::cases(),
        ]);
    }

    public function store(BookCopyRequest $request, Book $book): RedirectResponse
    {
        $copy = $book->copies()->create($request->validated());

        return redirect()
            ->route('admin.books.copies.index', $book)
            ->with('success', 'Eksemplar '.$copy->inventory_code.' berhasil ditambahkan.');
    }

    public function edit(Book $book, BookCopy $copy): View
    {
        return view('admin.books.copies.form', [
            'book' => $book,
            'copy' => $copy,
            'conditions' => BookCondition::cases(),
            'statuses' => BookCopyStatus::cases(),
        ]);
    }

    public function update(BookCopyRequest $request, Book $book, BookCopy $copy): RedirectResponse
    {
        $copy->update($request->validated());

        return redirect()
            ->route('admin.books.copies.index', $book)
            ->with('success', 'Eksemplar '.$copy->inventory_code.' berhasil diperbarui.');
    }

    public function destroy(Book $book, BookCopy $copy): RedirectResponse
    {
        if ($copy->activeLoan()->exists()) {
            return redirect()
                ->route('admin.books.copies.index', $book)
                ->with('error', 'Eksemplar masih memiliki pinjaman aktif dan belum dapat diarsipkan.');
        }

        if ($copy->loans()->exists()) {
            $copy->update(['status' => BookCopyStatus::Nonaktif]);

            return redirect()
                ->route('admin.books.copies.index', $book)
                ->with('success', 'Eksemplar '.$copy->inventory_code.' dinonaktifkan karena memiliki riwayat transaksi.');
        }

        $code = $copy->inventory_code;
        $copy->delete();

        return redirect()
            ->route('admin.books.copies.index', $book)
            ->with('success', 'Eksemplar '.$code.' berhasil dihapus.');
    }

    /**
     * @return array<string, int>
     */
    private function summary(): array
    {
        $counts = BookCopy::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(BookCopyStatus::cases())
            ->mapWithKeys(fn (BookCopyStatus $status) => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
