<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLTMOBITEL - @yield('title')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        body {
            padding-top: 70px; /* Space for fixed navbar */
            background-color: #f8f9fa;
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
            margin-right: 10px;
        }

        .container {
            margin-top: 20px;
        }

        @media (max-width: 576px) {
            .navbar-brand span {
                font-size: 1rem;
            }
            .btn-sm {
                font-size: 0.9rem;
                padding: 6px 10px;
            }
        }
    </style>

    {{-- View-specific styles --}}
    @stack('styles')
</head>
<body>
    <!-- Responsive Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            @php
                $homeUrl = route('login');
                if (Auth::check() && Auth::user()->role) {
                    $roleName = strtolower(trim(Auth::user()->role->name));
                    if (str_contains($roleName, 'admin')) {
                        $homeUrl = route('admin.dashboard');
                    } elseif (str_contains($roleName, 'driver')) {
                        $homeUrl = route('driver.dashboard');
                    } elseif (str_contains($roleName, 'section')) {
                        $homeUrl = route('section_manager.dashboard');
                    } elseif (str_contains($roleName, 'mechanic')) {
                        $homeUrl = route('mechanic_officer.pending');
                    } elseif (str_contains($roleName, 'transport')) {
                        $homeUrl = route('transport_officer.dashboard');
                    }
                }
            @endphp
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ $homeUrl }}">
                <img src="{{ asset('assets/images/logo2.png') }}" alt="SLTMOBITEL Logo">
                <span>SLTMOBITEL</span>
            </a>

            <!-- Toggler for small screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    @auth
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page content -->
    <main class="container">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    {{-- View-specific scripts --}}
    @stack('scripts')
</body>
</html>
