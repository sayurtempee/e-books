<x-app>
    @section('title', 'Lacak Paket')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                {{-- Judul Halaman --}}
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 border-l-4 border-teal-600 pl-4 tracking-tight">
                            Lacak <span class="text-teal-600">Paket</span>
                        </h1>
                        <p class="text-gray-500 text-sm ml-5">Pantau perjalanan pesanan Anda secara real-time.</p>
                    </div>
                </div>

                @php
                    // Ambil pesanan milik user login dengan status tertentu
                    $trackedOrders = \App\Models\Order::where('user_id', auth()->id())
                        ->whereIn('status', ['approved', 'shipping'])
                        ->with(['items.book.user']) // Penting untuk chat seller
                        ->latest()
                        ->get();
                @endphp

                <div class="grid grid-cols-1 gap-8">
                    @forelse ($trackedOrders as $order)
                        @php
                            // LOGIKA VALIDASI STATUS REALISTIK
                            $hasResi = !empty($order->tracking_number);
                            $isShipping = $order->status === 'shipping';
                            $isApproved = $order->status === 'approved';

                            // Menghitung persentase bar progress
                            $progressWidth = '33%'; // Default: Dipesan & Dibayar
                            if ($isShipping && $hasResi) {
                                $progressWidth = '100%';
                            } elseif ($isApproved || $hasResi) {
                                $progressWidth = '66%';
                            }
                        @endphp

                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-xl hover:shadow-gray-200/50">

                            {{-- Header Card Premium --}}
                            <div
                                class="bg-gradient-to-r from-teal-600 to-teal-500 p-6 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-2xl">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] opacity-80 uppercase font-black tracking-[0.2em]">ID Transaksi
                                        </p>
                                        <h3 class="font-mono text-xl font-bold">#ORD-{{ $order->id }}</h3>
                                    </div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                                    <p
                                        class="text-[10px] opacity-80 uppercase font-black tracking-widest text-center md:text-right">
                                        No. Resi (JNE)</p>
                                    <p class="font-mono font-bold text-lg italic tracking-wider">
                                        {{ $order->tracking_number ?? 'MENGALOKASIKAN...' }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-8">
                                {{-- Timeline Tracker --}}
                                <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center">

                                    {{-- Desktop Connecting Line --}}
                                    <div class="hidden md:block absolute top-5 left-0 w-full h-1 bg-gray-100 z-0">
                                        <div class="h-full bg-teal-500 transition-all duration-1000"
                                            style="width: {{ $progressWidth }}"></div>
                                    </div>

                                    {{-- Steps Configuration --}}
                                    @php
                                        $steps = [
                                            [
                                                'label' => 'Dipesan',
                                                'icon' => 'bi-cart-check',
                                                'active' => true,
                                                'desc' => $order->created_at->format('d M, H:i'),
                                            ],
                                            [
                                                'label' => 'Dibayar',
                                                'icon' => 'bi-cash-stack',
                                                'active' => (bool) $order->payment_proof,
                                                'desc' => $order->payment_proof ? 'Terverifikasi' : 'Menunggu',
                                            ],
                                            [
                                                'label' => 'Dipacking',
                                                'icon' => 'bi-box-seam',
                                                'active' => $isApproved || $isShipping || $hasResi,
                                                'desc' =>
                                                    $isApproved || $hasResi
                                                        ? ($order->approved_at
                                                            ? $order->approved_at->format('d M')
                                                            : 'Sedang Diproses')
                                                        : 'Antre',
                                            ],
                                            [
                                                'label' => 'Dikirim',
                                                'icon' => 'bi-truck',
                                                'active' => $isShipping && $hasResi,
                                                'desc' =>
                                                    $isShipping && $hasResi ? 'Kurir Menuju Lokasi' : 'Belum Dikirim',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($steps as $step)
                                        <div
                                            class="relative z-10 flex flex-row md:flex-col items-center gap-4 md:gap-3 bg-white md:bg-transparent py-2 md:py-0 w-full md:w-auto">
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-500
                                                {{ $step['active'] ? 'bg-teal-600 text-white scale-110' : 'bg-gray-100 text-gray-400' }}">
                                                <i class="bi {{ $step['icon'] }} text-xl"></i>
                                            </div>
                                            <div class="text-left md:text-center">
                                                <p
                                                    class="text-xs font-black uppercase tracking-tighter {{ $step['active'] ? 'text-gray-800' : 'text-gray-400' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                                <p class="text-[10px] font-medium text-gray-400">{{ $step['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Details & Items --}}
                                <div class="mt-10 pt-8 border-t border-gray-100" x-data="{ open: false }">
                                    <div
                                        class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                                        {{-- Address Info --}}
                                        <div
                                            class="flex gap-4 items-center bg-gray-50 p-3 rounded-2xl border border-gray-100 flex-1 w-full">
                                            <div
                                                class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-teal-600">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                    Tujuan Pengiriman</p>
                                                <p class="text-sm text-gray-700 font-bold truncate">
                                                    {{ $order->user->address }}</p>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex items-center gap-3 w-full lg:w-auto">
                                            <button @click="open = !open"
                                                class="flex-1 lg:flex-none px-6 py-3 bg-gray-900 text-white rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-teal-600 transition-all shadow-lg shadow-gray-200">
                                                <i class="bi" :class="open ? 'bi-eye-slash' : 'bi-eye'"></i>
                                                <span x-text="open ? 'Tutup Detail' : 'Detail Paket'"></span>
                                            </button>

                                            @php
                                                // Mengambil Seller dari item pertama untuk konsultasi chat
                                                $firstItem = $order->items->first();
                                                $sellerId =
                                                    $firstItem && $firstItem->book ? $firstItem->book->user_id : null;
                                            @endphp

                                            <a href="{{ $sellerId ? route('chat.index', $sellerId) : '#' }}"
                                                class="w-12 h-12 flex items-center justify-center bg-white border-2 border-teal-100 text-teal-600 rounded-xl hover:bg-teal-50 transition-all {{ !$sellerId ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                title="Chat Seller">
                                                <i class="bi bi-chat-dots-fill text-lg"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Dropdown Items --}}
                                    <div x-show="open" x-collapse>
                                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($order->items as $item)
                                                <div
                                                    class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-gray-100 hover:border-teal-200 transition-colors">
                                                    <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                                        class="w-16 h-16 object-cover rounded-xl shadow-sm">
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-black text-gray-800">
                                                            {{ $item->book->title }}</h4>
                                                        <p class="text-[10px] font-bold text-teal-600">
                                                            {{ $item->qty }} Unit x
                                                            Rp{{ number_format($item->price, 0, ',', '.') }}
                                                        </p>
                                                        <p class="text-[9px] text-gray-400">Seller:
                                                            {{ $item->book->user->name ?? 'Unknown' }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State --}}
                        <div
                            class="flex flex-col items-center justify-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-200">
                            <div class="w-32 h-32 bg-teal-50 rounded-full flex items-center justify-center mb-6">
                                <i class="bi bi-box-seam text-5xl text-teal-200"></i>
                            </div>
                            <h2 class="text-2xl font-black text-gray-800 mb-2">Belum Ada Pengiriman</h2>
                            <p class="text-gray-400 text-sm mb-8 text-center max-w-xs font-medium">Paket Anda yang sudah
                                diproses oleh seller akan muncul di sini untuk dilacak.</p>
                            <a href="{{ route('buyer.orders.index') }}"
                                class="px-8 py-3 bg-teal-600 text-white rounded-xl font-black shadow-lg shadow-teal-100 hover:bg-gray-900 transition-all uppercase tracking-widest text-xs">
                                Belanja Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
