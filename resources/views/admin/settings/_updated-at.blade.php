@php($setting = $settings->get($settingKey))

@if ($setting)
    <div class="small text-muted mt-1">
        Terakhir diperbarui {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
        @if ($setting->editor)
            oleh {{ $setting->editor->name }}
        @endif
    </div>
@else
    <div class="small text-muted mt-1">Masih menggunakan nilai awal.</div>
@endif
