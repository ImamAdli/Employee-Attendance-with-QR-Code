@extends('layouts.siswa')

@section('title', 'Scan QR Code')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Scan QR Code Absensi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <p class="mb-0 fw-semibold">Arahkan kamera ke QR Code absensi yang disediakan oleh admin untuk melakukan absensi.</p>
                                <small>Pastikan Anda memberikan izin kamera dan lokasi saat diminta oleh browser.</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alert untuk menampilkan error/success message -->
                    <div id="alert-container"></div>
                    
                    <div id="camera-permissions" class="text-center p-5 d-none">
                        <i class="fas fa-camera fa-4x mb-3 text-secondary"></i>
                        <h5 class="mb-3">Izin Kamera Diperlukan</h5>
                        <p class="text-muted mb-4">Kami membutuhkan akses ke kamera untuk melakukan scan QR code dan mengambil foto selfie.</p>
                        <button id="requestCameraPermission" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-camera me-2"></i> Izinkan Kamera
                        </button>
                    </div>
                    
                    <div id="scanner-container" class="mb-4 text-center">
                        <div class="position-relative">
                            <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        </div>
                        <div class="mt-3 text-muted">
                            <small><i class="fas fa-info-circle me-1"></i> Jika kamera tidak muncul, pastikan Anda memberikan izin kamera pada browser.</small>
                        </div>
                        <div class="mt-2">
                            <button id="refreshCamera" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-sync-alt me-1"></i> Muat Ulang Kamera
                            </button>
                        </div>
                    </div>
                    
                    <div id="scanResult" class="text-center mb-4 d-none">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5>Memproses QR Code...</h5>
                    </div>
                    
                    <!-- Lokasi dinonaktifkan untuk pengembangan 
                    <div id="locationInfo" class="alert alert-warning mb-4 d-none">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt fa-2x me-3"></i>
                            <div>
                                <p class="mb-0 fw-semibold">Mendapatkan lokasi Anda...</p>
                                <small>Pastikan GPS Anda aktif untuk mendapatkan lokasi yang akurat.</small>
                            </div>
                        </div>
                    </div>
                    -->
                    
                    <div id="selfieCapture" class="text-center mb-4 d-none">
                        <h5 class="mb-3">Ambil Selfie untuk Verifikasi</h5>
                        <div class="mb-3 d-inline-block position-relative">
                            <video id="selfie-video" class="rounded shadow-sm" width="400" height="300" autoplay></video>
                            <canvas id="selfie-canvas" class="d-none" width="400" height="300"></canvas>
                            <img id="selfie-preview" class="rounded shadow-sm mb-3 d-none" alt="Selfie Preview">
                        </div>
                        
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <button id="captureButton" class="btn btn-primary btn-lg">
                                <i class="fas fa-camera me-2"></i> Ambil Foto
                            </button>
                            <button id="retakeButton" class="btn btn-outline-secondary d-none">
                                <i class="fas fa-redo me-2"></i> Ambil Ulang
                            </button>
                        </div>
                    </div>
                    
                    <form id="attendanceForm" action="{{ route('siswa.process.attendance') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" id="qr_code" name="qr_code">
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <input type="hidden" id="photo" name="photo">
                        
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <button type="submit" id="submitButton" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle me-2"></i> Konfirmasi Absensi
                            </button>
                            <button type="button" id="cancelButton" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Batal
                            </button>
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
    #scanner-container {
        position: relative;
        max-width: 500px;
        margin: 0 auto;
    }
    
    #scanner-overlay {
        border: 2px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }
    
    #scanner-line {
        position: absolute;
        width: 80%;
        height: 2px;
        background: linear-gradient(to right, rgba(13, 110, 253, 0), rgba(13, 110, 253, 1), rgba(13, 110, 253, 0));
        animation: scan 2s linear infinite;
    }
    
    @keyframes scan {
        0% {
            transform: translateY(-100px);
        }
        50% {
            transform: translateY(100px);
        }
        100% {
            transform: translateY(-100px);
        }
    }
    
    #selfie-video, #selfie-preview {
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .capture-btn-container {
        position: relative;
        bottom: -20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cameraPermissionDiv = document.getElementById('camera-permissions');
        const scannerContainer = document.getElementById('scanner-container');
        const scanResult = document.getElementById('scanResult');
        const selfieCapture = document.getElementById('selfieCapture');
        const attendanceForm = document.getElementById('attendanceForm');
        const alertContainer = document.getElementById('alert-container');
        
        const selfieVideo = document.getElementById('selfie-video');
        const selfieCanvas = document.getElementById('selfie-canvas');
        const selfiePreview = document.getElementById('selfie-preview');
        const captureButton = document.getElementById('captureButton');
        const retakeButton = document.getElementById('retakeButton');
        const cancelButton = document.getElementById('cancelButton');
        const submitButton = document.getElementById('submitButton');
        
        let html5QrCode;
        let currentStream;
        
        // Set nilai default untuk lokasi
        document.getElementById('latitude').value = "0";
        document.getElementById('longitude').value = "0";
        
        // Trigger request untuk lokasi - akan memunculkan notifikasi browser
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Lokasi diizinkan - simpan koordinat
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    console.log("Lokasi diperoleh:", position.coords.latitude, position.coords.longitude);
                },
                function(error) {
                    // Lokasi ditolak atau error - tetap lanjutkan dengan nilai default
                    console.log("Akses lokasi ditolak atau error:", error.message);
                },
                {
                    enableHighAccuracy: true, // Meminta akurasi tinggi untuk memastikan notifikasi muncul
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        }
        
        // Implementasi scanner sederhana
        function startScanner() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start(
                { facingMode: "environment" }, 
                config, 
                onScanSuccess,
                (error) => {}
            ).catch((err) => {
                console.error("Failed to start scanner", err);
                cameraPermissionDiv.classList.remove('d-none');
                scannerContainer.style.display = 'none';
            });
            
            // Store scanner instance globally for later use
            window.scanner = html5QrCode;
        }
        
        // QR Code scan success handler
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`QR Code detected: ${decodedText}`);
            
            if (window.scanner) {
                window.scanner.stop().then(() => {
                    // Show loading
                    scannerContainer.style.display = 'none';
                    scanResult.classList.remove('d-none');
                    
                    // Save QR data
                    document.getElementById('qr_code').value = decodedText;
                    
                    // Show selfie capture
                    setTimeout(() => {
                        scanResult.classList.add('d-none');
                        selfieCapture.classList.remove('d-none');
                        startSelfieCapture();
                    }, 1500);
                });
            }
        }
        
        // Try starting scanner directly
        startScanner();
        
        // Request camera permissions button
        document.getElementById('requestCameraPermission').addEventListener('click', function() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    stream.getTracks().forEach(track => track.stop());
                    cameraPermissionDiv.classList.add('d-none');
                    scannerContainer.style.display = 'block';
                    startScanner();
                })
                .catch(function(err) {
                    console.error("Camera permission error:", err);
                    alert('Tidak dapat mengakses kamera. Silakan izinkan akses kamera dan coba lagi.');
                });
        });
        
        // Refresh camera button
        document.getElementById('refreshCamera').addEventListener('click', function() {
            if (window.scanner) {
                window.scanner.stop().then(() => {
                    console.log("Scanner stopped, restarting...");
                    setTimeout(() => {
                        startScanner();
                    }, 500);
                }).catch(err => {
                    console.error("Error stopping scanner:", err);
                    // Force refresh if can't stop scanner
                    location.reload();
                });
            } else {
                startScanner();
            }
        });
        
        // Start selfie capture
        function startSelfieCapture() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(function(stream) {
                    currentStream = stream;
                    selfieVideo.srcObject = stream;
                })
                .catch(function(error) {
                    console.error('Error accessing front camera:', error);
                    alert('Tidak dapat mengakses kamera depan. Silakan periksa izin kamera Anda.');
                });
        }
        
        // Capture selfie
        captureButton.addEventListener('click', function() {
            const context = selfieCanvas.getContext('2d');
            context.drawImage(selfieVideo, 0, 0, selfieCanvas.width, selfieCanvas.height);
            
            // Convert to base64
            const selfieDataUrl = selfieCanvas.toDataURL('image/jpeg');
            
            // Show preview
            selfiePreview.src = selfieDataUrl;
            selfiePreview.classList.remove('d-none');
            selfieVideo.classList.add('d-none');
            
            // Update buttons
            captureButton.classList.add('d-none');
            retakeButton.classList.remove('d-none');
            
            // Set form value
            document.getElementById('photo').value = selfieDataUrl;
            
            // Stop camera
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }
            
            // Show form
            attendanceForm.classList.remove('d-none');
        });
        
        // Retake selfie
        retakeButton.addEventListener('click', function() {
            // Hide preview, show video
            selfiePreview.classList.add('d-none');
            selfieVideo.classList.remove('d-none');
            
            // Update buttons
            retakeButton.classList.add('d-none');
            captureButton.classList.remove('d-none');
            
            // Hide form
            attendanceForm.classList.add('d-none');
            
            // Restart video
            startSelfieCapture();
        });
        
        // Submit form dengan AJAX
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Tampilkan loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
            
            const formData = new FormData(attendanceForm);
            
            fetch(attendanceForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Tampilkan pesan error
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.error}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    
                    // Reset state button
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i> Konfirmasi Absensi';
                } else if (data.success) {
                    // Tampilkan pesan sukses
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${data.success}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    
                    // Redirect ke dashboard
                    setTimeout(function() {
                        window.location.href = data.redirect || "{{ route('siswa.dashboard') }}";
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Terjadi kesalahan, silakan coba lagi.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                // Reset state button
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i> Konfirmasi Absensi';
            });
        });
        
        // Cancel attendance
        cancelButton.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin membatalkan absensi?')) {
                window.location.reload();
            }
        });
    });
</script>
@endpush 