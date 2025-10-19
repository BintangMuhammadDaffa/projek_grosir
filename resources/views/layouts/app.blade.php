<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Grosir Sendal')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        .offcanvas {
            background: linear-gradient(135deg, #ff005c 0%, #00fff7 100%);
        }
        .offcanvas .nav-link {
            color: rgba(255,255,255,.75);
            transition: all 0.3s;
        }
        .offcanvas .nav-link:hover {
            color: rgba(255,255,255,1);
            background-color: rgba(255,255,255,.1);
        }
        .offcanvas .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,.2);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,.08);
        }
        .btn {
            border-radius: 10px;
        }
        .navbar {
            background: linear-gradient(135deg, #ff005c 0%, #00fff7 100%);
        }
        .navbar .nav-link {
            color: white !important;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;color: white;
        }
        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #ffeb3b !important; /* warna hover/active opsional */
        }
        .spinning {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .success-check {
            animation: checkBounce 1s ease-in-out;
        }
        @keyframes checkBounce {
            0% { transform: scale(0) rotate(-180deg); opacity: 0; }
            50% { transform: scale(1.3) rotate(0deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        /* Dark Mode Styles */
        [data-bs-theme="dark"] {
            background-color: #121212;
            color: #ffffff;
        }
        [data-bs-theme="dark"] .navbar {
            background: linear-gradient(135deg, #2c2c2c 0%, #1f1f1f 100%) !important;
            border-color: #333;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1f1f1f;
            border-color: #333;
            color: #ffffff;
        }
        [data-bs-theme="dark"] .offcanvas {
            background: linear-gradient(135deg, #333 0%, #555 100%);
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1f1f1f;
            border-color: #333;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #ffffff;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #333;
        }
        [data-bs-theme="dark"] #loading-overlay {
            background: rgba(0,0,0,0.8);
        }
    </style>
</head>
<body>
    <div class="container-fluid">

        {{-- Loading overlay --}}
        <div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(255,255,255,0.8); z-index:1050; display:flex; justify-content:center; align-items:center;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Main content --}}
        <main class="@auth px-4 @else col-12 @endauth">

                {{-- Navbar hanya muncul kalau login --}}
                @auth
                <nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4 ">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="{{ route('dashboard') }}">
                            <i class="bi bi-shop"></i> Metro Grosir
                        </a>

                        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav me-auto">
                                @if(auth()->check() && auth()->user()->canAccessDashboard())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('cashier.*') ? 'active' : '' }}" href="{{ route('cashier.index') }}">
                                        <i class="bi bi-cash"></i> Kasir
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                        <i class="bi bi-boxes"></i> Stok Barang
                                    </a>
                                </li>

                                @if(auth()->check() && auth()->user()->canAccessTransactionHistory())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                                        <i class="bi bi-receipt"></i> Riwayat Transaksi
                                    </a>
                                </li>
                                @endif

                                @if(auth()->check() && auth()->user()->canAccessReports())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                        <i class="bi bi-bar-chart"></i> Laporan
                                    </a>
                                </li>
                                @endif

                                @if(auth()->check() && auth()->user()->canAccessProfit())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('profit.*') ? 'active' : '' }}" href="{{ route('profit.index') }}">
                                        <i class="bi bi-graph-up"></i> Profit
                                    </a>
                                </li>
                                @endif

                                @if(auth()->check() && auth()->user()->canAccessOperationalCosts())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('operational-costs.*') ? 'active' : '' }}" href="{{ route('operational-costs.index') }}">
                                        <i class="bi bi-calculator"></i> Biaya Operasional
                                    </a>
                                </li>
                                @endif

                                <li class="nav-item">
                                    <button class="btn btn-link nav-link theme-toggle" title="Toggle Dark/Light Mode">
                                        <i class="bi bi-sun theme-icon"></i>
                                    </button>
                                </li>
                            </ul>

                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-box-arrow-right"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                @endauth

                {{-- Page Content --}}
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- Offcanvas Sidebar for Mobile --}}
        @auth
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-white" id="offcanvasMenuLabel">
                    <i class="bi bi-shop"></i> Grosir Sendal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="nav flex-column">
                    @if(auth()->check() && auth()->user()->canAccessDashboard())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cashier.*') ? 'active' : '' }}" href="{{ route('cashier.index') }}">
                            <i class="bi bi-cash"></i> Kasir
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-boxes"></i> Stok Barang
                        </a>
                    </li>

                    @if(auth()->check() && auth()->user()->canAccessTransactionHistory())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                            <i class="bi bi-receipt"></i> Riwayat Transaksi
                        </a>
                    </li>
                    @endif

                    @if(auth()->check() && auth()->user()->canAccessReports())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <i class="bi bi-bar-chart"></i> Laporan
                        </a>
                    </li>
                    @endif

                    @if(auth()->check() && auth()->user()->canAccessProfit())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profit.*') ? 'active' : '' }}" href="{{ route('profit.index') }}">
                            <i class="bi bi-graph-up"></i> Profit
                        </a>
                    </li>
                    @endif

                    @if(auth()->check() && auth()->user()->canAccessOperationalCosts())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('operational-costs.*') ? 'active' : '' }}" href="{{ route('operational-costs.index') }}">
                            <i class="bi bi-calculator"></i> Biaya Operasional
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <button class="nav-link btn btn-link theme-toggle" title="Toggle Dark/Light Mode">
                            <i class="bi bi-sun theme-icon"></i>
                        </button>
                    </li>

                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="logout btn d-grid gap-2 col-6 mx-auto">
                            <i class="btn btn-outline-dark">Logout</i> 
                        </button>
                    </form>

                    <li class="nav-item mt-auto">
                        <div class="text-center pt-3 border-top">
                            <small class="text-white-50">
                                Logged in as: {{ auth()->user()->name }} <br>
                                <span class="badge bg-light text-dark">{{ ucfirst(auth()->user()->role) }}</span>
                            </small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        @endauth

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- QuaggaJS for Barcode Scanning -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Confirm delete with SweetAlert2
        async function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            const result = await Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            });
            return result.isConfirmed;
        }

        // Show SweetAlert for session messages
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Sukses!',
                    text: '{{ session("success") }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session("error") }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        @endif

        function redirectToProducts() {
            window.location.href = '{{ route("products.index") }}';
        }

        // Show loading overlay on navigation
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loading-overlay');

            function showLoading() {
                loadingOverlay.style.display = 'flex';
            }

            // Show loading on link clicks
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function(event) {
                    // Ignore if link has target _blank or external link
                    if (this.target === '_blank' || this.href.indexOf(location.origin) !== 0) {
                        return;
                    }
                    // Ignore if link has no-loading class
                    if (this.classList.contains('no-loading')) {
                        return;
                    }
                    showLoading();
                });
            });

            // Show loading on form submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    showLoading();
                });
            });

            // Hide loading on page load
            window.addEventListener('pageshow', function(event) {
                loadingOverlay.style.display = 'none';
            });
        });

        // Offcanvas automatically handled by Bootstrap, no custom JS needed for toggle

        // Dark/Light mode toggle script
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleButtons = document.querySelectorAll('.theme-toggle');
            const themeIcon = document.querySelectorAll('.theme-icon');

            function setTheme(theme) {
                if (theme === 'dark') {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    themeIcon.forEach(icon => {
                        icon.classList.remove('bi-sun');
                        icon.classList.add('bi-moon');
                    });
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    themeIcon.forEach(icon => {
                        icon.classList.remove('bi-moon');
                        icon.classList.add('bi-sun');
                    });
                }
                localStorage.setItem('theme', theme);
            }

            // Load saved theme or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            themeToggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    if (currentTheme === 'dark') {
                        setTheme('light');
                    } else {
                        setTheme('dark');
                    }
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
