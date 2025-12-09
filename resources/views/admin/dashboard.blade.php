@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
@php
    $statusLabels = [
        \App\Models\Approval::STATUS_PENDING => ['label' => 'Pending', 'class' => 'warning'],
        \App\Models\Approval::STATUS_PENDING_MECHANIC => ['label' => 'Pending - Mechanic', 'class' => 'info'],
        \App\Models\Approval::STATUS_PENDING_TRANSPORT => ['label' => 'Pending - Transport', 'class' => 'secondary'],
        \App\Models\Approval::STATUS_APPROVED => ['label' => 'Approved', 'class' => 'success'],
        \App\Models\Approval::STATUS_APPROVED_BY_MANAGER => ['label' => 'Approved by Manager', 'class' => 'success'],
        \App\Models\Approval::STATUS_APPROVED_BY_MECHANIC => ['label' => 'Approved by Mechanic', 'class' => 'success'],
        \App\Models\Approval::STATUS_APPROVED_BY_TRANSPORT => ['label' => 'Approved by Transport', 'class' => 'success'],
        \App\Models\Approval::STATUS_REJECTED => ['label' => 'Rejected', 'class' => 'danger'],
        \App\Models\Approval::STATUS_REJECTED_BY_MECHANIC => ['label' => 'Rejected by Mechanic', 'class' => 'danger'],
        \App\Models\Approval::STATUS_REJECTED_BY_TRANSPORT => ['label' => 'Rejected by Transport', 'class' => 'danger'],
    ];
@endphp

