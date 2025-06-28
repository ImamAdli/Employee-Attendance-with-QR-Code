<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\QrCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tanggal;
    protected $siswa_id;
    protected $tipe;
    protected $qrCodes;

    public function __construct($tanggal = null, $siswa_id = null, $tipe = null)
    {
        $this->tanggal = $tanggal;
        $this->siswa_id = $siswa_id;
        $this->tipe = $tipe;
        // Ambil semua QR codes sekali saja untuk digunakan nantinya
        $this->qrCodes = QrCode::all();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Absensi::query()
            ->with(['siswa.user', 'qrCode'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter berdasarkan tanggal
        if (!empty($this->tanggal)) {
            $query->whereDate('tanggal', $this->tanggal);
        }
        
        // Filter berdasarkan siswa
        if (!empty($this->siswa_id)) {
            $query->where('siswa_id', $this->siswa_id);
        }
        
        // Filter berdasarkan tipe (masuk/keluar)
        if (!empty($this->tipe) && in_array($this->tipe, ['masuk', 'keluar'])) {
            if ($this->tipe === 'masuk') {
                $query->whereNotNull('jam_masuk');
            } else {
                $query->whereNotNull('jam_keluar');
            }
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Siswa',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'QR Code',
        ];
    }

    public function map($absensi): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $absensi->siswa->nama_lengkap ?? $absensi->siswa->user->name ?? '-',
            \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y'),
            !empty($absensi->jam_masuk) ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '-',
            !empty($absensi->jam_keluar) ? \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i') : '-',
            $absensi->qrCode ? $absensi->qrCode->kode : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header baris pertama
            1 => ['font' => ['bold' => true]],
        ];
    }
} 