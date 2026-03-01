<x-app>
    @section('title', 'Pesanan Saya')
    @section('body-content')
        <x-sidebar>
            <div class="m-6">
                {{-- Header Section --}}
                <div class="mb-8 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-2xl p-6 border border-teal-100">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">📦 Pesanan Saya</h2>
                    <p class="text-gray-600">Pantau status dan riwayat transaksi belanja Anda</p>
                </div>

                {{-- Filter & Search Section --}}
                <div class="mb-8 bg-white p-6 rounded-2xl border border-teal-100 shadow-sm">
                    <form action="{{ route(Route::currentRouteName()) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">

                            {{-- Search Title Book --}}
                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Judul Buku</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="title" value="{{ request('title') }}"
                                        placeholder="Cari judul..."
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none transition-all text-sm">
                                </div>
                            </div>

                            {{-- Filter Tanggal Mulai --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Dari
                                    Tanggal</label>
                                <input type="text" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                    placeholder="Pilih Tanggal"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none transition-all text-sm bg-white">
                            </div>

                            {{-- Filter Tanggal Selesai --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Sampai
                                    Tanggal</label>
                                <input type="text" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                    placeholder="Pilih Tanggal"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none transition-all text-sm bg-white">
                            </div>

                            {{-- Dropdown Status --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Status</label>
                                <div class="relative">
                                    <select name="status"
                                        class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 outline-none appearance-none bg-white text-sm">
                                        <option value="">Semua Status</option>
                                        <option value="tolak" {{ request('status') == 'tolak' ? 'selected' : '' }}>Ditolak
                                        </option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                            Selesai</option>
                                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>
                                            Refunded</option>
                                    </select>
                                    <i
                                        class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-end gap-2">
                                <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-bold py-2.5 rounded-xl hover:shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                @if (request()->anyFilled(['title', 'start_date', 'end_date', 'status']))
                                    <a href="{{ route(Route::currentRouteName()) }}"
                                        class="px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all text-sm font-bold flex items-center justify-center"
                                        title="Reset Filter">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="space-y-4">
                    @forelse($purchases as $group)
                        @php
                            $firstItem = $group->first();
                            $order = $firstItem->order; // Data order utama
                            $seller = $firstItem->book->user; // Data toko
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                            {{-- Header Kartu --}}
                            <div class="p-4 bg-gradient-to-r from-gray-50 to-teal-50 border-b border-gray-100">
                                <div class="flex justify-between items-center flex-wrap gap-4 text-sm">
                                    <div class="flex flex-wrap gap-3 items-center">
                                        <span class="font-bold text-teal-700">
                                            <i class="bi bi-shop me-1"></i> {{ strtoupper($seller->name) }}
                                        </span>
                                        <span class="text-gray-400">|</span>
                                        <span class="text-gray-600">
                                            <i class="bi bi-calendar3 me-2"></i> {{ $order->created_at->format('d M Y') }}
                                        </span>
                                        <span class="font-mono text-gray-400 bg-white px-2 py-1 rounded">
                                            #ORD-{{ $order->id }}
                                        </span>
                                    </div>
                                    <span class="px-4 py-1 bg-teal-500 text-white font-bold rounded-full text-[10px]">
                                        {{ strtoupper($firstItem->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                {{-- Loop Item hanya di toko ini --}}
                                @foreach ($group as $item)
                                    <div class="flex gap-4 items-start mb-4">
                                        {{-- Gunakan pengecekan apakah book ada, lalu cek fotonya --}}
                                        @php
                                            $photo = $item->book?->photos_product;
                                            $imagePath =
                                                $photo && Storage::disk('public')->exists($photo)
                                                    ? asset('storage/' . $photo)
                                                    : asset('image/default-buku.avif' ?? 'Gambar Hilang'); // Sediakan gambar placeholder
                                        @endphp

                                        <img src="{{ $imagePath }}" alt="{{ $item->book?->title ?? 'Buku' }}"
                                            class="w-16 h-16 rounded-lg object-cover bg-gray-100">

                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800">
                                                {{ $item->book?->title ?? 'Judul Tidak Tersedia' }}
                                                @if ($item->book?->trashed())
                                                    <span
                                                        class="text-[10px] bg-red-100 text-red-600 px-1 rounded ml-1">Dihapus</span>
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Tampilkan Alasan jika Ditolak atau Refunded --}}
                                @if (in_array($firstItem->status, ['tolak', 'refunded']) && !empty($firstItem->cancel_reason))
                                    <div
                                        class="mt-4 p-4 rounded-xl border {{ $firstItem->status === 'tolak' ? 'border-red-200 bg-red-50' : 'border-orange-200 bg-orange-50' }} shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="{{ $firstItem->status === 'tolak' ? 'text-red-500' : 'text-orange-500' }} text-xl">
                                                <i
                                                    class="bi {{ $firstItem->status === 'tolak' ? 'bi-exclamation-octagon-fill' : 'bi-arrow-left-right' }}"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[10px] font-black uppercase tracking-wider {{ $firstItem->status === 'tolak' ? 'text-red-600' : 'text-orange-600' }} mb-1">
                                                    {{ $firstItem->status === 'tolak' ? 'Alasan Penolakan' : 'Informasi Refund' }}
                                                </p>
                                                <p
                                                    class="text-sm {{ $firstItem->status === 'tolak' ? 'text-red-700' : 'text-orange-700' }} leading-relaxed font-medium">
                                                    {{ $firstItem->cancel_reason }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <div>
                                        <p class="text-xs text-gray-400">Total Pesanan di Toko Ini:</p>
                                        <p class="text-xl font-black text-teal-600">
                                            Rp {{ number_format($group->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-2">
                                        {{-- Tombol Invoice --}}
                                        {{-- Gunakan $order->id atau $firstItem->order_id --}}
                                        @if (in_array($firstItem->status, ['approved', 'shipping', 'selesai']))
                                            <a href="{{ route('buyer.invoice.download', $order->id) }}"
                                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all flex items-center gap-2">
                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                                Invoice
                                            </a>
                                        @endif

                                        {{-- Tombol Lacak Paket --}}
                                        @if ($firstItem->status === 'shipping')
                                            <a href="https://www.jne.co.id/" target="_blank"
                                                class="px-4 py-2 bg-blue-500 text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition-all flex items-center gap-2">
                                                <i class="bi bi-geo-fill"></i>
                                                Lacak Paket
                                            </a>
                                        @endif

                                        {{-- Tombol Chat (Tambahan agar user mudah menghubungi seller spesifik) --}}
                                        <a href="{{ route('chat.index', $firstItem->seller_id) }}"
                                            class="px-4 py-2 bg-teal-50 text-teal-600 border border-teal-200 rounded-xl text-xs font-bold hover:bg-teal-100 transition-all flex items-center gap-2">
                                            <i class="bi bi-chat-dots"></i>
                                            Chat Seller
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="text-center py-24 bg-gradient-to-b from-gray-50 to-white rounded-3xl border-2 border-dashed border-teal-200">
                            <div
                                class="w-24 h-24 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <i class="bi bi-bag-x text-5xl text-teal-500"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum ada pesanan</h3>
                            <p class="text-gray-500 mb-6">Ayo mulai belanja buku favoritmu sekarang!</p>
                            <a href="/"
                                class="inline-block px-8 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-xl font-bold hover:shadow-lg transition-all">
                                🏪 Jelajahi Toko
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $purchases->links() }}
                </div>
            </div>
        </x-sidebar>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const config = {
                    locale: "id", // Menggunakan bahasa Indonesia
                    altInput: true,
                    altFormat: "d-m-Y", // Tampilan ke user: TANGGAL-BULAN-TAHUN
                    dateFormat: "Y-m-d", // Data yang dikirim ke database: TAHUN-BULAN-TANGGAL
                };

                flatpickr("#start_date", config);
                flatpickr("#end_date", config);
            });
        </script>
    @endsection
</x-app>
