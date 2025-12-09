@php
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp

@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@push('styles')
<style>
    .vehicle-create-shell { position: relative; padding: 1.75rem 0 2.75rem; background: #f6f8fb; }
    .vehicle-create-shell::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(520px 360px at 12% 14%, rgba(59,130,246,.08), transparent 60%),
            radial-gradient(520px 360px at 86% 12%, rgba(74,222,128,.06), transparent 60%),
            radial-gradient(640px 480px at 45% 70%, rgba(14,165,233,.05), transparent 70%);
        z-index: 0;
        pointer-events: none;
    }
    .vehicle-create-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,251,255,.99));
        border-radius: 22px;
        border: 1px solid rgba(12, 74, 165, 0.1);
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.14);
        padding: 2.4rem 2.25rem 2.1rem;
        isolation: isolate;
    }
    .vehicle-create-card::before,
    .vehicle-create-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        z-index: -1;
    }
    .vehicle-create-card::before {
        background-image:
            linear-gradient(120deg, rgba(42, 121, 255, .2), rgba(56, 189, 248, .22)),
            linear-gradient(120deg, rgba(74, 222, 128, .2), rgba(59, 130, 246, .18));
        background-size: 260px 540px, 220px 460px;
        background-position: -40px 6px, calc(100% - 70px) 130px;
        opacity: .78;
    }
    .vehicle-create-card::after {
        background-image:
            radial-gradient(220px 140px at 18% 78%, rgba(59, 130, 246, .12), transparent 70%),
            radial-gradient(260px 160px at 74% 35%, rgba(74, 222, 128, .12), transparent 70%),
            radial-gradient(180px 120px at 55% 62%, rgba(14, 165, 233, .12), transparent 75%);
        opacity: .7;
        filter: blur(.3px);
    }
    .vehicle-title {
        font-weight: 800;
        color: #0b4fb4;
        text-align: center;
        margin-bottom: 1.35rem;
        letter-spacing: .2px;
    }
    .vehicle-meta {
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
    .vehicle-meta .muted { color: #475569; font-weight: 600; }
    .vehicle-subline { color: #0f172a; font-weight: 600; margin-bottom: 1rem; text-align: center; }
    .vehicle-subline span { color: #475569; font-weight: 500; }
    .vehicle-form { max-width: 940px; margin: 0 auto; }
    .vehicle-field label {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .3rem;
        letter-spacing: .15px;
    }
    .vehicle-field .form-control {
        background: #f1f5f9;
        border: 1px solid #d3dded;
        border-radius: 14px;
        padding: .9rem 1rem;
        font-size: 1rem;
        box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.06);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background-color .12s ease;
    }
    .vehicle-field .form-control:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .18rem rgba(11, 79, 180, .15);
        background: #ffffff;
        transform: translateY(-1px);
    }
    .vehicle-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: 1rem;
        flex-wrap: wrap;
    }
    .vehicle-actions .btn {
        min-width: 132px;
        border-radius: 12px;
        padding: .78rem 1.25rem;
        font-weight: 800;
        letter-spacing: .15px;
    }
    .vehicle-actions .btn-success {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: #ffffff;
        border: 1px solid #0f9f54;
        box-shadow: 0 12px 24px rgba(22, 163, 74, 0.28);
    }
    .vehicle-actions .btn-success:hover { background: linear-gradient(90deg, #15803d, #16a34a); color: #ffffff; }
    .vehicle-actions .btn-secondary {
        background: #9ca3af;
        border-color: #9ca3af;
        color: #ffffff;
    }
    .vehicle-actions .btn-secondary:hover { background: #6b7280; border-color: #6b7280; color: #ffffff; }
    .alert-vehicle {
        border-radius: 12px;
        box-shadow: 0 12px 20px rgba(0,0,0,.08);
    }
    @media (max-width: 768px) {
        .vehicle-create-card { padding: 2.1rem 1.25rem; }
        .vehicle-actions { flex-direction: column; align-items: stretch; }
        .vehicle-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('title', 'Add Vehicle')

@section('content')
<div class="vehicle-create-shell">
    <div class="vehicle-meta">
        <span>Add a new vehicle to the fleet system</span>
        <span class="muted">All required fields are marked with *</span>
    </div>

    {{-- Success & Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-vehicle" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-vehicle" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-vehicle">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="vehicle-create-card">
        <h2 class="vehicle-title">Add New Vehicle</h2>
        <div class="vehicle-subline">
            Enter accurate vehicle details. <span>Use the official registration and branch names.</span>
        </div>

        <form
            action="{{ $layout === 'admin' ? route('admin.vehicles.store') : route('section_manager.vehicles.store') }}"
            method="POST"
            class="vehicle-form needs-validation"
            novalidate
        >
            @csrf
            <div class="row g-4">
                <div class="col-12 vehicle-field">
                    <label for="model" class="form-label">Model*</label>
                    <input type="text" name="model" id="model"
                           class="form-control @error('model') is-invalid @enderror"
                           value="{{ old('model') }}"
                           placeholder="Enter vehicle model" required
                           pattern="^[^0-9]*$" title="Model must not contain numbers">
                    @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 vehicle-field">
                    <label for="plate_no" class="form-label">Registration Number*</label>
                    <input type="text" name="plate_no" id="plate_no"
                           class="form-control text-uppercase @error('plate_no') is-invalid @enderror"
                           value="{{ old('plate_no') }}"
                           placeholder="Enter registration number (e.g. ABC123)" required>
                    @error('plate_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 vehicle-field">
                    <label for="branch" class="form-label">Branch*</label>
                    <input type="text" name="branch" id="branch"
                           class="form-control @error('branch') is-invalid @enderror"
                           value="{{ old('branch') }}"
                           placeholder="Enter branch name" required
                           pattern="^[^0-9]*$" title="Branch must not contain numbers">
                    @error('branch')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 vehicle-field">
                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                    <input type="text" name="vehicle_type" id="vehicle_type"
                           class="form-control @error('vehicle_type') is-invalid @enderror"
                           value="{{ old('vehicle_type') }}"
                           placeholder="e.g. Car, Van, Truck"
                           pattern="^[^0-9]*$" title="Vehicle type must not contain numbers">
                    @error('vehicle_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 vehicle-field">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" name="brand" id="brand"
                           class="form-control @error('brand') is-invalid @enderror"
                           value="{{ old('brand') }}"
                           placeholder="e.g. Toyota, Nissan, Honda"
                           pattern="^[^0-9]*$" title="Brand must not contain numbers">
                    @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 vehicle-field">
                    <label for="user_section" class="form-label">User Section</label>
                    <input type="text" name="user_section" id="user_section"
                           class="form-control @error('user_section') is-invalid @enderror"
                           value="{{ old('user_section') }}"
                           placeholder="e.g. Transport, Admin, Field Ops"
                           pattern="^[^0-9]*$" title="User section must not contain numbers">
                    @error('user_section')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="vehicle-actions">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ $layout === 'admin' ? route('admin.vehicles.index') : route('section_manager.vehicles.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
