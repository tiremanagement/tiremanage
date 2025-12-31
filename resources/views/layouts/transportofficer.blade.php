<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Transport Officer')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    @include('partials.theme')
    @stack('styles')
</head>

<body>
    {{-- Transport Officer Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('transport_officer.pending') }}">
                <img src="{{ asset('assets/images/logo3.png') }}" alt="logo"
                    style="height:36px; width:auto; margin-right:20px; margin-bottom: 4px;" />
                <span>Transport Officer</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#toNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="toNavbar">
                <ul class="navbar-nav ms-auto">
                    {{-- Dashboard / Pending Requests --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transport_officer.pending') ? 'active' : '' }}" href="{{ route('transport_officer.pending') }}">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    {{-- Approved Requests --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transport_officer.approved') ? 'active' : '' }}" href="{{ route('transport_officer.approved') }}">
                            <i class="bi bi-check2-circle me-1"></i> Approved
                        </a>
                    </li>
                    {{-- Rejected Requests --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transport_officer.rejected') ? 'active' : '' }}" href="{{ route('transport_officer.rejected') }}">
                            <i class="bi bi-x-circle me-1"></i> Rejected
                        </a>
                    </li>
                    {{-- Logout --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        @yield('content')
    </div>

    @include('partials.footer')

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')

    @if(session('success'))
        <div class="alert alert-success shadow-sm mx-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

</body>

</html>
