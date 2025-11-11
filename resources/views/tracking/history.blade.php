@extends('layout.main')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tracking History</h5>
                <form id="filterForm" class="d-flex align-items-center mb-0" style="gap: 0.25rem;">
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control form-control-sm" style="height: 36px;" required>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-control form-control-sm" style="height: 36px;" required>
                    <button type="submit" class="btn btn-sm btn-light" style="height: 36px;">Show</button>
                </form>
            </div>

            <div class="card-body">
                <div id="map" style="height: 600px; width: 100%; border-radius: 10px;"></div>

                <!-- Tombol kontrol animasi -->
                <div class="mt-3 text-center">
                    <button id="btnPlay" class="btn btn-success btn-sm me-2">▶ Play</button>
                    <button id="btnPause" class="btn btn-warning btn-sm me-2">⏸ Pause</button>
                    <button id="btnReset" class="btn btn-secondary btn-sm">⏮ Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([-6.1754, 106.8272], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let markerStart = null;
    let markerEnd = null;
    let polyline = null;
    let coords = [];
    let points = [];
    let index = 0;
    let interval = null;

    const form = document.getElementById('filterForm');
    const btnPlay = document.getElementById('btnPlay');
    const btnPause = document.getElementById('btnPause');
    const btnReset = document.getElementById('btnReset');

    // Icon custom
    const startIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/128/10109/10109952.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -30]
    });

    const endIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/128/7826/7826834.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -30]
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const start_time = document.getElementById('start_time').value;
        const end_time = document.getElementById('end_time').value;

        if (!start_time || !end_time) {
            alert('Pilih waktu mulai dan waktu akhir');
            return;
        }

        try {
            const res = await fetch(`{{ route('tracking.history.data') }}?start_time=${start_time}&end_time=${end_time}`);
            const data = await res.json();

            if (!data.Items || !data.Items.length) {
                alert('Tidak ada data untuk rentang waktu tersebut');
                return;
            }

            // Reset map & variabel
            [polyline, marker, markerStart, markerEnd].forEach(m => { if (m) map.removeLayer(m); });
            clearInterval(interval);

            points = data.Items;
            coords = points.map(i => [i.Lat, i.Lng]);
            index = 0;

            // Gambar jalur
            polyline = L.polyline(coords, { color: 'blue', weight: 4 }).addTo(map);
            map.fitBounds(polyline.getBounds());

            // Marker awal & akhir
            const first = points[0];
            const last = points[points.length - 1];

            markerStart = L.marker([first.Lat, first.Lng], { icon: startIcon })
                .addTo(map)
                .bindPopup(`<b>Start Point</b><br>${first.Time}`)
                .openPopup();

            markerEnd = L.marker([last.Lat, last.Lng], { icon: endIcon })
                .addTo(map)
                .bindPopup(`<b>End Point</b><br>${last.Time}`);

            // Marker utama (bergerak)
            marker = L.marker([first.Lat, first.Lng]).addTo(map)
                .bindPopup(getPopupHtml(first))
                .openPopup();

        } catch (err) {
            console.error(err);
            alert('Gagal memuat data');
        }
    });

    function getPopupHtml(p) {
        let waktu = '-';
        if (p.Time) {
            // Parse string tanpa 'Z' agar dianggap lokal UTC (bukan otomatis browser)
            let dt = new Date(p.Time.replace(' ', 'T')); // "2025-11-09 22:44:00" -> "2025-11-09T22:44:00"

            // Tambahkan 7 jam untuk timezone fisik
            dt.setHours(dt.getHours() + 7);

            // Format ke YYYY-MM-DD HH:MM:SS
            waktu = dt.getFullYear() + '-' +
                    String(dt.getMonth() + 1).padStart(2, '0') + '-' +
                    String(dt.getDate()).padStart(2, '0') + ' ' +
                    String(dt.getHours()).padStart(2, '0') + ':' +
                    String(dt.getMinutes()).padStart(2, '0') + ':' +
                    String(dt.getSeconds()).padStart(2, '0');
        }

        return `
            <div style="font-size: 13px; line-height: 1.4;">
                <b>📍 Location ID:</b> ${p.LocationId ?? '-'}<br>
                <b>🕒 Waktu:</b> ${waktu} <br>
                <b>🚚 Kecepatan:</b> ${p.Speed ?? '-'} km/h<br>
                <b>🧭 Arah:</b> ${p.Course ?? '-'}°<br>
                <b>Lat:</b> ${p.Lat}<br>
                <b>Lng:</b> ${p.Lng}
            </div>
        `;
    }

    function play() {
        if (!coords.length) {
            alert('Belum ada data untuk diputar');
            return;
        }
        if (interval) return;

        interval = setInterval(() => {
            index++;
            if (index >= coords.length) {
                clearInterval(interval);
                interval = null;
                marker.bindPopup(`<b>End of route</b>`).openPopup();
                return;
            }

            const currentPoint = points[index];
            marker.setLatLng(coords[index]);
            marker.setPopupContent(getPopupHtml(currentPoint));
            map.panTo(coords[index], { animate: true });
        }, 1000);
    }

    function pause() {
        clearInterval(interval);
        interval = null;
    }

    function reset() {
        pause();
        if (!coords.length) return;
        index = 0;
        const first = points[0];
        marker.setLatLng(coords[0]);
        marker.setPopupContent(getPopupHtml(first));
        map.panTo(coords[0]);
    }

    btnPlay.addEventListener('click', play);
    btnPause.addEventListener('click', pause);
    btnReset.addEventListener('click', reset);
});
</script>
@endpush
