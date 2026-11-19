<flux:modal name="confirm" class="min-w-[22rem] max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Tindakan</flux:heading>
            <flux:text class="mt-2" data-confirm-message>
                Apakah Anda yakin ingin melanjutkan tindakan ini?
            </flux:text>
        </div>

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled">Batalkan</flux:button>
            </flux:modal.close>
            <flux:button variant="primary" data-confirm-accept>Ya, Lanjutkan</flux:button>
        </div>
    </div>
</flux:modal>
