<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AQPA Dashboard') }}</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AdminLTE v4 CSS (Bootstrap 5 included/extended) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Tweaks for Modern Look -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.2px;
        }
        
        /* Sidebar styling tweaks */
        .app-sidebar {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sidebar-menu .nav-link {
            border-radius: 8px;
            margin: 0 0.5rem 0.2rem 0.5rem;
            font-weight: 500;
        }
        .sidebar-menu .nav-link.active {
            background-color: rgba(234, 88, 12, 0.15) !important;
            color: #fb923c !important;
            font-weight: 600;
        }
        .sidebar-menu .nav-link:hover {
            background-color: rgba(234, 88, 12, 0.08) !important;
            color: #fb923c !important;
        }

        /* Brand Styling */
        .brand-link {
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            display: flex !important;
            align-items: center;
        }
        .brand-icon {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: white;
            border-radius: 10px;
            padding: 0.4rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }

        body.sidebar-collapse .brand-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        body.sidebar-collapse .brand-icon {
            margin-right: 0 !important;
        }
        body.sidebar-collapse .brand-text {
            display: none !important;
        }
        
        /* Topbar Tweaks */
        .app-header {
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border-bottom: none !important;
        }

        /* Utilities */
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">

    <div class="app-wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="app-main">
            <!-- App Content Header -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            @isset($header)
                                <h3 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">{{ $header }}</h3>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- App Content -->
            <div class="app-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer text-muted" style="font-size: 0.85rem;">
            <div class="float-end d-none d-sm-inline">
                Version 1.0.0
            </div>
            <strong>Copyright &copy; <script>document.write(new Date().getFullYear())</script> AQPA Indonesia.</strong> All rights reserved.
        </footer>
    </div>

    <!-- AdminLTE v4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>

    <!-- Scripts for Interactivity -->
    <script>
        // Theme Toggle (Dark/Light Mode)
        const themeToggleBtn = document.getElementById('themeToggle');
        if (themeToggleBtn) {
            const themeIcon = themeToggleBtn.querySelector('i');
            const htmlElement = document.documentElement;
            
            // Cek LocalStorage untuk tema yang tersimpan
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                htmlElement.setAttribute('data-bs-theme', savedTheme);
                updateThemeIcon(savedTheme);
            }

            themeToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
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
        }
    </script>
</body>
</html>
