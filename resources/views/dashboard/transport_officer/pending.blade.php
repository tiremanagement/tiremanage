@extends('layouts.transportofficer')

@section('title', 'Pending Tire Requests')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-hourglass-split text-warning me-2"></i>Pending Requests</h1>
            <p class="text-muted mb-0">Awaiting your approval or rejection</p>
        </div>
        <div>
            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>{{ $pendingRequests->count() }} Pending</span>
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

    {{-- Pending Requests List --}}
    <div class="d-flex flex-column gap-3 mb-5">
        @forelse($pendingRequests as $req)
            <div class="card border-0 shadow-sm hover-shadow request-card">
                <div class="card-body">
                    {{-- Header with Driver & Status --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1"><i class="bi bi-person-badge text-primary me-2"></i>{{ $req->user->name ?? 'N/A' }}</h5>
                            <small class="text-muted">
                                <span class="me-3"><i class="bi bi-truck me-1"></i>{{ $req->vehicle->plate_no ?? 'N/A' }}</span>
                                <span class="me-3"><i class="bi bi-geo-alt me-1"></i>{{ $req->vehicle->branch ?? 'N/A' }}</span>
                            </small>
                        </div>
                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    </div>

                    {{-- Tire Details --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">
                            <span class="badge bg-light text-dark me-2"><i class="bi bi-circle-fill me-1"></i>{{ $req->tire->brand ?? 'N/A' }} {{ $req->tire->size ?? '' }}</span>
                            <span class="badge bg-light text-dark"><i class="bi bi-123 me-1"></i>Count: {{ $req->tire_count ?? 'N/A' }}</span>
                        </small>
                    </div>

                    {{-- Damage Description --}}
                    <div class="mb-3">
                        <strong class="d-block text-muted mb-1">Damage:</strong>
                        <p class="text-muted mb-0">{{ $req->damage_description ?? 'No description provided' }}</p>
                    </div>

                    {{-- Images --}}
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
                        <form action="{{ route('transport_officer.approve', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check2-circle me-1"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-request-id="{{ $req->id }}">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-3 mb-0">No pending requests found</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Rejection Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold">Rejection Reason:</label>
                    <select name="reason" class="form-select" required>
                        <option value="">-- Select a reason --</option>
                        <option value="Not repairable">Not repairable</option>
                        <option value="Safety concern">Safety concern</option>
                        <option value="Incorrect tire type">Incorrect tire type</option>
                        <option value="Duplicate request">Duplicate request</option>
                        <option value="Other">Other</option>
                    </select>
                    <label class="form-label fw-semibold mt-3">Additional Details:</label>
                    <textarea name="custom_reason" class="form-control" rows="4" placeholder="Provide additional details (optional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Reject</button>
                </div>
            </form>
        </div>
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
    .lightbox img { max-width: 90%; max-height: 90%; border-radius: 12px; animation: zoomIn 0.3s; }
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

    // Modal - Set form action
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');
    
    rejectModal.addEventListener('show.bs.modal', (e) => {
        const requestId = e.relatedTarget.getAttribute('data-request-id');
        rejectForm.setAttribute('action', '/transport-officer/reject/' + requestId);
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

