<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Bootstrap (local) -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Custom Admin Theme (Light, neutral palette with brand navbar) -->
    <style>
        :root {
            --nav-h: 96px;
            --bg: #f5f7fb;
            --surface: #f9fafb;
            --muted: #e5e7eb;
            --text: #111827;
            --border: #e5e7eb;
            --shadow: 0 10px 28px rgba(0, 0, 0, 0.16);
            --primary: #0b4fb4;
            --primary-600: #0a3f99;
            --brand-green: #39b54a;
            --danger: #ef4444;
            --primary-focus: rgba(11, 79, 180, 0.16);
        }

        /* Body and typography */
        body {
            background: var(--bg) url("{{ asset('assets/images/background.jpg') }}") center center / cover fixed no-repeat;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            line-height: 1.5;
        }

        /* Hero-style admin navbar */
        .navbar.admin-hero {
            background: #0b4fb4 !important;
            border: none;
            box-shadow: var(--shadow);
            padding: 1rem 0;
            min-height: var(--nav-h);
        }
        .admin-hero .nav-inner { display: flex; align-items: center; justify-content: flex-start; gap: 3rem; }
        .admin-hero .brand-block { display: flex; align-items: center; gap: .75rem; padding-left: 4.25rem; padding-right: 45rem; }
    .admin-hero .brand-logo { height: 46px; width: auto; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,.28)); }
        .admin-hero .status-block {
            color: #ffffff;
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            justify-content: flex-end;
            margin-left: auto;
        }
        .admin-hero .status-block .date-time { font-weight: 700; letter-spacing: .25px; }
        .admin-hero .status-block .muted { opacity: .9; font-size: .93rem; }
        .admin-hero .logout-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,.75);
            color: #ffffff;
            border-radius: 10px;
            padding: .55rem .95rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            transition: background-color .18s ease, transform .12s ease, box-shadow .18s ease;
        }
        .admin-hero .logout-btn:hover { background: rgba(255,255,255,.1); transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,.18); }
        .admin-hero .logout-btn:focus { box-shadow: 0 0 0 .16rem rgba(255,255,255,.35); }
        .admin-hero .logout-btn .bi { font-size: 1.05rem; }

        /* Main container spacing */
        .container { padding-top: .5rem; padding-bottom: 2rem; }

        /* Ensure space for fixed navbar */
        body { padding-top: calc(var(--nav-h) + 10px); }

        /* Cards */
        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
            background: var(--surface);
            color: var(--text);
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(17,24,39,.08); }
        .card-header { background: #f9fafb; border-bottom-color: var(--border); color: var(--text); font-weight: 600; }

        /* Buttons */
        .btn { border-radius: 10px; font-weight: 600; letter-spacing: .2px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-600); border-color: var(--primary-600); }
        .btn-success { background: var(--success); border-color: var(--success); }
        .btn-success:hover { background: var(--success-600); border-color: var(--success-600); }
        .btn-warning { background: var(--warning); border-color: var(--warning); color: #111827; }
        .btn-warning:hover { background: var(--warning-600); border-color: var(--warning-600); color: #111827; }
        .btn-danger { background: var(--danger); border-color: var(--danger); }
        .btn-danger:hover { background: var(--danger-600); border-color: var(--danger-600); }
        .btn-elevated { box-shadow: var(--shadow); transition: transform .15s ease, box-shadow .15s ease; }
        .btn-elevated:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(0,0,0,.22); }
        .btn:focus { box-shadow: 0 0 0 .16rem var(--primary-focus); }
        .btn .bi { margin-right: .4rem; position: relative; top: -1px; }

        /* Modern icon action buttons */
        .btn-icon { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; }
        .btn-sm.btn-icon { width: 30px; height: 30px; }
        .btn-icon .bi { margin: 0; top: 0; }
        .action-buttons { display: inline-flex; gap: .35rem; }

        /* Outline buttons for dark theme */
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
        .btn-outline-primary:hover { color: #fff; background-color: var(--primary); border-color: var(--primary); }
        .btn-outline-success { color: var(--success); border-color: var(--success); }
        .btn-outline-success:hover { color: #fff; background-color: var(--success); border-color: var(--success); }
        .btn-outline-warning { color: var(--warning); border-color: var(--warning); }
        .btn-outline-warning:hover { color: #111827; background-color: var(--warning); border-color: var(--warning); }
        .btn-outline-danger { color: var(--danger); border-color: var(--danger); }
        .btn-outline-danger:hover { color: #fff; background-color: var(--danger); border-color: var(--danger); }
        .btn-outline-dark { color: #e5e7eb; border-color: #4b5563; }
        .btn-outline-dark:hover { color: #fff; background-color: #374151; border-color: #374151; }

        /* Tables */
        table { background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        thead th { background: #f3f4f6; color: #111827; font-weight: 700; }
        th, td { vertical-align: middle; }
        tbody tr { transition: background-color .15s ease; }
        tbody tr:hover { background: rgba(255,255,255,0.03); }
        .table > :not(caption) > * > * { padding: .85rem 1rem; }

        /* Links */
        a { color: var(--primary); }
        a:hover { color: var(--primary-600); }

        /* Forms */
        .form-control { border-radius: 10px; border-color: var(--border); background: #ffffff; color: var(--text); }
        .form-control::placeholder { color: #9ca3af; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem var(--primary-focus); background: #ffffff; color: var(--text); }

        /* Footer intentionally removed from admin layout */

        /* Responsive */
        @media (max-width: 992px) {
            .admin-hero .nav-inner { flex-direction: column; align-items: flex-start; }
            .admin-hero .status-block { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
            .admin-hero .status-block .date-time { font-size: .95rem; text-align: right; }
            .admin-hero .status-block .muted { text-align: right; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark admin-hero fixed-top">
        <div class="container-fluid px-4">
            <div class="nav-inner">
                <div class="brand-block">
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
                                // Mechanic officer doesn't have a 'dashboard' route; use pending list
                                $homeUrl = route('mechanic_officer.pending');
                            } elseif (str_contains($roleName, 'transport')) {
                                $homeUrl = route('transport_officer.dashboard');
                            }
                        }
                    @endphp
                    <a href="{{ $homeUrl }}" aria-label="Home">
                        <img src="{{ asset('assets/images/logo2.png') }}" alt="SLT-MOBITEL" class="brand-logo">
                    </a>
                </div>
                <div class="status-block d-flex align-items-center gap-4 flex-wrap">
                    <div class="d-flex flex-column align-items-end text-end">
                        <div class="date-time" id="adminCurrentDateTime">Loading time...</div>
                        <div class="muted" id="adminLastUpdated">Last updated: —</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    

    <!-- Bootstrap JS (local) -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Live date/time display to mirror the reference header layout
            var dateTimeEl = document.getElementById('adminCurrentDateTime');
            var lastUpdatedEl = document.getElementById('adminLastUpdated');
            if (dateTimeEl && lastUpdatedEl) {
                var lastUpdatedSet = false;
                var optionsDate = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
                var optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

                var updateClock = function () {
                    var now = new Date();
                    var dateString = now.toLocaleDateString('en-US', optionsDate);
                    var timeString = now.toLocaleTimeString('en-US', optionsTime);
                    dateTimeEl.textContent = dateString + ' | ' + timeString;

                    if (!lastUpdatedSet) {
                        lastUpdatedEl.textContent = 'Last updated: ' + timeString;
                        lastUpdatedSet = true;
                    }
                };

                updateClock();
                setInterval(updateClock, 1000);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
