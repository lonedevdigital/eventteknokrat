<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-success navbar-dark border-bottom-0 text-sm">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{ auth()->guard('web')->user()->name }}
                <i class="far fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item dropdown-footer" href="{{ route('profile.edit') }}"> <i class="fa fa-edit"></i>
                    Perbarui Password
                </a>
                <a class="dropdown-item dropdown-footer" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </li>
        </li>

    </ul>
</nav>
<!-- /.navbar -->
