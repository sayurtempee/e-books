{{-- MODAL DETAIL BOOK --}}
<div x-show="openDetailBookModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-black/40 via-black/30 to-black/40 backdrop-blur-md p-4"
    style="display: none;" @keydown.escape.window="openDetailBookModal = false">

    {{-- MODAL WRAPPER --}}
    <div @click.away="openDetailBookModal = false"
        class="relative w-full max-w-5xl h-auto max-h-[90vh] overflow-y-auto
               bg-white/90 backdrop-blur-xl rounded-3xl p-7 md:p-8 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)]
               ring-1 ring-white/50">

        {{-- CLOSE BUTTON --}}
        <button @click="openDetailBookModal = false"
            class="absolute top-5 right-5 z-10 w-10 h-10 rounded-full bg-white/90 shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-800 hover:shadow-xl hover:scale-110 transition-all">
            <i class="bi bi-x-lg text-lg"></i>
        </button>

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200/60">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-gradient-to-br from-teal-100 to-teal-200 text-teal-700 shadow-sm">
                    <i class="bi bi-eye text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 tracking-wide">Detail Buku</h3>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="grid grid-cols-2 gap-6 text-sm text-gray-700">

            {{-- BOOK IMAGE --}}
            <div class="col-span-2 flex justify-center mb-4">
                <img src="{{ asset('storage/' . $book->photos_product) }}" alt="Foto Buku"
                    class="w-32 h-44 object-cover rounded-2xl border-4 border-white shadow-xl">
            </div>

            {{-- TITLE --}}
            <div class="col-span-2 text-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">{{ $book->title }}</h3>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full bg-teal-100 text-teal-700 text-xs font-semibold">
                    {{ $book->category->title ?? '-' }}
                </span>
            </div>

            {{-- DESCRIPTION --}}
            <div class="col-span-2 p-3 rounded-xl bg-gray-100">
                <p class="text-xs text-gray-500 font-medium mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $book->description ?? '-' }}</p>
            </div>

            {{-- STOCK --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-box-seam text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Stok</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $book->stock }} {{ $book->unit }}</p>
                </div>
            </div>

            {{-- CAPITAL --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-slate-400 to-gray-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-cash text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Harga Modal</p>
                    <p class="text-sm font-semibold text-gray-800">
                        Rp {{ number_format($book->capital, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- PRICE --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-cash-stack text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Harga Jual</p>
                    <p class="text-sm font-semibold text-gray-800">
                        Rp {{ number_format($book->price, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- MARGIN --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-fuchsia-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-percent text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Margin</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ number_format($book->margin, 2, ',', '.') }}%
                    </p>
                </div>
            </div>

            {{-- KEUNTUNGAN --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-lime-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-graph-up-arrow text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Total Keuntungan Terjual</p>
                    <p class="text-sm font-semibold text-green-600">
                        Rp {{ number_format($book->total_real_profit ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- CREATED DATE --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-sm">
                    <i class="bi bi-calendar-event text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Dibuat Pada</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $book->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>

        </div>

        {{-- ACTION --}}
        <div class="mt-6 flex justify-center">
            <button type="button" @click="openDetailBookModal = false"
                class="px-6 py-2.5 rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
