<x-app-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <div class="row g-4">
        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2 opacity-75">Total Penjualan</h6>
                        <h3 class="mb-0 fw-bold">Rp 45.5M</h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2 opacity-75">Project Aktif</h6>
                        <h3 class="mb-0 fw-bold">124</h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2 opacity-75">Menunggu Tagihan</h6>
                        <h3 class="mb-0 fw-bold">42</h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2 opacity-75">Stok Menipis</h6>
                        <h3 class="mb-0 fw-bold">18</h3>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100" style="background: var(--topbar-bg); color: var(--bs-body-color);">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-semibold mb-0">Selamat Datang, {{ Auth::user()->name }}!</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Anda berhasil masuk ke sistem Dashboard AQPA Indonesia. Pilih modul di menu samping untuk mengelola data perusahaan.</p>
                    
                    <div class="alert alert-primary border-0 bg-primary bg-opacity-10 text-primary d-flex align-items-center" role="alert">
                        <i class="fa-solid fa-circle-info me-3 fs-4"></i>
                        <div>
                            <strong>Pembaruan Sistem v1.0.0</strong><br>
                            Sistem baru saja diperbarui dengan tampilan Bootstrap Modern yang mendukung Dark Mode. Coba klik ikon bulan/matahari di pojok kanan atas!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="background: var(--topbar-bg); color: var(--bs-body-color);">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-semibold mb-0">Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm fw-semibold">Login Berhasil</h6>
                                    <small class="text-muted">Baru saja</small>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                    <i class="fa-solid fa-user-pen"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm fw-semibold">Profil Diperbarui</h6>
                                    <small class="text-muted">1 jam yang lalu</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
