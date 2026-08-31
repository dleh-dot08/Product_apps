# Proses Bisnis Logistik Terintegrasi (Web Admin & Aplikasi Mobile Driver)

Dokumen ini menjelaskan alur operasional logistik secara end-to-end yang menjembatani Web Admin (Backend) dengan Aplikasi Mobile Driver (Delicost). Secara garis besar, sistem operasional armada dibagi menjadi tiga pilar: **Tugas Driver (Pickup)**, **Pengiriman Barang (Delivery Order)**, dan **Kalkulasi Biaya & Shift (HPP Ritase)**.

---

## 1. Pilar Pertama: TUGAS DRIVER (Pickup Tasks)
Fitur ini digunakan secara khusus untuk alur Inbound atau penjemputan barang.

### 1.1. Kasus Penggunaan (Use Cases)
- Mengambil bahan baku (Raw Material) dari Supplier ke Gudang Utama.
- Mengambil barang *return* atau *reject* dari fasilitas pelanggan.
- Pemindahan barang mentah antar pabrik cabang.

### 1.2. Alur Sistem (System Flow)
1. **Pembuatan Tugas**: Admin Logistik membuat tugas baru di menu **Tugas Driver** pada Web Admin.
   - Field krusial: `Pickup Name` (Nama tempat ambil), `Pickup Location` (Alamat asal), `Destination` (Tujuan akhir, misal: Gudang Pusat), dan `Item Description` (Rincian barang yang diambil).
2. **Sinkronisasi Mobile**: Sistem akan memasukkan tugas tersebut ke dalam *database* (`pickup_tasks`) dan langsung muncul di notifikasi aplikasi mobile (Delicost) milik *Driver* yang ditugaskan.
3. **Eksekusi oleh Driver**:
   - Driver tiba di *Pickup Location*.
   - Driver memuat barang, dan mengambil foto bukti muatan (`proof_photo`) lewat aplikasi.
   - Driver menekan tombol "Selesai", lalu status di Web Admin berubah dari `Assigned` -> `On Route` -> `Delivered`.

---

## 2. Pilar Kedua: PENGIRIMAN BARANG (Delivery Orders)
Fitur ini digunakan untuk alur Outbound atau distribusi barang jadi (Finished Goods) ke pelanggan.

### 2.1. Kasus Penggunaan (Use Cases)
- Mengirim barang pesanan kepada Customer (PT A, CV B) sesuai dengan Sales Order / Purchase Order yang masuk.

### 2.2. Alur Sistem (System Flow)
1. **Pembuatan DO**: Admin membuat Delivery Order (DO) berisikan rincian pesanan. DO ini pada awalnya berstatus `Pending` karena belum ada armada yang disiapkan.
   - Field krusial: `so_number`, `customer_name`, `destination` (Alamat Customer), dan rincian `DeliveryItems` (Jenis barang, Kuantitas, Nilai/Harga Barang).
2. **Assignment (Penugasan)**: Saat truk siap memuat barang di *loading dock*, Admin melakukan aksi "Assign DO ke Driver". 
3. **Sinkronisasi Mobile**: Data masuk ke *database* (`delivery_assignments`) dan Driver menerima notifikasi pengiriman pesanan di aplikasi mobile mereka.
4. **Eksekusi oleh Driver**:
   - Driver meluncur ke *Destination*.
   - Sesampainya di pelanggan, pelanggan tanda tangan Surat Jalan / Driver memfoto bukti serah terima (`proof_photo`).
   - Status DO di Web Admin ter-update menjadi `Delivered`.

---

## 3. Pilar Ketiga: KALKULASI BIAYA & HPP RITASE (Shift Management)
Pilar inilah yang memadukan aktivitas Pickup (Pilar 1) dan Delivery (Pilar 2) menjadi satu laporan keuangan dan performa yang terukur.

### 3.1. Kasus Penggunaan (Use Cases)
- Menghitung profitabilitas riil dari setiap ritase pengiriman.
- Membagi (Prorata) beban operasional armada ke dalam Harga Pokok Penjualan (HPP) setiap unit/kardus barang yang diangkut.
- Merekam log konsumsi bensin, e-Toll, dan uang makan supir per hari kerja.

### 3.2. Alur Sistem (System Flow)
Sistem ini menggunakan pendekatan berbasis **Shift Harian (Ritase Perjalanan)**, bukan per-dokumen DO.

1. **Mulai Shift (Check In)**:
   - Pagi hari, Driver membuka aplikasi mobile, memilih Truk yang dipakai, lalu melakukan "Mulai Shift".
   - Driver **wajib** mencatat angka Odometer awal truk. Sistem mulai merekam waktu `started_at`.
2. **Melaksanakan Tugas (Pickup & Delivery)**:
   - Sepanjang hari tersebut (1 Shift), Driver mungkin melakukan 1 kali tugas *Pickup* dan 2 kali tugas *Delivery Order*. Ketiga tugas ini terikat pada 1 ID Shift yang sama di sistem.
3. **Selesai Shift (Check Out)**:
   - Sore/Malam harinya ketika truk kembali ke pangkalan, Driver menekan tombol "Akhiri Shift".
   - Driver **wajib** memasukkan Odometer akhir truk, serta melaporkan/mengunggah bon e-Toll, parkir, retribusi di aplikasi.
4. **Kalkulasi Otomatis (HPP Ritase Dashboard)**:
   - Karena Odometer Awal dan Akhir diketahui, sistem langsung menghitung Total Jarak Tempuh (KM).
   - Berdasarkan master data Truk (`km_per_liter` & `fuel_price`), sistem otomatis menghitung **Biaya BBM**.
   - Berdasarkan master data Driver (`manpower_rate`), sistem otomatis menghitung **Upah/Manpower Driver**.
   - Sistem lalu menjumlahkan: *Biaya BBM + Uang Makan + eToll + Parkir*. 
   - Nilai total biaya perjalanan ini akan dibagi rata secara prorata kepada seluruh barang yang diangkut hari itu (berdasarkan nilai barang/volume), sehingga menghasilkan **HPP Ritase**.
