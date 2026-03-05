{{-- MODAL DETAIL BOOK --}}
<div x-show="openDetailBookModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" style="display: none;"
    @keydown.escape.window="openDetailBookModal = false">

    {{-- MODAL WRAPPER (Dikecilkan ke max-w-3xl) --}}
    <div @click.away="openDetailBookModal = false"
        class="relative w-full max-w-3xl bg-white rounded-3xl p-6 shadow-2xl overflow-hidden">

        {{-- HEADER RINGKAS --}}
        <div class="flex items-center justify-between mb-5 border-b pb-3">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-info-circle text-teal-600"></i> Informasi Produk
            </h3>
            <button @click="openDetailBookModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            {{-- KIRI: FOTO (Lebih kecil) --}}
            <div class="w-full md:w-1/3 flex flex-col items-center">
                <img src="{{ asset('storage/' . $book->photos_product) }}"
                    class="w-full aspect-[3/4] object-cover rounded-2xl shadow-md border border-gray-100">

                <div class="mt-4 text-center">
                    <span
                        class="px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-[10px] font-bold uppercase tracking-wider border border-teal-100">
                        {{ $book->category->title ?? 'Umum' }}
                    </span>
                    <p class="mt-2 text-[10px] text-gray-400 font-medium italic">Dibuat:
                        {{ $book->created_at->format('d M Y') }}</p>
                </div>
            </div>

            {{-- KANAN: DETAIL (Grid lebih rapat) --}}
            <div class="w-full md:w-2/3 space-y-4">
                <div>
                    <h2 class="text-xl font-black text-gray-800 leading-tight">{{ $book->title }}</h2>
                </div>

                {{-- STATS GRID (Dibuat 2 kolom kecil) --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- Stok --}}
                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Stok</p>
                            <p class="text-xs font-bold text-gray-800">{{ $book->stock }} {{ $book->unit }}</p>
                        </div>
                    </div>

                    {{-- Margin --}}
                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-sm">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Margin</p>
                            <p class="text-xs font-bold text-gray-800">{{ number_format($book->margin, 1) }}%</p>
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-sm">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Modal</p>
                            <p class="text-xs font-bold text-gray-800">
                                Rp{{ number_format($book->capital, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Jual --}}
                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-sm">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">Jual</p>
                            <p class="text-xs font-bold text-gray-800">Rp{{ number_format($book->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Profit Box --}}
                <div
                    class="p-3 rounded-xl bg-teal-600 text-white shadow-lg shadow-teal-100 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-bold opacity-80 uppercase">Total Keuntungan Terjual</p>
                        @php
                            $totalProfitBuku = $book
                                ->item()
                                ->whereIn('status', ['approved', 'shipping', 'selesai'])
                                ->sum('profit');
                        @endphp
                        <p class="text-lg font-black">Rp{{ number_format($totalProfitBuku, 0, ',', '.') }}</p>
                    </div>
                    <i class="bi bi-graph-up-arrow text-2xl opacity-30"></i>
                </div>

                {{-- Description --}}
                <div class="bg-gray-50 p-3 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Deskripsi</p>
                    <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">
                        {{ $book->description ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" @click="openDetailBookModal = false"
                class="px-5 py-2 text-xs font-bold rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition active:scale-95">
                Tutup Detail
            </button>
        </div>
    </div>
</div>
