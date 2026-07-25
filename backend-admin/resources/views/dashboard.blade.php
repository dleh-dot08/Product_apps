<x-app-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <!-- Custom CSS untuk Animasi Card di Dashboard -->
    <style>
        .premium-card {
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.2);
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
        }
        .premium-icon-box {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
        }
        .data-card {
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }
    </style>

    <div class="row g-4">
        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; opacity: 0.8;">Total Penjualan</h6>
                        <h3 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">Rp 45.5M</h3>
                    </div>
                    <div class="premium-icon-box fs-4">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; opacity: 0.8;">Project Aktif</h6>
                        <h3 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">124</h3>
                    </div>
                    <div class="premium-icon-box fs-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; opacity: 0.8;">Menunggu Tagihan</h6>
                        <h3 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">42</h3>
                    </div>
                    <div class="premium-icon-box fs-4">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; opacity: 0.8;">Stok Menipis</h6>
                        <h3 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">18</h3>
                    </div>
                    <div class="premium-icon-box fs-4">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 data-card h-100" style="background: var(--topbar-bg); color: var(--bs-body-color);">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Selamat Datang, {{ Auth::user()->name }}! ✨</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4" style="line-height: 1.6;">Anda berhasil masuk ke sistem Dashboard terpadu <strong>AQPA Indonesia</strong>. Gunakan modul-modul di sebelah kiri untuk mengelola berbagai data seperti inventaris, proyek, akuntansi, dan penjualan perusahaan Anda.</p>
                    
                    <div class="alert border-0 d-flex align-items-center p-4 rounded-4 shadow-sm" style="background: rgba(37, 99, 235, 0.08); color: #2563eb;" role="alert">
                        <div class="fs-2 me-4">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1 fs-5">Pembaruan Sistem v1.1.0</strong>
                            Sistem baru saja diperbarui dengan desain **Premium Glassmorphism** yang sepenuhnya mendukung Mode Gelap (Dark Mode). Klik ikon bulan <i class="fa-solid fa-moon"></i> di kanan atas untuk mencobanya!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-xl-4">
            <div class="card border-0 data-card h-100" style="background: var(--topbar-bg); color: var(--bs-body-color);">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0">
                    <h6 class="fw-bold text-uppercase mb-0" style="letter-spacing: 0.5px; color: var(--sidebar-link);">Aktivitas Terakhir</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 position-relative">
                        <!-- Connecting line -->
                        <div class="position-absolute" style="left: 19px; top: 24px; bottom: 24px; border-left: 2px dashed var(--sidebar-border); z-index: 0;"></div>
                        
                        <li class="mb-4 position-relative z-1">
                            <div class="d-flex">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 4px solid var(--topbar-bg);">
                                    <i class="fa-solid fa-arrow-right-to-bracket fs-6"></i>
                                </div>
                                <div class="pt-1">
                                    <h6 class="mb-1 fw-bold">Login Berhasil</h6>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> Baru saja</small>
                                </div>
                            </div>
                        </li>
                        <li class="mb-0 position-relative z-1">
                            <div class="d-flex">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(37, 99, 235, 0.15); color: #2563eb; border: 4px solid var(--topbar-bg);">
                                    <i class="fa-solid fa-user-pen fs-6"></i>
                                </div>
                                <div class="pt-1">
                                    <h6 class="mb-1 fw-bold">Pembaruan UI Premium</h6>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> 1 jam yang lalu</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
