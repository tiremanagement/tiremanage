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
      :root {
        --driver-blue: #0b4fb4;
        --driver-blue-dark: #0a3f99;
        --driver-text-light: #e8f0ff;
        --nav-height: 78px;
        --subnav-height: 56px;
      }
      body {
        padding-top: calc(var(--nav-height) + var(--subnav-height));
        background: #f5f7fb;
      }

      /* Top bar */
      .driver-topbar {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 1030;
        background: var(--driver-blue);
        color: #fff;
        min-height: var(--nav-height);
        box-shadow: 0 8px 18px rgba(0,0,0,0.18);
      }
      .driver-topbar .inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        gap: 12px;
      }
      .driver-topbar .brand {
        display: flex;
        align-items: center;
        gap: 14px;
      }
      .driver-topbar .brand img { height: 42px; width: auto; }
      .driver-topbar .title {
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: .25px;
      }
      .driver-topbar .status {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
        font-size: 0.93rem;
      }
      .driver-topbar .status .muted { opacity: 0.9; }
      .driver-topbar .logout-btn {
        color: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.7);
        background: transparent;
        transition: background .18s ease, transform .12s ease, box-shadow .18s ease;
      }
      .driver-topbar .logout-btn:hover {
        background: rgba(255,255,255,0.1);
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.18);
      }
      .driver-topbar .logout-btn:focus { box-shadow: 0 0 0 .15rem rgba(255,255,255,0.35); }

      /* Sub navigation */
      .driver-subnav {
        position: fixed;
        top: var(--nav-height);
        left: 0; right: 0;
        z-index: 1025;
        background: #ffffff;
        min-height: var(--subnav-height);
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
      }
      .driver-subnav .links {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 26px;
        height: var(--subnav-height);
        font-weight: 700;
        max-width: 1180px;
        margin: 0 auto;
      }
      .driver-subnav a {
        color: #0a0f1a;
        text-decoration: none;
        padding: 8px 4px 6px;
        border-bottom: 3px solid transparent;
      }
      .driver-subnav a.active {
        color: #0c8a1f;
        border-color: #0c8a1f;
      }
      .driver-subnav a:hover { color: #0b4fb4; }

      @media (max-width: 992px) {
        body { padding-top: calc(var(--nav-height) + var(--subnav-height)); }
        .driver-topbar .inner { flex-direction: column; align-items: flex-start; }
        .driver-topbar .status { align-items: flex-start; }
        .driver-subnav .links { flex-wrap: wrap; gap: 14px; padding: 6px 14px; justify-content: flex-start; }
      }

      /* Content spacing below navbar */
      .page-content-wrapper {
        margin-top: 150px;
      }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Top bar --}}
    <header class="driver-topbar">
        <div class="inner container-fluid">
            <div class="brand">
                <a href="{{ route('driver.dashboard') }}" aria-label="Home">
                    <img src="{{ asset('assets/images/logo2.png') }}" alt="SLT-MOBITEL">
                </a>
                <span class="title">Driver Dashboard</span>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="status">
                    <div id="driverCurrentDateTime">Loading time...</div>
                    <div class="muted" id="driverLastUpdated">Last updated: —</div>
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
    </header>

    {{-- Sub navigation --}}
    <div class="driver-subnav">
        <div class="links container-fluid">
            <a class="{{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">Home</a>
            @if (Route::has('driver.receipts'))
                <a class="{{ request()->routeIs('driver.receipts*') ? 'active' : '' }}" href="{{ route('driver.receipts') }}">Receipts</a>
            @else
                <a href="{{ url('/driver/receipts') }}">Receipts</a>
            @endif
            <a class="{{ request()->routeIs('driver.requests.create') ? 'active' : '' }}" href="{{ route('driver.requests.create') }}">Request Tyre</a>
            <a class="{{ request()->routeIs('driver.requests.index') ? 'active' : '' }}" href="{{ route('driver.requests.index') }}">View Requests</a>
            <a class="{{ request()->routeIs('driver.profile.edit') ? 'active' : '' }}" href="{{ route('driver.profile.edit') }}">Manage Account</a>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="page-content-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap JS (local) --}}
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dateTimeEl = document.getElementById('driverCurrentDateTime');
            var lastUpdatedEl = document.getElementById('driverLastUpdated');
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
