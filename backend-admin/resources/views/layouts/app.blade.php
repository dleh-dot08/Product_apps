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
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Variables for Dark/Light Mode */
        :root[data-bs-theme="light"] {
            --sidebar-bg: #ffffff;
            --sidebar-border: #e9ecef;
            --sidebar-link: #495057;
            --sidebar-link-hover: #0d6efd;
            --sidebar-link-hover-bg: #f8f9fa;
            --topbar-bg: #ffffff;
            --main-bg: #f8f9fa;
        }

        :root[data-bs-theme="dark"] {
            --sidebar-bg: #1a1d20;
            --sidebar-border: #2c3034;
            --sidebar-link: #adb5bd;
            --sidebar-link-hover: #0d6efd;
            --sidebar-link-hover-bg: #212529;
            --topbar-bg: #1a1d20;
            --main-bg: #212529;
            --bs-body-bg: #212529;
        }

        /* Layout Structure */
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        #sidebar .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--bs-body-color);
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
        }

        #sidebar .sidebar-nav {
            padding: 1rem 0;
            flex-grow: 1;
        }

        #sidebar .nav-item {
            padding: 0 1rem;
            margin-bottom: 0.25rem;
        }

        #sidebar .nav-link {
            color: var(--sidebar-link);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
        }

        #sidebar .nav-link i {
            width: 1.5rem;
            font-size: 1.1rem;
            margin-right: 0.75rem;
            text-align: center;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: var(--sidebar-link-hover);
            background: var(--sidebar-link-hover-bg);
        }

        /* Main Content Styling */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--main-bg);
        }

        /* Header / Topbar */
        .topbar {
            height: 70px;
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .btn-theme-toggle {
            background: transparent;
            border: none;
            color: var(--bs-body-color);
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        .btn-theme-toggle:hover {
            color: var(--sidebar-link-hover);
        }

        /* Content Area */
        .page-content {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* Footer */
        .footer {
            padding: 1rem 1.5rem;
            background: var(--topbar-bg);
            border-top: 1px solid var(--sidebar-border);
            color: var(--sidebar-link);
            font-size: 0.875rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Mobile Toggle */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
                position: fixed;
                height: 100vh;
            }
            #sidebar.toggled {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <div id="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="bg-primary text-white rounded p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                    <i class="fa-solid fa-a"></i>
                </div>
                AQPA
            </a>
            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                </div>
                
                <div class="nav-item mt-3 mb-1 px-3 text-uppercase" style="font-size: 11px; font-weight: 700; color: #6c757d; letter-spacing: 0.5px;">
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
                    <button class="btn btn-theme-toggle me-3 d-md-none" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    @isset($header)
                        <h4 class="mb-0 fw-semibold fs-5">{{ $header }}</h4>
                    @endisset
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button class="btn-theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" style="color: var(--bs-body-color);" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=0d6efd&color=fff" alt="mdo" width="35" height="35" class="rounded-circle me-2">
                            <span class="fw-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
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
