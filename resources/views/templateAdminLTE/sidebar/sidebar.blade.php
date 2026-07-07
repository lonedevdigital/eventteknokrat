@php
    $currentUser = auth()->user();
    $isPenanggungJawab = $currentUser?->isPenanggungJawab() ?? false;
    $isKetuaPelaksana  = $currentUser?->isKetuaPelaksana() ?? false;
    $isAdminCore = $currentUser && ($currentUser->isSuperUser() || $currentUser->isBaak() || $currentUser->isKemahasiswaan());

    $eventMenuOpen = request()->routeIs('events.*')
        || request()->routeIs('events.recommendations.*')
        || request()->routeIs('event-categories.*')
        || request()->routeIs('certificates.*');
@endphp

{{-- ============ KETUA PELAKSANA: menu fokus (hanya Lomba) ============ --}}
@if($isKetuaPelaksana)
<li class="nav-item">
    <a href="{{ route('lomba.index') }}"
       class="nav-link {{ request()->routeIs('lomba.*') ? 'active' : '' }}">
        <i class="fa fa-trophy nav-icon {{ request()->routeIs('lomba.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>Lomba Saya</p>
    </a>
</li>
@else

@if(!$isPenanggungJawab)
<li class="nav-item">
    <a href="{{ url('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
        <i class="fa fa-home nav-icon {{ request()->routeIs('dashboard.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>Halaman Utama</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('data-mahasiswa.index') }}"
       class="nav-link {{ request()->routeIs('data-mahasiswa.*') ? 'active' : '' }}">
        <i class="fa fa-users nav-icon {{ request()->routeIs('data-mahasiswa.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>Mahasiswa</p>
    </a>
</li>
@endif

{{-- Manajemen User (Admin & BAAK/Kemahasiswaan) --}}
@if($isAdminCore)
@php
    $userMgmtOpen = request()->routeIs('user-management.*') || request()->routeIs('role-management.*');
@endphp
<li class="nav-item has-treeview {{ $userMgmtOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $userMgmtOpen ? 'active' : '' }}">
        <i class="fa fa-user-cog nav-icon {{ $userMgmtOpen ? 'text-white' : 'text-dark' }}"></i>
        <p>
            Manajemen User
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('user-management.index') }}"
               class="nav-link {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Daftar User</p>
            </a>
        </li>
        @if($currentUser->isSuperUser())
        <li class="nav-item">
            <a href="{{ route('role-management.index') }}"
               class="nav-link {{ request()->routeIs('role-management.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Manajemen Role</p>
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

<li class="nav-item has-treeview {{ $eventMenuOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $eventMenuOpen ? 'active' : '' }}">
        <i class="fa fa-calendar nav-icon {{ $eventMenuOpen ? 'text-white' : 'text-dark' }}"></i>
        <p>
            Event
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('events.index') }}"
               class="nav-link {{ request()->routeIs('events.index') || request()->routeIs('events.create') || request()->routeIs('events.edit') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Data Event</p>
            </a>
        </li>

        @if(!$isPenanggungJawab)
        <li class="nav-item">
            <a href="{{ route('events.recommendations.index') }}"
               class="nav-link {{ request()->routeIs('events.recommendations.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Rekomendasi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('event-categories.index') }}"
               class="nav-link {{ request()->routeIs('event-categories.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Kategori</p>
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('certificates.index') }}"
               class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Sertifikat</p>
            </a>
        </li>
    </ul>
</li>

{{-- Lomba (admin core & Penanggung Jawab) --}}
<li class="nav-item">
    <a href="{{ route('lomba.index') }}"
       class="nav-link {{ request()->routeIs('lomba.*') ? 'active' : '' }}">
        <i class="fa fa-trophy nav-icon {{ request()->routeIs('lomba.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>{{ $isPenanggungJawab ? 'Lomba Saya' : 'Kelola Lomba' }}</p>
    </a>
</li>

@if(!$isPenanggungJawab)
{{-- Info Terkini --}}
<li class="nav-item">
    <a href="{{ route('infos.index') }}"
       class="nav-link {{ request()->routeIs('infos.*') ? 'active' : '' }}">
        <i class="fa fa-info-circle nav-icon {{ request()->routeIs('infos.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>Info Terkini</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('sponsors.index') }}"
       class="nav-link {{ request()->routeIs('sponsors.*') ? 'active' : '' }}">
        <i class="fa fa-handshake nav-icon {{ request()->routeIs('sponsors.*') ? 'text-white' : 'text-dark' }}"></i>
        <p>Sponsor</p>
    </a>
</li>
@endif

@endif {{-- /isKetuaPelaksana --}}
