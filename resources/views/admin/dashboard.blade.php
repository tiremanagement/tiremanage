@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<style>
    /* Light dashboard backdrop with subtle blobs */
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

    /* Color variants */
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

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background: #fefefe;
        color: #333;
    }

    th {
        background: #0BA6DF;
        color: #fff;
        padding: 12px;
        text-align: center;
    }

    td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
    }

    tr:hover {
        background: #f0f4f8;
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    /* Buttons: use global theme from layout; only tweak spacing on this page */
    .btn { padding: 0.55rem 0.95rem; }
    .btn-sm { padding: 0.35rem 0.6rem; font-weight: 600; }

    /* Hover card effect */
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .row { flex-direction: column; }
        .card { flex: 1 1 100%; }
        th, td { font-size: 0.85rem; padding: 8px; }
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

{{-- Vehicles --}}
<h3>Vehicles</h3>
<a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-elevated mb-2"><i class="bi bi-car-front"></i> Add Vehicle</a>
<table>
    <thead>
        <tr>
            <th>No</th>
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
            <td>{{ $vehicle->plate_no }}</td>
            <td>{{ $vehicle->branch }}</td>
            <td>{{ $vehicle->vehicle_type }}</td>
            <td>{{ $vehicle->brand }}</td>
            <td>{{ $vehicle->user_section }}</td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this vehicle?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8">No vehicles found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Tires --}}
<h3>Tyres</h3>
<a href="{{ route('admin.tires.create') }}" class="btn btn-success btn-elevated mb-2"><i class="bi bi-life-preserver"></i> Add Tyre</a>
<table>
    <thead>
        <tr>
            <th>Brand</th>
            <th>Size</th>
            <th>Supplier Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tires as $tire)
        <tr>
            <td>{{ $tire->brand }}</td>
            <td>{{ $tire->size }}</td>
            <td>{{ $tire->supplier->name ?? 'N/A' }}</td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.tires.edit', $tire->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.tires.destroy', $tire->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this tyre?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
    <tr><td colspan="4">No tyres found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Suppliers --}}
<h3>Suppliers</h3>
<a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-elevated mb-2"><i class="bi bi-person-plus"></i> Add Supplier</a>
<table>
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
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->contact }}</td>
            <td>{{ $supplier->address }}</td>
            <td>{{ $supplier->town }}</td>
            <td>
                <div class="action-buttons">
                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this supplier?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
    <tr><td colspan="5">No suppliers found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Drivers --}}
<h3>Drivers</h3>
<a href="{{ route('admin.drivers.create') }}" class="btn btn-primary btn-elevated mb-2"><i class="bi bi-person-plus"></i> Add Driver</a>
<table>
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
            <td>{{ $driver->full_name }}</td>
            <td>{{ $driver->mobile }}</td>
            <td>{{ $driver->id_number }}</td>
            <td>
                <div class="action-buttons">
                    <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this driver?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7">No drivers found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Simple JS for card click feedback --}}
<script>
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', () => card.style.cursor = 'pointer');
    });
</script>

@endsection
