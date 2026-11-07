<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    private const HARI = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public function index()
    {
        $pasien = Auth::user();
        $jadwals = JadwalPeriksa::with(['dokter.poli'])->orderBy('jam_mulai')->get();
        $slots = [];

        for ($date = today(); $date->lte(today()->copy()->addDays(20)); $date->addDay()) {
            $hari = self::HARI[$date->dayOfWeekIso];
            foreach ($jadwals->where('hari', $hari) as $jadwal) {
                if ($date->isToday() && Carbon::parse($jadwal->jam_selesai)->lte(now())) {
                    continue;
                }

                $terisi = DaftarPoli::where('id_jadwal', $jadwal->id)->whereDate('tanggal_periksa', $date)->whereIn('status', ['menunggu', 'diterima', 'diperiksa', 'selesai'])->count();
                if ($terisi >= 20) {
                    continue;
                }

                $slots[] = [
                    'value' => $jadwal->id . '|' . $date->toDateString(),
                    'tanggal' => $date->locale('id')->translatedFormat('l, d F Y'),
                    'hari' => $hari,
                    'jadwal' => $jadwal,
                    'sisa' => 20 - $terisi,
                ];
            }
        }

        $riwayat = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'periksas.detailPeriksas.obat'])
            ->where('id_pasien', $pasien->id)
            ->orderByDesc('tanggal_periksa')->orderByDesc('id')->get();

        return view('pasien.pendaftaran.index', compact('pasien', 'slots', 'riwayat'));
    }

    public function store(Request $request)
    {
        $pasien = Auth::user();
        $validated = $request->validate([
            'nim' => $pasien->nim
                ? ['required', 'string', Rule::in([$pasien->nim])]
                : ['required', 'string', 'max:30', Rule::unique('users', 'nim')],
            'slot' => 'required|string',
            'keluhan' => 'required|string|max:2000',
        ], [
            'nim.in' => 'NIM tidak sesuai dengan akun pasien yang sedang masuk.',
            'nim.unique' => 'NIM tersebut sudah terhubung ke akun lain. Hubungi petugas klinik jika ini keliru.',
        ]);
        if (blank($pasien->nim)) {
            $pasien->update(['nim' => $validated['nim']]);
        }

        [$jadwalId, $tanggal] = array_pad(explode('|', $validated['slot'], 2), 2, null);
        if (! is_string($jadwalId) || ! ctype_digit($jadwalId) || ! is_string($tanggal) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return back()->withInput()->withErrors(['slot' => 'Format jadwal tidak valid. Silakan pilih jadwal yang tersedia.']);
        }
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $tanggal);
        } catch (\Throwable) {
            $date = false;
        }
        if (! $date || $date->format('Y-m-d') !== $tanggal || $date->lt(today()) || $date->gt(today()->copy()->addDays(20))) {
            return back()->withInput()->withErrors(['slot' => 'Tanggal pendaftaran tidak valid. Silakan pilih jadwal yang tersedia.']);
        }

        $hari = self::HARI[$date->dayOfWeekIso];
        $jadwal = JadwalPeriksa::whereKey($jadwalId)->where('hari', $hari)->firstOrFail();
        if ($date->isToday() && Carbon::parse($jadwal->jam_selesai)->lte(now())) {
            return back()->withInput()->with('error', 'Jam praktik dokter untuk hari ini sudah berakhir.');
        }

        $pendaftaran = DB::transaction(function () use ($jadwal, $date, $pasien, $validated) {
            JadwalPeriksa::whereKey($jadwal->id)->lockForUpdate()->firstOrFail();
            $terdaftar = DaftarPoli::where('id_jadwal', $jadwal->id)->whereDate('tanggal_periksa', $date)->whereIn('status', ['menunggu', 'diterima', 'diperiksa', 'selesai'])->count();
            if ($terdaftar >= 20) {
                return null;
            }
            if (DaftarPoli::where('id_pasien', $pasien->id)->whereDate('tanggal_periksa', $date)->whereIn('status', ['menunggu', 'diterima', 'diperiksa'])->exists()) {
                return false;
            }

            return DaftarPoli::create([
                'id_pasien' => $pasien->id,
                'id_jadwal' => $jadwal->id,
                'tanggal_periksa' => $date->toDateString(),
                'keluhan' => $validated['keluhan'],
                'no_antrian' => (DaftarPoli::where('id_jadwal', $jadwal->id)->whereDate('tanggal_periksa', $date)->max('no_antrian') ?? 0) + 1,
                'status' => 'menunggu',
            ]);
        });

        if ($pendaftaran === null) {
            return back()->withInput()->with('error', 'Jadwal tersebut sudah penuh. Silakan pilih waktu praktik lain.');
        }
        if ($pendaftaran === false) {
            return back()->withInput()->with('error', 'Anda sudah memiliki pendaftaran aktif pada tanggal tersebut.');
        }

        return redirect()->route('pendaftaran.index')->with('success', 'Pendaftaran berhasil. Nomor antrean Anda: ' . $pendaftaran->no_antrian . '.');
    }

    public function destroy(DaftarPoli $daftarPoli)
    {
        abort_unless($daftarPoli->id_pasien === Auth::id(), 404);
        if ($daftarPoli->status !== 'menunggu' || $daftarPoli->tanggal_periksa?->isPast()) {
            return back()->with('error', 'Pendaftaran hanya dapat dibatalkan saat masih menunggu dan belum masuk tanggal periksa.');
        }

        $daftarPoli->update(['status' => 'batal']);

        return back()->with('success', 'Pendaftaran berhasil dibatalkan.');
    }
}
