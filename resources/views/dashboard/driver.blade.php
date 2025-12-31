@extends('layouts.driver')

@section('title', 'Driver Dashboard')

@section('content')
@php
    $driver = \App\Models\Driver::with('user')->where('user_id', auth()->id())->first();
    $user = auth()->user();
    $name = $driver->full_name ?? $user?->name ?? 'Driver';
    $email = $driver->user->email ?? $user?->email ?? 'N/A';
    $mobile = $driver->mobile ?? 'N/A';
    $idNumber = $driver->id_number ?? 'N/A';
    $profilePhoto = $driver && $driver->profile_photo
        ? asset('storage/' . $driver->profile_photo)
        : asset('assets/images/default-profile.jpg');
    $now = now();

    $unreadReceipts = \App\Models\Receipt::whereHas('tireRequest', function ($query) {
        $query->where('user_id', auth()->id());
    })->where('is_read', false)->count();
@endphp

<div class="driver-dashboard">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flash-msg flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-msg flash-error">{{ session('error') }}</div>
    @endif

    {{-- Welcome --}}
    <div class="welcome-strip">
        <div class="avatar-wrap">
            <img src="{{ $profilePhoto }}" alt="Profile Photo" class="avatar-large">
        </div>
        <div>
            <p class="hello">Welcome, {{ $name }}!</p>
            <p class="hello-sub">{{ $email }}</p>
        </div>
    </div>

    {{-- Driver info card --}}
    <div class="driver-info-card fade-in">
        <div class="card-overlay"></div>
        <h2 class="info-title">Driver Information</h2>
        <div class="info-row">
            <div><strong>Full Name:</strong> {{ $name }}</div>
            <div><strong>Email:</strong> {{ $email }}</div>
            <div><strong>Mobile:</strong> {{ $mobile }}</div>
            <div><strong>ID Number:</strong> {{ $idNumber }}</div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="dashboard-two-col">

        {{-- LEFT COLUMN - Cards --}}
        <div class="left-col">
            <div class="cards-stack">
                <div class="action-card accent-blue clickable" data-href="{{ route('driver.requests.create') }}">
                    <div class="icon-wrap">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Request Tyre</h3>
                        <p class="card-text">Submit a new tyre request quickly and easily.</p>
                    </div>
                </div>

                <div class="action-card accent-green clickable" data-href="{{ route('driver.requests.index') }}">
                    <div class="icon-wrap">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="card-title">View Your Requests</h3>
                        <p class="card-text">Track the status of your tyre requests.</p>
                    </div>
                </div>

                <div class="action-card accent-purple clickable" data-href="{{ route('driver.receipts') }}">
                    @if($unreadReceipts > 0)
                        <span class="notif-badge">{{ $unreadReceipts }}</span>
                    @endif
                    <div class="icon-wrap">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h3 class="card-title">View Receipts</h3>
                        <p class="card-text">Check all your tyre request receipts.</p>
                    </div>
                </div>

                <div class="action-card accent-orange clickable" data-href="{{ route('driver.profile.edit') }}">
                    <div class="icon-wrap">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Manage Account</h3>
                        <p class="card-text">Update your profile and account details.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN - Image --}}
        <div class="right-col">
            <div class="image-panel">
                <div class="image-bg" style="background-image: url('{{ asset('assets/images/driver-right.jpg') }}');"></div>
                <div class="image-overlay"></div>
                <div class="image-content">
                    <h2 class="image-title">Smooth Rides Start Here</h2>
                    <p class="image-desc">Manage your tyre requests &mdash; request, track approvals, and view receipts all in one place.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
/* ===== Page Shell ===== */
.driver-dashboard {
    max-width: 1180px;
    margin: 0 auto;
    padding: 56px 18px 72px;
    position: relative;
}
.driver-dashboard::before,
.driver-dashboard::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(241,247,255,0.7), rgba(255,255,255,0.85));
    z-index: 0;
    pointer-events: none;
}
.driver-dashboard > * { position: relative; z-index: 1; }

/* ===== Welcome ===== */
.welcome-strip {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 16px 18px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 6px 14px rgba(15,23,42,0.06);
    margin-bottom: 20px;
}
.avatar-wrap { position: relative; }
.avatar-large {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    border: 3px solid #0d6efd;
    object-fit: cover;
    box-shadow: 0 10px 24px rgba(13,110,253,0.18);
}
.hello {
    margin: 0;
    font-weight: 700;
    font-size: 20px;
    color: #1d4ed8;
}
.hello-sub { margin: 2px 0 0; color: #1e293b; font-weight: 600; }

/* ===== Driver Info Card ===== */
.driver-info-card {
    position: relative;
    background: url("{{ asset('assets/images/driver-information.png') }}") no-repeat center center/cover;
    border-radius: 16px;
    padding: 22px 26px;
    margin-top: 6px;
    margin-bottom: 26px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.20);
    overflow: hidden;
    color: #fff;
    backdrop-filter: blur(4px);
}
.driver-info-card .card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(6, 42, 85, 0.72), rgba(13, 110, 253, 0.62));
    z-index: 1;
}
.driver-info-card * { position: relative; z-index: 2; }
.driver-info-card:hover { transform: translateY(-3px); transition: transform 0.25s ease; }
.info-title {
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 12px;
}
.info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 10px 22px;
    font-size: 15px;
}

