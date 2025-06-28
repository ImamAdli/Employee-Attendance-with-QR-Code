@extends('layouts.admin')

@section('title', 'Kelola QR Code')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Kelola QR Code</h3>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Generate QR Code Baru</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('absen.qrcode.index') }}" class="row align-items-end">
                <input type="hidden" name="generate" value="1">
                
                <div class="col-md-6 mb-3">
                    <label for="tipe" class="form-label">Tipe QR Code</label>
                    <select name="tipe" id="tipe" class="form-select">
                        <option value="masuk">Masuk (Check-in)</option>
                        <option value="keluar">Keluar (Check-out)</option>
                    </select>
                    <small class="text-muted">QR Code lama dengan tipe yang sama akan dinonaktifkan</small>
                </div>
                
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-qrcode me-2"></i> Generate QR Code Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @if($selectedQrCode)
                            QR Code {{ $selectedQrCode->tipe == 'masuk' ? 'Masuk' : 'Keluar' }} ({{ $selectedQrCode->is_active ? 'Aktif' : 'Nonaktif' }})
                        @elseif($qrTerbaru)
                            QR Code {{ $qrTerbaru->tipe == 'masuk' ? 'Masuk' : 'Keluar' }} Aktif
                        @else
                            QR Code Terbaru
                        @endif
                    </h5>
                    @if($selectedQrCode && !$selectedQrCode->is_active)
                    <a href="{{ route('absen.qrcode.index', ['activate' => $selectedQrCode->id]) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-check-circle"></i> Aktifkan
                    </a>
                    @endif
                </div>
                <div class="card-body text-center p-4">
                    @if($qrCodeSvg && ($qrTerbaru || $selectedQrCode))
                        <div class="mb-3">
                            <span class="badge bg-{{ ($qrTerbaru ? $qrTerbaru->tipe : $selectedQrCode->tipe) == 'masuk' ? 'primary' : 'warning' }} mb-2">
                                {{ ($qrTerbaru ? $qrTerbaru->tipe : $selectedQrCode->tipe) == 'masuk' ? 'Masuk (Check-in)' : 'Keluar (Check-out)' }}
                            </span>
                            <span class="badge bg-{{ ($qrTerbaru ? $qrTerbaru->is_active : $selectedQrCode->is_active) ? 'success' : 'secondary' }}">
                                {{ ($qrTerbaru ? $qrTerbaru->is_active : $selectedQrCode->is_active) ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="qr-container mb-3 bg-white p-3 rounded shadow-sm d-inline-block">
                            {!! $qrCodeSvg !!}
                        </div>
                        <div class="d-flex flex-column gap-1 text-start">
                            <div><strong>Kode:</strong> {{ $qrTerbaru ? $qrTerbaru->kode : $selectedQrCode->kode }}</div>
                            <div><strong>Berlaku:</strong> {{ ($qrTerbaru ? $qrTerbaru->berlaku_mulai : $selectedQrCode->berlaku_mulai)->format('d M Y H:i') }} s/d {{ ($qrTerbaru ? $qrTerbaru->berlaku_sampai : $selectedQrCode->berlaku_sampai)->format('d M Y H:i') }}</div>
                            <div><strong>Tipe:</strong> {{ ucfirst($qrTerbaru ? $qrTerbaru->tipe : $selectedQrCode->tipe) }}</div>
                        </div>
                    @else
                        <div class="text-muted py-5">Belum ada QR Code yang aktif.</div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">QR Code Aktif</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 {{ $activeQrMasuk ? 'border-primary' : 'bg-light' }}">
                                <div class="card-header {{ $activeQrMasuk ? 'bg-primary text-white' : '' }}">
                                    <h6 class="mb-0">QR Code Masuk</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($activeQrMasuk)
                                        <small class="d-block mb-1">{{ $activeQrMasuk->kode }}</small>
                                        <div class="d-flex justify-content-center mb-2">
                                            <a href="{{ route('absen.qrcode.index', ['show' => $activeQrMasuk->id]) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            <a href="{{ route('absen.qrcode.index', ['deactivate' => $activeQrMasuk->id]) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times-circle"></i> Nonaktifkan
                                            </a>
                                        </div>
                                        <small class="text-muted d-block">
                                            Berlaku hingga:
                                            <br>{{ $activeQrMasuk->berlaku_sampai->format('d M Y H:i') }}
                                        </small>
                                    @else
                                        <div class="py-3 text-muted">
                                            Tidak ada QR Code Masuk yang aktif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 {{ $activeQrKeluar ? 'border-warning' : 'bg-light' }}">
                                <div class="card-header {{ $activeQrKeluar ? 'bg-warning text-dark' : '' }}">
                                    <h6 class="mb-0">QR Code Keluar</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($activeQrKeluar)
                                        <small class="d-block mb-1">{{ $activeQrKeluar->kode }}</small>
                                        <div class="d-flex justify-content-center mb-2">
                                            <a href="{{ route('absen.qrcode.index', ['show' => $activeQrKeluar->id]) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            <a href="{{ route('absen.qrcode.index', ['deactivate' => $activeQrKeluar->id]) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times-circle"></i> Nonaktifkan
                                            </a>
                                        </div>
                                        <small class="text-muted d-block">
                                            Berlaku hingga:
                                            <br>{{ $activeQrKeluar->berlaku_sampai->format('d M Y H:i') }}
                                        </small>
                                    @else
                                        <div class="py-3 text-muted">
                                            Tidak ada QR Code Keluar yang aktif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Riwayat QR Code</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Berlaku</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $qr)
                                <tr>
                                    <td>{{ $qr->kode }}</td>
                                    <td>
                                        <span class="badge bg-{{ $qr->tipe == 'masuk' ? 'primary' : 'warning' }}">
                                            {{ $qr->tipe == 'masuk' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td>{{ $qr->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        {{ $qr->berlaku_mulai->format('d M Y H:i') }}
                                        <br>s/d
                                        <br>{{ $qr->berlaku_sampai->format('d M Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $qr->is_active ? 'success' : 'secondary' }}">
                                            {{ $qr->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('absen.qrcode.index', ['show' => $qr->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            @if(!$qr->is_active)
                                            <a href="{{ route('absen.qrcode.index', ['activate' => $qr->id]) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-check-circle"></i> Aktifkan
                                            </a>
                                            @else
                                            <a href="{{ route('absen.qrcode.index', ['deactivate' => $qr->id]) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times-circle"></i> Nonaktifkan
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">Belum ada riwayat QR Code</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $riwayat->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .qr-container img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush 