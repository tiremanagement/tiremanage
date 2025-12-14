@php
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp
@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')
@section('title', 'Driver List')

@section('content')
<div class="container-fluid py-4">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h1 class="h3 fw-bold" style="color:#0b4fb4;">
        <i class="bi bi-people-fill me-2"></i>Drivers
      </h1>
      <p class="text-muted">Manage all registered drivers</p>
    </div>
    <div class="col-md-4 text-md-end">
      <a href="{{ $layout === 'admin' ? route('admin.drivers.create') : route('section_manager.drivers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add Driver
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Search Section -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form action="{{ route('section_manager.drivers.index') }}" method="GET">
        <div class="row g-2 align-items-center">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" id="driverSearch" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email">
              @if(request('search'))
                <a href="{{ route('section_manager.drivers.index') }}" class="btn btn-outline-secondary">Reset</a>
              @endif
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </div>
          <div class="col-md-6 text-md-end">
            <span class="text-muted small">Total:</span>
            <span class="badge bg-primary">{{ $drivers->count() }} driver(s)</span>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Drivers List -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      @forelse($drivers as $driver)
        <div class="driver-card p-4 border-bottom">
          <div class="row align-items-center">
            <div class="col-md-7">
              <h6 class="fw-bold mb-1" style="color:#0b4fb4;">
                <i class="bi bi-person-circle me-2"></i>{{ $driver->full_name ?? 'N/A' }}
              </h6>
              <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                  <i class="bi bi-envelope me-1"></i>{{ $driver->user->email ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#f0fdf4; color:#16a34a;">
                  <i class="bi bi-telephone me-1"></i>{{ $driver->mobile ?? 'N/A' }}
                </span>
                <span class="badge" style="background:#f5f3ff; color:#7c3aed;">
                  <i class="bi bi-123 me-1"></i>{{ $driver->id_number ?? 'N/A' }}
                </span>
              </div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
              <div class="d-flex justify-content-md-end gap-2">
                <form action="{{ $layout === 'admin' ? route('admin.drivers.destroy', $driver->id) : route('section_manager.drivers.destroy', $driver->id) }}"
                      method="POST" onsubmit="return confirm('Are you sure you want to delete this driver?')" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                    <i class="bi bi-trash me-1"></i>Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="p-5">
          <div class="text-center">
            <div class="mb-3"><i class="bi bi-people fs-2" style="color:#d1d5db;"></i></div>
            <div class="fw-semibold mb-2">No drivers found</div>
            <div class="small text-muted mb-3">Try adjusting your search or add a new driver.</div>
            <a href="{{ $layout === 'admin' ? route('admin.drivers.create') : route('section_manager.drivers.create') }}" class="btn btn-primary btn-sm">
              <i class="bi bi-person-plus me-1"></i> Add Driver
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
/* Driver Card */
.driver-card {
  border-radius: 0;
  transition: background-color 0.2s;
}

.driver-card:hover {
  background-color: #f9fafb;
}

.driver-card h6 {
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
  // Client-side search filter
  const searchInput = document.getElementById('driverSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      document.querySelectorAll('.driver-card').forEach(card => {
        const fullName = card.querySelector('h6').textContent.toLowerCase();
        card.style.display = (query === '' || fullName.includes(query)) ? '' : 'none';
      });
    });
  }
  
  // Enable tooltips if Bootstrap is available
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  }
});
</script>
@endpush