/* ===== Two Column Layout ===== */
.dashboard-two-col {
    display: flex;
    flex-direction: column;
    gap: 22px;
}
@media (min-width: 1024px) {
    .dashboard-two-col {
        flex-direction: row;
        gap: 28px;
        align-items: stretch;
    }
}
.left-col { flex: 1.1; }
.right-col { flex: 0.9; display: flex; align-items: stretch; }

/* ===== Action Cards ===== */
.cards-stack {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.action-card {
    background: #fff;
    border-radius: 14px;
    border: 2px solid #e2e8f0;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    position: relative;
    box-shadow: 0 6px 14px rgba(15,23,42,0.05);
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    cursor: pointer;
}
.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 22px rgba(15,23,42,0.12);
}
.action-card .icon-wrap {
    height: 42px;
    width: 42px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    flex-shrink: 0;
}
.action-card .card-title {
    font-size: 17px;
    margin: 0 0 4px;
    font-weight: 700;
    color: #111827;
}
.action-card .card-text {
    margin: 0;
    color: #475569;
    font-size: 13.5px;
}

.accent-blue { border-color: #b7d2ff; }
.accent-blue .icon-wrap { background: linear-gradient(135deg, #0d6efd, #2563eb); box-shadow: 0 6px 14px rgba(13,110,253,0.18); }
.accent-green { border-color: #c7f3d7; }
.accent-green .icon-wrap { background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 6px 14px rgba(34,197,94,0.18); }
.accent-purple { border-color: #dbc8ff; }
.accent-purple .icon-wrap { background: linear-gradient(135deg, #8b5cf6, #6b21a8); box-shadow: 0 6px 14px rgba(139,92,246,0.18); }
.accent-orange { border-color: #f8d7b0; }
.accent-orange .icon-wrap { background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 6px 14px rgba(234,88,12,0.18); }

/* ===== Image Panel ===== */
.image-panel {
    position: relative;
    height: 480px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(0,0,0,0.14);
    background: #0d6efd;
}
.image-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transition: transform 0.6s ease;
}
.image-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.35), rgba(0,0,0,0.58));
}
.image-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    padding: 28px;
    color: #fff;
    justify-content: flex-end;
}
.image-title {
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 8px;
}
.image-desc {
    max-width: 460px;
    color: rgba(255,255,255,0.9);
}
.image-panel:hover .image-bg { transform: scale(1.05); }

/* ===== Flash Messages ===== */
.flash-msg {
    position: fixed;
    top: 90px;
    right: 25px;
    padding: 12px 18px;
    border-radius: 6px;
    font-weight: bold;
    color: white;
    z-index: 1000;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: fadeOut 4s forwards;
}
.flash-success { background: linear-gradient(135deg, #16a34a, #22c55e); }
.flash-error { background: linear-gradient(135deg, #dc2626, #b91c1c); }
@keyframes fadeOut {
    0% { opacity: 1; }
    80% { opacity: 1; }
    100% { opacity: 0; transform: translateY(-20px); }
}

/* Fade-in animation */
.fade-in { animation: fadeInCard 0.8s ease forwards; opacity: 0; }
@keyframes fadeInCard {
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive tweaks */
@media (max-width: 992px) {
    .image-panel { height: 340px; }
}
@media (max-width: 768px) {
    .driver-dashboard { padding: 22px 10px 60px; }
}

/* Notification Badge */
.notif-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(145deg, #ef4444, #b91c1c);
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
    animation: pulseBadge 1.5s infinite;
    z-index: 10;
}
@keyframes pulseBadge {
    0%   { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.5); }
    70%  { transform: scale(1.12); box-shadow: 0 0 0 8px rgba(239,68,68,0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Fade-out flash messages
    setTimeout(() => {
        document.querySelectorAll('.flash-msg').forEach(msg => {
            msg.style.transition = 'opacity 0.6s, transform 0.6s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-12px)';
            setTimeout(() => msg.remove(), 600);
        });
    }, 4000);

    // Card click navigation
    document.querySelectorAll('.action-card.clickable').forEach(card => {
        card.addEventListener('click', () => {
            const link = card.getAttribute('data-href');
            if (link) window.location.href = link;
        });
    });
});
</script>
@endpush
