<?php
namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JadwalPeriksaController extends Controller
{
    public function index()
    {
        $dokter = Auth::user();

        $jadwalPeriksas = JadwalPeriksa::where(
            'id_dokter',
            $dokter->id
        )->get()->sortBy(fn ($jadwal) => array_search($jadwal->hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'], true))->values();

        return view(
            'dokter.jadwal-periksa.index',
            compact('jadwalPeriksas')
        );
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSchedule($request);

        JadwalPeriksa::create([
            'id_dokter' => Auth::id(),
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal praktik berhasil disimpan.');
    }

    public function edit($id)
    {
        $jadwalPeriksa = Auth::user()->jadwalPeriksas()->findOrFail($id);

        return view(
            'dokter.jadwal-periksa.edit',
            compact('jadwalPeriksa')
        );
    }

    public function update(Request $request, string $id)
    {
        $jadwalPeriksa = Auth::user()->jadwalPeriksas()->findOrFail($id);
        if ($jadwalPeriksa->daftarPolis()->exists()) {
            return back()->with('error', 'Jadwal ini sudah terkait pendaftaran pasien sehingga tidak dapat diubah. Tambahkan jadwal baru untuk waktu praktik berbeda.');
        }
        $validated = $this->validateSchedule($request, (int) $id);

        $jadwalPeriksa->update([
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal praktik berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $jadwalPeriksa = Auth::user()->jadwalPeriksas()->findOrFail($id);

        if ($jadwalPeriksa->daftarPolis()->exists()) {
            return back()->with('error', 'Jadwal ini sudah memiliki pendaftaran atau riwayat pasien sehingga tidak dapat dihapus.');
        }

        $jadwalPeriksa->delete();

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal praktik berhasil dihapus.');
    }

    private function validateSchedule(Request $request, ?int $ignoreId = null): array
    {
        $validator = Validator::make($request->all(), [
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);
        $validator->validate();
        $validated = $validator->validated();

        $overlap = Auth::user()->jadwalPeriksas()
            ->where('hari', $validated['hari'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai']);
        if ($ignoreId) {
            $overlap->where('id', '!=', $ignoreId);
        }
        if ($overlap->exists()) {
            throw ValidationException::withMessages(['hari' => 'Jadwal pada hari dan jam tersebut bertabrakan dengan jadwal lain.']);
        }

        return $validated;
    }
}
