@extends($layout === 'admin' ? 'layouts.admin' : 'layouts.section_manager')

@section('title', 'Vehicles List')

@section('content')
<h3 class="mb-3">Vehicles</h3>

{{-- Search Vehicle --}}
<form action="{{ route('section_manager.vehicles.index') }}" method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by Plate Number...">
    <button class="btn btn-primary">Search</button>
</form>

{{-- Add Vehicle --}}
<a href="{{ route('section_manager.vehicles.create') }}" class="btn btn-success mb-3">Add Vehicle</a>

<table class="table table-bordered text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th style="width: 10%;">No</th>
            <th style="width: 25%;">Driver ID</th>
            <th style="width: 25%;">Plate Number</th>
            <th style="width: 15%;">Branch</th>
            <th style="width: 15%;">Vehicle Type</th>
            <th style="width: 15%;">Brand</th>
            <th style="width: 15%;">User Section</th>
            <th style="width: 15%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vehicles as $vehicle)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $vehicle->driver_id_number }}</td>
            <td>{{ $vehicle->plate_no }}</td>
            <td>{{ $vehicle->branch }}</td>
            <td>{{ $vehicle->vehicle_type }}</td>
            <td>{{ $vehicle->brand }}</td>
            <td>{{ $vehicle->user_section }}</td>
            <td>
                <a href="{{ route('section_manager.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('section_manager.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this vehicle?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">No vehicles found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
