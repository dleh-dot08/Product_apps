<x-app-layout>
    <!-- Tambahkan CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        .map-container {
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.1);
            height: 70vh;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Animasi fade in & slide up untuk Leaflet Popup */
        .leaflet-popup {
            animation: slideUpFadeIn 0.4s ease-out forwards;
        }
        @keyframes slideUpFadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        /* Modifikasi Popup Leaflet agar lebih cantik */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 0;
            overflow: hidden;
        }
        .leaflet-popup-content {
            margin: 0;
            line-height: 1.5;
        }

        /* Hero Panel CSS disamakan dengan Tugas Driver */
        .hero-panel {
            min-height: 140px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            border-radius: 24px;
            background:
                radial-gradient(circle at 96% 10%, rgba(249,115,22,.13) 0 74px, transparent 75px),
                linear-gradient(115deg, #ffffff 0%, #fffdfa 60%, #fff1e6 100%);
            position: relative;
            z-index: 10;
        }
        html[data-bs-theme="dark"] .hero-panel {
            background: radial-gradient(circle at 96% 10%, rgba(249, 115, 22, .14) 0 74px, transparent 75px), #0d0f12;
        }
        .hero-kicker { 
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(249, 115, 22, .10);
            color: #c2410c; /* orange-700 */
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
            margin-bottom: 13px;
        }
        html[data-bs-theme="dark"] .hero-kicker { color: #fdba74; }
        .hero-title { font-size: clamp(27px, 2.3vw, 36px); margin-bottom: 8px; font-weight: 800; color: #172033; }
        html[data-bs-theme="dark"] .hero-title { color: #f8fafc; }
        .hero-subtitle { font-size: 13px; color: #64748b; margin:0; }
        html[data-bs-theme="dark"] .hero-subtitle { color: #94a3b8; }
        .hero-copy { max-width: 700px; position:relative; z-index:3; }
        
        .hero-visual {
            position:absolute;
            right: 180px;
            bottom: 0;
            width: 290px;
            height: 132px;
            pointer-events:none;
            opacity:.92;
            z-index:1;
        }
        .hero-city { position:absolute; right:0; bottom:0; width:210px; height:95px; opacity:.18; }
        .hero-city span { position:absolute; bottom:0; background:#94a3b8; border-radius:3px 3px 0 0; }
        .hero-city span:nth-child(1){left:4px;width:26px;height:48px}.hero-city span:nth-child(2){left:38px;width:35px;height:72px}
        .hero-city span:nth-child(3){left:82px;width:24px;height:58px}.hero-city span:nth-child(4){left:114px;width:42px;height:88px}
        .hero-city span:nth-child(5){left:164px;width:32px;height:64px}
        .hero-truck {
            position:absolute; left:2px; bottom:12px; width:154px; height:72px;
            color:rgba(249,115,22,.88); font-size:74px; display:flex; align-items:center;
            filter:drop-shadow(0 8px 12px rgba(234,88,12,.10));
        }
        .hero-boxes { position:absolute; right:4px; bottom:6px; display:flex; align-items:flex-end; gap:4px; opacity:.42; }
        .hero-boxes span { width:35px; height:26px; background:#fdba74; border:1px solid #fb923c; border-radius:3px; }
        .hero-boxes span:nth-child(2){height:38px}.hero-boxes span:nth-child(3){height:22px}
        
        @media (max-width: 991.98px) { .hero-visual { display:none; } .hero-panel { min-height:auto; } }
        @media (max-width: 767.98px) { .hero-panel{padding:20px; border-radius:18px;} }
    </style>

    <div class="hero-panel mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative w-100" style="z-index:2;">
            <div class="hero-copy">
                <span class="hero-kicker"><i class="fa-solid fa-satellite-dish me-2 animate-pulse"></i> Tracker Aktif</span>
                <h1 class="hero-title"><i class="fa-solid fa-map-location-dot text-orange me-2" style="color: #ea580c;"></i>Live Map: Find Driver</h1>
                <p class="hero-subtitle">Pantau lokasi real-time driver yang sedang aktif bertugas di lapangan.</p>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-city"><span></span><span></span><span></span><span></span><span></span></div>
                <div class="hero-truck"><i class="fa-solid fa-map-pin"></i></div>
                <div class="hero-boxes"><span></span><span></span><span></span></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 map-container">
                <div id="driverMap" style="width: 100%; height: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Tambahkan JS Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Peta (Default ke Jakarta)
            var map = L.map('driverMap').setView([-6.200000, 106.816666], 11);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var markers = {}; // Menyimpan referensi marker per user_id

            // Custom Icon untuk mobil/truk
            var driverIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/3097/3097180.png',
                iconSize: [38, 38], // size of the icon
                iconAnchor: [19, 38], // point of the icon which will correspond to marker's location
                popupAnchor: [0, -38] // point from which the popup should open relative to the iconAnchor
            });

            function fetchDriverLocations() {
                fetch('/api/driver/locations', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.data) {
                        data.data.forEach(location => {
                            let lat = parseFloat(location.latitude);
                            let lng = parseFloat(location.longitude);
                            
                            let driverName = location.user.full_name || location.user.username || 'Driver Tidak Diketahui';
                            let popupHtml = `
                                <div style="min-width: 220px;">
                                    <div class="bg-primary text-white p-3 border-bottom d-flex align-items-center">
                                        <i class="fa-solid fa-user-circle fa-2x me-2 opacity-75"></i>
                                        <h6 class="mb-0 fw-bold" style="font-size: 1.1rem; color: #fff;">${driverName}</h6>
                                    </div>
                                    <div class="p-3 bg-white text-dark">
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="fa-solid fa-clipboard-list text-secondary mt-1 me-2" style="width: 16px;"></i>
                                            <div>
                                                <span class="d-block text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Tugas Aktif</span>
                                                <span class="fw-medium">${location.task_id || 'Tidak ada ID Tugas'}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i class="fa-solid fa-location-crosshairs text-secondary mt-1 me-2" style="width: 16px;"></i>
                                            <div>
                                                <span class="d-block text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Koordinat</span>
                                                <span class="fw-medium" style="font-size: 0.85rem;">${lat.toFixed(5)}, ${lng.toFixed(5)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            if (markers[location.user_id]) {
                                // Update posisi marker yang sudah ada
                                markers[location.user_id].setLatLng([lat, lng]);
                                // Update isi popup barangkali tugas berubah
                                markers[location.user_id].getPopup().setContent(popupHtml);
                            } else {
                                // Buat marker baru
                                let marker = L.marker([lat, lng], {icon: driverIcon}).addTo(map)
                                    .bindPopup(popupHtml);
                                markers[location.user_id] = marker;
                            }
                        });
                        
                        // Jika baru pertama load dan ada data, sesuaikan view ke semua marker
                        if(Object.keys(markers).length > 0 && !window.mapCentered) {
                            var group = new L.featureGroup(Object.values(markers));
                            map.fitBounds(group.getBounds().pad(0.1));
                            window.mapCentered = true;
                        }
                    }
                })
                .catch(error => console.error('Error fetching driver locations:', error));
            }

            // Ambil data pertama kali
            fetchDriverLocations();

            // Refresh data setiap 10 detik
            setInterval(fetchDriverLocations, 10000);
        });
    </script>
</x-app-layout>
