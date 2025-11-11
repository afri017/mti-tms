@extends('layout.main')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Live Vehicle Tracking</h5>
            </div>
            <div class="card-body">
                <div id="map" style="height: 600px; width: 100%; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([-6.1754, 106.8272], 12); // Default Jakarta

    // Layer dasar
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    async function loadTracking() {
        try {
            const res = await fetch('{{ route("tracking.latest") }}');
            const data = await res.json();

            if (!data.Item) {
                console.warn('Tidak ada data koordinat');
                return;
            }

            const point = data.Item;

            // Hapus marker lama
            if (marker) map.removeLayer(marker);

            // Tambahkan marker baru
            marker = L.marker([point.Latitude, point.Longitude]).addTo(map);
            marker.bindPopup(`
                <b>${point.DeviceName}</b><br>
                Kecepatan: ${point.Speed} km/h<br>
                Arah: ${point.Course}°<br>
                Waktu: ${point.DeviceUtcDate}
            `).openPopup();

            // Geser map ke lokasi terbaru
            map.setView([point.Latitude, point.Longitude], 15);
        } catch (err) {
            console.error('Gagal memuat tracking:', err);
        }
    }

    // Muat pertama kali
    loadTracking();

    // Refresh setiap 10 detik
    setInterval(loadTracking, 10000);
});
</script>
@endpush
