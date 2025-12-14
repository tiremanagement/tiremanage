@php
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp
@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@section('title', 'Vehicles List')

@section('content')
<div class="container-fluid py-4">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h1 class="h3 fw-bold" style="color:#0b4fb4;">
        <i class="bi bi-truck me-2"></i>Vehicles
      </h1>
      <p class="text-muted">Manage all registered vehicles</p>
    </div>
    <div class="col-md-4 text-md-end">
      <a href="{{ route('section_manager.vehicles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Vehicle
      </a>
    </div>
  </div>

  <!-- Search Section -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form action="{{ route('section_manager.vehicles.index') }}" method="GET">
        <div class="row g-2 align-items-center">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by plate number">
              @if(request('search'))
                <a href="{{ route('section_manager.vehicles.index') }}" class="btn btn-outline-secondary">Reset</a>
              @endif
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </div>
          <div class="col-md-6 text-md-end">
            <span class="text-muted small">Total:</span>
            <span class="badge bg-primary">{{ $vehicles->count() }} vehicle(s)</span>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Vehicles List -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      @forelse($vehicles as $vehicle)
        <div class="vehicle-card p-4 border-bottom">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6 class="fw-bold mb-1" style="color:#0b4fb4;">
                <i class="bi bi-truck me-2"></i>{{ $vehicle->plate_no ?? 'N/A' }}
              </h6>
              <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                  <i class="bi bi-123 me-1"></i>Driver ID: {{ $vehicle->driver_id_number ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#f0fdf4; color:#16a34a;">
                  <i class="bi bi-geo-alt me-1"></i>{{ $vehicle->branch ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#fef3c7; color:#92400e;">
                  <i class="bi bi-speedometer2 me-1"></i>{{ $vehicle->vehicle_type ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#f5f3ff; color:#7c3aed;">
                  <i class="bi bi-palette me-1"></i>{{ $vehicle->brand ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#fce7f3; color:#be185d;">
                  <i class="bi bi-building me-1"></i>{{ $vehicle->user_section ?? 'N/A' }}
                </span>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <div class="d-flex justify-content-md-end gap-2">
                <a href="{{ route('section_manager.vehicles.edit', $vehicle->id) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form action="{{ route('section_manager.vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="p-5">
          <div class="text-center">
            <div class="mb-3"><i class="bi bi-truck fs-2" style="color:#d1d5db;"></i></div>
            <div class="fw-semibold mb-2">No vehicles found</div>
            <div class="small text-muted mb-3">Try adjusting your search or add a new vehicle.</div>
            <a href="{{ route('section_manager.vehicles.create') }}" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-circle me-1"></i> Add Vehicle
            </a>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Vehicle Card */
.vehicle-card {
  border-radius: 0;
  transition: background-color 0.2s;
}

.vehicle-card:hover {
  background-color: #f9fafb;
}

.vehicle-card h6 {
  font-size: 1.05rem;
  margin-bottom: 0.75rem;
}

/* Badge Styling */
.badge {
  padding: 0.4rem 0.8rem;
  font-weight: 500;
  font-size: 0.85rem;
  border-radius: 0.35rem;
}

.badge i {
  margin-right: 0.3rem;
}

/* Button Styling */
.btn-warning {
  background: #f59e0b;
  border-color: #f59e0b;
  color: #fff;
  transition: all 0.2s;
}

.btn-warning:hover {
  background: #d97706;
  border-color: #d97706;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-danger {
  background: #ef4444;
  border-color: #ef4444;
  transition: all 0.2s;
}

.btn-danger:hover {
  background: #dc2626;
  border-color: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .badge {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
  }
  
  .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
  }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Enable tooltips if Bootstrap is available
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }
});
</script>
@endpush
