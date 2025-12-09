@php
    // Determine layout based on user role
    $layout = Auth::user() && Auth::user()->role
        ? strtolower(str_replace(' ', '_', Auth::user()->role->name))
        : 'admin';
@endphp

@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

<<<<<<< ours
@push('styles')
<style>
    .driver-create-shell {
        position: relative;
        padding: 1.5rem 0 2.5rem;
    }
    .driver-create-card {
        position: relative;
        overflow: hidden;
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(12, 74, 165, 0.08);
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.14);
        padding: 2.5rem 2rem 2rem;
        isolation: isolate;
    }
    .driver-create-card::before,
    .driver-create-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        background-size: 220px 520px, 220px 520px, 160px 360px;
        background-position: -40px 30px, calc(100% - 80px) 120px, 40% -60px;
        opacity: 0.18;
        z-index: -1;
    }
    .driver-create-card::before {
        background-image:
            linear-gradient(135deg, rgba(42, 121, 255, .18), rgba(56, 189, 248, .22)),
            linear-gradient(135deg, rgba(74, 222, 128, .2), rgba(59, 130, 246, .18)),
            linear-gradient(135deg, rgba(59, 130, 246, .16), rgba(59, 130, 246, .04));
    }
    .driver-create-card::after {
        background-image:
            radial-gradient(180px 120px at 15% 20%, rgba(59, 130, 246, .12), transparent 65%),
            radial-gradient(220px 140px at 85% 35%, rgba(74, 222, 128, .12), transparent 65%),
            radial-gradient(160px 140px at 60% 75%, rgba(56, 189, 248, .12), transparent 65%);
        opacity: 0.3;
        filter: blur(1px);
    }
    .driver-page-title {
        font-weight: 800;
        color: #0b4fb4;
        text-align: center;
        margin-bottom: 1.75rem;
        letter-spacing: .2px;
    }
    .driver-form {
        max-width: 920px;
        margin: 0 auto;
    }
    .driver-field label {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: .35rem;
    }
    .driver-field .form-control {
        background: #f7f9fc;
        border: 1px solid #dce3ee;
        border-radius: 12px;
        padding: .85rem 1rem;
        font-size: 1rem;
        box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.06);
    }
    .driver-field .form-control:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .2rem rgba(11, 79, 180, .12);
        background: #ffffff;
    }
    .driver-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .75rem;
        padding-top: 1rem;
        border-top: 1px solid #dbeafe;
        flex-wrap: wrap;
    }
    .driver-actions .btn {
        min-width: 120px;
        border-radius: 12px;
        padding: .7rem 1.2rem;
        font-weight: 700;
        letter-spacing: .1px;
        box-shadow: 0 10px 18px rgba(16, 185, 129, 0.18);
    }
    .driver-actions .btn-secondary {
        background: #6b7280;
        border-color: #6b7280;
        box-shadow: none;
    }
<<<<<<< ours
    .driver-actions .btn-secondary:hover { background: #111827; border-color: #111827; }
    .driver-actions .btn-success {
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: #ffffff;
        border: 1px solid #0f9f54;
        box-shadow: 0 12px 24px rgba(22, 163, 74, 0.28);
    }
    .driver-actions .btn-success:hover { background: linear-gradient(90deg, #15803d, #16a34a); color: #ffffff; }
=======
    .driver-actions .btn-secondary:hover {
        background: #4b5563;
        border-color: #4b5563;
    }
>>>>>>> theirs
    .driver-meta {
        background: rgba(11, 79, 180, .06);
        border: 1px solid rgba(11, 79, 180, .12);
        color: #0f172a;
        border-radius: 12px;
        padding: .85rem 1rem;
        margin-bottom: 1.25rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .driver-meta .muted { color: #475569; font-weight: 500; }
    .alert-driver {
        border-radius: 12px;
        box-shadow: 0 12px 20px rgba(0,0,0,.08);
    }
    @media (max-width: 768px) {
        .driver-create-card { padding: 2rem 1.25rem; }
        .driver-create-card::before,
        .driver-create-card::after { background-size: 180px 420px, 180px 420px, 140px 320px; }
        .driver-actions { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

=======
>>>>>>> theirs
@section('title', 'Register Driver')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="mb-4">Register Driver</h2>

    {{-- Success & Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<<<<<<< ours
    <div class="driver-create-card">
<<<<<<< ours
        <div class="driver-header">
            <h2 class="title">Add New Driver</h2>
            <div class="badge-pill">Step 1 - Driver Profile</div>
        </div>
        <div class="driver-subline">
            Tell us about the driver. <span>Use strong usernames and the official ID.</span>
        </div>
=======
        <h2 class="driver-page-title">Add New Driver</h2>
>>>>>>> theirs
=======
    <form action="{{ $layout === 'admin' ? route('admin.drivers.store') : route('section_manager.drivers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Username*</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email (for login)*</label>
            <input type="email" name="email" class="form-control" required>
        </div>
>>>>>>> theirs

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control">
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="e.g. 0711234567 or +94711234567">
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number*</label>
            <input type="text" id="id_number" name="id_number" maxlength="12" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" inputmode="numeric" required>
            @error('id_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="idFeedback" class="form-text text-danger" style="display:none;">This ID number is already registered.</div>
        </div>

        <button type="submit" class="btn btn-primary">Register Driver</button>
    </form>
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



