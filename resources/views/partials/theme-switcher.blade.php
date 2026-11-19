<div x-data>
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="subtle" square aria-label="Ubah tema tampilan" data-theme-switcher>
            <flux:icon.sun x-show="! $flux.dark" x-cloak variant="mini" class="size-5!" />
            <flux:icon.moon x-show="$flux.dark" x-cloak variant="mini" class="size-5!" />
        </flux:button>

        <flux:menu class="min-w-44!">
            <flux:menu.radio.group x-model="$flux.appearance">
                <flux:menu.radio value="light">Terang</flux:menu.radio>
                <flux:menu.radio value="dark">Gelap</flux:menu.radio>
                <flux:menu.radio value="system">Ikuti Sistem</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</div>
