@props(['size' => 'md'])
@inject('identity', 'App\Services\LibraryIdentity')

@php
    $classes = match ($size) {
        'sm' => 'h-9 max-w-[9.5rem]',
        'lg' => 'h-14 max-w-[12.5rem]',
        default => 'h-12 max-w-[11rem]',
    };
@endphp

<img
    src="{{ $identity->logoUrl() }}"
    alt=""
    {{ $attributes->class(['block w-auto shrink-0 object-contain '.$classes]) }}
    width="64"
    height="64"
>
