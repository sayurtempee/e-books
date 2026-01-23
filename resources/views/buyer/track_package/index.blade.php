<x-app>
    @section('title', 'Lacak Paket')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-extrabold text-gray-800 border-l-4 border-teal-600 pl-4">
                        Lacak Paket
                    </h1>
                </div>

                @php
                    // Ambil pesanan yang statusnya 'shipping' atau 'approved' (sedang diproses)
                    $trackedOrders = \App\Models\Order::where('user_id', auth()->id())
                        ->whereIn('status', ['approved', 'shipping'])
                        ->with('items.book')
                        ->latest()
                        ->get();
                @endphp

                <div class="grid grid-cols-1 gap-6">
                    @forelse ($trackedOrders as $order)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            {{-- Header Card --}}
                            <div class="bg-teal-600 p-4 text-white flex justify-between items-center">
                                <div>
                                    <p class="text-xs opacity-80 uppercase font-bold tracking-wider">No. Pesanan</p>
                                    <h3 class="font-mono text-lg font-bold">#ORD-{{ $order->id }}</h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs opacity-80 uppercase font-bold">Kurir / Resi</p>
                                    <p class="font-bold">{{ $order->tracking_number ?? 'Menunggu Resi...' }}</p>
                                </div>
                            </div>

                            <div class="p-6">
                                {{-- Timeline Tracker --}}
                                <div
                                    class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-0 mt-4">

                                    {{-- Garis Penghubung (Hanya muncul di Desktop) --}}
                                    <div
                                        class="hidden md:block absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 z-0">
                                    </div>

                                    {{-- Step 1: Pesanan Dibuat --}}
                                    <div
                                        class="relative z-10 flex flex-row md:flex-col items-center gap-3 md:gap-2 bg-white md:bg-transparent pr-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-lg">
                                            <i class="bi bi-cart-check"></i>
                                        </div>
                                        <div class="text-left md:text-center">
                                            <p class="text-xs font-bold text-gray-800">Dipesan</p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ $order->created_at->format('d M, H:i') }}</p>
                                        </div>
                                    </div>

                                    {{-- Step 2: Pembayaran --}}
                                    <div
                                        class="relative z-10 flex flex-row md:flex-col items-center gap-3 md:gap-2 bg-white md:bg-transparent pr-4">
                                        <div
                                            class="w-10 h-10 rounded-full {{ $order->payment_proof ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center shadow-lg">
                                            <i class="bi bi-cash-stack"></i>
                                        </div>
                                        <div class="text-left md:text-center">
                                            <p
                                                class="text-xs font-bold {{ $order->payment_proof ? 'text-gray-800' : 'text-gray-400' }}">
                                                Pembayaran</p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ $order->payment_proof ? 'Diverifikasi' : 'Belum Bayar' }}</p>
                                        </div>
                                    </div>

                                    {{-- Step 3: Diproses (Approved) --}}
                                    <div
                                        class="relative z-10 flex flex-row md:flex-col items-center gap-3 md:gap-2 bg-white md:bg-transparent pr-4">
                                        <div
                                            class="w-10 h-10 rounded-full {{ $order->approved_at ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center shadow-lg">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="text-left md:text-center">
                                            <p
                                                class="text-xs font-bold {{ $order->approved_at ? 'text-gray-800' : 'text-gray-400' }}">
                                                Dipacking</p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ $order->approved_at ? $order->approved_at->format('d M') : '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Step 4: Dikirim (Shipping) --}}
                                    <div
                                        class="relative z-10 flex flex-row md:flex-col items-center gap-3 md:gap-2 bg-white md:bg-transparent">
                                        <div
                                            class="w-10 h-10 rounded-full {{ $order->status === 'shipping' ? 'bg-blue-500 text-white animate-pulse' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center shadow-lg">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="text-left md:text-center">
                                            <p
                                                class="text-xs font-bold {{ $order->status === 'shipping' ? 'text-blue-600' : 'text-gray-400' }}">
                                                Dikirim</p>
                                            <p class="text-[10px] text-gray-500">
                                                {{ $order->status === 'shipping' ? 'Dalam Perjalanan' : '-' }}</p>
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100" x-data="{ open: false }">
                                    <div
                                        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                        {{-- Alamat --}}
                                        <div class="flex gap-3 items-start flex-1">
                                            <div class="p-2 bg-gray-100 rounded-lg">
                                                <i class="bi bi-geo-alt text-teal-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                    Alamat Tujuan</p>
                                                <p class="text-sm text-gray-600 line-clamp-1">{{ $order->user->address }}</p>
                                            </div>
                                        </div>

                                        {{-- Tombol Aksi --}}
                                        <div class="flex items-center gap-3 w-full md:w-auto">
                                            <button @click="open = !open"
                                                class="flex-1 md:flex-none text-xs font-bold px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                                                <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                                <span
                                                    x-text="open ? 'Sembunyikan Barang' : 'Lihat Barang Yang Dibeli'"></span>
                                            </button>

                                            <a href="{{ route('buyer.orders.index') }}"
                                                class="flex-1 md:flex-none text-xs font-bold px-4 py-2 bg-teal-50 text-teal-600 rounded-lg hover:bg-teal-100 transition-all text-center">
                                                Pesan Kembali <i class="bi bi-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Panel List Barang (Muncul saat tombol diklik) --}}
                                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                                        x-transition:enter-end="opacity-100 transform translate-y-0"
                                        class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-3">

                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Daftar Produk:</p>

                                        @foreach ($order->items as $item)
                                            <div class="flex items-center gap-4 bg-white p-2 rounded-lg shadow-sm">
                                                <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                                    class="w-12 h-12 object-cover rounded-md border border-gray-100">
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-1">
                                                        {{ $item->book->title }}</h4>
                                                    <p class="text-[10px] text-gray-500">{{ $item->qty }} x Rp
                                                        {{ number_format($item->price, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-black text-teal-600">
                                                        Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div
                                            class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center px-1">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">Total
                                                Pembayaran</span>
                                            <span class="text-sm font-black text-gray-900">Rp
                                                {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 rounded-2xl border border-dashed border-gray-300 text-center">
                            <div class="text-5xl mb-4">🚚</div>
                            <h2 class="text-xl font-bold text-gray-800">Belum ada paket dikirim</h2>
                            <p class="text-gray-500 mb-6">Paket yang sedang dalam perjalanan akan muncul di sini.</p>
                            <a href="{{ route('buyer.orders.index') }}"
                                class="bg-teal-600 text-white px-6 py-2 rounded-lg font-bold">Cek Status Pesanan</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
