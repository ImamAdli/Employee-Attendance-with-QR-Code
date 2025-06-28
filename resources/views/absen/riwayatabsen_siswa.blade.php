@extends('layouts.siswa')
    
@section('title', 'Riwayat Absensi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filter Data</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('siswa.attendance.history') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="from_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="to_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">Jenis Absensi</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Semua</option>
                                <option value="check_in" {{ request('type') == 'check_in' ? 'selected' : '' }}>Masuk</option>
                                <option value="check_out" {{ request('type') == 'check_out' ? 'selected' : '' }}>Pulang</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('siswa.attendance.history') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt me-2"></i>Reset
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Riwayat Absensi</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="attendanceTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->created_at)->format('H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->type == 'check_in' ? 'primary' : 'info' }}">
                                                {{ $attendance->type == 'check_in' ? 'Masuk' : 'Pulang' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = 'Tepat Waktu';
                                                $statusClass = 'success';
                                                
                                                if ($attendance->type == 'check_in' && \Carbon\Carbon::parse($attendance->created_at)->format('H:i') > '08:00') {
                                                    $status = 'Terlambat';
                                                    $statusClass = 'warning';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $status }}</span>
                                        </td>
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
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h5>Belum ada data absensi</h5>
                                                <p class="text-muted">Data absensi akan muncul setelah Anda melakukan absensi</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $attendances->links() }}
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
        // Modal Photo Handler
        $('.view-photo').on('click', function() {
            const photoUrl = $(this).data('photo');
            $('#modalPhoto').attr('src', photoUrl);
        });
        
        // Initialize datepicker if needed
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        }
    });
</script>
@endpush
