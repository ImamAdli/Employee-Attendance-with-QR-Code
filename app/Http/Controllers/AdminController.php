<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\QrCode as QrCodeModel;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        date_default_timezone_set('Asia/Jakarta');
        $pendingUsers = User::where('role', 'siswa')->where('status', 'pending')->count();
        $activeUsers = User::where('role', 'siswa')->where('status', 'active')->count();
        $todayPresent = \App\Models\Absensi::whereDate('tanggal', date('Y-m-d'))
            ->whereNotNull('jam_masuk')
            ->count();
        
        return view('absen.dasboard_admin', compact('pendingUsers', 'activeUsers', 'todayPresent'));
    }

    public function pendingUsers()
    {
        $pendingUsers = \App\Models\User::where('role', 'siswa')->where('status', 'pending')->get();
        $siswaList = \App\Models\Siswa::whereIn('user_id', $pendingUsers->pluck('id'))->get()->keyBy('user_id');
        return view('absen.pending_users', compact('pendingUsers', 'siswaList'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return redirect()->back()->with('success', 'Pengguna berhasil diaktifkan');
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil ditolak');
    }

    public function allUsers()
    {
        $users = User::where('role', 'siswa')->get();
        return view('absen.all_users', compact('users'));
    }

    /**
     * Menampilkan daftar siswa
     */
    public function siswaIndex()
    {
        $users = User::where('role', 'siswa')->get();
        return view('absen.all_users', compact('users'));
    }

    public function absensiIndex()
    {
        $absensi = \App\Models\Absensi::with('siswa.user')->orderBy('tanggal', 'desc')->get();
        return view('absen.absensi_index', compact('absensi'));
    }

    public function qrcodeIndex(Request $request)
    {
        // Generate QR code jika tombol generate ditekan
        if ($request->has('generate')) {
            $kode = strtoupper(uniqid('QR'));
            
            // Tentukan tipe QR code (masuk atau keluar) berdasarkan waktu saat ini
            // Sebelum jam 12 siang = masuk, setelah jam 12 siang = keluar
            $tipe = (date('H') < 12) ? 'masuk' : 'keluar';
            
            // Atau jika admin menentukan tipe secara manual
            if ($request->has('tipe') && in_array($request->tipe, ['masuk', 'keluar'])) {
                $tipe = $request->tipe;
            }
            
            $now = now();
            $berlaku_mulai = $now;
            $berlaku_sampai = $now->copy()->addHours(12); // QR berlaku 12 jam
            
            // Nonaktifkan QR lama dengan tipe yang sama
            \App\Models\QrCode::where('is_active', 1)
                ->where('tipe', $tipe)
                ->update(['is_active' => 0]);
                
            // Simpan QR baru
            $qr = \App\Models\QrCode::create([
                'kode' => $kode,
                'tipe' => $tipe,
                'berlaku_mulai' => $berlaku_mulai,
                'berlaku_sampai' => $berlaku_sampai,
                'is_active' => 1,
            ]);
        }
        
        // Mengaktifkan QR code yang dipilih
        if ($request->has('activate') && $request->activate) {
            $qrCodeId = $request->activate;
            $qrCode = \App\Models\QrCode::findOrFail($qrCodeId);
            
            // Nonaktifkan QR lama dengan tipe yang sama
            \App\Models\QrCode::where('is_active', 1)
                ->where('tipe', $qrCode->tipe)
                ->where('id', '!=', $qrCodeId)
                ->update(['is_active' => 0]);
                
            // Aktifkan QR code yang dipilih
            $qrCode->is_active = 1;
            $qrCode->berlaku_mulai = now();
            $qrCode->berlaku_sampai = now()->addHours(12);
            $qrCode->save();
            
            return redirect()->route('absen.qrcode.index')->with('success', 'QR Code berhasil diaktifkan');
        }
        
        // Menonaktifkan QR code
        if ($request->has('deactivate') && $request->deactivate) {
            $qrCodeId = $request->deactivate;
            $qrCode = \App\Models\QrCode::findOrFail($qrCodeId);
            $qrCode->is_active = 0;
            $qrCode->save();
            
            return redirect()->route('absen.qrcode.index')->with('success', 'QR Code berhasil dinonaktifkan');
        }
        
        // Jika ada ID QR code yang dipilih untuk ditampilkan
        $selectedQrCode = null;
        if ($request->has('show') && $request->show) {
            $selectedQrCode = \App\Models\QrCode::findOrFail($request->show);
            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($selectedQrCode->kode);
            $qrTerbaru = $selectedQrCode;
        } else {
            // Ambil QR code terbaru yang aktif
            $qrTerbaru = \App\Models\QrCode::where('is_active', 1)->orderByDesc('created_at')->first();
            $qrCodeSvg = $qrTerbaru ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrTerbaru->kode) : null;
        }
        
        // Ambil QR code aktif untuk tipe masuk dan keluar
        $activeQrMasuk = \App\Models\QrCode::where('is_active', 1)->where('tipe', 'masuk')->orderByDesc('created_at')->first();
        $activeQrKeluar = \App\Models\QrCode::where('is_active', 1)->where('tipe', 'keluar')->orderByDesc('created_at')->first();
        
        // Riwayat QR code (tampilkan lebih banyak)
        $riwayat = \App\Models\QrCode::orderByDesc('created_at')->paginate(15);
        
        return view('absen.qrcode_index', compact('qrCodeSvg', 'qrTerbaru', 'riwayat', 'activeQrMasuk', 'activeQrKeluar', 'selectedQrCode'));
    }


    /**
     * Menampilkan riwayat scan QR code oleh siswa
     */
    public function scanHistory(Request $request)
    {
        $query = \App\Models\Absensi::query()
            ->with(['siswa.user', 'qrCode'])
            ->whereNotNull('qr_code_id')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        // Filter berdasarkan siswa
        if ($request->has('siswa_id') && !empty($request->siswa_id)) {
            $query->where('siswa_id', $request->siswa_id);
        }
        
        // Filter berdasarkan tipe (masuk/keluar)
        if ($request->has('tipe') && in_array($request->tipe, ['masuk', 'keluar'])) {
            if ($request->tipe === 'masuk') {
                $query->whereNotNull('jam_masuk');
            } else {
                $query->whereNotNull('jam_keluar');
            }
        }
        
        $absensi = $query->paginate(20);
        
        // Ambil data QR code untuk masuk dan keluar
        $qrCodes = \App\Models\QrCode::orderBy('created_at', 'desc')->get();
        
        // Cek apakah foto absensi ada
        foreach ($absensi as $item) {
            if ($item->foto_masuk) {
                $path = 'public/' . $item->foto_masuk;
                $item->foto_masuk_exists = \Illuminate\Support\Facades\Storage::exists($path);
                $item->foto_masuk_url = route('absen.view.image', ['id' => $item->id, 'type' => 'masuk']);
            }
            if ($item->foto_keluar) {
                $path = 'public/' . $item->foto_keluar;
                $item->foto_keluar_exists = \Illuminate\Support\Facades\Storage::exists($path);
                $item->foto_keluar_url = route('absen.view.image', ['id' => $item->id, 'type' => 'keluar']);
            }
            
            // Mencari QR code yang sesuai untuk masuk dan keluar berdasarkan tanggal
            $tanggal = \Carbon\Carbon::parse($item->tanggal);
            
            // QR code untuk masuk (cari yang tipe=masuk dan tanggal berlaku sesuai)
            $item->qrcode_masuk = $qrCodes->where('tipe', 'masuk')
                ->where('created_at', '<=', $item->jam_masuk ?? $tanggal->endOfDay())
                ->sortByDesc('created_at')
                ->first();
                
            // QR code untuk keluar (cari yang tipe=keluar dan tanggal berlaku sesuai)
            $item->qrcode_keluar = $qrCodes->where('tipe', 'keluar')
                ->where('created_at', '<=', $item->jam_keluar ?? $tanggal->endOfDay())
                ->sortByDesc('created_at')
                ->first();
        }
        
        // Data untuk filter dropdown
        $siswaList = \App\Models\Siswa::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            })
            ->get();
        
        return view('absen.riwayat_qr', compact('absensi', 'siswaList'));
    }
    
    /**
     * Menampilkan foto absensi
     */
    public function viewImage($id, $type)
    {
        $absensi = \App\Models\Absensi::findOrFail($id);
        
        $path = null;
        if ($type === 'masuk' && $absensi->foto_masuk) {
            $path = $absensi->foto_masuk;
        } elseif ($type === 'keluar' && $absensi->foto_keluar) {
            $path = $absensi->foto_keluar;
        } else {
            abort(404);
        }
        
        return response()->file(storage_path('app/public/' . $path));
    }
    
    /**
     * Memperbarui status siswa (aktif/nonaktif)
     */
    public function updateSiswaStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $status = $request->status;
        
        // Update status user
        $user->status = $status;
        $user->save();
        
        $statusText = ($status == 'active') ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('absen.siswa.index')
            ->with('success', "Siswa berhasil $statusText");
    }

    /**
     * Export data absensi ke Excel
     */
    public function exportAbsensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $siswa_id = $request->siswa_id;
        $tipe = $request->tipe;
        
        $filename = 'Laporan_Absensi';
        
        // Tambahkan tanggal ke nama file jika ada filter tanggal
        if (!empty($tanggal)) {
            $date = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
            $filename .= "_Tanggal_{$date}";
        }
        
        // Tambahkan siswa ke nama file jika ada filter siswa
        if (!empty($siswa_id)) {
            $siswa = \App\Models\Siswa::find($siswa_id);
            $namaSiswa = $siswa ? ($siswa->nama_lengkap ?? $siswa->user->name) : '';
            if (!empty($namaSiswa)) {
                $filename .= "_Siswa_{$namaSiswa}";
            }
        }
        
        // Tambahkan tipe ke nama file jika ada filter tipe
        if (!empty($tipe)) {
            $filename .= "_Tipe_{$tipe}";
        }
        
        $filename .= '.xlsx';
        
        return Excel::download(new AbsensiExport($tanggal, $siswa_id, $tipe), $filename);
    }
}
