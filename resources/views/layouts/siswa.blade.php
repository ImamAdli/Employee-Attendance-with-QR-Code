<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sistem Absensi Magang Bappeda Sumbar'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bappeda.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-bappeda.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar d-flex flex-column p-3">
            <div class="sidebar-header">
                @if(Auth::user()->siswa && Auth::user()->siswa->foto_profil)
                    <img src="{{ asset('storage/' . Auth::user()->siswa->foto_profil) }}" alt="Profil" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff'">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Profil">
                @endif
                <h5>{{ Auth::user()->siswa ? Auth::user()->siswa->nama_lengkap : Auth::user()->name }}</h5>
                <span class="badge bg-light text-primary">Siswa Magang</span>
            </div>
            <ul class="nav flex-column mt-4">
                <li class="nav-item mb-2">
                    <a class="nav-link @if(request()->routeIs('siswa.dashboard')) active @endif" href="{{ route('siswa.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link @if(request()->routeIs('siswa.profile')) active @endif" href="{{ route('siswa.profile') }}">
                        <i class="fas fa-user me-2"></i> Profil
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link @if(request()->routeIs('siswa.attendance.history')) active @endif" href="{{ route('siswa.attendance.history') }}">
                        <i class="fas fa-history me-2"></i> Riwayat Absensi
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link @if(request()->routeIs('siswa.scan.qr')) active @endif" href="{{ route('siswa.scan.qr') }}">
                        <i class="fas fa-qrcode me-2"></i> Scan QR
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
            </ul>
        </nav>
        <div class="main-content flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-siswa sticky-top">
                <div class="container-fluid">
                    <span class="navbar-brand fw-bold fs-5">@yield('page-title', 'Dashboard')</span>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="text-end me-3">
                            <span id="current-date" class="d-block small text-muted"></span>
                            <span id="current-time" class="d-block fw-bold"></span>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Flash Messages -->
            <div class="container-fluid py-2">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
            
            <div>
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Current Date Time Script -->
    <script>
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
            
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', timeOptions);
        }
        
        // Update every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
    
    <!-- Auto-dismiss alerts after 5 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>