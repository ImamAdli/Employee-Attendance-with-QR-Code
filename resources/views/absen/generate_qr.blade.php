@extends('layouts.admin')

@section('title', 'Generate QR Code')

@section('content')
<div class="container">
    <h3 class="mb-4">Generate QR Code Absensi</h3>
    <form method="GET" action="{{ route('absen.generate.qr') }}">
        <button type="submit" class="btn btn-primary mb-3">Generate QR Code Baru</button>
    </form>
    <div class="card p-4 text-center">
        <h5 class="mb-3">QR Code Hari Ini</h5>
        @if(isset($qrCodeUrl))
            <img src="{{ $qrCodeUrl }}" alt="QR Code" style="max-width:200px;">
        @else
            <div class="text-muted">Belum ada QR Code digenerate hari ini.</div>
        @endif
    </div>
</div>
@endsection 