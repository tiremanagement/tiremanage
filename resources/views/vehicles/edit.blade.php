@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Vehicle</h1>

    <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Plate number</label>
            <input type="text" name="plate_no" class="form-control" value="{{ $vehicle->plate_no }}" required>
        </div>
        <div class="mb-3">
            <label>Branch</label>
            <input type="text" name="branch" class="form-control" value="{{ old('branch', $vehicle->branch) }}" required pattern="^[^0-9]*$" title="Branch must not contain numbers">
        </div>
        <div class="mb-3">
            <label>Vehicle Type</label>
            <input type="text" name="vehicle_type" class="form-control" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" placeholder="Car, Van, Truck" pattern="^[^0-9]*$" title="Vehicle type must not contain numbers">
        </div>
        <div class="mb-3">
            <label>Brand</label>
            <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand) }}" placeholder="e.g. Honda Civic, Toyota Corolla, KDH Van" pattern="^[^0-9]*$" title="Brand must not contain numbers">
        </div>

        <div class="mb-3">
            <label>Driver ID number</label>
            <input type="text" name="driver_id_number" class="form-control" value="{{ old('driver_id_number', $vehicle->driver_id_number) }}" placeholder="Driver ID number (as registered)">
        </div>
        <div class="mb-3">
            <label>User Section</label>
            <input type="text" name="user_section" class="form-control" value="{{ old('user_section', $vehicle->user_section) }}" pattern="^[^0-9]*$" title="User section must not contain numbers">
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
