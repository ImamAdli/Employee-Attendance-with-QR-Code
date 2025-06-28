@extends('layouts.siswa')

@section('title', 'Edit Profil')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Profil</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <div class="profile-image-container">
                                        @if($user->siswa && $user->siswa->foto_profil)
                                            <img src="{{ asset('storage/' . $user->siswa->foto_profil) }}" alt="Foto Profil" class="img-fluid rounded-circle profile-image" id="preview-image">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff" alt="Foto Profil" class="img-fluid rounded-circle profile-image" id="preview-image">
                                        @endif
                                    </div>
                                    <label for="foto_profil" class="form-label mt-2">Ubah Foto</label>
                                    <input type="file" class="form-control" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text">Format: JPG, PNG. Maks: 2MB</div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama User</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->siswa->nama_lengkap ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ (old('jenis_kelamin', $user->siswa->jenis_kelamin ?? '') == 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ (old('jenis_kelamin', $user->siswa->jenis_kelamin ?? '') == 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                                        <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $user->siswa->asal_sekolah ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="jurusan" class="form-label">Jurusan</label>
                                        <input type="text" class="form-control" id="jurusan" name="jurusan" value="{{ old('jurusan', $user->siswa->jurusan ?? '') }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="no_hp" class="form-label">Nomor HP</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->siswa->no_hp ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nis" class="form-label">NIS (Opsional)</label>
                                        <input type="text" class="form-control" id="nis" name="nis" value="{{ old('nis', $user->siswa->nis ?? '') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai Magang</label>
                                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $user->siswa->tanggal_mulai ? $user->siswa->tanggal_mulai->format('Y-m-d') : '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai Magang</label>
                                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $user->siswa->tanggal_selesai ? $user->siswa->tanggal_selesai->format('Y-m-d') : '') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-image-container {
        margin: 0 auto;
        width: 150px;
        height: 150px;
        overflow: hidden;
        border: 4px solid #eee;
        border-radius: 50%;
    }
    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview image before upload
        const inputFile = document.getElementById('foto_profil');
        const previewImage = document.getElementById('preview-image');
        
        inputFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush 