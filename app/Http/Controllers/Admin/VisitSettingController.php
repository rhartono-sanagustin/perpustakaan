<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateVisitSettingsRequest;
use App\Models\LibrarySetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VisitSettingController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function edit(): View
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $setting = LibrarySetting::query()
            ->with('editor')
            ->where('key', 'visit_kiosk_enabled')
            ->first();

        return view('admin.settings.visits', [
            'setting' => $setting,
            'enabled' => $setting === null
                ? true
                : filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function update(UpdateVisitSettingsRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $enabled = $request->boolean('visit_kiosk_enabled');
        $setting = LibrarySetting::query()->firstOrNew(['key' => 'visit_kiosk_enabled']);
        $oldValue = $setting->exists
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : null;

        if ($setting->exists && $oldValue === $enabled) {
            return redirect()
                ->route('admin.settings.visits.edit')
                ->with('info', 'Tidak ada perubahan pengaturan yang disimpan.');
        }

        $setting->fill([
            'value' => $enabled ? '1' : '0',
            'type' => 'boolean',
            'group_name' => 'kunjungan',
            'label' => 'Kiosk kunjungan mandiri',
            'description' => 'Izinkan pengunjung mencatat kunjungan sendiri melalui halaman kiosk publik.',
            'updated_by' => $request->user()?->id,
        ]);

        $setting->save();

        $this->auditLogger->record(
            'library_setting.updated',
            $setting,
            ['key' => 'visit_kiosk_enabled', 'value' => $oldValue],
            ['key' => 'visit_kiosk_enabled', 'value' => $enabled]
        );

        return redirect()
            ->route('admin.settings.visits.edit')
            ->with(
                'success',
                $enabled
                    ? 'Kiosk kunjungan mandiri berhasil diaktifkan.'
                    : 'Kiosk kunjungan mandiri berhasil dinonaktifkan.'
            );
    }
}
