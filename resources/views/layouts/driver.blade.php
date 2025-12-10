<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Driver Dashboard')</title>

    {{-- Bootstrap & Icons (local) --}}
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    @include('partials.theme')
    <style>
      /* Driver-specific dark navbar */
      .driver-navbar.navbar { background: linear-gradient(180deg, #0b1220, #111827) !important; border-bottom: 1px solid rgba(148,163,184,.16); box-shadow: 0 10px 28px rgba(2,6,23,.35); }
      .driver-navbar .navbar-brand { color: #e2e8f0 !important; }
      .driver-navbar .navbar-brand img { filter: brightness(1.1) contrast(1.05); }
      .driver-navbar .nav-link { color: #cbd5e1 !important; font-weight: 600; border-radius: 8px; }
      .driver-navbar .nav-link:hover, .driver-navbar .nav-link.active { background: rgba(59,130,246,.12); color: #ffffff !important; }
      .driver-navbar .btn-link.nav-link { color: #fca5a5 !important; }
      .driver-navbar .navbar-toggler { border-color: rgba(250, 250, 250, 0.35); }
      .driver-navbar .navbar-toggler:focus { box-shadow: 0 0 0 .15rem rgba(59,130,246,.35); }
      .driver-navbar .navbar-toggler-icon { filter: invert(1) brightness(1.2); }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Driver Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark driver-navbar fixed-top">
        <div class="container">
             <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('driver.dashboard') }}">
             <img src="{{ asset('assets/images/logo2.png') }}" alt="logo"
                 style="height:36px; width:auto; margin-right:15px; margin-bottom: 4px;">
                <span>Driver Dashboard</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#driverNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="driverNavbar">
                <ul class="navbar-nav ms-auto">
                    {{-- Home Tab: explicit link to dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                          Home
                        </a>
                    </li>

                    {{-- Receipts: safe link that checks available route names or falls back to URL --}}
                    <li class="nav-item">
                        @if (Route::has('driver.receipts'))
                            <a class="nav-link {{ request()->routeIs('driver.receipts*') ? 'active' : '' }}" href="{{ route('driver.receipts') }}">Receipts</a>
                        @else
                            <a class="nav-link" href="{{ url('/driver/receipts') }}">Receipts</a>
                        @endif
                    </li>

                    {{-- Request Tire --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('driver.requests.create') }}">
                          Request Tire
                        </a>
                    </li>

                    {{-- View Requests --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('driver.requests.index') }}">
                         View Requests
                        </a>
                    </li>
                    {{-- Manage Account --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('driver.profile.edit') }}">
                            Manage Account
                        </a>
                    </li>

                    {{-- Logout --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">
                             Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <div class="container mt-5 pt-4">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap JS (local) --}}
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
