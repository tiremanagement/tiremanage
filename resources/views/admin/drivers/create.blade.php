@php
    // Determine layout based on user role
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp

@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@push('styles')
<style>
    .driver-create-shell {
        position: relative;
        padding: 1.75rem 0 2.75rem;
        background: linear-gradient(180deg, #eef3fb 0%, #fdfefe 70%);
    }
    .driver-create-shell::before {
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
    .driver-panel {
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
    .driver-title {
        text-align: center;
        color: #0b4fb4;
        font-weight: 800;
        margin-bottom: 1.5rem;
        letter-spacing: .2px;
    }
    .driver-form { max-width: 980px; margin: 0 auto; }
    .driver-field label {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .3rem;
        letter-spacing: .15px;
    }
    .driver-field .form-control {
        background: #f1f5f9;
        border: 1px solid #d3dded;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: 1rem;
        transition: border-color .12s ease, box-shadow .12s ease, background-color .12s ease;
    }
    .driver-field .form-control:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .18rem rgba(11, 79, 180, .14);
        background: #ffffff;
    }
    .driver-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: 1rem;
        flex-wrap: wrap;
    }
    .driver-actions .btn {
        min-width: 126px;
        border-radius: 10px;
        padding: .7rem 1.15rem;
        font-weight: 800;
        letter-spacing: .12px;
    }
    .driver-actions .btn-success {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: #ffffff;
        border: 1px solid #0f9f54;
        box-shadow: 0 10px 18px rgba(22, 163, 74, 0.22);
    }
    .driver-actions .btn-success:hover { background: linear-gradient(90deg, #15803d, #16a34a); color: #ffffff; }
    .driver-actions .btn-secondary {
        background: #9ca3af;
        border-color: #9ca3af;
        color: #ffffff;
    }
    .driver-actions .btn-secondary:hover { background: #6b7280; border-color: #6b7280; color: #ffffff; }
    .alert-driver {
        border-radius: 12px;
        box-shadow: 0 12px 20px rgba(0,0,0,.08);
    }
    @media (max-width: 768px) {
        .driver-panel { padding: 1.8rem 1.1rem; }
        .driver-actions { flex-direction: column; align-items: stretch; }
        .driver-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('title', 'Register Driver')

@section('content')
<div class="driver-create-shell">
    {{-- Success & Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-driver">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-driver">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-driver mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="driver-panel">
        <h2 class="driver-title">Register Driver</h2>

        <form action="{{ $layout === 'admin' ? route('admin.drivers.store') : route('section_manager.drivers.store') }}" method="POST" class="driver-form">
            @csrf
            <div class="row g-4">
                <div class="col-12 driver-field">
                    <label for="name" class="form-label">User Name*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter username" required>
                </div>
                <div class="col-12 driver-field">
                    <label for="email" class="form-label">Email (for login)*</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email address" required>
                </div>
                <div class="col-12 driver-field">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Enter full name">
                </div>
                <div class="col-12 driver-field">
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="text" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="e.g. 0711234567 or +94711234567">
                    @error('mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 driver-field">
                    <label for="id_number" class="form-label">ID Number*</label>
                    <input type="text" id="id_number" name="id_number" maxlength="12" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" inputmode="numeric" placeholder="Enter NIC / license number" required>
                    @error('id_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="idFeedback" class="form-text text-danger" style="display:none;">This ID number is already registered.</div>
                </div>
            </div>

            <div class="mt-4 driver-actions">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const input = document.getElementById('id_number');
        const feedback = document.getElementById('idFeedback');
        let timeout = null;

        if (!input) return;

        input.addEventListener('input', function () {
            feedback.style.display = 'none';
            input.classList.remove('is-invalid');
            input.setCustomValidity('');

            const val = this.value.trim();
            if (val.length === 0) return; // empty -- nothing to check

            // debounce
            if (timeout) clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch("{{ route('admin.drivers.checkId') }}?q=" + encodeURIComponent(val), { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            feedback.style.display = 'block';
                            input.classList.add('is-invalid');
                            input.setCustomValidity('This ID number is already registered.');
                        } else {
                            feedback.style.display = 'none';
                            input.classList.remove('is-invalid');
                            input.setCustomValidity('');
                        }
                    })
                    .catch(err => {
                            // enforce maxlength client-side (extra safety)
                            if (this.value.length > 12) {
                                this.value = this.value.slice(0, 12);
                            }
                        // silent fail; server may be unreachable during dev
                        console.error('ID check failed', err);
                    });
            }, 450);
        });
        
        // Auto-complete email domain when user types '@'
        const emailInput = document.querySelector('input[name="email"]');
        if (emailInput) {
            emailInput.addEventListener('keyup', function (e) {
                try {
                    // Trigger only when user typed the '@' character and the value currently ends with '@'
                    if (e.key === '@' && this.value.endsWith('@')) {
                        this.value = this.value + 'gmail.com';
                        // put caret at end
                        this.setSelectionRange(this.value.length, this.value.length);
                    }
                } catch (ex) {
                    // ignore any selection errors on older browsers
                    console.error('Email autocomplete error', ex);
                }
            });
        }
    })();
</script>
@endpush
