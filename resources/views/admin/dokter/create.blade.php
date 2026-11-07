<x-layouts.app title="Tambah Dokter">
    <div class="mb-6 flex items-center gap-3"><a href="{{ route('dokter.index') }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-slate-200"><i class="fas fa-arrow-left"></i></a><div><p class="text-xs font-bold uppercase tracking-[.16em] text-indigo-600">Data Dokter</p><h1 class="mt-1 text-2xl font-extrabold text-slate-800">Tambah Dokter</h1></div></div>
    <section class="max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-6 py-5"><h2 class="font-bold text-slate-800">Informasi akun dokter</h2><p class="mt-1 text-sm text-slate-500">Akun dapat digunakan dokter untuk masuk dan mengatur jadwal praktik.</p></div><form method="POST" action="{{ route('dokter.store') }}" class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">@csrf
        @include('admin.dokter._fields', ['dokter' => null])
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:col-span-2 sm:flex-row sm:justify-end"><a href="{{ route('dokter.index') }}" class="rounded-xl bg-slate-100 px-5 py-3 text-center text-sm font-bold text-slate-600">Batal</a><button class="rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white"><i class="fas fa-user-plus mr-1"></i> Simpan Dokter</button></div>
    </form></section>
</x-layouts.app>
