<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    public function index()
    {
        $dokters = User::where('role', 'dokter')->with('poli')->withCount('jadwalPeriksas')->orderBy('nama')->get();
        return view('admin.dokter.index', compact('dokters'));
    }

    public function create()
    {
        $polis = Poli::orderBy('nama_poli')->get();
        return view('admin.dokter.create', compact('polis'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['role'] = 'dokter';
        User::create($validated);
        return redirect()->route('dokter.index')->with('success', 'Akun dokter berhasil ditambahkan.');
    }

    public function edit(User $dokter)
    {
        abort_unless($dokter->role === 'dokter', 404);
        $polis = Poli::orderBy('nama_poli')->get();
        return view('admin.dokter.edit', compact('dokter', 'polis'));
    }

    public function update(Request $request, User $dokter)
    {
        abort_unless($dokter->role === 'dokter', 404);
        $validated = $this->validated($request, $dokter);
        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }
        $dokter->update($validated);
        return redirect()->route('dokter.index')->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(User $dokter)
    {
        abort_unless($dokter->role === 'dokter', 404);
        if ($dokter->jadwalPeriksas()->exists()) {
            return back()->with('error', 'Dokter ini masih memiliki jadwal atau riwayat layanan sehingga tidak dapat dihapus.');
        }
        $dokter->delete();
        return redirect()->route('dokter.index')->with('success', 'Akun dokter berhasil dihapus.');
    }

    private function validated(Request $request, ?User $dokter = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($dokter?->id)],
            'no_hp' => 'nullable|string|max:30',
            'id_poli' => 'nullable|exists:poli,id',
            'password' => [$dokter ? 'nullable' : 'required', 'string', 'min:8'],
        ]);
    }
}
