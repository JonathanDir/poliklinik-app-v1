<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        $polis = Poli::withCount('dokters')->orderBy('nama_poli')->get();

        return view('admin.poli.index', compact('polis'));
    }

    public function create()
    {
        return view('admin.poli.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_poli' => 'required|string|max:25',
            'keterangan' => 'nullable|string|max:5000',
        ]);

        Poli::create($validated);

        return redirect()->route('poli.index')->with('success', 'Data poli berhasil ditambahkan.');
    }

    public function edit(Poli $poli)
    {
        return view('admin.poli.edit', compact('poli'));
    }

    public function update(Request $request, Poli $poli)
    {
        $validated = $request->validate([
            'nama_poli' => 'required|string|max:25',
            'keterangan' => 'nullable|string|max:5000',
        ]);

        $poli->update($validated);

        return redirect()->route('poli.index')->with('success', 'Data poli berhasil diperbarui.');
    }

    public function destroy(Poli $poli)
    {
        if ($poli->dokters()->exists()) {
            return redirect()->route('poli.index')->with('error', 'Poli ini masih digunakan oleh dokter dan tidak dapat dihapus.');
        }

        $poli->delete();

        return redirect()->route('poli.index')->with('success', 'Data poli berhasil dihapus.');
    }
}
