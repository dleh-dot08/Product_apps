<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AQPA Dashboard') }}</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Premium CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bs-body-bg);
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* Variables for Dark/Light Mode */
        :root[data-bs-theme="light"] {
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(0,0,0,0.05);
            --sidebar-link: #64748b;
            --sidebar-link-hover: #2563eb;
            --sidebar-link-hover-bg: #eff6ff;
            --sidebar-link-active: #2563eb;
            --sidebar-link-active-bg: #eff6ff;
            --topbar-bg: rgba(255, 255, 255, 0.85);
            --main-bg: #f8fafc;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        :root[data-bs-theme="dark"] {
            --sidebar-bg: #0f172a;
            --sidebar-border: #1e293b;
            --sidebar-link: #94a3b8;
            --sidebar-link-hover: #60a5fa;
            --sidebar-link-hover-bg: #1e293b;
            --sidebar-link-active: #60a5fa;
            --sidebar-link-active-bg: #1e293b;
            --topbar-bg: rgba(15, 23, 42, 0.85);
            --main-bg: #0b1320;
            --bs-body-bg: #0b1320;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
        }

        /* Layout Structure */
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        #sidebar .sidebar-brand {
            height: 76px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--bs-body-color);
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .brand-icon {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border-radius: 10px;
            padding: 0.4rem;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-right: 12px;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }

        #sidebar .sidebar-nav {
            padding: 1.5rem 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        /* Custom Scrollbar for Sidebar */
        #sidebar .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: var(--sidebar-border);
            border-radius: 4px;
        }

        #sidebar .nav-item {
            padding: 0 1rem;
            margin-bottom: 0.5rem;
        }

        #sidebar .nav-link {
            color: var(--sidebar-link);
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        #sidebar .nav-link i {
            width: 1.5rem;
            font-size: 1.15rem;
            margin-right: 1rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        #sidebar .nav-link:hover {
            color: var(--sidebar-link-hover);
            background: var(--sidebar-link-hover-bg);
            transform: translateX(4px);
        }

        #sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        #sidebar .nav-link.active {
            color: var(--sidebar-link-active);
            background: var(--sidebar-link-active-bg);
            border-color: rgba(37, 99, 235, 0.1);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        #sidebar .nav-link.active i {
            color: var(--sidebar-link-active);
        }

        /* Main Content Styling */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--main-bg);
        }

        /* Header / Topbar - Glassmorphism */
        .topbar {
            height: 76px;
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .btn-theme-toggle {
            background: var(--sidebar-link-hover-bg);
            border: 1px solid var(--sidebar-border);
            color: var(--sidebar-link);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-theme-toggle:hover {
            color: var(--sidebar-link-hover);
            transform: rotate(15deg);
            box-shadow: var(--shadow-sm);
        }

        /* User Profile Dropdown */
        .user-dropdown-toggle {
            background: var(--sidebar-bg);
            border: 1px solid var(--sidebar-border);
            padding: 0.3rem 0.8rem 0.3rem 0.3rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .user-dropdown-toggle:hover {
            background: var(--sidebar-link-hover-bg);
            box-shadow: var(--shadow-sm);
        }
        
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--sidebar-border);
            background-color: var(--sidebar-bg);
        }
        .dropdown-item {
            color: var(--bs-body-color);
            padding: 0.7rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .dropdown-item:hover {
            background-color: var(--sidebar-link-hover-bg);
            color: var(--sidebar-link-hover);
        }
        .dropdown-item.text-danger:hover {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444 !important;
        }

        /* Content Area */
        .page-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Footer */
        .footer {
            padding: 1.25rem 2rem;
            background: transparent;
            color: var(--sidebar-link);
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Mobile Toggle */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -280px;
                position: fixed;
                height: 100vh;
            }
            #sidebar.toggled {
                margin-left: 0;
            }
            .topbar {
                padding: 0 1rem;
            }
            .page-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <div id="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                AQPA
            </a>
            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i> User Management
                    </a>
                </div>
                
                <div class="nav-item mt-4 mb-2 px-3 text-uppercase" style="font-size: 11px; font-weight: 700; color: var(--sidebar-link); letter-spacing: 0.8px;">
                    Modules
                </div>

                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Accounting
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-boxes-stacked"></i> Persediaan
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-cart-shopping"></i> Pembelian
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-store"></i> Penjualan
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-briefcase"></i> Project
                    </a>
                </div>
            </div>
        </aside>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Topbar (Header) -->
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-theme-toggle me-3 d-md-none" id="sidebarToggle" style="background:transparent; border:none; color: var(--bs-body-color);">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    @isset($header)
                        <h4 class="mb-0 fw-bold fs-5" style="letter-spacing: -0.3px;">{{ $header }}</h4>
                    @endisset
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button class="btn-theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-dropdown-toggle" style="color: var(--bs-body-color);" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=2563eb&color=fff&bold=true" alt="mdo" width="34" height="34" class="rounded-circle me-2 shadow-sm">
                            <span class="fw-semibold" style="font-size: 0.9rem; padding-right: 4px;">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fa-regular fa-user me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <!-- End Topbar -->

            <!-- Main Page Content -->
            <main class="page-content">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div>
                    &copy; <script>document.write(new Date().getFullYear())</script> <strong>AQPA Indonesia</strong>. All Rights Reserved.
                </div>
                <div>
                    Version 1.0.0
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts for Interactivity -->
    <script>
        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
        });

        // Theme Toggle (Dark/Light Mode)
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = themeToggleBtn.querySelector('i');
        const htmlElement = document.documentElement;
        
        // Cek LocalStorage untuk tema yang tersimpan
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            htmlElement.setAttribute('data-bs-theme', savedTheme);
            updateThemeIcon(savedTheme);
        }

        themeToggleBtn.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    </script>
</body>
</html>
