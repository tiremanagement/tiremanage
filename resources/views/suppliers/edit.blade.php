@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Supplier</h1>
    <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $supplier->contact) }}" placeholder="e.g. 0711234567 or +94711234567" required>
            @error('contact')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}" placeholder="e.g. supplier@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control">{{ $supplier->address }}</textarea>
        </div>
        <div class="mb-3">
            <label>Town</label>
            <input type="text" name="town" class="form-control" value="{{ old('town', $supplier->town) }}" maxlength="100">
        </div>
        <button type="submit" class="btn btn-success">Update Supplier</button>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
