@props(['user', 'barcode'])
@inject('identity', 'App\Services\LibraryIdentity')

<article class="member-card" aria-label="Kartu anggota {{ $user->name }}">
    <header class="member-card-header">
        <img src="{{ $identity->logoUrl() }}" alt="" class="member-card-logo">
        <div class="member-card-brand">
            <div class="member-card-school">{{ $identity->schoolName() }}</div>
            <div class="member-card-library">{{ $identity->libraryName() }}</div>
        </div>
    </header>

    <div class="member-card-title">Kartu Anggota Perpustakaan</div>

    <div class="member-card-body">
        <div class="member-card-name">{{ $user->name }}</div>

        <dl class="member-card-meta">
            <div>
                <dt>Nomor Induk</dt>
                <dd>{{ $user->nomor_induk }}</dd>
            </div>
            <div>
                <dt>Peran</dt>
                <dd>{{ $user->roleLabel() }}</dd>
            </div>
            <div>
                <dt>{{ $user->isGuru() ? 'Unit Kerja' : 'Kelas' }}</dt>
                <dd>{{ $user->isGuru() ? ($user->unit_kerja ?? '-') : ($user->kelas ?? '-') }}</dd>
            </div>
        </dl>
    </div>

    <footer class="member-card-footer">
        <div class="member-card-barcode-wrap">{!! $barcode !!}</div>
        <div class="member-card-number">{{ $user->nomor_induk }}</div>
        <div class="member-card-note">Berlaku selama menjadi anggota aktif.</div>
    </footer>
</article>
