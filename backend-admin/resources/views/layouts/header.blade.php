<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <!-- Start navbar links -->
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fa-solid fa-bars fs-5"></i>
                </a>
            </li>
            
            <!-- Mobile Brand Logo -->
            <li class="nav-item d-lg-none ms-2">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                    <div class="brand-icon me-2" style="width: 26px; height: 26px; font-size: 11px; margin-bottom: 0;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <span class="fw-bold text-body fs-5" style="letter-spacing: -0.5px;">AQPA</span>
                </a>
            </li>
        </ul>
        
        <!-- End navbar links -->
        <ul class="navbar-nav ms-auto gap-2 align-items-center">
            
            <!-- Notification Bell -->
            <li class="nav-item">
                <a class="nav-link position-relative" href="#">
                    <i class="fa-regular fa-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="margin-top: 10px; margin-left: -12px; font-size: 0.6rem;">
                        3
                    </span>
                </a>
            </li>

            <!-- Dark Mode Toggle -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="themeToggle">
                    <i class="fa-regular fa-moon fs-5"></i>
                </a>
            </li>
            
            <div class="vr mx-2" style="height: 30px; opacity: 0.15;"></div>

            <!-- User Dropdown -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="padding-right: 0; padding-left: 0.5rem;">
                    <div class="d-none d-md-flex flex-column text-end lh-1">
                        <span class="fw-bold text-body" style="font-size: 0.85rem; letter-spacing: -0.2px;">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="text-muted mt-1" style="font-size: 0.7rem; font-weight: 500;">{{ Auth::user()->role->name ?? 'Admin' }}</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=ea580c&color=fff&bold=true" class="rounded-circle shadow-sm" alt="User Image" style="width: 38px; height: 38px; object-fit: cover; border: 2px solid rgba(234, 88, 12, 0.2);">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 rounded-4" style="min-width: 240px; padding: 0.5rem;">
                    <li>
                        <a class="dropdown-item rounded-3 py-2 my-1" href="{{ route('profile.edit') }}">
                            <i class="fa-regular fa-circle-user me-2 opacity-75"></i> Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2 my-1" href="#">
                            <i class="fa-solid fa-gear me-2 opacity-75"></i> Pengaturan
                        </a>
                    </li>
                    <li><hr class="dropdown-divider opacity-10 my-2"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-3 text-danger py-2 my-1 fw-semibold">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2 opacity-75"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
