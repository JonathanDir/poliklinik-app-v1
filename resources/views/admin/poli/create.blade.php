<x-layouts.app title="Tambah Poli">
    <div class="mb-6 flex items-center gap-3">
        <a aria-label="Kembali ke daftar poli" href="{{ route('poli.index') }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50"><i class="fas fa-arrow-left"></i></a>
        <div><p class="text-xs font-bold uppercase tracking-[.16em] text-indigo-600">Manajemen Poli</p><h1 class="mt-1 text-2xl font-extrabold text-slate-800">Tambah Poli</h1></div>
    </div>
    <section class="max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5"><h2 class="font-bold text-slate-800">Informasi poli</h2><p class="mt-1 text-sm text-slate-500">Isi nama layanan dan keterangan yang akan ditampilkan di daftar poli.</p></div>
        <form action="{{ route('poli.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf
            <div class="mb-6">
                <label for="nama_poli" class="mb-2 block text-sm font-bold text-slate-700">Nama poli <span class="text-rose-500">*</span></label>
                <input id="nama_poli" name="nama_poli" type="text" maxlength="25" required autofocus value="{{ old('nama_poli') }}" placeholder="Contoh: Poli Umum" class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50 @error('nama_poli') border-rose-400 @else border-slate-200 @enderror">
                <div class="mt-2 flex justify-between gap-3"><p class="text-xs text-slate-500">Nama poli maksimal 25 karakter.</p><span id="nama-count" class="text-xs text-slate-400">0/25</span></div>
                @error('nama_poli')<p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="mb-8">
                <label for="keterangan" class="mb-2 block text-sm font-bold text-slate-700">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea id="keterangan" name="keterangan" rows="5" maxlength="5000" placeholder="Jelaskan layanan atau informasi tambahan tentang poli ini..." class="w-full resize-y rounded-xl border px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50 @error('keterangan') border-rose-400 @else border-slate-200 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')<p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('poli.index') }}" class="inline-flex justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">Batal</a>
                <button type="submit" class="inline-flex justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90 focus:outline-none focus:ring-4 focus:ring-indigo-100"><i class="fas fa-check mt-0.5"></i> Simpan Poli</button>
            </div>
        </form>
    </section>
</x-layouts.app>
@push('scripts')
<script>
    const poliNameInput = document.getElementById('nama_poli');
    const poliNameCount = document.getElementById('nama-count');
    const updatePoliNameCount = () => poliNameCount.textContent = `${poliNameInput.value.length}/25`;
    poliNameInput.addEventListener('input', updatePoliNameCount);
    updatePoliNameCount();
</script>
@endpush
