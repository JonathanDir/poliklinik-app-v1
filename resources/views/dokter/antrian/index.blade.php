<x-layouts.app title="Antrian Pasien">
    <div class="mb-6">
        <p class="mb-2 text-xs font-bold uppercase tracking-[.18em] text-indigo-600">Layanan dokter</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Pendaftaran &amp; Antrian Pasien</h1>
        <p class="mt-2 text-sm text-slate-500">Tinjau pendaftaran yang masuk, terima pasien, lalu catat hasil pemeriksaan.</p>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div><h2 class="font-bold text-slate-800">Pendaftaran aktif</h2><p class="mt-1 text-sm text-slate-500">{{ $antrians->count() }} pasien menunggu atau diterima pada jadwal Anda</p></div>
            <span class="w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700"><i class="fas fa-user-clock mr-1"></i> Jadwal mendatang</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($antrians as $item)
                @php
                    $statusStyle = match($item->status) {
                        'diterima' => 'bg-emerald-50 text-emerald-700',
                        'diperiksa' => 'bg-blue-50 text-blue-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <article class="p-5 sm:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-700 text-lg font-extrabold text-white">{{ $item->no_antrian }}</span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-slate-800">{{ $item->pasien->nama }}</h3>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">NIM {{ $item->pasien->nim ?? '—' }}</span>
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusStyle }}">{{ ucfirst($item->status) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $item->tanggal_periksa?->locale('id')->translatedFormat('l, d F Y') }}
                                    · {{ substr($item->jadwalPeriksa->jam_mulai, 0, 5) }}–{{ substr($item->jadwalPeriksa->jam_selesai, 0, 5) }}
                                    @if($item->pasien->no_rm) · No. RM {{ $item->pasien->no_rm }} @endif
                                </p>
                                <div class="mt-3 max-w-2xl rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Keluhan pasien</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-700">{{ $item->keluhan }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2 xl:justify-end">
                            @if($item->status === 'menunggu')
                                <form action="{{ route('dokter.antrian.status', $item) }}" method="POST" onsubmit="return confirm('Terima pendaftaran pasien ini?')">
                                    @csrf<input type="hidden" name="status" value="diterima">
                                    <button class="rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"><i class="fas fa-check mr-1"></i> Terima / ACC</button>
                                </form>
                            @elseif($item->status === 'diterima' && $item->tanggal_periksa?->isToday())
                                <form action="{{ route('dokter.antrian.status', $item) }}" method="POST">
                                    @csrf<input type="hidden" name="status" value="diperiksa">
                                    <button class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700"><i class="fas fa-play mr-1"></i> Mulai Periksa</button>
                                </form>
                            @elseif($item->status === 'diterima')
                                <span class="rounded-lg bg-emerald-50 px-3 py-2.5 text-xs font-bold text-emerald-700"><i class="fas fa-calendar-check mr-1"></i> Siap pada hari praktik</span>
                            @elseif($item->status === 'diperiksa')
                                <details class="group">
                                    <summary class="cursor-pointer list-none rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white transition hover:bg-primary/90"><i class="fas fa-notes-medical mr-1"></i> Catat Hasil Pemeriksaan</summary>
                                    <form action="{{ route('dokter.antrian.periksa', $item) }}" method="POST" class="mt-3 w-full min-w-[min(90vw,420px)] rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                                        @csrf
                                        <label class="mb-1 block text-xs font-bold text-slate-700">Catatan pemeriksaan <span class="text-rose-500">*</span></label>
                                        <textarea name="catatan" rows="3" required maxlength="5000" class="mb-3 w-full rounded-lg border border-slate-200 p-2.5 text-sm" placeholder="Diagnosis dan saran tindak lanjut..."></textarea>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">Biaya pemeriksaan (Rp) <span class="text-rose-500">*</span></label>
                                        <input type="number" name="biaya_periksa" min="0" required value="0" class="mb-3 w-full rounded-lg border border-slate-200 p-2.5 text-sm">
                                        <label class="mb-2 block text-xs font-bold text-slate-700">Obat (opsional)</label>
                                        <div class="mb-4 max-h-36 space-y-2 overflow-y-auto rounded-lg bg-slate-50 p-2">
                                            @forelse($obats as $obat)
                                                <label class="flex items-center gap-2 text-xs text-slate-700"><input type="checkbox" name="obat[]" value="{{ $obat->id }}" class="checkbox checkbox-sm checkbox-primary">{{ $obat->nama_obat }} · {{ $obat->kemasan }} · Rp {{ number_format($obat->harga, 0, ',', '.') }}</label>
                                            @empty
                                                <p class="text-xs text-slate-500">Belum ada data obat.</p>
                                            @endforelse
                                        </div>
                                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">Simpan Hasil Pemeriksaan</button>
                                    </form>
                                </details>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-6 py-16 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-clipboard-check"></i></span>
                    <p class="mt-4 font-bold text-slate-700">Belum ada pendaftaran aktif</p>
                    <p class="mt-1 text-sm text-slate-500">Pendaftaran pasien akan muncul di sini sesuai jadwal praktik Anda.</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
