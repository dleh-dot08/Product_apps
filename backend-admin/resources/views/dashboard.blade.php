<x-app-layout>

    <!-- Custom CSS untuk Header Banner & Card Dashboard -->
    <style>
        /* Modern Header Banner Style (Inspired by Image) */
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1rem;
            border: 1px solid rgba(251, 146, 60, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }

        /* Light Mode Gradient untuk Header Banner */
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(255, 247, 237, 0.85) 60%, rgba(255, 237, 213, 0.85) 100%);
            box-shadow: 0 4px 20px -2px rgba(251, 146, 60, 0.15);
        }

        /* Dark Mode Styling untuk Header Banner (Hitam Obsidian) */
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(13, 15, 18, 0.75) 0%, rgba(23, 25, 30, 0.75) 100%);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* Subtitle Pill Badge */
        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        html:not([data-bs-theme="dark"]) .banner-badge {
            background-color: rgba(251, 146, 60, 0.15);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.2);
        }

        html[data-bs-theme="dark"] .banner-badge {
            background-color: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        /* Title inside Banner */
        .banner-title {
            font-size: clamp(1.35rem, 2.2vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
        }

        html:not([data-bs-theme="dark"]) .banner-title {
            color: #0f172a;
        }

        html[data-bs-theme="dark"] .banner-title {
            color: #f8fafc;
        }

        /* Custom CSS Card Stat Ringkas */
        .premium-card {
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.25);
        }
        .premium-icon-box {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
        }
        .data-card {
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        /* Data Card Dark Mode Override */
        html[data-bs-theme="dark"] .data-card {
            background-color: #0d0f12 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }

        html:not([data-bs-theme="dark"]) .data-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }
    </style>

    <!-- 1. Header Banner khusus Dashboard (Mengikuti Konsep Gambar) -->
    <div class="row mb-3" style="position: sticky; top: 1rem; z-index: 1020;">
        <div class="col-12">
            <div class="dashboard-banner">
                <!-- Decorative Background Element (Gradasi Oranye Khas AQPA) -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(251, 146, 60, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                <div style="position: absolute; bottom: -50px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="banner-badge">
                        <i class="fa-solid fa-chart-pie"></i>
                        AQPA DASHBOARD
                    </div>
                    <h1 class="banner-title mb-2">Dashboard Overview Summary</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan Kartu Statistik (Padding Dikecilkan) -->
    <div class="row g-3">
        <!-- Card 1 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Total Penjualan</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">Rp 45.5M</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Project Aktif</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">124</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Menunggu Tagihan</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">42</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 premium-card h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1" style="font-size: 0.725rem; letter-spacing: 0.5px; opacity: 0.85;">Stok Menipis</h6>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">18</h4>
                    </div>
                    <div class="premium-icon-box fs-5">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Area Konten Utama -->
    <div class="row mt-3 g-3">
        <div class="col-12 col-xl-8">
            <div class="card border-0 data-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-3 pb-0">
                    <h6 class="fw-bold mb-0">Selamat Datang, {{ Auth::user()->name }}! ✨</h6>
                </div>
                <div class="card-body p-3">
                    <p class="text-secondary small mb-3" style="line-height: 1.5;">Anda berhasil masuk ke sistem Dashboard terpadu <strong>AQPA Indonesia</strong>. Gunakan modul-modul di sebelah kiri untuk mengelola berbagai data seperti inventaris, proyek, akuntansi, dan penjualan perusahaan Anda.</p>
                    
                    <div class="alert border-0 d-flex align-items-center p-3 rounded-3 shadow-sm mb-0" style="background: rgba(234, 88, 12, 0.08); color: #ea580c;" role="alert">
                        <div class="fs-3 me-3 flex-shrink-0">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div class="small">
                            <strong class="d-block mb-1 fs-6">Pembaruan Sistem v1.1.0</strong>
                            Sistem baru saja diperbarui dengan desain <strong>Modern Pitch Black</strong> yang mendukung penuh Mode Gelap dan Terang secara otomatis melalui toggle di sidebar bawah.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-xl-4">
            <div class="card border-0 data-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-3 pb-0">
                    <h6 class="fw-bold text-uppercase mb-0 small" style="letter-spacing: 0.5px;">Aktivitas Terakhir</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 position-relative">
                        <!-- Connecting line -->
                        <div class="position-absolute" style="left: 17px; top: 18px; bottom: 18px; border-left: 2px dashed rgba(148, 163, 184, 0.25); z-index: 0;"></div>
                        
                        <li class="mb-3 position-relative z-1">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="fa-solid fa-arrow-right-to-bracket fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold small">Login Berhasil</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-regular fa-clock me-1"></i> Baru saja</small>
                                </div>
                            </div>
                        </li>
                        <li class="mb-0 position-relative z-1">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(234, 88, 12, 0.15); color: #ea580c;">
                                    <i class="fa-solid fa-user-pen fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold small">Pembaruan UI Premium</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-regular fa-clock me-1"></i> 1 jam yang lalu</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>