@extends('layouts.driver')

@section('title', 'Manage Account')

@section('content')
<div class="settings-shell container px-0">
  <div class="settings-header d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div>
      <h1 class="h4 mb-1 fw-semibold">Account</h1>
      <div class="text-muted">Manage your profile information and photo.</div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <div class="row g-4">
    <aside class="col-lg-3">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img id="photoPreview" src="{{ $profilePhoto }}" alt="Profile Photo" class="rounded-circle border avatar-md shadow-sm">
          </div>
          <nav class="settings-nav nav flex-column">
            <a class="nav-link active" href="{{ route('driver.profile.edit') }}">Profile</a>
            <a class="nav-link" href="{{ route('driver.password.form') }}">Security</a>
          </nav>
          <div class="mt-3 d-flex gap-2">
            <label for="profilePhotoInputSidebar" class="btn btn-light border btn-icon" title="Upload" aria-label="Upload">
              <i class="bi bi-upload"></i>
            </label>
            <button type="button" class="btn btn-outline-danger btn-icon" title="Remove" aria-label="Remove" onclick="removePhoto()">
              <i class="bi bi-trash"></i>
            </button>
          </div>
          <input type="file" id="profilePhotoInputSidebar" name="profile_photo_sidebar" accept="image/*" onchange="previewPhoto(event)" class="d-none">
          <input type="hidden" id="removePhotoFlag" name="remove_photo" value="0">
        </div>
      </div>
    </aside>

    <section class="col-lg-9">
      <div class="panel card">
        <div class="card-header bg-white">
          <div class="fw-semibold">Profile</div>
          <div class="small text-muted">Keep your details up to date.</div>
        </div>
        <div class="card-body">
          <form action="{{ route('driver.profile.update') }}" method="POST" enctype="multipart/form-data" class="row g-3" id="profileForm">
            @csrf
            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" onchange="previewPhoto(event)" class="d-none">
            <input type="hidden" id="removePhotoFlag" name="remove_photo" value="0">
            <div class="col-12">
              <label class="form-label">Username</label>
              <input type="text" name="name" class="form-control form-control-modern" value="{{ $driver->user->name }}" required>
            </div>
            <div class="col-12">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control form-control-modern" value="{{ $driver->full_name }}" required>
            </div>
            <div class="col-12">
              <label class="form-label">Mobile</label>
              <input type="text" name="mobile" class="form-control form-control-modern @error('mobile') is-invalid @enderror" value="{{ old('mobile', $driver->mobile) }}" placeholder="e.g. 0711234567 or +94711234567">
              @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control form-control-modern" value="{{ $driver->user->email }}" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">ID Number</label>
              <input type="text" class="form-control form-control-modern" value="{{ $driver->id_number }}" readonly>
            </div>
            <div class="col-12 pt-2 d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
              <a href="{{ route('driver.password.form') }}" class="btn btn-outline-secondary">Change Password</a>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
function previewPhoto(event) {
  const output = document.getElementById('photoPreview');
  const sidebarInput = document.getElementById('profilePhotoInputSidebar');
  const formInput = document.getElementById('profilePhotoInput');
  
  if (event.target.files && event.target.files[0]) {
    output.src = URL.createObjectURL(event.target.files[0]);
    document.getElementById('removePhotoFlag').value = 0;
    
    // Copy the file from sidebar input to form input using DataTransfer
    if (event.target.id === 'profilePhotoInputSidebar' && formInput) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(event.target.files[0]);
      formInput.files = dataTransfer.files;
    }
  }
}

function removePhoto() {
  const output = document.getElementById('photoPreview');
  output.src = "{{ asset('assets/images/default-profile.jpg') }}";
  document.getElementById('removePhotoFlag').value = 1;
  const sidebarInput = document.getElementById('profilePhotoInputSidebar');
  const formInput = document.getElementById('profilePhotoInput');
  if (sidebarInput) sidebarInput.value = "";
  if (formInput) formInput.value = "";
}
</script>

<style>
:root { --surface: #ffffff; --border: rgba(0,0,0,.08); --muted: #64748b; --accent: #0d6efd; }

.settings-header { padding: .25rem 0 1rem; border-bottom: 1px solid var(--border); }
.avatar-md { width: 72px; height: 72px; object-fit: cover; }
.card { border-radius: 14px; border: 1px solid var(--border); background: var(--surface); }
.card-header { border-bottom: 1px solid var(--border); }
.panel { box-shadow: 0 6px 24px rgba(2,6,23,.06); }
.form-control-modern { border-radius: .6rem; }
.form-control-modern:focus { box-shadow: 0 0 0 .2rem rgba(13,110,253,.12); border-color: #86b7fe; }

.settings-nav .nav-link { color: #0f172a; border-radius: 10px; padding: .5rem .75rem; transition: background .15s ease, color .15s ease, border-color .15s ease; }
.settings-nav .nav-link:hover { background: #f0f6ff; color: #0b5ed7; }
.settings-nav .nav-link.active { background: #e7f1ff; color: #0d6efd; border: 1px solid rgba(13,110,253,.25); }
.btn-icon { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; }
</style>
@endsection
