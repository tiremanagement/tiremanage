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
    .tyre-field label, .form-label {
        font-weight: 500;
        color: #334155;
        margin-bottom: .3rem;
        letter-spacing: .12px;
        font-size: 1.03rem;
    }
    .required-star {
        color: #e11d48;
        margin-left: 2px;
        font-size: 1em;
        font-weight: 500;
        opacity: 0.85;
    }
    .locked-detail, .form-control[readonly] {
        color: #6b7280 !important;
        background: #f3f4f6 !important;
        border-color: #e5e7eb !important;
        font-weight: 600;
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
    
    /* Image Upload Styles */
    .tire-upload-zone {
        border: 2px dashed #0b4fb4;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, rgba(11, 79, 180, 0.05) 0%, rgba(11, 110, 219, 0.02) 100%);
    }
    .tire-upload-zone:hover {
        border-color: #0a5ec0;
        background: linear-gradient(135deg, rgba(11, 79, 180, 0.1) 0%, rgba(11, 110, 219, 0.05) 100%);
        box-shadow: 0 4px 16px rgba(11, 79, 180, 0.15);
    }
    .tire-upload-zone.drag-over {
        border-color: #0b6edb;
        background: linear-gradient(135deg, rgba(11, 79, 180, 0.15) 0%, rgba(11, 110, 219, 0.1) 100%);
        box-shadow: 0 8px 24px rgba(11, 79, 180, 0.25);
        transform: scale(1.01);
    }
    .tire-upload-icon {
        color: #0b4fb4;
        margin-bottom: 0.75rem;
        opacity: 0.8;
    }
    .tire-upload-text {
        color: #0f172a;
        margin: 0;
        font-size: 0.95rem;
    }
    .tire-upload-text small {
        color: #6b7280;
        display: block;
        margin-top: 0.25rem;
    }
    
    /* Preview Grid */
    .tire-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
    }
    .tire-preview-box {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: #f1f5f9;
        border: 2px solid #d3dded;
        aspect-ratio: 1;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tire-preview-box:hover {
        border-color: #0b4fb4;
        box-shadow: 0 4px 12px rgba(11, 79, 180, 0.2);
        transform: translateY(-2px);
    }
    .tire-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .tire-preview-box .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 28px;
        height: 28px;
        background: rgba(239, 68, 68, 0.9);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        opacity: 0;
        transition: opacity 0.2s ease;
        padding: 0;
    }
    .tire-preview-box:hover .remove-btn {
        opacity: 1;
    }
    .tire-preview-box .remove-btn:hover {
        background: rgba(220, 38, 38, 1);
    }
    .tire-preview-add {
        border: 2px dashed #0b4fb4;
        background: rgba(11, 79, 180, 0.05);
    }
    .tire-preview-add:hover {
        background: rgba(11, 79, 180, 0.1);
        border-color: #0a5ec0;
    }
    .tire-preview-add-icon {
        font-size: 2rem;
        color: #0b4fb4;
    }

    @media (max-width: 768px) {
        .tyre-panel { padding: 1.8rem 1.1rem; }
        .tyre-actions { flex-direction: column; align-items: stretch; }
        .tyre-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-tyre">{{ session('success') }}</div>
    @endif


    <div class="tyre-panel">
        <h2 class="tyre-title">Request a Tyre</h2>

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
                        <div class="mb-3 tyre-field">
                            <label for="plate_no" class="form-label">Vehicle Plate Number <span class="required-star">*</span></label>
                            <input list="plates"
                                type="text"
                                name="plate_no"
                                id="plate_no"
                                class="form-control @error('vehicle_id') is-invalid @enderror @if($driverVehicles->isNotEmpty()) locked-detail @endif"
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
                        <div class="mb-3 tyre-field">
                            <label for="tire_id" class="form-label">Tyre Size <span class="required-star">*</span></label>
                            <select name="tire_id" id="tire_id" class="form-control @error('tire_id') is-invalid @enderror" required>
                                <option value="">Choose tyre size</option>
                                @foreach($tires as $tire)
                                    <option value="{{ $tire->id }}" {{ old('tire_id') == $tire->id ? 'selected' : '' }}>{{ $tire->brand }} — {{ $tire->size }}</option>
                                @endforeach
                            </select>
                            @error('tire_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tyre Count --}}
                        <div class="mb-3 tyre-field">
                            <label for="tire_count" class="form-label">Number of Tyres <span class="required-star">*</span></label>
                            <input type="number" name="tire_count" id="tire_count" class="form-control @error('tire_count') is-invalid @enderror" min="1" value="{{ old('tire_count', 1) }}" required>
                            @error('tire_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Make of Existing Tyre --}}
                        <div class="mb-3 tyre-field">
                            <label for="existing_tire_make" class="form-label">Make of Existing Tyre</label>
                            <input type="text" name="existing_tire_make" id="existing_tire_make" class="form-control @error('existing_tire_make') is-invalid @enderror" maxlength="255" value="{{ old('existing_tire_make') }}">
                            @error('existing_tire_make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Right column: delivery, date, images, description --}}
                    <div class="col-md-6">
                        {{-- Branch (auto-filled, read-only) --}}
                        <div class="mb-3 tyre-field">
                            <label for="branch" class="form-label">Branch</label>
                            <input type="text" name="branch" id="branch" class="form-control locked-detail" readonly value="{{ old('branch') }}">
                        </div>

                        {{-- Vehicle Type (auto-filled, read-only) --}}
                        <div class="mb-3 tyre-field">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <input type="text" id="vehicle_type" class="form-control locked-detail" readonly value="{{ old('vehicle_type', optional($driverVehicles->first())->vehicle_type) }}">
                        </div>

                        {{-- Brand (auto-filled, read-only) --}}
                        <div class="mb-3 tyre-field">
                            <label for="vehicle_brand" class="form-label">Brand</label>
                            <input type="text" id="vehicle_brand" class="form-control locked-detail" readonly value="{{ old('vehicle_brand', optional($driverVehicles->first())->brand) }}">
                        </div>

                        {{-- Delivery Place grouped --}}
                        <div class="mb-3 tyre-field">
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
                        <div class="mb-3 tyre-field">
                            <label for="last_tire_replacement_date" class="form-label">Last Tire Replacement Date</label>
                            <input type="date" name="last_tire_replacement_date" id="last_tire_replacement_date" class="form-control @error('last_tire_replacement_date') is-invalid @enderror" value="{{ old('last_tire_replacement_date') }}">
                            @error('last_tire_replacement_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Upload Images with Preview --}}
                        <div class="mb-3 tyre-field">
                            <label class="form-label">Upload Tyre Images (max 4, each &lt; 2MB) <span class="required-star">*</span></label>
                            
                            <!-- Hidden file input -->
                            <input type="file" name="images[]" id="images" class="d-none" multiple accept="image/*">
                            
                            <!-- Drag & Drop Area -->
                            <div id="dropZone" class="tire-upload-zone mb-3">
                                <svg class="tire-upload-icon" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <p class="tire-upload-text">
                                    <strong>Click to upload</strong> or drag and drop<br>
                                    <small>PNG, JPG, GIF up to 2MB</small>
                                </p>
                            </div>
                            
                            <!-- Image Preview Grid -->
                            <div id="imagePreviewContainer" class="tire-preview-grid mb-3">
                                <!-- Preview boxes will be added here by JS -->
                            </div>
                            
                            @error('images')
                                <div class="alert alert-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Full-width Damage Description --}}
                    <div class="col-12">
                        <div class="mb-4 px-md-5 py-3" style="background: #f8fafc; border-radius: 12px; box-shadow: 0 2px 8px rgba(11,79,180,0.06);">
                            <label for="damage_description" class="form-label fw-bold" style="font-size:1.1rem; color:#0b4fb4;">Damage Description <span class="required-star">*</span></label>
                            <textarea name="damage_description" id="damage_description" class="form-control @error('damage_description') is-invalid @enderror" rows="5" style="font-size:1.08rem; min-height:120px; background:#f1f5f9; border-radius:10px; border:1.5px solid #d3dded;" required>{{ old('damage_description') }}</textarea>
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

    // ============== Image Upload Handler ==============
    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const MAX_FILES = 4;
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    
    // Store files in a DataTransfer container to update the file input
    const fileStore = new DataTransfer();

    // Click to upload
    dropZone.addEventListener('click', () => imageInput.click());

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }, false);

    // File input change
    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        let validFilesAdded = 0;

        for (let file of files) {
            if (fileStore.items.length >= MAX_FILES) {
                alert(`Maximum ${MAX_FILES} images allowed!`);
                break;
            }

            if (!file.type.startsWith('image/')) {
                alert(`${file.name} is not a valid image file!`);
                continue;
            }

            if (file.size > MAX_FILE_SIZE) {
                alert(`${file.name} exceeds 2MB limit!`);
                continue;
            }

            // Add file to DataTransfer
            fileStore.items.add(file);

            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                addPreview(e.target.result, file.name);
            };
            reader.readAsDataURL(file);
            validFilesAdded++;
        }

        // Update the actual file input with the DataTransfer files
        imageInput.files = fileStore.files;
        updateFileInput();
    }

    function addPreview(imageSrc, fileName) {
        const box = document.createElement('div');
        box.className = 'tire-preview-box';
        box.innerHTML = `
            <img src="${imageSrc}" alt="preview" title="${fileName}">
            <button type="button" class="remove-btn" title="Remove image">×</button>
        `;

        box.querySelector('.remove-btn').addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Remove the file from DataTransfer by creating a new one without this file
            // This is a workaround since we can't directly remove from DataTransfer
            const newFileStore = new DataTransfer();
            const fileIndex = Array.from(previewContainer.querySelectorAll('[data-file-index]')).indexOf(box);
            
            for (let i = 0; i < fileStore.files.length; i++) {
                if (i !== fileIndex) {
                    newFileStore.items.add(fileStore.files[i]);
                }
            }
            
            Object.assign(fileStore, newFileStore);
            imageInput.files = newFileStore.files;
            box.remove();
            updateFileInput();
        });

        // Add data attribute for tracking
        box.setAttribute('data-file-index', previewContainer.querySelectorAll('[data-file-index]').length);
        previewContainer.appendChild(box);
    }

    function updateFileInput() {
        // No add button: just update previews
    }

    // Call once on load to clear previews
    updateFileInput();
});
</script>
@endpush
