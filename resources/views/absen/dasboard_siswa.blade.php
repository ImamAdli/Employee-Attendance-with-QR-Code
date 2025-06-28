@extends('layouts.siswa')
    
@section('title', 'Dashboard Siswa')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary shadow-sm d-flex align-items-center alert-welcome" role="alert">
                <i class="fas fa-user-graduate fa-2x me-3"></i>
                <div>
                    <h5 class="mb-0">Selamat Datang, {{ $user->siswa->nama_lengkap ?? $user->name }}!</h5>
                    <small>Status: <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }}">{{ $user->status == 'active' ? 'Aktif' : 'Menunggu Persetujuan' }}</span></small>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Status Absensi Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card {{ $checkIn ? 'bg-success' : 'bg-light' }} text-{{ $checkIn ? 'white' : 'dark' }} status-card">
                                <div class="card-body text-center py-3">
                                    <h6 class="mb-2">Check-In</h6>
                                    @if($checkIn)
                                        <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($checkIn->created_at)->format('H:i') }}</p>
                                    @else
                                        <p class="mb-0">Belum Absen</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card {{ $checkOut ? 'bg-success' : 'bg-light' }} text-{{ $checkOut ? 'white' : 'dark' }} status-card">
                                <div class="card-body text-center py-3">
                                    <h6 class="mb-2">Check-Out</h6>
                                    @if($checkOut)
                                        <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($checkOut->created_at)->format('H:i') }}</p>
                                    @else
                                        <p class="mb-0">Belum Absen</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('siswa.scan.qr') }}" class="btn btn-primary btn-lg shadow btn-scan-qr">
                            <i class="fas fa-qrcode me-2"></i> Scan QR untuk Absen
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Informasi Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">Nama Lengkap</div>
                        <div class="col-7">{{ $user->siswa->nama_lengkap ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">Email</div>
                        <div class="col-7">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">Asal Sekolah</div>
                        <div class="col-7">{{ $user->siswa->asal_sekolah ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">Jurusan</div>
                        <div class="col-7">{{ $user->siswa->jurusan ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">No HP</div>
                        <div class="col-7">{{ $user->siswa->no_hp ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 fw-bold">Tanggal Magang</div>
                        <div class="col-7">{{ $user->siswa->tanggal_mulai ? \Carbon\Carbon::parse($user->siswa->tanggal_mulai)->format('d M Y') : '-' }} s/d {{ $user->siswa->tanggal_selesai ? \Carbon\Carbon::parse($user->siswa->tanggal_selesai)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('siswa.profile') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-edit me-2"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Riwayat Absensi Terakhir</h5>
                    <a href="{{ route('siswa.attendance.history') }}" class="btn btn-outline-light btn-sm">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Jenis</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->created_at)->format('H:i') }}</td>
                                        <td>{{ $attendance->type == 'check_in' ? 'Masuk' : 'Pulang' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-photo" 
                                                    data-bs-toggle="modal" data-bs-target="#photoModal"
                                                    data-photo="{{ asset('storage/' . $attendance->photo) }}">
                                                <i class="fas fa-image"></i> Lihat
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada riwayat absensi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" class="img-fluid rounded" alt="Attendance Photo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.view-photo').on('click', function() {
            const photoUrl = $(this).data('photo');
            $('#modalPhoto').attr('src', photoUrl);
        });
    });
</script>
@endpush
