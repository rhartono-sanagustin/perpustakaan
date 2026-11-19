@props([
    'mode' => 'admin',
    'endpoint',
    'sound' => false,
])

<div data-notifications data-mode="{{ $mode }}" data-endpoint="{{ $endpoint }}" class="relative">
    <flux:dropdown position="bottom" align="end">
        <flux:button
            variant="subtle"
            square
            icon="bell"
            aria-label="Notifikasi"
            data-notification-trigger
            class="relative"
        >
            <span
                data-notification-badge
                class="absolute -end-1 -top-1 hidden min-w-5 rounded-full bg-red-500 px-1 text-center text-[0.65rem] leading-5 font-semibold text-white"
            >0</span>
        </flux:button>

        <flux:menu class="w-80! max-w-[calc(100vw-2rem)]! p-0!">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-4 py-3 dark:border-white/10">
                <span class="text-sm font-semibold">Notifikasi</span>

                @if ($sound)
                    <button
                        type="button"
                        data-notification-sound
                        aria-pressed="true"
                        aria-label="Matikan suara notifikasi"
                        class="inline-flex size-8 items-center justify-center rounded-md text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-800 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <flux:icon.speaker-wave data-sound-on variant="mini" class="size-4" />
                        <flux:icon.speaker-x-mark data-sound-off variant="mini" class="hidden size-4" />
                    </button>
                @endif
            </div>

            <div data-notification-body class="max-h-80 overflow-y-auto">
                <div class="notification-empty">Memuat notifikasi...</div>
            </div>

            <div data-notification-updated class="notification-footer"></div>
        </flux:menu>
    </flux:dropdown>
</div>
