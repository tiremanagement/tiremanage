@extends(($layout ?? null) === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@section('title', 'Vehicles List')

@section('content')
@php($isAdmin = (($layout ?? null) === 'admin'))

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Vehicles</h3>
  <a href="{{ route($isAdmin ? 'admin.vehicles.create' : 'section_manager.vehicles.create') }}" class="btn btn-primary btn-elevated">
    <i class="bi bi-plus-lg"></i> Add Vehicle
  </a>
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
  <style>
    .bg-primary-subtle { background: rgba(13,110,253,.10) !important; }
    .bg-warning-subtle { background: rgba(255,193,7,.12) !important; }
    .bg-secondary-subtle { background: rgba(108,117,125,.12) !important; }
    .bg-info-subtle { background: rgba(13,202,240,.12) !important; }
    .bg-dark-subtle { background: rgba(33,37,41,.10) !important; }
    .text-bg-light { background-color: #d7d9dcff !important; }
    .table-responsive thead th { position: sticky; top: 0; background: #fff; z-index: 1; }
    .action-buttons { display: inline-flex; gap: .35rem; align-items: center; }
  </style>
</div>

<form action="{{ route($isAdmin ? 'admin.vehicles.index' : 'section_manager.vehicles.index') }}" method="GET" class="mb-3">
  <div class="row g-2 align-items-center">
        <div class="col-sm-8 col-md-6">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by plate number or driver ID">
        @if(request('search'))
          <a href="{{ route($isAdmin ? 'admin.vehicles.index' : 'section_manager.vehicles.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
        <button class="btn btn-primary" type="submit">Search</button>
      </div>
    </div>
    <div class="col-sm-4 col-md-6 text-sm-end mt-2 mt-sm-0">
      <span class="text-muted small">Showing</span>
      <span class="badge bg-primary-subtle text-primary border small">{{ $vehicles->count() }}</span>
      <span class="text-muted small">vehicles</span>
      @if(!$isAdmin)
       
      @endif
    </div>
  </div>
  @csrf
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 text-center">
      <thead>
        <tr>
          <th style="width:10%">No</th>
          <th>Driver ID</th>
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
          <td>{{ $vehicle->driver_id_number }}</td>
          <td><span class="badge text-bg-light border fw-semibold">{{ $vehicle->plate_no }}</span></td>
          <td><span class="badge bg-secondary-subtle text-secondary border">{{ $vehicle->branch }}</span></td>
          <td><span class="badge bg-info-subtle text-info border">{{ $vehicle->vehicle_type }}</span></td>
          <td><span class="badge bg-dark-subtle text-dark border">{{ $vehicle->brand }}</span></td>
          <td>{{ $vehicle->user_section }}</td>
          <td>
            <div class="action-buttons">
              @if ($isAdmin)
                <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure to delete this vehicle?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @else
                <a href="{{ route('section_manager.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('section_manager.vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure to delete this vehicle?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="py-5">
            <div class="text-center text-muted">
              <div class="mb-2"><i class="bi bi-truck fs-3"></i></div>
              <div class="fw-semibold">No vehicles found</div>
              <div class="small">Try adjusting your search or add a new vehicle.</div>
              <div class="mt-3">
                <a href="{{ route($isAdmin ? 'admin.vehicles.create' : 'section_manager.vehicles.create') }}" class="btn btn-primary btn-sm">
                  <i class="bi bi-plus-lg"></i> Add Vehicle
                </a>
              </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.bootstrap && bootstrap.Tooltip) {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  }
});
</script>
@endsection

