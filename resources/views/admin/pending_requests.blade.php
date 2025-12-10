@extends('layouts.admin')

@section('title', 'Pending Requests Overview')

@push('styles')
<style>
    .pending-shell {
        position: relative;
        padding: 1.25rem 0 2.5rem;
        background: linear-gradient(180deg, #eef3fb 0%, #fdfefe 70%);
    }
    .pending-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(520px 360px at 12% 14%, rgba(59,130,246,.12), transparent 60%),
            radial-gradient(520px 360px at 86% 12%, rgba(74,222,128,.1), transparent 60%),
            radial-gradient(640px 480px at 45% 70%, rgba(14,165,233,.08), transparent 70%);
        z-index: 0;
        pointer-events: none;
    }
    .pending-wrapper {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        z-index: 1;
    }
    .pending-hero {
        background: #0b4fb4;
        color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 16px 36px rgba(11,79,180,.28);
        padding: 1.4rem 1.6rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
    }
    .pending-hero h2 { margin: 0; font-weight: 800; letter-spacing: .2px; }
    .pending-hero .meta { opacity: .92; font-weight: 600; }
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin: 1.2rem 0 1.6rem;
    }
    .metric-card {
        background: #ffffff;
        border: 1px solid rgba(15,23,42,.06);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        box-shadow: 0 12px 28px rgba(15,23,42,.10);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .metric-card .label { font-weight: 700; color: #0f172a; margin: 0; }
    .metric-card .value { font-weight: 900; font-size: 1.5rem; color: #0b4fb4; }
    .metric-card .pill {
        background: rgba(11,79,180,.08);
        color: #0b4fb4;
        border-radius: 999px;
        padding: .25rem .65rem;
        font-weight: 700;
        font-size: .9rem;
    }
    .tab-card {
        background: #ffffff;
        border: 1px solid rgba(15,23,42,.06);
        border-radius: 16px;
        box-shadow: 0 14px 32px rgba(15,23,42,.12);
        overflow: hidden;
    }
    .tab-card .nav-link {
        border: none;
        border-radius: 12px;
        padding: .85rem 1rem;
        font-weight: 700;
        color: #0f172a;
        background: #e7effc;
        margin-right: .4rem;
        transition: all .16s ease;
    }
    .tab-card .nav-link.active {
        background: linear-gradient(90deg, #0ba6df, #0b6edb);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(11,110,219,.28);
    }
    .tab-card .nav-link:last-child { margin-right: 0; }
    .tab-pane { padding: 1rem; }
    .table-modern {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(15,23,42,.08);
    }
    .table-modern thead th {
        background: #f3f6fb;
        border-bottom: 1px solid #e3e9f5;
        color: #0f172a;
        font-weight: 800;
    }
    .table-modern tbody tr:hover { background: #f8fbff; }
    .badge-soft {
        border: 1px solid currentColor;
        background: rgba(255,255,255,.35);
        font-weight: 700;
    }
    .empty-state {
        padding: 1.5rem;
        text-align: center;
        color: #64748b;
        font-weight: 700;
    }
    @media (max-width: 768px) {
        .pending-hero { flex-direction: column; align-items: flex-start; }
        .tab-card .nav-link { width: 100%; margin-right: 0; margin-bottom: .4rem; }
    }
</style>
@endpush

@section('content')
<div class="pending-shell">
    <div class="pending-wrapper">
        <div class="pending-hero mb-4">
            <div>
                <h2>Pending Requests Overview</h2>
                <div class="meta">Track approvals across Section Manager, Mechanic, and Transport officers.</div>
            </div>
            <div class="meta">Live status • Updated just now</div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <div>
                    <p class="label">Section Manager</p>
                    <div class="pill">Pending</div>
                </div>
                <div class="value">{{ $sectionManagerRequests->count() }}</div>
            </div>
            <div class="metric-card">
                <div>
                    <p class="label">Mechanic Officer</p>
                    <div class="pill">Pending</div>
                </div>
                <div class="value">{{ $mechanicOfficerRequests->count() }}</div>
            </div>
            <div class="metric-card">
                <div>
                    <p class="label">Transport Officer</p>
                    <div class="pill">Pending</div>
                </div>
                <div class="value">{{ $transportOfficerRequests->count() }}</div>
            </div>
        </div>

        <div class="tab-card">
            <div class="p-3 pb-0">
                <ul class="nav nav-pills flex-wrap" id="pendingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="section-tab" data-bs-toggle="tab" data-bs-target="#section" type="button" role="tab" aria-controls="section" aria-selected="true">
                            <i class="bi bi-people me-1"></i> Section Manager
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mechanic-tab" data-bs-toggle="tab" data-bs-target="#mechanic" type="button" role="tab" aria-controls="mechanic" aria-selected="false">
                            <i class="bi bi-wrench me-1"></i> Mechanic Officer
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transport-tab" data-bs-toggle="tab" data-bs-target="#transport" type="button" role="tab" aria-controls="transport" aria-selected="false">
                            <i class="bi bi-truck me-1"></i> Transport Officer
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                {{-- Section Manager --}}
                <div class="tab-pane fade show active" id="section" role="tabpanel" aria-labelledby="section-tab">
                    @if($sectionManagerRequests->isEmpty())
                        <div class="empty-state">No pending requests at Section Manager level.</div>
                    @else
                        <div class="table-responsive table-modern">
                            <table class="table mb-0 align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Tyre Size</th>
                                        <th>Tyre Brand</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sectionManagerRequests as $req)
                                    <tr>
                                        <td>{{ $req->id }}</td>
                                        <td>{{ $req->user->name ?? 'N/A' }}</td>
                                        <td>{{ $req->vehicle->plate_no ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->size ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->brand ?? 'N/A' }}</td>
                                        <td>{{ optional($req->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge badge-soft text-warning">Pending</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Mechanic Officer --}}
                <div class="tab-pane fade" id="mechanic" role="tabpanel" aria-labelledby="mechanic-tab">
                    @if($mechanicOfficerRequests->isEmpty())
                        <div class="empty-state">No pending requests at Mechanic Officer level.</div>
                    @else
                        <div class="table-responsive table-modern">
                            <table class="table mb-0 align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Tyre Size</th>
                                        <th>Tyre Brand</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mechanicOfficerRequests as $req)
                                    <tr>
                                        <td>{{ $req->id }}</td>
                                        <td>{{ $req->user->name ?? 'N/A' }}</td>
                                        <td>{{ $req->vehicle->plate_no ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->size ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->brand ?? 'N/A' }}</td>
                                        <td>{{ optional($req->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge badge-soft text-warning">Pending</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Transport Officer --}}
                <div class="tab-pane fade" id="transport" role="tabpanel" aria-labelledby="transport-tab">
                    @if($transportOfficerRequests->isEmpty())
                        <div class="empty-state">No pending requests at Transport Officer level.</div>
                    @else
                        <div class="table-responsive table-modern">
                            <table class="table mb-0 align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Tyre Size</th>
                                        <th>Tyre Brand</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transportOfficerRequests as $req)
                                    <tr>
                                        <td>{{ $req->id }}</td>
                                        <td>{{ $req->user->name ?? 'N/A' }}</td>
                                        <td>{{ $req->vehicle->plate_no ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->size ?? 'N/A' }}</td>
                                        <td>{{ $req->tire->brand ?? 'N/A' }}</td>
                                        <td>{{ optional($req->created_at)->format('Y-m-d') }}</td>
                                        <td><span class="badge badge-soft text-warning">Pending</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
