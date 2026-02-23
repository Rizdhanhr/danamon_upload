<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href=".">
                <img src="{{ asset('template') }}/static/logo.svg" width="110" height="32" alt="Tabler"
                    class="navbar-brand-image">
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">

            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm"
                        style="background-image: url({{ asset('template') }}/static/avatars/000m.jpg)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ Auth::user()->role->name }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="./settings.html" class="dropdown-item">Settings</a>
                    <a href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="dropdown-item">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.index') }}">
                            <i class="bi bi-house-door"></i>
                            &nbsp;
                            <span class="nav-link-title">
                                Home
                            </span>
                        </a>
                    </li>
                    @can('VIEW-UPLOAD-RECIPIENT')
                        <li class="nav-item {{ request()->routeIs('upload-recipient.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('upload-recipient.index') }}">
                                <i class="bi bi-upload"></i>
                                &nbsp;
                                <span class="nav-link-title">
                                    Upload Recipient
                                </span>
                            </a>
                        </li>
                    @endcan

                    @canany(['VIEW-ROLE', 'VIEW-USER'])
                        <li class="nav-item dropdown  {{ request()->routeIs(['user.*', 'role.*']) ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" role="button" aria-expanded="false">
                                <i class="bi bi-gear"></i>
                                &nbsp;
                                <span class="nav-link-title">
                                    Access Management
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        @can('VIEW-USER')
                                            <a class="dropdown-item {{ request()->routeIs('user*') ? 'active' : '' }}"
                                                href="{{ route('user.index') }}">
                                                User
                                            </a>
                                        @endcan
                                        @can('VIEW-ROLE')
                                            <a class="dropdown-item {{ request()->routeIs('role*') ? 'active' : '' }}"
                                                href="{{ route('role.index') }}">
                                                Role
                                            </a>
                                        @endcan

                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcanany
                </ul>
                <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">

                </div>
            </div>
        </div>
    </div>
</header>
