@extends('layouts.admin')

@section('title', 'Riwayat Scan QR Code')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3">Riwayat Scan QR Code Siswa</h3>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filter Data</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('absen.scan.history') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="siswa_id" class="form-label">Siswa</label>
                            <select class="form-select" id="siswa_id" name="siswa_id">
                                <option value="">Semua Siswa</option>
                                @foreach($siswaList as $siswa)
                                    <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                        {{ $siswa->nama_lengkap ?? $siswa->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tipe" class="form-label">Tipe Absen</label>
                            <select class="form-select" id="tipe" name="tipe">
                                <option value="">Semua</option>
                                <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                            <a href="{{ route('absen.scan.history') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-redo me-2"></i> Reset
                            </a>
                            <a href="{{ route('absen.export', ['tanggal' => request('tanggal'), 'siswa_id' => request('siswa_id'), 'tipe' => request('tipe')]) }}" class="btn btn-success px-4">
                                <i class="fas fa-file-excel me-2"></i> Export
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Daftar Riwayat Scan QR Code</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">Nama Siswa</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="20%">Waktu</th>
                                    <th width="10%">Tipe</th>
                                    <th width="15%">QR Code</th>
                                    <th width="15%">Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi as $index => $item)
                                    <tr>
                                        <td>{{ $absensi->firstItem() + $index }}</td>
                                        <td>{{ $item->siswa->nama_lengkap ?? $item->siswa->user->name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                        <td>
                                            @if(!empty($item->jam_masuk) && (request('tipe') == 'masuk' || empty(request('tipe'))))
                                                <span class="badge bg-primary">Masuk: {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                                            @endif
                                            
                                            @if(!empty($item->jam_keluar) && (request('tipe') == 'keluar' || empty(request('tipe'))))
                                                <span class="badge bg-danger">Keluar: {{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($item->jam_masuk) && (request('tipe') == 'masuk' || empty(request('tipe'))))
                                                <span class="badge bg-success">Masuk</span>
                                            @endif
                                            
                                            @if(!empty($item->jam_keluar) && (request('tipe') == 'keluar' || empty(request('tipe'))))
                                                <span class="badge bg-warning text-dark">Keluar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($item->jam_masuk) && (request('tipe') == 'masuk' || empty(request('tipe'))))
                                                @if($item->qrcode_masuk)
                                                    <span class="badge bg-success">{{ $item->qrcode_masuk->kode }}</span>
                                                @else
                                                    <span class="badge bg-success">-</span>
                                                @endif
                                            @endif
                                            
                                            @if(!empty($item->jam_keluar) && (request('tipe') == 'keluar' || empty(request('tipe'))))
                                                @if($item->qrcode_keluar)
                                                    <span class="badge bg-warning text-dark">{{ $item->qrcode_keluar->kode }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">-</span>
                                                @endif
                                            @endif
                                            
                                            @if(empty($item->jam_masuk) && empty($item->jam_keluar))
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($item->foto_masuk) && (request('tipe') == 'masuk' || empty(request('tipe'))))
                                                <button type="button" class="btn btn-sm btn-primary view-photo" 
                                                        data-bs-toggle="modal" data-bs-target="#photoModal"
                                                        data-photo="{{ route('absen.view.image', ['id' => $item->id, 'type' => 'masuk']) }}"
                                                        data-title="Foto Masuk - {{ $item->siswa->nama_lengkap ?? 'Siswa' }}">
                                                    <i class="fas fa-image"></i> Masuk
                                                </button>
                                            @endif
                                            
                                            @if(!empty($item->foto_keluar) && (request('tipe') == 'keluar' || empty(request('tipe'))))
                                                <button type="button" class="btn btn-sm btn-warning text-dark view-photo" 
                                                        data-bs-toggle="modal" data-bs-target="#photoModal"
                                                        data-photo="{{ route('absen.view.image', ['id' => $item->id, 'type' => 'keluar']) }}"
                                                        data-title="Foto Keluar - {{ $item->siswa->nama_lengkap ?? 'Siswa' }}">
                                                    <i class="fas fa-image"></i> Keluar
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">Tidak ada data riwayat scan QR code</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $absensi->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" class="img-fluid rounded" alt="Foto Absensi">
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
        // Gunakan vanilla JavaScript untuk menangani klik tombol foto
        document.querySelectorAll('.view-photo').forEach(function(button) {
            button.addEventListener('click', function() {
                var photoUrl = this.getAttribute('data-photo');
                var photoTitle = this.getAttribute('data-title');
                
                // Set image source
                var modalPhoto = document.getElementById('modalPhoto');
                modalPhoto.src = photoUrl;
                document.getElementById('photoModalLabel').textContent = photoTitle || 'Foto Absensi';
                
                // Update full image link
                
                // Cek apakah gambar berhasil dimuat
                modalPhoto.onerror = function() {
                    this.src = '';
                    this.insertAdjacentHTML('afterend', '<div class="alert alert-danger mt-2">Gagal memuat gambar. File tidak ditemukan.</div>');

                };
            });
        });
    });
</script>
@endpush 