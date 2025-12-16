@extends('layouts.mechanicofficer')

@section('title', 'Rejected Requests')

@section('content')
<div class="container-fluid py-4">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-md-8">
      <h1 class="h3 fw-bold" style="color:#ef4444;">
        <i class="bi bi-x-circle me-2"></i>Rejected Requests
      </h1>
      <p class="text-muted">Review all rejected tire requests and rejection reasons</p>
    </div>
    <div class="col-md-4">
      <form id="rejected-search-form" class="m-0">
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
          <input type="text" id="rejectedSearch" class="form-control" placeholder="Search by driver name">
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
      <ul class="requests-list list-unstyled mb-0" id="rejectedList">
        @forelse($rejectedRequests as $req)
          @php
            $approval = $req->approvals()
              ->where('level', 2)
              ->where('status', 'rejected_by_mechanic')
              ->first();
          @endphp
          <li class="request-card border-bottom">
            <div class="request-content p-4">
              <!-- Request Header -->
              <div class="row align-items-start mb-3">
                <div class="col-md-8">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <h6 class="fw-bold mb-1" style="color:#ef4444;">
                        <i class="bi bi-person-circle me-2"></i><span class="driver-name">{{ $req->user->name ?? 'N/A' }}</span>
                      </h6>
                      <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>{{ optional($approval->updated_at ?? $req->updated_at)->format('M d, Y H:i') ?? '-' }}
                      </small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 text-md-end">
                  <span class="badge" style="background:#fee2e2; color:#991b1b;">
                    <i class="bi bi-x-circle me-1"></i>Rejected
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
                <p class="text-muted" style="border-left:4px solid #ef4444; padding-left:12px; margin:0;">
                  {{ $req->damage_description ?? 'No description provided' }}
                </p>
              </div>

              <!-- Rejection Remarks -->
              @if($approval && $approval->remarks)
                <div class="mb-3" style="background:#fef2f2; border-left:4px solid #ef4444; padding:12px; border-radius:4px;">
                  <strong class="d-block mb-2" style="color:#991b1b;">Rejection Reason:</strong>
                  <p class="text-muted mb-0" style="white-space: pre-wrap;">
                    {{ $approval->remarks }}
                  </p>
                </div>
              @endif

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
                <a href="{{ route('mechanic_officer.edit_request', $req->id) }}" class="btn btn-primary btn-sm">
                  <i class="bi bi-pencil me-1"></i> Edit
                </a>
              </div>
            </div>
          </li>
        @empty
          <li class="p-5">
            <div class="text-center">
              <i class="bi bi-inbox" style="font-size:3rem; color:#d1d5db; margin-bottom:1rem; display:block;"></i>
              <p class="text-muted mb-0">No rejected requests found</p>
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
  background: white; 
  transition: all 0.3s ease;
}

.request-card:hover { 
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Request Content */
.request-content { 
  display: flex; 
  justify-content: space-between; 
  align-items: flex-start;
}

.request-info { 
  flex: 1;
}

.request-actions { 
  display: flex; 
  gap: 0.5rem; 
  justify-content: flex-end;
}

/* Images Container */
.images-container { 
  display: flex; 
  flex-wrap: wrap; 
  gap: 0.75rem; 
  margin-top: 0.75rem;
}

.request-img { 
  width: 100px; 
  height: 70px; 
  object-fit: cover; 
  border-radius: 6px; 
  border: 1px solid #e5e7eb; 
  cursor: pointer; 
  transition: all 0.25s ease;
}

.request-img:hover { 
  transform: scale(1.08); 
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* Lightbox Styles */
.lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: none;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.8);
  z-index: 10000;
  animation: fadeIn 0.3s ease;
}

.lightbox.active {
  display: flex;
}

.lightbox img {
  max-width: 90%;
  max-height: 85%;
  border-radius: 8px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
  animation: zoomIn 0.3s ease;
}

.lightbox .close-btn {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 2.5rem;
  color: white;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.lightbox .close-btn:hover {
  transform: scale(1.2);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes zoomIn {
  from { transform: scale(0.85); }
  to { transform: scale(1); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Lightbox for images
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

    closeBtn.addEventListener('click', () => {
        lightbox.classList.remove('active');
        imgEl.src = '';
    });

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            lightbox.classList.remove('active');
            imgEl.src = '';
        }
    });

    // Live search filter
    const searchInput = document.getElementById('rejectedSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = searchInput.value.toLowerCase();
            document.querySelectorAll('.request-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
