@php
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp

@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@push('styles')
<style>
    .supplier-create-shell {
        position: relative;
        padding: 1.75rem 0 2.75rem;
        background: linear-gradient(180deg, #eef3fb 0%, #fdfefe 70%);
    }
    .supplier-create-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(520px 360px at 12% 14%, rgba(59,130,246,.12), transparent 60%),
            radial-gradient(520px 360px at 86% 12%, rgba(74,222,128,.1), transparent 60%),
            radial-gradient(640px 480px at 45% 70%, rgba(14,165,233,.08), transparent 70%);
        z-index: 0;
        pointer-events: none;
    }
    .supplier-panel {
        position: relative;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10);
        padding: 2.2rem 2rem 1.9rem;
        max-width: 1080px;
        margin: 0 auto;
        z-index: 1;
    }
    .supplier-title {
        text-align: center;
        color: #0b4fb4;
        font-weight: 800;
        margin-bottom: 1.5rem;
        letter-spacing: .2px;
    }
    .supplier-form { max-width: 980px; margin: 0 auto; }
    .supplier-field label {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .3rem;
        letter-spacing: .15px;
    }
    .supplier-field .form-control,
    .supplier-field textarea {
        background: #f1f5f9;
        border: 1px solid #d3dded;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: 1rem;
        transition: border-color .12s ease, box-shadow .12s ease, background-color .12s ease;
    }
    .supplier-field .form-control:focus,
    .supplier-field textarea:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .18rem rgba(11, 79, 180, .14);
        background: #ffffff;
    }
    textarea { min-height: 110px; resize: vertical; }
    .supplier-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: 1rem;
        flex-wrap: wrap;
    }
    .supplier-actions .btn {
        min-width: 126px;
        border-radius: 10px;
        padding: .7rem 1.15rem;
        font-weight: 800;
        letter-spacing: .12px;
    }
    .supplier-actions .btn-success {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: #ffffff;
        border: 1px solid #0f9f54;
        box-shadow: 0 10px 18px rgba(22, 163, 74, 0.22);
    }
    .supplier-actions .btn-success:hover { background: linear-gradient(90deg, #15803d, #16a34a); color: #ffffff; }
    .supplier-actions .btn-secondary {
        background: #9ca3af;
        border-color: #9ca3af;
        color: #ffffff;
    }
    .supplier-actions .btn-secondary:hover { background: #6b7280; border-color: #6b7280; color: #ffffff; }
    .alert-supplier {
        border-radius: 12px;
        box-shadow: 0 12px 20px rgba(0,0,0,.08);
    }
    @media (max-width: 768px) {
        .supplier-panel { padding: 1.8rem 1.1rem; }
        .supplier-actions { flex-direction: column; align-items: stretch; }
        .supplier-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('title', 'Add Supplier')

@section('content')
<div class="supplier-create-shell">
    {{-- Success & Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-supplier" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-supplier" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-supplier">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="supplier-panel">
        <h2 class="supplier-title">Add Supplier</h2>

        <form action="{{ $layout === 'admin' ? route('admin.suppliers.store') : route('section_manager.suppliers.store') }}" method="POST" class="supplier-form">
            @csrf
            <div class="row g-4">
                <div class="col-12 supplier-field">
                    <label for="name" class="form-label">Name*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter supplier name" required>
                </div>
                <div class="col-12 supplier-field">
                    <label for="contact" class="form-label">Contact*</label>
                    <input type="text" id="contact" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact') }}" placeholder="e.g. 0711234567 or +94711234567" required>
                    @error('contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 supplier-field">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. supplier@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 supplier-field">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" class="form-control" placeholder="Enter address">{{ old('address') }}</textarea>
                </div>
                <div class="col-12 supplier-field">
                    <label for="town" class="form-label">Town</label>
                    <input type="text" id="town" name="town" class="form-control" value="{{ old('town') }}" placeholder="Enter town" maxlength="100">
                </div>
            </div>

            <div class="mt-4 supplier-actions">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ $layout === 'admin' ? route('admin.suppliers.index') : route('section_manager.suppliers.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
