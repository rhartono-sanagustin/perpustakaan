@props(['size' => 'md'])
@inject('identity', 'App\Services\LibraryIdentity')

<img
    src="{{ $identity->logoUrl() }}"
    alt=""
    class="brand-logo brand-logo-{{ $size }}"
    width="64"
    height="64"
>