<style>
    body {
        background: #f7f9fb;
        background-image:
            radial-gradient(at 20% 20%, rgba(59, 130, 246, 0.08) 0, transparent 35%),
            radial-gradient(at 70% 10%, rgba(16, 185, 129, 0.08) 0, transparent 32%),
            radial-gradient(at 40% 70%, rgba(236, 72, 153, 0.07) 0, transparent 30%);
        color: #111827;
    }

    h2, h3 {
        font-family: 'Poppins', sans-serif;
        color: #0f172a;
        text-shadow: none;
    }

    /* Stats cards */
    .stats-grid { margin-bottom: 1.5rem; }
    .stat-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.15rem 1rem;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e6e9f1;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        transition: transform .18s ease, box-shadow .2s ease, border-color .2s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(17,24,39,.1); border-color: #d7dce8; }
    .stat-card:active { transform: translateY(-1px); }

    .stat-icon {
        width: 44px; height: 44px; min-width: 44px;
        border-radius: 12px;
        display: grid; place-items: center;
        color: #fff;
        box-shadow: 0 10px 18px rgba(17,24,39,.12);
        font-size: 1.25rem;
    }
    .stat-content { flex: 1; }
    .stat-label { margin: 0; font-size: .95rem; color: #8a94a6; font-weight: 600; letter-spacing: .2px; }
    .stat-value { margin: 2px 0 0; font-size: 1.55rem; font-weight: 800; color: #111827; line-height: 1.1; }

    .stat-primary  { border-top: 4px solid #3b82f6; }
    .stat-success  { border-top: 4px solid #22c55e; }
    .stat-warning  { border-top: 4px solid #f59e0b; }
    .stat-danger   { border-top: 4px solid #ef4444; }
    .stat-indigo   { border-top: 4px solid #8b5cf6; }

    .icon-primary  { background: linear-gradient(135deg, #4f8df9, #2563eb); }
    .icon-success  { background: linear-gradient(135deg, #4ade80, #16a34a); }
    .icon-warning  { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
    .icon-danger   { background: linear-gradient(135deg, #f87171, #ef4444); }
    .icon-indigo   { background: linear-gradient(135deg, #a78bfa, #7c3aed); }

    /* Tab shell */
    .tab-shell {
        border-radius: 16px;
        border: 1px solid #dbe4f2;
        box-shadow: 0 16px 48px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        background: #fefefe;
    }

    .dashboard-tabs .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        padding: 0.85rem 0.75rem;
        font-weight: 700;
        color: #0f172a;
        background: linear-gradient(180deg, #e7effc, #d5e3fa);
        transition: all .18s ease;
    }

    .dashboard-tabs .nav-link:hover { color: #0b6edb; }

    .dashboard-tabs .nav-link.active {
        color: #ffffff;
        background: linear-gradient(180deg, #0ba6df, #0b6edb);
        box-shadow: inset 0 -4px 0 rgba(255,255,255,0.12), 0 14px 24px rgba(11,166,223,0.35);
    }

    .tab-pane {
        border-top: 1px solid #e2e8f3;
        padding-top: 1rem;
    }

    .search-block .input-group-text {
        background: #ffffff;
        border-right: none;
    }
    .search-block .form-control {
        border-left: none;
    }

    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        background: #fff;
    }
    .table-modern thead th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-modern tbody tr:hover { background: #f8fafc; }
    .table-modern tbody td { vertical-align: middle; }

    .badge-soft {
        border: 1px solid currentColor;
        background: rgba(255,255,255,.3);
    }

    .btn-elevated { box-shadow: 0 12px 24px rgba(13,110,253,.22); }
    .btn-add {
        background: linear-gradient(90deg, #0ba6df, #0b6edb);
        border-color: #0b6edb;
        color: #fff !important;
        box-shadow: 0 12px 24px rgba(11, 110, 219, 0.28);
    }
    .btn-add:hover {
        background: linear-gradient(90deg, #0a8dc4, #0a5ec0);
        border-color: #0a5ec0;
        color: #fff !important;
    }

    @media (max-width: 768px) {
        .dashboard-tabs .nav-link { font-size: .95rem; }
        .search-block .input-group { flex-wrap: nowrap; }
    }
</style>

<h2 class="mb-4">Admin Dashboard</h2>

{{-- Stats Cards --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3 stats-grid">
    <div class="col">
        <a class="stat-card stat-primary" href="{{ route('admin.vehicles.index') }}">
            <div class="stat-icon icon-primary"><i class="bi bi-car-front"></i></div>
            <div class="stat-content">
                <p class="stat-label">Total Vehicles</p>
                <p class="stat-value">{{ $vehicles_count }}</p>
            </div>
        </a>
    </div>
    <div class="col">
        <a class="stat-card stat-success" href="{{ route('admin.tires.index') }}">
            <div class="stat-icon icon-success"><i class="bi bi-life-preserver"></i></div>
            <div class="stat-content">
                <p class="stat-label">Total Tyres</p>
                <p class="stat-value">{{ $tires_count }}</p>
            </div>
        </a>
    </div>
    <div class="col">
        <a class="stat-card stat-indigo" href="{{ route('admin.suppliers.index') }}">
            <div class="stat-icon icon-indigo"><i class="bi bi-person-lines-fill"></i></div>
            <div class="stat-content">
                <p class="stat-label">Suppliers</p>
                <p class="stat-value">{{ $suppliers_count }}</p>
            </div>
        </a>
    </div>
    <div class="col">
        <a class="stat-card stat-primary" href="{{ route('admin.reports.index') }}">
            <div class="stat-icon icon-primary"><i class="bi bi-file-earmark-text"></i></div>
            <div class="stat-content">
                <p class="stat-label">Reports</p>
                <p class="stat-value">Download</p>
            </div>
        </a>
    </div>
    <div class="col">
        <a class="stat-card stat-danger" href="{{ route('admin.request.pending') }}">
            <div class="stat-icon icon-danger"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-content">
                <p class="stat-label">Pending Requests</p>
                <p class="stat-value">{{ $pending_requests }}</p>
            </div>
        </a>
    </div>
</div>

{{-- Tabbed tables --}}
<div class="tab-shell">
    <ul class="nav nav-tabs nav-justified dashboard-tabs px-3 pt-3" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#vehiclesPane" type="button" role="tab" aria-controls="vehiclesPane" aria-selected="true">
                Vehicles
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tyres-tab" data-bs-toggle="tab" data-bs-target="#tyresPane" type="button" role="tab" aria-controls="tyresPane" aria-selected="false">
                Tyres
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#driversPane" type="button" role="tab" aria-controls="driversPane" aria-selected="false">
                Drivers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliersPane" type="button" role="tab" aria-controls="suppliersPane" aria-selected="false">
                Suppliers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requestsPane" type="button" role="tab" aria-controls="requestsPane" aria-selected="false">
                Requests
            </button>
        </li>
    </ul>

    <div class="tab-content p-3" id="dashboardTabsContent">
        {{-- Vehicles --}}
        <div class="tab-pane fade show active" id="vehiclesPane" role="tabpanel" aria-labelledby="vehicles-tab">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 search-block">
                <div class="flex-grow-1" style="min-width: 260px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by plate number or model..." data-table-filter="#vehiclesTable">
                        <button class="btn btn-outline-primary" type="button" data-filter-button="#vehiclesTable">Search</button>
                    </div>
                </div>
                <a href="{{ route('admin.vehicles.create') }}" class="btn btn-add btn-elevated">
                    <i class="bi bi-plus-lg me-1"></i> Add Vehicle
                </a>
            </div>

            <div class="table-responsive table-modern">
                <table class="table table-hover align-middle mb-0 text-center" id="vehiclesTable">
                    <thead>
                        <tr>
                            <th style="width:10%;">No</th>
                            <th>Model</th>
                            <th>Plate Number</th>
                            <th>Branch</th>
                            <th>Vehicle Type</th>
                            <th>Brand</th>
                            <th>User Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $vehicle->model }}</td>
                                <td><span class="badge text-bg-light border fw-semibold">{{ $vehicle->plate_no }}</span></td>
                                <td><span class="badge bg-secondary-subtle text-secondary border">{{ $vehicle->branch }}</span></td>
                                <td><span class="badge bg-info-subtle text-info border">{{ $vehicle->vehicle_type }}</span></td>
                                <td><span class="badge bg-dark-subtle text-dark border">{{ $vehicle->brand }}</span></td>
                                <td>{{ $vehicle->user_section }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this vehicle?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-4 text-muted">No vehicles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tyres --}}
        <div class="tab-pane fade" id="tyresPane" role="tabpanel" aria-labelledby="tyres-tab">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 search-block">
                <div class="flex-grow-1" style="min-width: 260px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by brand, size, or supplier..." data-table-filter="#tyresTable">
                        <button class="btn btn-outline-primary" type="button" data-filter-button="#tyresTable">Search</button>
                    </div>
                </div>
                <a href="{{ route('admin.tires.create') }}" class="btn btn-add btn-elevated">
                    <i class="bi bi-plus-circle me-1"></i> Add Tyre
                </a>
            </div>

            <div class="table-responsive table-modern">
                <table class="table table-hover align-middle mb-0 text-center" id="tyresTable">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tires as $tire)
                            <tr>
                                <td class="tyre-brand">{{ $tire->brand }}</td>
                                <td class="tyre-size">{{ $tire->size }}</td>
                                <td class="tyre-supplier">{{ $tire->supplier->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.tires.edit', $tire->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.tires.destroy', $tire->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this tyre?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-muted">No tyres found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Drivers --}}
        <div class="tab-pane fade" id="driversPane" role="tabpanel" aria-labelledby="drivers-tab">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 search-block">
                <div class="flex-grow-1" style="min-width: 260px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by name or username..." data-table-filter="#driversTable">
                        <button class="btn btn-outline-primary" type="button" data-filter-button="#driversTable">Search</button>
                    </div>
                </div>
                <a href="{{ route('admin.drivers.create') }}" class="btn btn-add btn-elevated">
                    <i class="bi bi-person-plus me-1"></i> Add Driver
                </a>
            </div>

            <div class="table-responsive table-modern">
                <table class="table table-hover align-middle mb-0 text-center" id="driversTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Mobile</th>
                            <th>ID Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $driver->user->name ?? 'N/A' }}</td>
                                <td>{{ $driver->user->email ?? 'N/A' }}</td>
                                <td>{{ $driver->full_name ?? 'N/A' }}</td>
                                <td><span class="badge text-bg-light border fw-semibold">{{ $driver->mobile ?? 'N/A' }}</span></td>
                                <td><span class="badge bg-secondary-subtle text-secondary border">{{ $driver->id_number ?? 'N/A' }}</span></td>
                                <td>
                                    <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this driver?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 text-muted">No drivers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Suppliers --}}
        <div class="tab-pane fade" id="suppliersPane" role="tabpanel" aria-labelledby="suppliers-tab">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 search-block">
                <div class="flex-grow-1" style="min-width: 260px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by name or contact..." data-table-filter="#suppliersTable">
                        <button class="btn btn-outline-primary" type="button" data-filter-button="#suppliersTable">Search</button>
                    </div>
                </div>
                <a href="{{ route('admin.suppliers.create') }}" class="btn btn-add btn-elevated">
                    <i class="bi bi-building-add me-1"></i> Add Supplier
                </a>
            </div>

            <div class="table-responsive table-modern">
                <table class="table table-hover align-middle mb-0 text-center" id="suppliersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Town</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="supplier-name">{{ $supplier->name }}</td>
                                <td class="supplier-contact">{{ $supplier->contact }}</td>
                                <td class="supplier-address">{{ $supplier->address }}</td>
                                <td class="supplier-town">{{ $supplier->town }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-muted">No suppliers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Requests --}}
        <div class="tab-pane fade" id="requestsPane" role="tabpanel" aria-labelledby="requests-tab">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 search-block">
                <div class="flex-grow-1" style="min-width: 260px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by driver, vehicle, or tyre..." data-table-filter="#requestsTable">
                        <button class="btn btn-outline-primary" type="button" data-filter-button="#requestsTable">Search</button>
                    </div>
                </div>
                <a href="{{ route('admin.request.pending') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-right-circle me-1"></i> Pending Queue
                </a>
            </div>

            <div class="table-responsive table-modern">
                <table class="table table-hover align-middle mb-0 text-center" id="requestsTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Tyre</th>
                            <th>Count</th>
                            <th>Status</th>
                            <th>Requested</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $request)
                            @php
                                $statusMeta = $statusLabels[$request->status] ?? ['label' => ucfirst(str_replace('_', ' ', $request->status)), 'class' => 'secondary'];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $request->user->name ?? $request->driver->full_name ?? 'N/A' }}</td>
                                <td>{{ $request->vehicle->plate_no ?? 'N/A' }}</td>
                                <td>{{ trim(($request->tire->brand ?? 'N/A').' '.$request->tire->size) }}</td>
                                <td><span class="badge bg-primary-subtle text-primary border">{{ $request->tire_count ?? '-' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $statusMeta['class'] }}-subtle text-{{ $statusMeta['class'] }} border">{{ $statusMeta['label'] }}</span>
                                </td>
                                <td>{{ optional($request->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 text-muted">No requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lightweight table filter that works per-tab
    const applyFilter = (selector, term) => {
        const rows = document.querySelectorAll(`${selector} tbody tr`);
        const query = term.trim().toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-table-filter]').forEach(input => {
            const target = input.dataset.tableFilter;
            const handler = () => applyFilter(target, input.value);
            input.addEventListener('input', handler);

            const button = input.closest('.input-group')?.querySelector(`[data-filter-button="${target}"]`);
            if (button) button.addEventListener('click', handler);
        });

        if (window.bootstrap && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        }
    });
</script>
@endpush
