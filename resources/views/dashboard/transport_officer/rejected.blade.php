@extends('layouts.transportofficer')

@section('title', 'Rejected Requests')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-x-circle text-danger me-2"></i>Rejected Requests</h1>
            <p class="text-muted mb-0">Requests that have been rejected</p>
        </div>
        <div>
            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>{{ $rejectedRequests->count() }} Rejected</span>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="search-input" class="form-control" placeholder="Search by driver name, vehicle, or branch...">
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-octagon me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Rejected Requests List --}}
    <div class="d-flex flex-column gap-3 mb-5">
        @forelse($rejectedRequests as $req)
            <div class="card border-0 shadow-sm hover-shadow request-card">
                <div class="card-body">
                    {{-- Header with Driver & Status --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1"><i class="bi bi-person-badge text-danger me-2"></i>{{ $req->user->name ?? 'N/A' }}</h5>
                            <small class="text-muted">
                                <span class="me-3"><i class="bi bi-truck me-1"></i>{{ $req->vehicle->plate_no ?? 'N/A' }}</span>
                                <span class="me-3"><i class="bi bi-geo-alt me-1"></i>{{ $req->vehicle->branch ?? 'N/A' }}</span>
                            </small>
                        </div>
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                    </div>

                    {{-- Tire Details --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">
                            <span class="badge bg-light text-dark me-2"><i class="bi bi-circle-fill me-1"></i>{{ $req->tire->brand ?? 'N/A' }} {{ $req->tire->size ?? '' }}</span>
                            <span class="badge bg-light text-dark"><i class="bi bi-123 me-1"></i>Count: {{ $req->tire_count ?? 'N/A' }}</span>
                        </small>
                    </div>

                    {{-- Rejection Remarks --}}
                    @php
                        $approval = $req->approvals()->where('level', 3)->where('status', 'rejected_by_transport')->first();
                    @endphp
                    @if($approval && $approval->remarks)
                        <div class="alert alert-danger mb-3" style="border-left: 4px solid #dc3545;">
                            <strong class="d-block mb-2"><i class="bi bi-exclamation-circle me-1"></i>Rejection Reason:</strong>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $approval->remarks }}</p>
                        </div>
                    @endif

                    {{-- Damage Description --}}
                    <div class="mb-3">
                        <strong class="d-block text-muted mb-1">Damage:</strong>
                        <p class="text-muted mb-0">{{ $req->damage_description ?? 'No description provided' }}</p>
                    </div>

                    {{-- Images --}}
                    @php
                        $images = [];
                        if (isset($req->tire_images)) {
                            $decoded = is_array($req->tire_images)
                                ? $req->tire_images
                                : json_decode(str_replace('\\/', '/', $req->tire_images), true);
                            if (is_array($decoded)) $images = $decoded;
                        }
                        if (empty($images) && isset($req->images) && $req->images) {
                            $images = is_array($req->images) ? $req->images : array_map('trim', explode(',', $req->images));
                        }
                    @endphp

                    @if(count($images) > 0)
                        <div class="mb-3">
                            <strong class="d-block text-muted mb-2">Images:</strong>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($images as $img)
                                    <img src="{{ asset('storage/' . ltrim($img, '/')) }}" alt="tire" class="rounded border request-img" style="width:100px; height:100px; object-fit:cover; cursor:pointer;" data-full="{{ asset('storage/' . ltrim($img, '/')) }}" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2 mt-3 pt-3 border-top">
                        <a href="{{ route('transport_officer.edit_request', $req->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-pencil-square me-1"></i> Edit & Resubmit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-3 mb-0">No rejected requests found</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('styles')
<style>
    .hover-shadow { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; transform: translateY(-2px); }
    .request-img { transition: transform 0.2s; }
    .request-img:hover { transform: scale(1.05); }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    
    .lightbox { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.8); z-index: 10000; animation: fadeIn 0.3s; }
    .lightbox.active { display: flex; }
    .lightbox img { max-width: 90%; max-height: 90%; border-radius: 12px; animation: zoomIn 0.3s; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
    .lightbox .close-btn { position: absolute; top: 20px; right: 30px; font-size: 2rem; color: #fff; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox
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
        if(e.target === lightbox) {
            lightbox.classList.remove('active');
            imgEl.src = '';
        }
    });

    // Live Search
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.request-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(term) ? '' : 'none';
        });
    });
});
</script>
@endpush

