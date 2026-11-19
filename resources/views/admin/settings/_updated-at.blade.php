@php($setting = $settings->get($settingKey))

@if ($setting)
    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
        Terakhir diperbarui {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
        @if ($setting->editor)
            oleh {{ $setting->editor->name }}
        @endif
    </div>
@else
    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Masih menggunakan nilai awal.</div>
@endif
