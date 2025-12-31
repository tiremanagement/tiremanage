@extends('layouts.admin')

@section('title', 'Manage Suppliers')

@section('content')
<div class="container mx-auto p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Suppliers Management</h2>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-elevated"><i class="bi bi-building-add"></i> Add Supplier</a>
    </div>

    {{-- Search Bar --}}
    <div class="mb-3">
        <input type="text" id="supplierSearch" class="form-control" placeholder="Search by name, contact, or email...">
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="suppliersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Town</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="supplier-name">{{ $supplier->name }}</td>
                        <td class="supplier-contact">{{ $supplier->contact }}</td>
                        <td class="supplier-email">
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">{{ $supplier->email }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="supplier-address">{{ $supplier->address }}</td>
                        <td class="supplier-town">{{ $supplier->town }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-outline-primary btn-icon btn-sm" data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this supplier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" data-bs-toggle="tooltip" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-title { font-size: 1.8rem; font-weight: 800; color: var(--text); }
    #supplierSearch { max-width: 320px; }
</style>
@endpush

@push('scripts')
<script>
    // Simple search filter for suppliers table
    document.getElementById('supplierSearch').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#suppliersTable tbody tr');
        rows.forEach(row => {
            const name = row.querySelector('.supplier-name')?.textContent.toLowerCase() ?? '';
            const contact = row.querySelector('.supplier-contact')?.textContent.toLowerCase() ?? '';
            const email = row.querySelector('.supplier-email')?.textContent.toLowerCase() ?? '';
            row.style.display = (name.includes(filter) || contact.includes(filter) || email.includes(filter)) ? '' : 'none';
        });
    });
    </script>
@endpush
