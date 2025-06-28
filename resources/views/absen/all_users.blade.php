@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3">Data Siswa Aktif</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Daftar Siswa Aktif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">Nama</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">Asal Sekolah</th>
                                    <th width="15%">Jurusan</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse ($users->where('status', 'active') as $user)
                                @php $siswa = $user->siswa ?? null; @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $siswa->asal_sekolah ?? '-' }}</td>
                                    <td>{{ $siswa->jurusan ?? '-' }}</td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#nonaktifModal{{ $user->id }}">
                                            <i class="fas fa-user-slash"></i> Nonaktifkan
                                        </button>
                                    </td>
                                </tr>
                                <!-- Modal Detail -->
                                <div class="modal fade" id="detailModal{{ $user->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailModalLabel{{ $user->id }}">Detail Siswa</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    @if($siswa && $siswa->foto_profil)
                                                        <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="Foto Profil" class="rounded-circle" width="100" height="100" style="object-fit:cover;">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff" alt="Foto Profil" class="rounded-circle" width="100" height="100">
                                                    @endif
                                                </div>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>Nama Lengkap:</strong> {{ $siswa->nama_lengkap ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                                                    <li class="list-group-item"><strong>Jenis Kelamin:</strong> {{ $siswa->jenis_kelamin ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Asal Sekolah:</strong> {{ $siswa->asal_sekolah ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Jurusan:</strong> {{ $siswa->jurusan ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>No HP:</strong> {{ $siswa->no_hp ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tanggal Mulai:</strong> {{ $siswa->tanggal_mulai ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tanggal Selesai:</strong> {{ $siswa->tanggal_selesai ?? '-' }}</li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Nonaktifkan -->
                                <div class="modal fade" id="nonaktifModal{{ $user->id }}" tabindex="-1" aria-labelledby="nonaktifModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="nonaktifModalLabel{{ $user->id }}">Nonaktifkan Siswa</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('absen.siswa.update-status', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin menonaktifkan siswa <strong>{{ $user->name }}</strong>?</p>
                                                    <input type="hidden" name="status" value="inactive">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Nonaktifkan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada data siswa aktif</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Siswa Nonaktif -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Daftar Siswa Nonaktif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">Nama</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">Asal Sekolah</th>
                                    <th width="15%">Jurusan</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse ($users->where('status', 'inactive') as $user)
                                @php $siswa = $user->siswa ?? null; @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $siswa->asal_sekolah ?? '-' }}</td>
                                    <td>{{ $siswa->jurusan ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">Nonaktif</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModalInactive{{ $user->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#aktifkanModal{{ $user->id }}">
                                            <i class="fas fa-user-check"></i> Aktifkan
                                        </button>
                                    </td>
                                </tr>
                                <!-- Modal Detail -->
                                <div class="modal fade" id="detailModalInactive{{ $user->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailModalLabel{{ $user->id }}">Detail Siswa</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    @if($siswa && $siswa->foto_profil)
                                                        <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="Foto Profil" class="rounded-circle" width="100" height="100" style="object-fit:cover;">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff" alt="Foto Profil" class="rounded-circle" width="100" height="100">
                                                    @endif
                                                </div>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>Nama Lengkap:</strong> {{ $siswa->nama_lengkap ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                                                    <li class="list-group-item"><strong>Jenis Kelamin:</strong> {{ $siswa->jenis_kelamin ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Asal Sekolah:</strong> {{ $siswa->asal_sekolah ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Jurusan:</strong> {{ $siswa->jurusan ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>No HP:</strong> {{ $siswa->no_hp ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tanggal Mulai:</strong> {{ $siswa->tanggal_mulai ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tanggal Selesai:</strong> {{ $siswa->tanggal_selesai ?? '-' }}</li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Aktifkan -->
                                <div class="modal fade" id="aktifkanModal{{ $user->id }}" tabindex="-1" aria-labelledby="aktifkanModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title" id="aktifkanModalLabel{{ $user->id }}">Aktifkan Siswa</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('absen.siswa.update-status', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin mengaktifkan kembali siswa <strong>{{ $user->name }}</strong>?</p>
                                                    <input type="hidden" name="status" value="active">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Aktifkan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada data siswa nonaktif</td>
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
@endsection 