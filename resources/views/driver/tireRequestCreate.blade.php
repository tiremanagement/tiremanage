@extends('layouts.driver')

@section('title', 'Request a Tyre')

@push('styles')
<style>
    .tyre-request-shell {
        position: relative;
        padding: 1.75rem 0 2.75rem;
        background: linear-gradient(180deg, #eef3fb 0%, #fdfefe 70%);
    }
    .tyre-request-shell::before {
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
    .tyre-panel {
        position: relative;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10);
        padding: 1.8rem 1.6rem 1.6rem;
        max-width: 1040px;
        margin: 0 auto;
        z-index: 1;
    }
    .tyre-title {
        text-align: center;
        color: #0b4fb4;
        font-weight: 800;
        margin-bottom: 1.5rem;
        letter-spacing: .2px;
    }
    .tyre-form { max-width: 980px; margin: 0 auto; }
    .tyre-field label {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .3rem;
        letter-spacing: .15px;
    }
    .tyre-field .form-control,
    .tyre-field textarea {
        background: #f1f5f9;
        border: 1px solid #d3dded;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: 1rem;
        transition: border-color .12s ease, box-shadow .12s ease, background-color .12s ease;
    }
    .tyre-field .form-control:focus,
    .tyre-field textarea:focus {
        border-color: #0b4fb4;
        box-shadow: 0 0 0 .18rem rgba(11, 79, 180, .14);
        background: #ffffff;
    }
    textarea { min-height: 110px; resize: vertical; }
    .tyre-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: 1rem;
        flex-wrap: wrap;
    }
    .tyre-actions .btn {
        min-width: 126px;
        border-radius: 10px;
        padding: .7rem 1.15rem;
        font-weight: 800;
        letter-spacing: .12px;
    }
    .tyre-actions .btn-primary {
        background: linear-gradient(90deg, #0ba6df, #0b6edb);
        border-color: #0b6edb;
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(11, 110, 219, 0.28);
    }
    .tyre-actions .btn-primary:hover {
        background: linear-gradient(90deg, #0a8dc4, #0a5ec0);
        border-color: #0a5ec0;
        color: #ffffff;
    }
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
    .helper-text { color: #6b7280; font-weight: 600; margin-top: .25rem; }
    @media (max-width: 768px) {
        .tyre-panel { padding: 1.8rem 1.1rem; }
        .tyre-actions { flex-direction: column; align-items: stretch; }
        .tyre-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="tyre-request-shell">
    {{-- Success & Error messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-tyre">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-tyre">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="tyre-panel">
        <h2 class="tyre-title">Request a Tyre</h2>

        <form action="{{ route('driver.requests.store') }}" method="POST" enctype="multipart/form-data" class="tyre-form">
            @csrf

            @php
                $vehicles = \App\Models\Vehicle::orderBy('plate_no')->get();
            @endphp

            <div class="row g-4">
                <div class="col-12 tyre-field">
                    <label for="plate_no" class="form-label">Vehicle Plate Number*</label>
                    <input list="plates"
                           type="text"
                           name="plate_no"
                           id="plate_no"
                           class="form-control"
                           placeholder="Enter plate number (e.g. ABC-1234)"
                           required
                           autocomplete="off">
                    <datalist id="plates">
                        @foreach($vehicles as $v)
                            <option value="{{ $v->plate_no }}"></option>
                        @endforeach
                    </datalist>
                    <small class="helper-text">If the plate is registered, branch and other vehicle details will auto-fill.</small>
                    <input type="hidden" name="vehicle_id" id="vehicle_id">
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="branch" class="form-label">Branch</label>
                    <input type="text" name="branch" id="branch" class="form-control" readonly>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                    <input type="text" id="vehicle_type" class="form-control" readonly>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="vehicle_brand" class="form-label">Vehicle Brand</label>
                    <input type="text" id="vehicle_brand" class="form-control" readonly>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="user_section" class="form-label">User Section</label>
                    <input type="text" id="user_section" class="form-control" readonly>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="tire_id" class="form-label">Tyre Size*</label>
                    <select name="tire_id" id="tire_id" class="form-control" required>
                        @foreach($tires as $tire)
                            <option value="{{ $tire->id }}">{{ $tire->size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="tire_count" class="form-label">Number of Tyres*</label>
                    <input type="number" name="tire_count" id="tire_count" class="form-control" min="1" value="1" required>
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="delivery_place_office" class="form-label">Delivery Place - Office Name</label>
                    <input type="text" name="delivery_place_office" id="delivery_place_office" class="form-control" maxlength="255">
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="delivery_place_street" class="form-label">Delivery Place - Street Name</label>
                    <input type="text" name="delivery_place_street" id="delivery_place_street" class="form-control" maxlength="255">
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="delivery_place_town" class="form-label">Delivery Place - Town</label>
                    <input type="text" name="delivery_place_town" id="delivery_place_town" class="form-control" maxlength="255">
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="last_tire_replacement_date" class="form-label">Last Tire Replacement Date</label>
                    <input type="date" name="last_tire_replacement_date" id="last_tire_replacement_date" class="form-control">
                </div>

                <div class="col-12 col-md-6 tyre-field">
                    <label for="existing_tire_make" class="form-label">Make of Existing Tyre</label>
                    <input type="text" name="existing_tire_make" id="existing_tire_make" class="form-control" maxlength="255">
                </div>

                <div class="col-12 tyre-field">
                    <label for="damage_description" class="form-label">Damage Description*</label>
                    <textarea name="damage_description" id="damage_description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="col-12 tyre-field">
                    <label for="images" class="form-label">Upload Tyre Images (max 4, each < 2MB)</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                </div>
            </div>

            <div class="mt-4 tyre-actions">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <a href="{{ route('driver.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const plateInput = document.getElementById('plate_no');
    const branchInput = document.getElementById('branch');
    const vehicleId   = document.getElementById('vehicle_id');
    const vehicleType = document.getElementById('vehicle_type');
    const vehicleBrand = document.getElementById('vehicle_brand');
    const userSection = document.getElementById('user_section');

    async function lookupPlate(plate) {
        plate = (plate || '').trim();
        if (!plate) {
            branchInput.value = '';
            vehicleId.value   = '';
            vehicleType.value = '';
            vehicleBrand.value = '';
            userSection.value = '';
            return;
        }

        try {
            const res = await fetch("{{ route('driver.vehicles.lookup') }}?plate_no=" + encodeURIComponent(plate), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();

            if (data.found) {
                branchInput.value = data.branch || '';
                vehicleId.value   = data.id || '';
                vehicleType.value = data.vehicle_type || '';
                vehicleBrand.value = data.brand || '';
                userSection.value = data.user_section || '';
            } else {
                branchInput.value = '';
                vehicleId.value   = '';
                vehicleType.value = '';
                vehicleBrand.value = '';
                userSection.value = '';
            }
        } catch (err) {
            console.error('Lookup failed', err);
            branchInput.value = '';
            vehicleId.value   = '';
            vehicleType.value = '';
            vehicleBrand.value = '';
            userSection.value = '';
        }
    }

    plateInput.addEventListener('input', () => lookupPlate(plateInput.value));
    plateInput.addEventListener('change', () => lookupPlate(plateInput.value));

    // Set client-side max date to enforce "older than 3 months" rule and provide hint
    const lastDateInput = document.getElementById('last_tire_replacement_date');
    if (lastDateInput) {
        const now = new Date();
        const threshold = new Date(now.getFullYear(), now.getMonth() - 3, now.getDate());
        threshold.setDate(threshold.getDate() - 1);
        const isoMax = threshold.toISOString().split('T')[0];
        lastDateInput.max = isoMax;

        const hint = document.createElement('small');
        hint.className = 'text-muted d-block';
        hint.textContent = 'Please enter a date older than 3 months (latest allowed: ' + isoMax + ').';
        lastDateInput.parentNode.appendChild(hint);

        const form = lastDateInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const val = lastDateInput.value;
                if (val && val >= lastDateInput.max) {
                    e.preventDefault();
                    alert('Last Tire Replacement Date must be older than 3 months.');
                    lastDateInput.focus();
                }
            });
        }
    }
});
</script>
@endpush
