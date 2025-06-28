@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary shadow-sm d-flex align-items-center alert-welcome" role="alert">
                <i class="fas fa-user-shield fa-2x me-3"></i>
                <div>
                    <h5 class="mb-0">Selamat Datang Admin!</h5>
                    <small>Panel Kontrol Sistem Monitoring Absensi Siswa Magang Bappeda Sumbar</small>
                </div>
            </div>
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>QR Code untuk absensi harus digenerate setiap hari agar siswa dapat melakukan absensi.</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>Pastikan untuk mengonfirmasi siswa baru yang mendaftar agar mereka dapat login ke sistem.</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-info rounded-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-user-clock fa-2x mb-2"></i>
                    <h1 class="display-4 mb-3">{{ $pendingUsers }}</h1>
                    <h5>Siswa Menunggu Persetujuan</h5>
                    <a href="{{ route('absen.pending.users') }}" class="btn btn-outline-light btn-lg shadow rounded-pill px-4 py-2 fw-bold mt-3">
                        <i class="fas fa-user-clock me-2"></i> Kelola
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-success rounded-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h1 class="display-4 mb-3">{{ $activeUsers }}</h1>
                    <h5>Siswa Aktif</h5>
                    <a href="{{ route('absen.siswa.index') }}" class="btn btn-outline-light btn-lg shadow rounded-pill px-4 py-2 fw-bold mt-3">
                        <i class="fas fa-users me-2"></i> Lihat Semua
                    </a>
                </div>  
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-warning rounded-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h1 class="display-4 mb-3">{{ $todayPresent }}</h1>
                    <h5>Absensi Hari Ini</h5>
                    <a href="{{ route('absen.scan.history') }}?tanggal={{ date('Y-m-d') }}" class="btn btn-outline-light btn-lg shadow rounded-pill px-4 py-2 fw-bold mt-3">
                        <i class="fas fa-calendar-check me-2"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm text-white bg-primary rounded-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-qrcode fa-2x mb-2"></i>
                    <h1 class="display-4 mb-3">QR</h1>
                    <h5>Generate QR Code</h5>
                    <a href="{{ route('absen.qrcode.index') }}" class="btn btn-outline-light btn-lg shadow rounded-pill px-4 py-2 fw-bold mt-3">
                        <i class="fas fa-qrcode me-2"></i> Generate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil waktu server dari PHP
        let serverTime = new Date("{{ date('Y-m-d H:i:s') }}");
        function updateServerTime() {
            serverTime.setSeconds(serverTime.getSeconds() + 1);
            let hours = serverTime.getHours().toString().padStart(2, '0');
            let minutes = serverTime.getMinutes().toString().padStart(2, '0');
            let seconds = serverTime.getSeconds().toString().padStart(2, '0');
            document.getElementById('serverTime').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateServerTime, 1000);
    });
</script>
@endsection
