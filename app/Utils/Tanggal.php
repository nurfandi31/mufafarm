<?php

namespace App\Utils;

use Carbon\Carbon;
use App\Utils\Keuangan; // pastikan class Keuangan ada

class Tanggal
{
    // Format tanggal Indonesia
    public function tglIndo($tanggal, $format = 'DD/MM/YYYY')
    {
        if ($tanggal != '') {
            try {
                $tgl = Carbon::parse($tanggal);
                return $tgl->isoFormat($format);
            } catch (\Exception $e) {
                return date('d/m/Y');
            }
        }
        return date('d/m/Y');
    }

    // Format tanggal nasional Y-m-d
    public function tglNasional($tanggal)
    {
        try {
            $tgl = Carbon::createFromFormat('d/m/Y', $tanggal);
            return $tgl->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // Format bulan/tanggal romawi, misal 15/07/2025 -> VII/2025
    public function tglRomawi($tanggal)
    {
        $keuangan = new Keuangan();
        try {
            $tgl = Carbon::parse($tanggal);
            $bulan_rom = $keuangan->romawi($tgl->format('m'));
            $tahun = $tgl->format('Y');
            return $bulan_rom . '/' . $tahun;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Format Latin: 15 Juli 2025
    public function tglLatin($tanggal)
    {
        try {
            $tgl = Carbon::parse($tanggal);
            $hari = $tgl->format('d');
            $bulan = $this->namaBulan($tgl->format('Y-m-d'));
            $tahun = $tgl->format('Y');
            return $hari . ' ' . $bulan . ' ' . $tahun;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function tahun($tanggal)
    {
        try {
            $tgl = Carbon::parse($tanggal);
            return $tgl->format('Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function bulan($tanggal)
    {
        try {
            $tgl = Carbon::parse($tanggal);
            return $tgl->format('m');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function namaBulan($tanggal)
    {
        $bln = $this->bulan($tanggal);
        $arrayBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $arrayBulan[$bln] ?? 'Tidak diketahui';
    }

    public function hari($tanggal)
    {
        try {
            $tgl = Carbon::parse($tanggal);
            return $tgl->format('d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function namaHari($tanggal)
    {
        try {
            $tgl = Carbon::parse($tanggal);
            $hari = $tgl->format('D'); // Sun, Mon, ...
            $arrayHari = [
                'Sun' => 'Minggu',
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu'
            ];
            return $arrayHari[$hari] ?? 'Tidak diketahui';
        } catch (\Exception $e) {
            return 'Tidak diketahui';
        }
    }
}
