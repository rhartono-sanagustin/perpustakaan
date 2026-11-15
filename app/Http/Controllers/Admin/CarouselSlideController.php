<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarouselSlideRequest;
use App\Models\CarouselSlide;
use App\Services\AuditLogger;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CarouselSlideController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        $this->authorize('viewAny', CarouselSlide::class);

        return view('admin.settings.carousel.index', [
            'slides' => CarouselSlide::query()->ordered()->get(),
            'maxActive' => (int) config('library.carousel.max_active', 5),
            'activeCount' => CarouselSlide::query()->where('is_active', true)->count(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarouselSlide::class);

        $maxActive = (int) config('library.carousel.max_active', 5);
        $activeCount = CarouselSlide::query()->where('is_active', true)->count();

        return view('admin.settings.carousel.form', [
            'slide' => new CarouselSlide([
                'sort_order' => ((int) CarouselSlide::query()->max('sort_order')) + 1,
                'is_active' => $activeCount < $maxActive,
            ]),
        ]);
    }

    public function store(CarouselSlideRequest $request): RedirectResponse
    {
        $this->authorize('create', CarouselSlide::class);

        $this->ensureActiveLimit($request->boolean('is_active'));

        $data = $request->validated();
        unset($data['image']);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = ((int) CarouselSlide::query()->max('sort_order')) + 1;

        $path = null;

        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store(
                    config('library.carousel.path', 'carousel'),
                    config('library.carousel.disk', 'public')
                );
                $data['image_path'] = $path;
            }

            $slide = DB::transaction(fn (): CarouselSlide => CarouselSlide::create($data));
        } catch (Throwable $exception) {
            if ($path !== null) {
                $this->disk()->delete($path);
            }

            throw $exception;
        }

        $this->auditLogger->record('carousel_slide.created', $slide, null, $this->auditSnapshot($slide));

        return redirect()
            ->route('admin.settings.carousel.index')
            ->with('success', 'Slide carousel berhasil ditambahkan.');
    }

    public function edit(CarouselSlide $slide): View
    {
        $this->authorize('update', $slide);

        return view('admin.settings.carousel.form', [
            'slide' => $slide,
        ]);
    }

    public function update(CarouselSlideRequest $request, CarouselSlide $slide): RedirectResponse
    {
        $this->authorize('update', $slide);

        $this->ensureActiveLimit($request->boolean('is_active'), $slide);

        $before = $this->auditSnapshot($slide);
        $data = $request->validated();
        unset($data['image']);

        $data['is_active'] = $request->boolean('is_active');

        $oldPath = $slide->image_path;
        $newPath = null;

        try {
            if ($request->hasFile('image')) {
                $newPath = $request->file('image')->store(
                    config('library.carousel.path', 'carousel'),
                    config('library.carousel.disk', 'public')
                );
                $data['image_path'] = $newPath;
            }

            $slide->update($data);
        } catch (Throwable $exception) {
            if ($newPath !== null) {
                $this->disk()->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath !== null && $oldPath !== $newPath) {
            $this->disk()->delete($oldPath);
        }

        $this->auditLogger->recordChanges(
            'carousel_slide.updated',
            $slide,
            $before,
            $this->auditSnapshot($slide->refresh())
        );

        return redirect()
            ->route('admin.settings.carousel.index')
            ->with('success', 'Slide carousel berhasil diperbarui.');
    }

    public function destroy(CarouselSlide $slide): RedirectResponse
    {
        $this->authorize('delete', $slide);

        $before = $this->auditSnapshot($slide);
        $path = $slide->image_path;

        DB::transaction(fn () => $slide->delete());

        if ($path !== null) {
            $this->disk()->delete($path);
        }

        $this->auditLogger->record('carousel_slide.deleted', $slide, $before, null);

        return redirect()
            ->route('admin.settings.carousel.index')
            ->with('success', 'Slide carousel berhasil dihapus.');
    }

    public function toggle(CarouselSlide $slide): RedirectResponse
    {
        $this->authorize('toggle', $slide);

        $activate = ! $slide->is_active;

        if ($activate) {
            $this->ensureActiveLimit(true, $slide);
        }

        $before = $this->auditSnapshot($slide);
        $slide->update(['is_active' => $activate]);

        $this->auditLogger->recordChanges(
            $activate ? 'carousel_slide.activated' : 'carousel_slide.deactivated',
            $slide,
            $before,
            $this->auditSnapshot($slide)
        );

        return back()->with('success', $activate ? 'Slide diaktifkan.' : 'Slide dinonaktifkan.');
    }

    public function move(Request $request, CarouselSlide $slide): RedirectResponse
    {
        $this->authorize('move', $slide);

        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $slides = CarouselSlide::query()->ordered()->get()->values();
        $index = $slides->search(fn (CarouselSlide $item): bool => $item->is($slide));

        if ($index === false) {
            return back()->with('info', 'Slide tidak ditemukan.');
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($swapIndex < 0 || $swapIndex >= $slides->count()) {
            return back()->with('info', 'Posisi slide sudah berada di batas urutan.');
        }

        $oldOrder = $slide->sort_order;
        $ordered = $slides->all();
        [$ordered[$index], $ordered[$swapIndex]] = [$ordered[$swapIndex], $ordered[$index]];

        DB::transaction(function () use ($ordered): void {
            foreach ($ordered as $position => $item) {
                $item->update(['sort_order' => $position + 1]);
            }
        });

        $slide->refresh();

        $this->auditLogger->record(
            'carousel_slide.reordered',
            $slide,
            ['sort_order' => $oldOrder],
            ['sort_order' => $slide->sort_order]
        );

        return back()->with('success', 'Urutan slide diperbarui.');
    }

    private function ensureActiveLimit(bool $activating, ?CarouselSlide $except = null): void
    {
        if (! $activating) {
            return;
        }

        $max = (int) config('library.carousel.max_active', 5);

        $activeCount = CarouselSlide::query()
            ->where('is_active', true)
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->count();

        if ($activeCount >= $max) {
            throw ValidationException::withMessages([
                'is_active' => 'Maksimal '.$max.' slide dapat aktif di beranda. Nonaktifkan slide lain terlebih dahulu.',
            ]);
        }
    }

    private function disk(): Filesystem
    {
        return Storage::disk(config('library.carousel.disk', 'public'));
    }

    /**
     * @return array<string, mixed>
     */
    private function auditSnapshot(CarouselSlide $slide): array
    {
        return [
            'title' => $slide->title,
            'caption' => $slide->caption,
            'alt_text' => $slide->alt_text,
            'image_path' => $slide->image_path,
            'link_url' => $slide->link_url,
            'sort_order' => $slide->sort_order,
            'is_active' => $slide->is_active,
        ];
    }
}
