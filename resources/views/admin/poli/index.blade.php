<x-layouts.app title="Manajemen Poli">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-indigo-600">Administrasi klinik</p>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Manajemen Poli</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Kelola daftar poli yang tersedia dan informasi singkat setiap layanan.</p>
        </div>
        <a href="{{ route('poli.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <i class="fas fa-plus text-xs"></i> Tambah Poli
        </a>
    </div>

    <section class="mb-6 overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-br from-white to-indigo-50/70 shadow-sm">
        <div class="flex items-start gap-4 p-5 sm:p-6">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"><i class="fas fa-circle-info"></i></div>
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-slate-800">Struktur tabel poli</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">Tabel <code class="rounded bg-white px-1.5 py-0.5 font-semibold text-indigo-700">poli</code> menyimpan identitas dan keterangan layanan klinik.</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-slate-100 bg-white/90 p-3"><span class="text-xs font-bold uppercase tracking-wider text-slate-400">id</span><p class="mt-1 text-sm text-slate-700">Kunci utama otomatis.</p></div>
                    <div class="rounded-xl border border-slate-100 bg-white/90 p-3"><span class="text-xs font-bold uppercase tracking-wider text-slate-400">nama_poli</span><p class="mt-1 text-sm text-slate-700">Nama poli, wajib, maksimal 25 karakter.</p></div>
                    <div class="rounded-xl border border-slate-100 bg-white/90 p-3"><span class="text-xs font-bold uppercase tracking-wider text-slate-400">keterangan</span><p class="mt-1 text-sm text-slate-700">Deskripsi tambahan, boleh dikosongkan.</p></div>
                    <div class="rounded-xl border border-slate-100 bg-white/90 p-3"><span class="text-xs font-bold uppercase tracking-wider text-slate-400">timestamps</span><p class="mt-1 text-sm text-slate-700">Waktu data dibuat dan diperbarui.</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
            <div><h2 class="font-bold text-slate-800">Daftar poli</h2><p class="mt-0.5 text-xs text-slate-500">{{ $polis->count() }} poli terdaftar</p></div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700"><i class="fas fa-hospital mr-1"></i> Layanan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-4 font-bold">Poli</th><th class="px-6 py-4 font-bold">Keterangan</th><th class="px-6 py-4 font-bold">Dokter</th><th class="px-6 py-4 text-right font-bold">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($polis as $poli)
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-6 py-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fas fa-house-medical"></i></span><div><p class="font-bold text-slate-800">{{ $poli->nama_poli }}</p><p class="text-xs text-slate-400">ID #{{ $poli->id }}</p></div></div></td>
                        <td class="max-w-md px-6 py-4 leading-6">{{ $poli->keterangan ?: 'Belum ada keterangan' }}</td>
                        <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $poli->dokters_count }} dokter</span></td>
                        <td class="px-6 py-4"><div class="flex justify-end gap-2"><a aria-label="Ubah {{ $poli->nama_poli }}" href="{{ route('poli.edit', $poli) }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100"><i class="fas fa-pen"></i><span>Ubah</span></a><form action="{{ route('poli.destroy', $poli) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus poli ini? Data poli yang masih digunakan dokter tidak dapat dihapus.')">@csrf @method('DELETE')<button aria-label="Hapus {{ $poli->nama_poli }}" type="submit" class="inline-flex items-center gap-2 rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100"><i class="fas fa-trash"></i><span>Hapus</span></button></form></div></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center"><span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-hospital"></i></span><p class="font-bold text-slate-700">Belum ada data poli</p><p class="mt-1 text-sm text-slate-500">Tambahkan poli pertama untuk mulai mengatur layanan klinik.</p><a href="{{ route('poli.create') }}" class="mt-4 inline-flex items-center gap-2 font-bold text-indigo-700 hover:text-indigo-900"><i class="fas fa-plus"></i> Tambah poli</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
