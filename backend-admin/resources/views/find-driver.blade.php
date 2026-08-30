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
    </style>

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold mb-0">Live Map: Find Driver</h2>
            <p class="text-muted">Memantau lokasi driver yang sedang menjalankan tugas (aktif dalam 1 jam terakhir).</p>
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
                            
                            if (markers[location.user_id]) {
                                // Update posisi marker yang sudah ada
                                markers[location.user_id].setLatLng([lat, lng]);
                            } else {
                                // Buat marker baru
                                let marker = L.marker([lat, lng], {icon: driverIcon}).addTo(map)
                                    .bindPopup(`<b>${location.user.name}</b><br>Tugas ID: ${location.task_id}`);
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
