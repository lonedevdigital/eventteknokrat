@include('templateAdminLTE.1Header')
@include('templateAdminLTE.2Navbar')
@include('templateAdminLTE.3Aside')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">@yield('sub-breadcrumb')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>
    @yield('modal')
</div>
@include('templateAdminLTE.4Footer')
@include('templateAdminLTE.5Javascript')
