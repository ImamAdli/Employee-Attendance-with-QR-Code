@extends('layouts.admin')

@section('title', 'Persetujuan Siswa')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3">Siswa Menunggu Persetujuan</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark"> {{-- Menggunakan bg-warning untuk status pending --}}
                    <h5 class="mb-0">Daftar Siswa Menunggu Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">Foto Profil</th>
                                    <th width="15%">Nama Lengkap</th>
                                    <th width="15%">Email</th>
                                    <th width="10%">Jenis Kelamin</th>
                                    <th width="15%">Asal Sekolah</th>
                                    <th width="10%">Jurusan</th>
                                    <th width="10%">No HP</th>
                                    <th width="10%">Tgl Mulai</th>
                                    <th width="10%">Tgl Selesai</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingUsers ?? [] as $user)
                                @php $siswa = $siswaList[$user->id] ?? null; @endphp
                                <tr>
                                    <td>
                                        @if($siswa && $siswa->foto_profil)
                                            <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="Foto Profil" class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap ?? ($user->name ?? 'Siswa')) }}&background=0D8ABC&color=fff" alt="Foto Profil" class="rounded-circle" width="48" height="48">
                                        @endif
                                    </td>
                                    <td>{{ $siswa->nama_lengkap ?? ($user->name ?? '-') }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                                    <td>{{ $siswa->asal_sekolah ?? '-' }}</td>
                                    <td>{{ $siswa->jurusan ?? '-' }}</td>
                                    <td>{{ $siswa->no_hp ?? '-' }}</td>
                                    <td>{{ $siswa->tanggal_mulai ? \Carbon\Carbon::parse($siswa->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $siswa->tanggal_selesai ? \Carbon\Carbon::parse($siswa->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <form action="{{ route('absen.approve.user', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fas fa-check"></i> Approve</button>
                                        </form>
                                        <form action="{{ route('absen.reject.user', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-3">Tidak ada siswa yang menunggu persetujuan.</td>
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