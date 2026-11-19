@props(['title' => 'Periksa kembali data berikut:'])

@if ($errors->any())
    <flux:callout variant="danger" icon="x-circle" class="mb-4">
        <flux:callout.heading>{{ $title }}</flux:callout.heading>
        <flux:callout.text>
            <ul class="list-disc space-y-1 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:callout.text>
    </flux:callout>
@endif
