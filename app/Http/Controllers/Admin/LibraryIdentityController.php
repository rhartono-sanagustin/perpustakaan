<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLibraryIdentityRequest;
use App\Models\LibrarySetting;
use App\Services\AuditLogger;
use App\Services\LibraryIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class LibraryIdentityController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly LibraryIdentity $identity,
    ) {}

    public function edit(): View
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $definitions = config('library.identity_definitions', []);

        $settings = LibrarySetting::query()
            ->with('editor')
            ->whereIn('key', array_keys($definitions))
            ->get()
            ->keyBy('key');

        return view('admin.settings.identity', [
            'definitions' => $definitions,
            'settings' => $settings,
            'identity' => $this->identity,
        ]);
    }

    public function update(UpdateLibraryIdentityRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', LibrarySetting::class);

        $definitions = config('library.identity_definitions', []);
        $data = $request->validated();
        $oldLogo = $this->identity->logoPath();
        $newLogo = $oldLogo;
        $storedLogo = null;

        if ($request->hasFile('logo')) {
            /** @var UploadedFile $file */
            $file = $request->file('logo');
            $storedLogo = $file->store(
                config('library.branding.path', 'branding'),
                config('library.branding.disk', 'public')
            );
            $newLogo = $storedLogo;
        } elseif ($request->boolean('remove_logo')) {
            $newLogo = null;
        }

        $values = [
            'school_name' => trim((string) $data['school_name']),
            'library_name' => trim((string) $data['library_name']),
            'tagline' => trim((string) ($data['tagline'] ?? '')),
            'logo_path' => $newLogo,
        ];

        $current = [
            'school_name' => $this->identity->schoolName(),
            'library_name' => $this->identity->libraryName(),
            'tagline' => $this->identity->tagline(),
            'logo_path' => $oldLogo,
        ];

        $updated = 0;

        try {
            DB::transaction(function () use ($request, $definitions, $values, $current, &$updated): void {
                foreach ($values as $key => $value) {
                    if ($current[$key] === $value) {
                        continue;
                    }

                    if ($key === 'logo_path' && $value === null && ! LibrarySetting::query()->where('key', $key)->exists()) {
                        continue;
                    }

                    $definition = $definitions[$key] ?? null;

                    $setting = LibrarySetting::query()->updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'type' => 'string',
                            'group_name' => 'Identitas Sekolah',
                            'label' => $definition['label'] ?? $key,
                            'description' => $definition['description'] ?? null,
                            'updated_by' => $request->user()?->id,
                        ]
                    );

                    $this->auditLogger->record(
                        'library_identity.updated',
                        $setting,
                        ['key' => $key, 'value' => $current[$key]],
                        ['key' => $key, 'value' => $value]
                    );

                    $updated++;
                }
            });
        } catch (Throwable $exception) {
            if ($storedLogo !== null) {
                Storage::disk(config('library.branding.disk', 'public'))->delete($storedLogo);
            }

            throw $exception;
        }

        if ($oldLogo !== null && $oldLogo !== $newLogo) {
            Storage::disk(config('library.branding.disk', 'public'))->delete($oldLogo);
        }

        $this->identity->forget();

        return redirect()
            ->route('admin.settings.identity.edit')
            ->with(
                'success',
                $updated > 0
                    ? 'Identitas sekolah berhasil diperbarui.'
                    : 'Tidak ada perubahan identitas yang disimpan.'
            );
    }
}
