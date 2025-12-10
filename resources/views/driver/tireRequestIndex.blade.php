@extends('layouts.driver')

@section('title', 'My Tyre Requests')

@section('content')
<style>
body { background: #f3f4f6; }
.tire-requests-container { max-width: 1200px; margin: 0 auto; padding: 2rem 0; }
.tire-table-wrap { overflow-x: auto; border-radius: 14px; box-shadow: 0 8px 32px rgba(11,79,180,0.08); background: #fff; }
.tire-table { width: 100%; border-collapse: collapse; font-size: 0.97rem; }
.tire-table th, .tire-table td { padding: 10px 8px; text-align: center; }
.tire-table th { background: #0b4fb4; color: #fff; font-weight: 600; letter-spacing: .04em; }
.tire-table tr { border-bottom: 1px solid #e5e7eb; }
.tire-table tr:last-child { border-bottom: none; }
.tire-table tr:hover { background: #f1f5f9; }
.img-thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(11,79,180,0.08); cursor: pointer; transition: transform 0.18s; border: 2px solid #e5e7eb; }
.img-thumbnail:hover { transform: scale(1.18); border-color: #0b4fb4; }
.badge { padding: 5px 12px; border-radius: 12px; font-size: 0.92rem; font-weight: 600; display: inline-block; }
.bg-warning { background: #facc15; color: #000; }
.bg-success { background: #16a34a; color: #fff; }
.bg-danger  { background: #dc2626; color: #fff; }
.bg-secondary { background: #6b7280; color: #fff; }
.btn-danger { background: #dc3545; color: #fff; border: none; padding: 6px 14px; border-radius: 10px; cursor: pointer; font-weight: 600; }
.btn-danger:hover { background: #b91c1c; }
.alert-info { background: #e6f0ff; padding: 18px; border-radius: 8px; text-align: center; font-weight: 600; color: #0b4fb4; margin-top: 2rem; }
@media (max-width: 900px) {
  .tire-table th, .tire-table td { font-size: 0.93rem; padding: 7px 4px; }
}
@media (max-width: 700px) {
  .tire-table th, .tire-table td { font-size: 0.89rem; padding: 6px 2px; }
  .tire-table-wrap { border-radius: 0; box-shadow: none; }
}
#imgModal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; }
#imgModal.active { display: flex; }
#imgModal img { max-width: 90vw; max-height: 80vh; border-radius: 12px; box-shadow: 0 8px 32px rgba(11,79,180,0.18); background: #fff; }
#imgModal .close-btn { position: absolute; top: 32px; right: 48px; font-size: 2.2rem; color: #fff; background: none; border: none; cursor: pointer; z-index: 10001; }
@media (max-width: 600px) { #imgModal .close-btn { right: 16px; top: 16px; } }
</style>

<div class="tire-requests-container">
    <h2 style="color:#0b4fb4; font-weight:800; text-align:center; margin-bottom:1.8rem; letter-spacing:.04em;">My Tyre Requests</h2>

    @forelse ($requests as $index => $request)
        @if ($loop->first)
        <div class="tire-table-wrap">
        <table class="tire-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Branch</th>
                    <th>Vehicle & Brand</th>
                    <th>User Section</th>
                    <th>Tyre Size</th>
                    <th>Delivery Address</th>
                    <th>Last Replacement</th>
                    <th>Existing Tyre Make</th>
                    <th>Damage Description</th>
                    <th>Tyre Count</th>
                    <th>Images</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        @endif
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ method_exists($request, 'branchName') ? ($request->branchName() ?? 'N/A') : ($request->branch_name ?? 'N/A') }}</td>
                    <td>
                        <div style="font-weight:600; color:#0b4fb4;">{{ $request->vehicle?->vehicle_type ?? 'N/A' }}</div>
                        <div style="color:#64748b; font-size:0.97em;">{{ $request->vehicle?->brand ?? 'N/A' }}</div>
                        <div style="color:#6b7280; font-size:0.93em;">{{ $request->vehicle?->plate_no ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $request->vehicle?->user_section ?? 'N/A' }}</td>
                    <td>{{ $request->tire?->size ?? 'N/A' }}</td>
                    <td>
                        <div>{{ $request->delivery_place_office ?? '-' }}</div>
                        <div>{{ $request->delivery_place_street ?? '-' }}</div>
                        <div>{{ $request->delivery_place_town ?? '-' }}</div>
                    </td>
                    <td>{{ optional($request->last_tire_replacement_date)->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $request->existing_tire_make ?? '-' }}</td>
                    <td>{{ $request->damage_description ?? '-' }}</td>
                    <td>{{ $request->tire_count ?? 1 }}</td>
                    <td>
                        @php
                            $images = [];
                            if (is_array($request->tire_images) && !empty($request->tire_images)) {
                                $images = $request->tire_images;
                            } elseif (!empty($request->images)) {
                                $decoded = json_decode($request->images, true);
                                if (is_array($decoded)) {
                                    $images = $decoded;
                                }
                            }
                        @endphp
                        @if (!empty($images))
                            @foreach ($images as $image)
                                @php
                                    $cleanImage = ltrim($image, '/');
                                    $imagePath = 'storage/' . $cleanImage;
                                @endphp
                                <img src="{{ asset($imagePath) }}" class="img-thumbnail previewable-img" alt="tyre image" data-img="{{ asset($imagePath) }}" onclick="showImgModal(this)" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22%3E%3Crect fill=%22%23e5e7eb%22 width=%2260%22 height=%2260%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 fill=%22%236b7280%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                            @endforeach
                        @else
                            <span style="color:#6b7280;">No Images</span>
                        @endif
                    </td>
                    <td>
                        @switch($request->status)
                            @case('pending')
                                <span class="badge bg-warning">Pending</span>
                                @break
                            @case('approved')
                                <span class="badge bg-success">Approved</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ ucfirst($request->status ?? 'unknown') }}</span>
                        @endswitch
                    </td>
                    <td>{{ optional($request->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>
                        @if(($request->status ?? 'pending') === 'pending')
                            <form action="{{ route('driver.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirmDelete(this);">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                </tr>
        @if ($loop->last)
            </tbody>
        </table>
        </div>
        @endif
    @empty
        <div class="alert-info">You have not submitted any tyre requests yet.</div>
    @endforelse
</div>

<!-- Modal for image preview -->
<div id="imgModal">
    <button class="close-btn" onclick="closeImgModal()">&times;</button>
    <img id="imgModalImg" src="" alt="Preview">
</div>

<script>
function confirmDelete(form) {
    return confirm("Are you sure you want to delete this request?");
}
// Modal image preview logic
function showImgModal(img) {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    modal.classList.add('active');
    modalImg.src = img.getAttribute('data-img');
}
function closeImgModal() {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    modal.classList.remove('active');
    modalImg.src = '';
}
document.getElementById('imgModal').addEventListener('click', function(e) {
    if (e.target === this) closeImgModal();
});
</script>
@endsection
