<aside class="main-sidebar sidebar-light-success elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link bg-success d-flex align-items-center py-1">
        <img src="{{ asset('logo.png') }}" alt="Logo Universitas Teknokrat Indonesia" class="brand-image mr-4">
        <span class="brand-text font-weight-light d-flex flex-column text-center">
            <span style="font-size: 12px">Sistem</span>
            <span style="font-size: 14px">Manajemen Event</span>
        </span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                @include('templateAdminLTE.sidebar.sidebar')

            </ul>
        </nav>
    </div>
</aside>
