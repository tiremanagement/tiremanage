@php
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp

@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@push('styles')
<style>
    .tyre-create-shell { position: relative; padding: 1.75rem 0 2.75rem; background: linear-gradient(180deg, #eef3fb 0%, #fdfefe 70%); }
    .tyre-create-shell::before { content: none; }

    .tyre-create-card {
        position: relative;
        overflow: hidden;
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.12);
        padding: 2.3rem 2.1rem 2.1rem;
        isolation: isolate;
    }
    .tyre-title {
        font-weight: 800;
        color: #0b4fb4;
        text-align: center;
        margin-bottom: 1.35rem;
        letter-spacing: .2px;
    }
    .tyre-meta {
        background: rgba(11, 79, 180, .08);
        border: 1px solid rgba(11, 79, 180, .14);
        color: #0f172a;
        border-radius: 14px;
        padding: .95rem 1.1rem;
        margin-bottom: 1.25rem;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .tyre-meta .muted { color: #475569; font-weight: 600; }
    .tyre-subline { color: #0f172a; font-weight: 600; margin-bottom: 1rem; text-align: center; }
    .tyre-subline span { color: #475569; font-weight: 500; }
    .tyre-form { max-width: 940px; margin: 0 auto; }
    .tyre-field label {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .3rem;
        letter-spacing: .15px;
    }
    .tyre-field .form-control {
        background: #f1f5f9;
        border: 1px solid #d3dded;
        border-radius: 14px;
        padding: .9rem 1rem;
        font-size: 1rem;
        box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.06);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background-color .12s ease;
    }
    .tyre-field .form-control:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .18rem rgba(11, 79, 180, .15);
        background: #ffffff;
        transform: translateY(-1px);
    }
    .tyre-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: 1rem;
        flex-wrap: wrap;
    }
    .tyre-actions .btn {
        min-width: 132px;
        border-radius: 12px;
        padding: .78rem 1.25rem;
        font-weight: 800;
        letter-spacing: .15px;
    }
    .tyre-actions .btn-success {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: #ffffff;
        border: 1px solid #0f9f54;
        box-shadow: 0 12px 24px rgba(22, 163, 74, 0.28);
    }
    .tyre-actions .btn-success:hover { background: linear-gradient(90deg, #15803d, #16a34a); color: #ffffff; }
    .tyre-actions .btn-secondary {
        background: #9ca3af;
        border-color: #9ca3af;
        color: #ffffff;
    }
    .tyre-actions .btn-secondary:hover { background: #6b7280; border-color: #6b7280; color: #ffffff; }
    .alert-tyre {
        border-radius: 12px;
        box-shadow: 0 12px 20px rgba(0,0,0,.08);
    }
    @media (max-width: 768px) {
        .tyre-create-card { padding: 2.1rem 1.25rem; }
        .tyre-actions { flex-direction: column; align-items: stretch; }
        .tyre-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('title', 'Add Tyre')

@section('content')
<div class="tyre-create-shell">
    <div class="tyre-meta">
        <span>Add a new tyre to inventory</span>
        <span class="muted">All required fields are marked with *</span>
    </div>

    {{-- Success & Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-tyre" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-tyre" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-tyre">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="tyre-create-card">
        <h2 class="tyre-title">Add Tyre</h2>
        <div class="tyre-subline">
            Provide brand, size, and supplier. <span>Keep data consistent with supplier records.</span>
        </div>

        <form action="{{ $layout === 'admin' ? route('admin.tires.store') : route('section_manager.tires.store') }}" method="POST" class="tyre-form">
            @csrf
            <div class="row g-4">
                <div class="col-12 tyre-field">
                    <label for="brand" class="form-label">Brand*</label>
                    <input type="text" id="brand" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" placeholder="Enter tyre brand" required>
                    @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 tyre-field">
                    <label for="size" class="form-label">Size*</label>
                    <input type="text" id="size" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size') }}" placeholder="Enter tyre size" required>
                    @error('size')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 tyre-field">
                    <label for="supplier_id" class="form-label">Supplier*</label>
                    <select id="supplier_id" name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="tyre-actions">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ $layout === 'admin' ? route('admin.tires.index') : route('section_manager.tires.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
