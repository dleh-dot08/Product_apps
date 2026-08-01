<!-- Custom CSS Floating Sidebar Fix Burger Click -->
<style>
    /* 1. Container Utama Sidebar Floating */
    .app-sidebar.sidebar-floating {
        margin: 1rem;
        border-radius: 1.25rem !important;
        display: flex !important;
        flex-direction: column !important;
        height: calc(100vh - 2rem) !important;
        overflow: hidden;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.3s ease, 
                    transform 0.3s ease, 
                    background 0.3s ease;
        z-index: 1038;
    }

    .app-sidebar.sidebar-floating .sidebar-wrapper {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        height: 100% !important;
        overflow: hidden !important;
    }

    .sidebar-menu-container {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Styling Tombol Burger Toggle */
    .sidebar-toggle-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
        z-index: 1040 !important;
        position: relative;
    }

    /* Container Li Menu */
    .app-sidebar.sidebar-floating .nav-item {
        width: 100% !important;
        padding: 0 0.5rem;
    }

    /* Standard Links Base */
    .app-sidebar.sidebar-floating .nav-link {
        font-weight: 600;
        margin: 0.2rem 0 !important;
        padding: 0.75rem 1rem !important;
        border-radius: 0.75rem !important;
        transition: all 0.2s ease;
        display: flex !important;
        align-items: center !important;
        white-space: nowrap;
    }

    /* Styling Tombol Aksi di Profil */
    .profile-action-btn {
        width: 32px;
        height: 32px;
        transition: all 0.2s ease;
    }
    .profile-action-btn:hover {
        transform: scale(1.08);
    }

    /* ========================================================= */
    /* 2. LIGHT MODE COLORS                                      */
    /* ========================================================= */
    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating {
        background-color: #f8fafc !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 
            0 10px 25px -5px rgba(0, 0, 0, 0.08),
            0 20px 25px -5px rgba(15, 23, 42, 0.05) !important;
    }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating:hover {
        box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.12) !important;
    }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .sidebar-brand {
        border-bottom: 1px solid #e2e8f0 !important;
        background-color: transparent !important;
    }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .brand-text { color: #0f172a !important; }
    html:not([data-bs-theme="dark"]) .sidebar-toggle-btn { color: #64748b; }
    html:not([data-bs-theme="dark"]) .sidebar-toggle-btn:hover { background-color: #e2e8f0; color: #0f172a; }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .nav-link { color: #475569 !important; }
    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .nav-link i { color: #64748b !important; }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .nav-link.active {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 15px -3px rgba(234, 88, 12, 0.4) !important;
    }

    html:not([data-bs-theme="dark"]) .app-sidebar.sidebar-floating .nav-link.active i { color: #ffffff !important; }

    html:not([data-bs-theme="dark"]) .sidebar-user-footer {
        border-top: 1px solid #e2e8f0 !important;
        background-color: #f1f5f9 !important;
    }

    html:not([data-bs-theme="dark"]) .sidebar-user-footer img { box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important; }
    html:not([data-bs-theme="dark"]) .sidebar-user-footer .user-name { color: #0f172a !important; }
    html:not([data-bs-theme="dark"]) .sidebar-user-footer .user-role { color: #64748b !important; }

    /* ========================================================= */
    /* 3. DARK MODE CUSTOM                                       */
    /* ========================================================= */
    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating {
        background-color: #0d0f12 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7) !important;
    }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating:hover {
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9) !important;
    }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .sidebar-brand {
        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
        background-color: transparent !important;
    }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .brand-text { color: #f8fafc !important; }
    html[data-bs-theme="dark"] .sidebar-toggle-btn { color: #94a3b8; }
    html[data-bs-theme="dark"] .sidebar-toggle-btn:hover { background-color: rgba(255, 255, 255, 0.08); color: #ffffff; }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .nav-link { color: #94a3b8 !important; }
    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .nav-link i { color: #64748b !important; }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
    }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .nav-link.active {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 20px rgba(249, 115, 22, 0.4) !important;
    }

    html[data-bs-theme="dark"] .app-sidebar.sidebar-floating .nav-link.active i { color: #ffffff !important; }

    html[data-bs-theme="dark"] .sidebar-user-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.07) !important;
        background-color: #050608 !important;
    }

    html[data-bs-theme="dark"] .sidebar-user-footer .user-name { color: #f8fafc !important; }
    html[data-bs-theme="dark"] .sidebar-user-footer .user-role { color: #64748b !important; }

    /* ========================================================= */
    /* 4. STATE COLLAPSED & HOVER EXPAND (STABILIZED CLICK)      */
    /* ========================================================= */
    
    /* A. STATE DITUTUP / DIAM */
    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating {
        width: 4.5rem !important;
        min-width: 4.5rem !important;
        max-width: 4.5rem !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .sidebar-brand {
        padding: 0 0.5rem !important;
        justify-content: center !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .brand-link {
        padding: 0 !important;
        margin: 0 !important;
        justify-content: center !important;
        width: auto !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .brand-icon {
        margin: 0 !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .brand-text,
    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .nav-link p,
    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .user-info-wrapper {
        display: none !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .nav-link {
        padding: 0.75rem 0 !important;
        justify-content: center !important;
        align-items: center !important;
        text-align: center !important;
        width: 100% !important;
        margin: 0.25rem 0 !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .nav-link .nav-icon {
        margin: 0 !important;
        padding: 0 !important;
        font-size: 1.2rem !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .sidebar-user-footer {
        padding: 0.75rem 0.25rem !important;
    }

    body.sidebar-collapse:not(.sidebar-hover) .app-sidebar.sidebar-floating .profile-actions-wrapper {
        width: 100% !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    /* B. STATE KURSOR DI-HOVER KE SIDEBAR */
    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating {
        width: 250px !important;
        min-width: 250px !important;
    }

    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .brand-text,
    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .sidebar-toggle-btn,
    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .nav-link p,
    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .user-info-wrapper {
        display: flex !important;
    }

    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .profile-actions-wrapper {
        flex-direction: row !important;
    }

    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .nav-link {
        justify-content: flex-start !important;
        padding: 0.75rem 1rem !important;
    }

    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .nav-link .nav-icon {
        margin-right: 0.5rem !important;
    }

    body.sidebar-collapse.sidebar-hover .app-sidebar.sidebar-floating .sidebar-brand {
        justify-content: space-between !important;
        padding: 0 1rem !important;
    }
</style>

<!-- Sidebar Floating -->
<aside class="app-sidebar sidebar-floating" data-bs-theme="dark">
    <!-- Sidebar Brand & Toggle Burger Button -->
    <div class="sidebar-brand d-flex align-items-center justify-content-between px-3">
        <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center text-decoration-none">
            <div class="brand-icon">
                <i class="fa-solid fa-bolt text-warning"></i>
            </div>
            <span class="brand-text fw-bold ms-2">AQPA</span>
        </a>

        <!-- Tombol Burger Toggle Sidebar -->
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
            <i class="fa-solid fa-bars fs-6"></i>
        </button>
    </div>
    
    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <!-- Area Menu Utama -->
        <div class="sidebar-menu-container py-2">
            <nav>
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
    
                    <!-- 1. Dashboard (Route Asli) -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-chart-simple"></i>
                            <p class="ms-2 mb-0">Dashboard</p>
                        </a>
                    </li>

                    <!-- 2. Delivery Order -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-regular fa-clipboard"></i>
                            <p class="ms-2 mb-0">Delivery Order</p>
                        </a>
                    </li>

                    <!-- 3. Pembelian -->
                    <li class="nav-item">
                        <a href="{{ route('sales-orders.index') }}" class="nav-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-cart-shopping"></i>
                            <p class="ms-2 mb-0">Pembelian</p>
                        </a>
                    </li>

                    <!-- 4. Packing (Route Asli) -->
                    <li class="nav-item">
                        <a href="{{ route('packaging.index') }}" class="nav-link {{ request()->routeIs('packaging.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-box"></i>
                            <p class="ms-2 mb-0">Packing</p>
                        </a>
                    </li>

                    <!-- 5. Tugas Driver -->
                    <li class="nav-item">
                        <a href="{{ route('pickup-tasks.index') }}" class="nav-link {{ request()->routeIs('pickup-tasks.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-regular fa-paper-plane"></i>
                            <p class="ms-2 mb-0">Tugas Driver</p>
                        </a>
                    </li>

                    <!-- 6. Daftar Tugas (Badge angka 0) -->
                    <li class="nav-item">
                        <a href="{{ route('daftar-tugas.index') }}" class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('daftar-tugas.*') ? 'active' : '' }}">
                            <div class="d-flex align-items-center">
                                <i class="nav-icon fa-solid fa-clipboard-list"></i>
                                <p class="ms-2 mb-0">Daftar Tugas</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-2 py-1 fs-7">0</span>
                        </a>
                    </li>

                    <!-- 7. HPP Ritase -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-solid fa-dollar-sign"></i>
                            <p class="ms-2 mb-0">HPP Ritase</p>
                        </a>
                    </li>

                    <!-- 8. User Management (Route Asli) -->
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-regular fa-user"></i>
                            <p class="ms-2 mb-0">User Management</p>
                        </a>
                    </li>

                    <!-- 9. Kendaraan -->
                    <li class="nav-item mt-1">
                        <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-truck"></i>
                            <p class="ms-2 mb-0">Kendaraan</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>

        <!-- Profile, Dark Mode & Logout Footer -->
        <div class="sidebar-user-footer p-3 mt-auto">
            <div class="d-flex align-items-center justify-content-between w-100">
                <!-- User Info -->
                <a href="{{ route('profile.edit') }}" class="user-info-wrapper d-flex align-items-center gap-2 text-decoration-none overflow-hidden me-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=ea580c&color=fff&bold=true" class="rounded-circle flex-shrink-0" alt="User Image" style="width: 36px; height: 36px; object-fit: cover;">
                    <div class="user-text-details d-flex flex-column text-truncate lh-sm">
                        <span class="fw-bold user-name text-truncate" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="user-role text-truncate" style="font-size: 0.7rem;">{{ Auth::user()->role->name ?? 'Admin' }}</span>
                    </div>
                </a>

                <!-- Action Buttons: Dark Mode & Logout -->
                <div class="profile-actions-wrapper d-flex align-items-center gap-1 flex-shrink-0">
                    <!-- Tombol Dark Mode Toggle -->
                    <button type="button" id="sidebarThemeToggle" class="profile-action-btn btn btn-sm btn-link text-secondary border-0 rounded-circle p-0 d-flex align-items-center justify-content-center" title="Ubah Tema">
                        <i class="fa-regular fa-moon fs-6"></i>
                    </button>

                    <!-- Tombol Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="profile-action-btn btn btn-sm btn-outline-danger border-0 rounded-circle p-0 d-flex align-items-center justify-content-center" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Script Override Toggle Burger, Hover & Dark Mode -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.app-sidebar.sidebar-floating');
        const body = document.body;
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        // 1. Toggle Sidebar saat diklik (Tetap Stay Terbuka / Tertutup)
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle status permanent lock
                if (body.classList.contains('sidebar-collapse')) {
                    body.classList.remove('sidebar-collapse', 'sidebar-hover');
                    localStorage.setItem('sidebarState', 'expanded');
                } else {
                    body.classList.add('sidebar-collapse');
                    body.classList.remove('sidebar-hover');
                    localStorage.setItem('sidebarState', 'collapsed');
                }
            });
        }

        // Restore state dari LocalStorage agar pilihan user tersimpan
        const savedState = localStorage.getItem('sidebarState');
        if (savedState === 'collapsed') {
            body.classList.add('sidebar-collapse');
        } else if (savedState === 'expanded') {
            body.classList.remove('sidebar-collapse');
        }

        // 2. Logic Hover Expand HANYA jika dalam keadaan collapse
        if (sidebar) {
            sidebar.addEventListener('mouseenter', function () {
                if (body.classList.contains('sidebar-collapse')) {
                    body.classList.add('sidebar-hover');
                }
            });

            sidebar.addEventListener('mouseleave', function () {
                if (body.classList.contains('sidebar-collapse')) {
                    body.classList.remove('sidebar-hover');
                }
            });
        }

        // 3. Logic Toggle Dark Mode
        const themeBtn = document.getElementById('sidebarThemeToggle');
        const html = document.documentElement;

        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                const currentTheme = html.getAttribute('data-bs-theme');
                const icon = themeBtn.querySelector('i');

                if (currentTheme === 'dark') {
                    html.setAttribute('data-bs-theme', 'light');
                    icon.className = 'fa-regular fa-moon fs-6';
                    localStorage.setItem('themeState', 'light');
                } else {
                    html.setAttribute('data-bs-theme', 'dark');
                    icon.className = 'fa-regular fa-sun fs-6 text-warning';
                    localStorage.setItem('themeState', 'dark');
                }
            });

            // Restore theme
            const savedTheme = localStorage.getItem('themeState');
            if (savedTheme) {
                html.setAttribute('data-bs-theme', savedTheme);
                const icon = themeBtn.querySelector('i');
                if (icon) {
                    icon.className = savedTheme === 'dark' ? 'fa-regular fa-sun fs-6 text-warning' : 'fa-regular fa-moon fs-6';
                }
            }
        }
    });
</script>