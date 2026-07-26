3D RANGKA PACKAGING KAYU – THREE.JS
===================================

CARA MEMBUKA
1. Ekstrak file ZIP.
2. Klik dua kali file "index.html".
3. File utama index.html dapat dibuka tanpa internet dan rangka langsung tampil.

FITUR
- Input Panjang, Lebar, Tinggi dalam mm.
- Ukuran balok rangka dan balok penyangga.
- Jarak maksimum untuk perhitungan otomatis.
- Sisi: Depan, Belakang, Kanan, Kiri, Atas, Bawah.
- Setiap sisi dapat diaktifkan/nonaktifkan.
- Orientasi penyangga Horizontal atau Vertikal.
- Jumlah otomatis atau manual 1, 2, atau 3 penyangga.
- Tampilan Isometrik, Depan, Kanan, dan Atas.
- Dimensi, grid, ringkasan jumlah balok, total panjang kayu, dan simpan PNG.

CATATAN ORIENTASI
- Depan/Belakang: Horizontal = arah panjang; Vertikal = arah tinggi.
- Kanan/Kiri: Horizontal = arah lebar; Vertikal = arah tinggi.
- Atas/Bawah: Horizontal = arah panjang; Vertikal = arah lebar.

FILE
- index.html : halaman utama.
- app.js     : logika Three.js.
- BUKA_APLIKASI.bat : pembuka cepat untuk Windows.

PERBAIKAN V2
- Menambahkan renderer offline agar layar tidak kosong saat CDN diblokir atau internet tidak tersedia.
- Model dapat diputar dengan mouse dan diperbesar dengan scroll.

PILIHAN FILE
- index.html: versi utama offline dan paling stabil.
- index_threejs_online.html: versi Three.js resmi melalui CDN, membutuhkan internet/CDN yang tidak diblokir.
