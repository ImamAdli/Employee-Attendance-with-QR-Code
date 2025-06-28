<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - Sistem Absensi Magang Bappeda Sumbar</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bappeda.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-bappeda.png') }}" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>
    <div class="container">
        <div class="login-header text-center mt-5 mb-4">
            <img src="{{ asset('images/logo-bappeda.png') }}" alt="Logo Bappeda" class="mb-2" style="height:60px;">
            <h2 class="fw-bold">Sistem Absensi Magang Bappeda Sumbar</h2>
        </div>
        @yield('content')
        <div class="footer text-center mt-5 mb-3 text-muted small">
            &copy; {{ date('Y') }} Sistem Absensi Magang Bappeda Sumbar. All rights reserved.
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    @yield('scripts')
</body>
</html>