@extends('layouts.section_manager')

@section('title', 'Approved Requests')

@section('content')
<div class="container-fluid py-4">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h1 class="h3 fw-bold" style="color:#10b981;">
        <i class="bi bi-check2-circle me-2"></i>Approved Requests
      </h1>
      <p class="text-muted">Review all approved tire requests</p>
    </div>
    <div class="col-md-4">
      <form id="driver-search-form" action="{{ route('section_manager.dashboard') }}" method="GET" class="m-0">
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
          <input type="text" id="approvedSearch" class="form-control" placeholder="Search by driver name">
        </div>
      </form>
    </div>
  </div>

  <!-- Requests List -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
      <h5 class="mb-0 fw-bold">Request List</h5>
    </div>
    <div class="card-body p-0">
      <ul class="requests-list list-unstyled mb-0" id="approvedList">
        @forelse($approvedRecords as $approval)
          @php $req = $approval->request; @endphp
          @if($req)
          <li class="request-card border-bottom">
            <div class="request-content p-4">
              <!-- Request Header -->
              <div class="row align-items-start mb-3">
                <div class="col-md-8">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <h6 class="fw-bold mb-1" style="color:#10b981;">
                        <i class="bi bi-person-circle me-2"></i><span class="driver-name">{{ $req->user->name ?? 'N/A' }}</span>
                      </h6>
                      <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>{{ optional($approval->updated_at)->format('M d, Y H:i') ?? '-' }}
                      </small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 text-md-end">
                  <span class="badge" style="background:#d1fae5; color:#065f46;">
                    <i class="bi bi-check2-circle me-1"></i>Approved
                  </span>
                </div>
              </div>

              <!-- Vehicle & Tyre Info -->
              <div class="row mb-3">
                <div class="col-12">
                  <div class="d-flex flex-wrap gap-2">
                    <span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">
                      <i class="bi bi-truck me-1"></i><strong>{{ $req->vehicle->plate_no ?? 'N/A' }}</strong>
                    </span>
                    <span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                      <i class="bi bi-geo-alt me-1"></i>{{ $req->vehicle->branch ?? 'N/A' }}
                    </span>
                    <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;">
                      <i class="bi bi-record2 me-1"></i>{{ $req->tire->brand ?? 'N/A' }}
                    </span>
                    <span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #e9d5ff;">
                      <i class="bi bi-aspect-ratio me-1"></i>{{ $req->tire->size ?? 'N/A' }}
                    </span>
                    <span class="badge" style="background:#fce7f3; color:#be185d; border:1px solid #fbcfe8;">
                      <i class="bi bi-123 me-1"></i>{{ $req->tire_count ?? 'N/A' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Damage Description -->
              <div class="mb-3">
                <strong class="d-block mb-2">Damage Description:</strong>
                <p class="text-muted" style="border-left:4px solid #10b981; padding-left:12px; margin:0;">
                  {{ $req->damage_description ?? 'No description provided' }}
                </p>
              </div>

              <!-- Images -->
              @php
                $images = [];
                if(!empty($req->tire_images)) {
                  if(is_array($req->tire_images)) {
                    $images = $req->tire_images;
                  } elseif(is_string($req->tire_images)) {
                    $decoded = json_decode($req->tire_images, true);
                    if(is_array($decoded)) $images = $decoded;
                  }
                }
                if(empty($images) && !empty($req->images)) {
                  $images = is_array($req->images) ? $req->images : array_map('trim', explode(',', $req->images));
                }
              @endphp

              @if(count($images) > 0)
                <div class="mb-3">
                  <strong class="d-block mb-2">Tire Images:</strong>
                  <div class="images-container">
                    @foreach($images as $img)
                      @php $imgPath = str_replace('\\/', '/', trim($img)); @endphp
                      <img src="{{ asset('storage/' . ltrim($imgPath, '/')) }}" alt="tire-image" class="request-img" data-full="{{ asset('storage/' . ltrim($imgPath, '/')) }}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%2270%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22100%22 height=%2270%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 fill=%22%236b7280%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Image%3C/text%3E%3C/svg%3E'" />
                    @endforeach
                  </div>
                </div>
              @endif

              <!-- Actions -->
              <div class="request-actions pt-3 border-top">
                <a href="{{ route('section_manager.requests.edit', $req->id) }}" class="btn btn-primary btn-sm">
                  <i class="bi bi-pencil me-1"></i> Edit
                </a>
              </div>
            </div>
          </li>
          @endif
        @empty
          <li class="p-5">
            <div class="text-center">
              <i class="bi bi-inbox" style="font-size:3rem; color:#d1d5db; margin-bottom:1rem; display:block;"></i>
              <p class="text-muted mb-0">No approved requests found</p>
            </div>
          </li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Request List Container */
.requests-list { 
  display: flex; 
  flex-direction: column; 
  gap: 0;
}

/* Individual Request Card */
.request-card { 
  border-bottom: 1px solid #e5e7eb;
  transition: background-color 0.2s, box-shadow 0.2s;
}

.request-card:last-child {
  border-bottom: none;
}

.request-card:hover {
  background-color: #f9fafb;
}

/* Request Header Styling */
.request-content h6 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

/* Badges Custom Styling */
.badge {
  padding: 0.4rem 0.8rem;
  font-weight: 500;
  font-size: 0.85rem;
  border-radius: 0.35rem;
}

.badge i {
  margin-right: 0.25rem;
}

/* Damage Description Box */
.request-content p[style*="border-left"] {
  background-color: #f0fdf4;
  padding: 12px !important;
  border-radius: 0.35rem;
  font-size: 0.95rem;
  color: #333;
}

/* Images Container */
.images-container { 
  display: flex; 
  flex-wrap: wrap; 
  gap: 0.75rem; 
  margin-top: 0.5rem; 
}

.request-img { 
  width: 120px; 
  height: 85px; 
  object-fit: cover; 
  border-radius: 8px; 
  border: 1px solid #d1d5db;
  cursor: pointer; 
  transition: transform 0.25s, box-shadow 0.25s, filter 0.25s;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.request-img:hover { 
  transform: scale(1.08); 
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
  filter: brightness(1.05);
}

/* Request Actions */
.request-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.request-actions .btn {
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 0.35rem;
  padding: 0.5rem 1rem;
  transition: all 0.2s;
}

.request-actions .btn-primary {
  background: #3b82f6;
  border-color: #3b82f6;
}

.request-actions .btn-primary:hover {
  background: #2563eb;
  border-color: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Lightbox */
.lightbox { 
  position: fixed; 
  inset: 0; 
  display: none; 
  align-items: center; 
  justify-content: center; 
  background: rgba(0,0,0,0.85); 
  z-index: 10000; 
  backdrop-filter: blur(4px);
}

.lightbox.active { 
  display: flex; 
}

.lightbox img { 
  max-width: 90%; 
  max-height: 85%; 
  border-radius: 12px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.5);
}

.lightbox .close-btn { 
  position: absolute; 
  top: 20px; 
  right: 30px; 
  font-size: 2.5rem; 
  color: #fff; 
  cursor: pointer;
  transition: opacity 0.2s;
  opacity: 0.8;
}

.lightbox .close-btn:hover {
  opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .request-img {
    width: 100px;
    height: 70px;
  }
  
  .request-actions {
    gap: 0.5rem;
  }
  
  .request-actions .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
  }
  
  .badge {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
  }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Client-side filter for approved list
  const searchInput = document.getElementById('approvedSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      document.querySelectorAll('#approvedList .request-card').forEach(card => {
        const nameEl = card.querySelector('.driver-name');
        const name = nameEl ? nameEl.textContent.toLowerCase() : '';
        card.style.display = (q === '' || name.includes(q)) ? '' : 'none';
      });
    });
  }

  // Lightbox for image preview
  const lightbox = document.createElement('div');
  lightbox.className = 'lightbox';
  lightbox.innerHTML = '<span class="close-btn">&times;</span><img src="" alt="preview"/>';
  document.body.appendChild(lightbox);
  
  const imgEl = lightbox.querySelector('img');
  const closeBtn = lightbox.querySelector('.close-btn');
  
  document.querySelectorAll('.request-img').forEach(img => {
    img.addEventListener('click', () => {
      imgEl.src = img.dataset.full || img.src;
      lightbox.classList.add('active');
    });
  });
  
  const close = () => { lightbox.classList.remove('active'); imgEl.src=''; };
  closeBtn.addEventListener('click', close);
  lightbox.addEventListener('click', (e) => { if (e.target === lightbox) close(); });
});
</script>
@endpush


