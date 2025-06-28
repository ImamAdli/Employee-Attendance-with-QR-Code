<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:siswa');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $today = date('Y-m-d');
        
        // Inisialisasi variabel
        $checkIn = null;
        $checkOut = null;
        $attendances = collect([]);
        
        if ($siswa) {
            // Cek absensi hari ini
            $absensiHariIni = Absensi::where('siswa_id', $siswa->id)
                ->where('tanggal', $today)
                ->first();
            
            // Atur nilai checkIn dan checkOut berdasarkan absensi hari ini
            if ($absensiHariIni) {
                if ($absensiHariIni->jam_masuk) {
                    $checkIn = (object) [
                        'created_at' => $absensiHariIni->jam_masuk
                    ];
                }
                
                if ($absensiHariIni->jam_keluar) {
                    $checkOut = (object) [
                        'created_at' => $absensiHariIni->jam_keluar
                    ];
                }
            }
            
            // Ambil riwayat absensi terbaru
            $riwayatAbsensi = Absensi::where('siswa_id', $siswa->id)
                ->orderBy('tanggal', 'desc')
                ->take(7)
                ->get();
                
            // Konversi riwayatAbsensi menjadi format yang diharapkan view
            foreach ($riwayatAbsensi as $absensi) {
                // Tambahkan record check_in jika ada jam masuk
                if ($absensi->jam_masuk) {
                    $attendances->push((object) [
                        'created_at' => $absensi->jam_masuk,
                        'type' => 'check_in',
                        'photo' => $absensi->foto_masuk,
                        'tanggal' => $absensi->tanggal
                    ]);
                }
                
                // Tambahkan record check_out jika ada jam keluar
                if ($absensi->jam_keluar) {
                    $attendances->push((object) [
                        'created_at' => $absensi->jam_keluar,
                        'type' => 'check_out',
                        'photo' => $absensi->foto_keluar,
                        'tanggal' => $absensi->tanggal
                    ]);
                }
            }
            
            // Urutkan attendances berdasarkan created_at terbaru
            $attendances = $attendances->sortByDesc('created_at')->take(5);
        }
        
        return view('absen.dasboard_siswa', compact('user', 'siswa', 'checkIn', 'checkOut', 'attendances'));
    }

    public function scanQR()
    {
        return view('absen.siswa_qr');
    }

    public function processAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $qrCode = $request->qr_code;
        
        // Validasi QR Code dari database
        $validQrCode = QrCode::active()->where('kode', $qrCode)->first();
        
        if (!$validQrCode) {
            if ($request->ajax()) {
                return response()->json(['error' => 'QR Code tidak valid'], 400);
            }
            return redirect()->back()->with('error', 'QR Code tidak valid');
        }

        $user = Auth::user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Data siswa tidak ditemukan'], 400);
            }
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
        }
        
        $today = date('Y-m-d');
        $now = date('H:i:s');
        
        // Tentukan apakah ini absen masuk atau keluar berdasarkan tipe QR
        $isCheckIn = ($validQrCode->tipe == 'masuk');
        
        // Cari absensi hari ini
        $absensi = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();
        
        // Validasi absensi
        if ($isCheckIn) {
            // Jika absen masuk, cek apakah sudah absen masuk hari ini
            if ($absensi && $absensi->jam_masuk) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda sudah melakukan absensi masuk hari ini'], 400);
                }
                return redirect()->back()->with('error', 'Anda sudah melakukan absensi masuk hari ini');
            }
        } else {
            // Jika absen keluar, cek apakah sudah absen masuk dan belum absen keluar
            if (!$absensi || !$absensi->jam_masuk) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda belum melakukan absensi masuk hari ini'], 400);
                }
                return redirect()->back()->with('error', 'Anda belum melakukan absensi masuk hari ini');
            }
            
            if ($absensi->jam_keluar) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda sudah melakukan absensi keluar hari ini'], 400);
                }
                return redirect()->back()->with('error', 'Anda sudah melakukan absensi keluar hari ini');
            }
        }

        // Save the photo
        $image = $request->photo;
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $siswa->id . '_' . time() . '.jpg';
        $photoPath = 'absensi_photos/' . $imageName;
        
        Storage::disk('public')->put($photoPath, base64_decode($image));

        // Simpan data absensi
        if (!$absensi) {
            // Buat absensi baru jika belum ada
            $absensi = new Absensi();
            $absensi->siswa_id = $siswa->id;
            $absensi->tanggal = $today;
        }
        
        // Update data absensi sesuai tipe (masuk/keluar)
        if ($isCheckIn) {
            $absensi->jam_masuk = $now;
            $absensi->foto_masuk = $photoPath;
            $absensi->lokasi_masuk = $request->latitude . ',' . $request->longitude;
        } else {
            $absensi->jam_keluar = $now;
            $absensi->foto_keluar = $photoPath;
            $absensi->lokasi_keluar = $request->latitude . ',' . $request->longitude;
        }
        
        // Simpan referensi QR code yang digunakan
        $absensi->qr_code_id = $validQrCode->id;
        
        $absensi->save();

        $successMessage = 'Absensi ' . ($isCheckIn ? 'masuk' : 'keluar') . ' berhasil direkam';
        
        if ($request->ajax()) {
            return response()->json([
                'success' => $successMessage,
                'redirect' => route('siswa.dashboard')
            ], 200);
        }
        
        return redirect()->route('siswa.dashboard')->with('success', $successMessage);
    }

    public function attendanceHistory(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Ambil parameter filter
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $type = $request->type;
        
        // Query dasar untuk absensi
        $query = Absensi::where('siswa_id', $siswa->id);
        
        // Terapkan filter jika ada
        if ($fromDate) {
            $query->where('tanggal', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->where('tanggal', '<=', $toDate);
        }
        
        // Ambil data absensi
        $absensiData = $query->orderBy('tanggal', 'desc')->get();
        
        // Inisialisasi koleksi untuk data yang akan ditampilkan
        $attendances = collect();
        
        // Transformasi data absensi menjadi format yang diharapkan view
        foreach ($absensiData as $absensi) {
            // Tambahkan record check_in jika ada jam masuk dan (tidak ada filter tipe atau filter tipe adalah check_in)
            if ($absensi->jam_masuk && (!$type || $type == 'check_in')) {
                $attendances->push((object) [
                    'id' => $absensi->id,
                    'created_at' => $absensi->jam_masuk,
                    'type' => 'check_in',
                    'photo' => $absensi->foto_masuk,
                    'tanggal' => $absensi->tanggal
                ]);
            }
            
            // Tambahkan record check_out jika ada jam keluar dan (tidak ada filter tipe atau filter tipe adalah check_out)
            if ($absensi->jam_keluar && (!$type || $type == 'check_out')) {
                $attendances->push((object) [
                    'id' => $absensi->id,
                    'created_at' => $absensi->jam_keluar,
                    'type' => 'check_out',
                    'photo' => $absensi->foto_keluar,
                    'tanggal' => $absensi->tanggal
                ]);
            }
        }
        
        // Urutkan berdasarkan created_at terbaru
        $attendances = $attendances->sortByDesc('created_at');
        
        // Ambil statistik untuk ringkasan
        // Total hari kehadiran (unik) berdasarkan tanggal
        $totalDays = $absensiData->unique('tanggal')->count();
        
        // Hitung tepat waktu dan terlambat
        $onTime = 0;
        $late = 0;
        
        foreach ($absensiData as $absensi) {
            if ($absensi->jam_masuk) {
                $jamMasuk = Carbon::parse($absensi->jam_masuk);
                if ($jamMasuk->format('H:i') <= '08:00') {
                    $onTime++;
                } else {
                    $late++;
                }
            }
        }
        
        // Hitung total hari kerja dalam rentang waktu
        $startDate = $fromDate ? Carbon::parse($fromDate) : Carbon::parse($siswa->tanggal_mulai);
        $endDate = $toDate ? Carbon::parse($toDate) : Carbon::now();
        
        // Hanya ambil hari kerja (Senin-Jumat) dalam rentang tanggal
        $totalWorkDays = 0;
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) { // Senin-Jumat
                $totalWorkDays++;
            }
        }
        
        // Hitung total tidak hadir (total hari kerja - total hari hadir)
        $absent = max(0, $totalWorkDays - $totalDays);
        
        // Pagination manual
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $pagedData = $attendances->forPage($currentPage, $perPage);
        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $attendances->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('absen.riwayatabsen_siswa', compact(
            'attendances', 
            'totalDays', 
            'onTime', 
            'late', 
            'absent'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        return view('absen.profile', compact('user', 'siswa'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'asal_sekolah' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update user data
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        // Update or create siswa data
        $siswa = $user->siswa;
        if (!$siswa) {
            $siswa = new Siswa();
            $siswa->user_id = $user->id;
        }
        
        $siswa->nama_lengkap = $request->nama_lengkap;
        $siswa->jenis_kelamin = $request->jenis_kelamin;
        $siswa->asal_sekolah = $request->asal_sekolah;
        $siswa->jurusan = $request->jurusan;
        $siswa->no_hp = $request->no_hp;
        $siswa->nis = $request->nis;
        $siswa->tanggal_mulai = $request->tanggal_mulai;
        $siswa->tanggal_selesai = $request->tanggal_selesai;
        
        // Handle foto profile upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo if exists
            if ($siswa->foto_profil) {
                Storage::disk('public')->delete($siswa->foto_profil);
            }
            $siswa->foto_profil = $request->file('foto_profil')->store('foto_profil', 'public');
        }
        
        $siswa->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }
}
