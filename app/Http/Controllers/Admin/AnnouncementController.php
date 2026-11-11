<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AnnouncementAudience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Announcement::class);

        $search = $this->stringQuery($request, 'q', 100);
        $status = $this->stringQuery($request, 'status', 20);
        $audience = AnnouncementAudience::tryFrom((string) $request->query('audience'));

        if (! in_array($status, ['aktif', 'draf'], true)) {
            $status = '';
        }

        $announcements = Announcement::query()
            ->with('creator')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.$search.'%';

                $query->where(fn (Builder $query) => $query
                    ->where('title', 'like', $like)
                    ->orWhere('body', 'like', $like));
            })
            ->when($status === 'aktif', fn (Builder $query) => $query->where('is_active', true))
            ->when($status === 'draf', fn (Builder $query) => $query->where('is_active', false))
            ->when($audience !== null, fn (Builder $query) => $query->where('audience', $audience->value))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.announcements.index', [
            'announcements' => $announcements,
            'search' => $search,
            'status' => $status,
            'audience' => $audience,
            'audiences' => AnnouncementAudience::cases(),
            'activeCount' => Announcement::query()->active()->count(),
            'draftCount' => Announcement::query()->where('is_active', false)->count(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Announcement::class);

        return view('admin.announcements.form', [
            'announcement' => new Announcement([
                'audience' => AnnouncementAudience::Semua,
                'is_active' => true,
                'published_at' => now(),
            ]),
            'audiences' => AnnouncementAudience::cases(),
        ]);
    }

    public function store(AnnouncementRequest $request): RedirectResponse
    {
        $this->authorize('create', Announcement::class);

        $data = $this->payload($request);
        $data['created_by'] = $request->user()?->id;

        $announcement = Announcement::query()->create($data);
        $this->auditLogger->record('announcement.created', $announcement, null, $this->snapshot($announcement));

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman "'.$announcement->title.'" berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement): View
    {
        $this->authorize('update', $announcement);

        return view('admin.announcements.form', [
            'announcement' => $announcement->load('creator'),
            'audiences' => AnnouncementAudience::cases(),
        ]);
    }

    public function update(AnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $announcement);

        $before = $this->snapshot($announcement);
        $announcement->update($this->payload($request));
        $this->auditLogger->recordChanges(
            'announcement.updated',
            $announcement,
            $before,
            $this->snapshot($announcement->refresh())
        );

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman "'.$announcement->title.'" berhasil diperbarui.');
    }

    public function toggle(Announcement $announcement): RedirectResponse
    {
        $this->authorize('toggle', $announcement);

        $before = $this->snapshot($announcement);
        $isActive = ! $announcement->is_active;

        $announcement->update([
            'is_active' => $isActive,
            'published_at' => $isActive && $announcement->published_at === null
                ? now()
                : $announcement->published_at,
        ]);

        $this->auditLogger->recordChanges(
            'announcement.toggled',
            $announcement,
            $before,
            $this->snapshot($announcement->refresh())
        );

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman "'.$announcement->title.'" '.($isActive ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $before = $this->snapshot($announcement);
        $title = $announcement->title;
        $announcement->delete();
        $this->auditLogger->record('announcement.deleted', $announcement, $before, null);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman "'.$title.'" berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(AnnouncementRequest $request): array
    {
        $data = $request->validated();
        $isActive = (bool) ($data['is_active'] ?? false);

        $publishedAt = filled($data['published_at'] ?? null)
            ? Carbon::parse($data['published_at'])
            : ($isActive ? now() : null);

        return [
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'is_active' => $isActive,
            'published_at' => $publishedAt,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Announcement $announcement): array
    {
        return [
            'title' => $announcement->title,
            'body' => $announcement->body,
            'audience' => $announcement->audience->value,
            'is_active' => $announcement->is_active,
            'published_at' => $announcement->published_at?->toDateTimeString(),
            'created_by' => $announcement->created_by,
        ];
    }

    private function stringQuery(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }
}
