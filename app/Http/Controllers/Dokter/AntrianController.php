<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    public function index()
    {
        $antrians = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereDate('tanggal_periksa', '>=', today())
            ->whereHas('jadwalPeriksa', fn ($query) => $query->where('id_dokter', Auth::id()))
            ->whereIn('status', ['menunggu', 'diterima', 'diperiksa'])
            ->orderBy('tanggal_periksa')->orderBy('no_antrian')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        return view('dokter.antrian.index', compact('antrians', 'obats'));
    }

    public function status(Request $request, DaftarPoli $daftarPoli)
    {
        $validated = $request->validate(['status' => 'required|in:diterima,diperiksa']);
        $this->authorizeDoctor($daftarPoli);

        if ($validated['status'] === 'diterima') {
            if ($daftarPoli->status !== 'menunggu' || $daftarPoli->tanggal_periksa?->lt(today())) {
                return back()->with('error', 'Pendaftaran ini tidak lagi dapat diterima.');
            }
            $message = 'Pendaftaran pasien berhasil diterima.';
        } else {
            if ($daftarPoli->status !== 'diterima' || ! $daftarPoli->tanggal_periksa?->isToday()) {
                return back()->with('error', 'Pemeriksaan hanya dapat dimulai pada hari jadwal setelah pendaftaran diterima.');
            }
            $message = 'Pemeriksaan dimulai.';
        }

        $daftarPoli->update(['status' => $validated['status']]);
        return back()->with('success', $message);
    }

    public function periksa(Request $request, DaftarPoli $daftarPoli)
    {
        $validated = $request->validate([
            'catatan' => 'required|string|max:5000',
            'biaya_periksa' => 'required|integer|min:0',
            'obat' => 'nullable|array',
            'obat.*' => 'integer|exists:obat,id',
        ]);
        $this->authorizeDoctor($daftarPoli);
        if (! $daftarPoli->tanggal_periksa?->isToday() || $daftarPoli->status !== 'diperiksa' || $daftarPoli->periksas()->exists()) {
            return back()->with('error', 'Pemeriksaan untuk antrean ini sudah dicatat atau tidak lagi aktif.');
        }

        DB::transaction(function () use ($validated, $daftarPoli) {
            $periksa = $daftarPoli->periksas()->create([
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'],
                'biaya_periksa' => $validated['biaya_periksa'],
            ]);
            foreach ($validated['obat'] ?? [] as $obatId) {
                $periksa->detailPeriksas()->create(['id_obat' => $obatId]);
            }
            $daftarPoli->update(['status' => 'selesai']);
        });

        return back()->with('success', 'Hasil pemeriksaan berhasil disimpan.');
    }

    private function authorizeDoctor(DaftarPoli $daftarPoli): void
    {
        abort_unless($daftarPoli->jadwalPeriksa()->where('id_dokter', Auth::id())->exists(), 404);
    }
}
