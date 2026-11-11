<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLibrarySettingsRequest;
use App\Models\LibrarySetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LibrarySettingController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $definitions = config('library.setting_definitions', []);

        $settings = LibrarySetting::query()
            ->with('editor')
            ->whereIn('key', array_keys($definitions))
            ->get()
            ->keyBy('key');

        return view('admin.settings.index', [
            'definitions' => $definitions,
            'settings' => $settings,
        ]);
    }

    public function update(UpdateLibrarySettingsRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $definitions = config('library.setting_definitions', []);
        $updated = 0;

        DB::transaction(function () use ($request, $definitions, &$updated): void {
            foreach ($definitions as $key => $definition) {
                $setting = LibrarySetting::query()->firstOrNew(['key' => $key]);
                $oldValue = $setting->exists ? (int) $setting->value : null;
                $newValue = (int) $request->validated($key);

                if ($oldValue === $newValue) {
                    continue;
                }

                $setting->fill([
                    'value' => (string) $newValue,
                    'type' => 'integer',
                    'group_name' => $definition['group'],
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'updated_by' => $request->user()?->id,
                ]);

                $setting->save();

                $this->auditLogger->record(
                    'library_setting.updated',
                    $setting,
                    ['key' => $key, 'value' => $oldValue],
                    ['key' => $key, 'value' => $newValue]
                );

                $updated++;
            }
        });

        return redirect()
            ->route('admin.settings.index')
            ->with(
                'success',
                $updated > 0
                    ? $updated.' pengaturan berhasil diperbarui. Perubahan berlaku untuk transaksi baru.'
                    : 'Tidak ada perubahan pengaturan yang disimpan.'
            );
    }
}
