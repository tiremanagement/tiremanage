@extends('layouts.transportofficer')

@section('title', 'Approved Requests')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-check2-circle text-success me-2"></i>Approved Requests</h1>
            <p class="text-muted mb-0">Completed and awaiting receipt generation</p>
        </div>
        <div>
            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>{{ $approvedRequests->count() }} Approved</span>
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Approved Requests List --}}
    <div class="d-flex flex-column gap-3 mb-5">
        @forelse($approvedRequests as $req)
            <div class="card border-0 shadow-sm hover-shadow request-card">
                <div class="card-body">
                    {{-- Header with Driver & Status --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1"><i class="bi bi-person-badge text-success me-2"></i>{{ $req->user->name ?? 'N/A' }}</h5>
                            <small class="text-muted">
                                <span class="me-3"><i class="bi bi-truck me-1"></i>{{ $req->vehicle->plate_no ?? 'N/A' }}</span>
                                <span class="me-3"><i class="bi bi-geo-alt me-1"></i>{{ $req->vehicle->branch ?? 'N/A' }}</span>
                            </small>
                        </div>
                        <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Approved</span>
                    </div>

                    {{-- Tire Details --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">
                            <span class="badge bg-light text-dark me-2"><i class="bi bi-circle-fill me-1"></i>{{ $req->tire->brand ?? 'N/A' }} {{ $req->tire->size ?? '' }}</span>
                            <span class="badge bg-light text-dark"><i class="bi bi-123 me-1"></i>Count: {{ $req->tire_count ?? 'N/A' }}</span>
                        </small>
                    </div>

                    {{-- Approval Date --}}
                    @php
                        $approval = $req->approvals()->where('level', 3)->where('status', 'approved_by_transport')->first();
                    @endphp
                    @if($approval)
                        <div class="mb-3 p-2 bg-light rounded">
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i>Approved on {{ $approval->created_at->format('M d, Y H:i') }}
                            </small>
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
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#receipt-form-{{ $req->id }}" aria-expanded="false">
                            <i class="bi bi-receipt me-1"></i> Receipt
                        </button>
                    </div>

                    {{-- Receipt Form --}}
                    <div id="receipt-form-{{ $req->id }}" class="collapse mt-3">
                        <form action="{{ route('transport_officer.receipt.store') }}" method="POST" class="border rounded p-3 bg-light">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ $req->id }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="supplier_id-{{ $req->id }}" class="form-label fw-semibold">Supplier:</label>
                                    <select name="supplier_id" id="supplier_id-{{ $req->id }}" class="form-select supplier-select" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" data-contact="{{ $supplier->contact }}" data-email="{{ $supplier->email }}" data-town="{{ $supplier->town }}">{{ $supplier->name }} @if($supplier->town) - {{ $supplier->town }} @endif</option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 small text-muted">
                                        <span id="supplier-contact-{{ $req->id }}">Contact: —</span>
                                        <span class="mx-2">|</span>
                                        <span id="supplier-email-{{ $req->id }}">Email: —</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="description-{{ $req->id }}" class="form-label fw-semibold">Description:</label>
                                    <input type="text" name="description" id="description-{{ $req->id }}" class="form-control" placeholder="Optional">
                                </div>
                                <div class="col-md-4">
                                    <label for="amount-{{ $req->id }}" class="form-label fw-semibold">Amount (₹):</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" name="amount" id="amount-{{ $req->id }}" class="form-control" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-send-check me-1"></i> Generate Receipt
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-3 mb-0">No approved requests found</p>
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

    // Supplier select preview (contact / email)
    document.querySelectorAll('.supplier-select').forEach(select => {
        const updateInfo = () => {
            const opt = select.selectedOptions[0];
            const contact = (opt && opt.dataset && opt.dataset.contact) ? opt.dataset.contact : '—';
            const email = (opt && opt.dataset && opt.dataset.email) ? opt.dataset.email : '—';
            const id = select.id.split('-').pop();
            const contactEl = document.getElementById('supplier-contact-' + id);
            const emailEl = document.getElementById('supplier-email-' + id);
            if (contactEl) contactEl.textContent = 'Contact: ' + contact;
            if (emailEl) emailEl.textContent = 'Email: ' + email;
        };
        select.addEventListener('change', updateInfo);
        // initialize
        updateInfo();
    });

        // Email Preview Modal Handler
        @if(session('email_preview'))
        (function() {
                const preview = @json(session('email_preview'));

                const modalHtml = `
                <div class="modal fade" id="emailPreviewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Email Preview: ${preview.subject}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div style="border-bottom:1px solid #ddd;padding-bottom:12px;margin-bottom:12px;">
                                  <div class="mb-2">
                                    <label class="form-label fw-semibold">To:</label>
                                    <input type="email" id="email-to" class="form-control" value="${preview.to}" required>
                                  </div>
                                  <div class="mb-2">
                                    <label class="form-label fw-semibold">CC:</label>
                                    <input type="email" id="email-cc" class="form-control" value="${preview.cc}" placeholder="Optional">
                                  </div>
                                  <div class="mb-2">
                                    <label class="form-label fw-semibold">BCC:</label>
                                    <input type="email" id="email-bcc" class="form-control" value="${preview.bcc}" placeholder="Optional">
                                  </div>
                                </div>
                                <div id="email-body-preview">${preview.html}</div>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('transport_officer.receipt.send') }}" id="sendEmailForm">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="receipt_id" value="${preview.receipt_id}">
                                    <input type="hidden" id="hidden-to" name="to" value="">
                                    <input type="hidden" id="hidden-cc" name="cc" value="">
                                    <input type="hidden" id="hidden-bcc" name="bcc" value="">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success" onclick="submitForm()">Send Email</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>`;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                window.submitForm = function() {
                    const to = document.getElementById('email-to').value;
                    const cc = document.getElementById('email-cc').value;
                    const bcc = document.getElementById('email-bcc').value;
                    
                    if (!to) {
                        alert('To field is required');
                        return;
                    }
                    
                    document.getElementById('hidden-to').value = to;
                    document.getElementById('hidden-cc').value = cc;
                    document.getElementById('hidden-bcc').value = bcc;
                    document.getElementById('sendEmailForm').submit();
                };
                
                var modalEl = document.getElementById('emailPreviewModal');
                var bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
        })();
        @endif
});
</script>
@endpush
