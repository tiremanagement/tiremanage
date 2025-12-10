@extends('layouts.driver')

@section('title', 'Request a Tyre')

@section('content')
<div class="container">
    <h2 class="mb-4">Request a Tyre</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('driver.requests.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @php
            $vehicles = \App\Models\Vehicle::orderBy('plate_no')->get();

            $driverVehicles = collect();
            $currentDriver = null;
            if (auth()->check()) {
                $currentDriver = \App\Models\Driver::where('user_id', auth()->id())->first();
                if ($currentDriver) {
                    $driverVehicles = \App\Models\Vehicle::where('driver_id_number', $currentDriver->id_number)->orderBy('plate_no')->get();
                }
            }
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    {{-- Left column: vehicle & tyre selection --}}
                    <div class="col-md-6">
                        {{-- Vehicle Plate Number --}}
                        <div class="mb-3">
                            <label for="plate_no" class="form-label">Vehicle Plate Number</label>
                            <input list="plates"
                                type="text"
                                name="plate_no"
                                id="plate_no"
                                class="form-control @error('vehicle_id') is-invalid @enderror"
                                placeholder="Enter plate number (e.g. ABC-1234)"
                                value="{{ old('plate_no', optional($driverVehicles->first())->plate_no) }}"
                                required
                                autocomplete="off"
                                @if($driverVehicles->isNotEmpty()) readonly @endif>
                            <datalist id="plates">
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->plate_no }}"></option>
                                @endforeach
                            </datalist>
                            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', optional($driverVehicles->first())->id) }}">
                            <input type="hidden" name="user_section" id="user_section" value="{{ old('user_section', optional($driverVehicles->first())->user_section) }}">
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">If the plate is registered, branch and other vehicle details will auto-fill.</small>
                        </div>

                        {{-- Tyre Size --}}
                        <div class="mb-3">
                            <label for="tire_id" class="form-label">Tyre Size</label>
                            <select name="tire_id" id="tire_id" class="form-control @error('tire_id') is-invalid @enderror" required>
                                <option value="">Choose tyre size</option>
                                @foreach($tires as $tire)
                                    <option value="{{ $tire->id }}" {{ old('tire_id') == $tire->id ? 'selected' : '' }}>{{ $tire->brand }} — {{ $tire->size }}</option>
                                @endforeach
                            </select>
                            @error('tire_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tyre Count --}}
                        <div class="mb-3">
                            <label for="tire_count" class="form-label">Number of Tyres</label>
                            <input type="number" name="tire_count" id="tire_count" class="form-control @error('tire_count') is-invalid @enderror" min="1" value="{{ old('tire_count', 1) }}" required>
                            @error('tire_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Make of Existing Tyre --}}
                        <div class="mb-3">
                            <label for="existing_tire_make" class="form-label">Make of Existing Tyre</label>
                            <input type="text" name="existing_tire_make" id="existing_tire_make" class="form-control @error('existing_tire_make') is-invalid @enderror" maxlength="255" value="{{ old('existing_tire_make') }}">
                            @error('existing_tire_make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Right column: delivery, date, images, description --}}
                    <div class="col-md-6">
                        {{-- Branch (auto-filled, read-only) --}}
                        <div class="mb-3">
                            <label for="branch" class="form-label">Branch</label>
                            <input type="text" name="branch" id="branch" class="form-control" readonly value="{{ old('branch') }}">
                        </div>

                        {{-- Vehicle Type (auto-filled, read-only) --}}
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <input type="text" id="vehicle_type" class="form-control" readonly value="{{ old('vehicle_type', optional($driverVehicles->first())->vehicle_type) }}">
                        </div>

                        {{-- Brand (auto-filled, read-only) --}}
                        <div class="mb-3">
                            <label for="vehicle_brand" class="form-label">Brand</label>
                            <input type="text" id="vehicle_brand" class="form-control" readonly value="{{ old('vehicle_brand', optional($driverVehicles->first())->brand) }}">
                        </div>

                        {{-- Delivery Place grouped --}}
                        <div class="mb-3">
                            <label class="form-label">Delivery Place</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Office</span>
                                <input type="text" name="delivery_place_office" id="delivery_place_office" class="form-control @error('delivery_place_office') is-invalid @enderror" maxlength="255" value="{{ old('delivery_place_office') }}">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Street</span>
                                <input type="text" name="delivery_place_street" id="delivery_place_street" class="form-control @error('delivery_place_street') is-invalid @enderror" maxlength="255" value="{{ old('delivery_place_street') }}">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Town</span>
                                <input type="text" name="delivery_place_town" id="delivery_place_town" class="form-control @error('delivery_place_town') is-invalid @enderror" maxlength="255" value="{{ old('delivery_place_town') }}">
                            </div>
                        </div>

                        {{-- Last Tire Replacement Date --}}
                        <div class="mb-3">
                            <label for="last_tire_replacement_date" class="form-label">Last Tire Replacement Date</label>
                            <input type="date" name="last_tire_replacement_date" id="last_tire_replacement_date" class="form-control @error('last_tire_replacement_date') is-invalid @enderror" value="{{ old('last_tire_replacement_date') }}">
                            @error('last_tire_replacement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Upload Images --}}
                        <div class="mb-3">
                            <label for="images" class="form-label">Upload Tyre Images (max 4, each &lt; 2MB)</label>
                            <input type="file" name="images[]" id="images" class="form-control @error('images') is-invalid @enderror" multiple accept="image/*">
                            @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Full-width Damage Description --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="damage_description" class="form-label">Damage Description</label>
                            <textarea name="damage_description" id="damage_description" class="form-control @error('damage_description') is-invalid @enderror" rows="4" required>{{ old('damage_description') }}</textarea>
                            @error('damage_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
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

    // If server detected driver-assigned vehicle(s), make those values read-only
    const driverVehicles = @json($driverVehicles->toArray());

    if (driverVehicles && driverVehicles.length > 0) {
        const v = driverVehicles[0];
        plateInput.value = v.plate_no || '';
        plateInput.readOnly = true;
        // hide datalist to prevent selection changes
        if (plateInput.hasAttribute('list')) plateInput.removeAttribute('list');

        vehicleId.value = v.id || '';
        branchInput.value = v.branch || '';
        vehicleType.value = v.vehicle_type || '';
        if (vehicleBrand) vehicleBrand.value = v.brand || '';

        // if user_section hidden input exists set it
        if (userSection) userSection.value = v.user_section || '';
    }

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

    // Only attach live lookup when driver is not locked to a vehicle
    if (!(driverVehicles && driverVehicles.length > 0)) {
        plateInput.addEventListener('input', () => lookupPlate(plateInput.value));
        plateInput.addEventListener('change', () => lookupPlate(plateInput.value));
    }

    // Set client-side max date to enforce "older than 3 months" rule and provide hint
    const lastDateInput = document.getElementById('last_tire_replacement_date');
    if (lastDateInput) {
        const now = new Date();
        // compute threshold: 3 months ago
        const threshold = new Date(now.getFullYear(), now.getMonth() - 3, now.getDate());
        // require strictly older than 3 months -> set max to the day before threshold
        threshold.setDate(threshold.getDate() - 1);
        const isoMax = threshold.toISOString().split('T')[0];
        lastDateInput.max = isoMax;

        // Add a small hint below the input
        const hint = document.createElement('small');
        hint.className = 'text-muted d-block';
        hint.textContent = 'Please enter a date older than 3 months (latest allowed: ' + isoMax + ').';
        lastDateInput.parentNode.appendChild(hint);

        // Prevent form submission if the date is not older than 3 months (client-side check)
        const form = lastDateInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const val = lastDateInput.value;
                if (val) {
                    if (val >= lastDateInput.max) {
                        e.preventDefault();
                        alert('Last Tire Replacement Date must be older than 3 months.');
                        lastDateInput.focus();
                    }
                }
            });
        }
    }
});
</script>
@endpush
