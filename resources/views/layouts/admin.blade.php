<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - Sistem Absensi Magang Bappeda Sumbar</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bappeda.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-bappeda.png') }}" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar bg-dark text-white flex-shrink-0 p-3">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo-bappeda.png') }}" alt="Logo" class="mb-2" style="height:60px;">
                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                <span class="badge bg-primary">Administrator</span>
            </div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('absen.dashboard') }}" class="nav-link text-white {{ request()->routeIs('absen.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('absen.siswa.index') }}" class="nav-link text-white {{ request()->routeIs('absen.siswa.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate me-2"></i> Data Siswa
                    </a>
                </li>
                <li>
                    <a href="{{ route('absen.scan.history') }}" class="nav-link text-white {{ request()->routeIs('absen.scan.*') ? 'active' : '' }}">
                        <i class="fas fa-history me-2"></i> Riwayat Scan QR
                    </a>
                </li>
                <li>
                    <a href="{{ route('absen.qrcode.index') }}" class="nav-link text-white {{ request()->routeIs('absen.qrcode.*') ? 'active' : '' }}">
                        <i class="fas fa-qrcode me-2"></i> Kelola QR Code
                    </a>
                </li>

                <li class="mt-3">
                    <a href="{{ route('logout') }}" class="nav-link text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /Sidebar -->

        <!-- Page Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
                <div class="container-fluid">
                    <span class="navbar-brand fw-bold">@yield('title', 'Dashboard Admin')</span>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="text-end me-3">
                            <span id="current-date-admin" class="d-block small text-muted"></span>
                            <span id="current-time-admin" class="d-block fw-bold"></span>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- /Navbar -->

            <main class="p-4">
                @yield('content')
            </main>
        </div>
        <!-- /Page Content -->
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk update tanggal dan waktu di navbar admin
        document.addEventListener('DOMContentLoaded', function() {
            function updateDateTimeAdmin() {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                let now = new Date();
                let day = days[now.getDay()];
                let date = now.getDate().toString().padStart(2, '0');
                let month = months[now.getMonth()];
                let year = now.getFullYear();
                let hours = now.getHours().toString().padStart(2, '0');
                let minutes = now.getMinutes().toString().padStart(2, '0');
                let seconds = now.getSeconds().toString().padStart(2, '0');
                document.getElementById('current-date-admin').textContent = `${day}, ${date} ${month} ${year}`;
                document.getElementById('current-time-admin').textContent = `${hours}:${minutes}:${seconds}`;
            }
            updateDateTimeAdmin();
            setInterval(updateDateTimeAdmin, 1000);
        });
    </script>
    @stack('scripts')
</body>
</html> 