@extends('layouts.lgnlayout')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-70">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 login-card-custom">
                <div class="card-header bg-white text-center border-0 pb-0">
                    <img src="{{ asset('images/logo-bappeda.png') }}" alt="Logo Sumatera Barat" style="height:70px; margin-bottom:15px;">
                    <h4 class="fw-bold mb-0 mt-2">Login</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                Login
                            </button>
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('register') }}">Belum punya akun? Daftar disini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection