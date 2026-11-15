<ul class="nav nav-tabs flex-wrap mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.identity.*') ? 'active' : '' }}" href="{{ route('admin.settings.identity.edit') }}">
            <i class="bi bi-building me-1" aria-hidden="true"></i>Identitas Sekolah
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="bi bi-sliders me-1" aria-hidden="true"></i>Kebijakan Sirkulasi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.carousel.*') ? 'active' : '' }}" href="{{ route('admin.settings.carousel.index') }}">
            <i class="bi bi-images me-1" aria-hidden="true"></i>Carousel Beranda
        </a>
    </li>
</ul>
