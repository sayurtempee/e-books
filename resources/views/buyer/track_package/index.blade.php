<x-app>
    @section('title', 'Lacak Paket')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">

                {{-- Header --}}
                <div class="mb-10">
                    <h1 class="text-3xl font-black text-gray-800 border-l-4 border-teal-600 pl-4">
                        Lacak <span class="text-teal-600">Paket</span>
                    </h1>
                    <p class="text-gray-500 text-sm ml-5">
                        Setiap paket dilacak berdasarkan seller & resi masing-masing.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8">

                    @forelse ($items as $item)
                        @php
                            $hasResi = !empty($item->tracking_number);
                            $isApproved = $item->status === 'approved';
                            $isShipping = $item->status === 'shipping';
                            $isSelesai = $item->status === 'selesai';
                            $hasProof = !empty($item->payment_proof);

                            // Logika Progress Bar (5 Tahap = Kelipatan 20%)
                            $progressWidth = '20%'; // Default: Dipesan
                            if ($isSelesai) {
                                $progressWidth = '100%';
                            } elseif ($isShipping && $hasResi) {
                                $progressWidth = '80%';
                            } elseif ($isApproved || $isShipping) {
                                $progressWidth = '60%';
                            } elseif ($hasProof) {
                                $progressWidth = '40%';
                            }
                        @endphp

                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition duration-500">

                            {{-- Header Card --}}
                            <div
                                class="bg-gradient-to-r {{ $isSelesai ? 'from-emerald-600 to-teal-500' : 'from-teal-600 to-teal-500' }} p-6 text-white flex flex-col md:flex-row justify-between gap-4">

                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                                        <i class="bi {{ $isSelesai ? 'bi-check-all' : 'bi-box-seam' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-black tracking-widest opacity-80">
                                            ID Pesanan
                                        </p>
                                        <p class="font-mono text-lg font-bold">
                                            #ORD-{{ $item->order->id }}-{{ $item->id }}
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/20">
                                    <p class="text-[10px] uppercase tracking-widest opacity-80">
                                        Ekspedisi & Resi
                                    </p>
                                    <p class="font-mono font-bold text-lg uppercase">
                                        {{ $item->expedisi_name ?? 'Kurir' }} -
                                        {{ $item->tracking_number ?? 'MENUNGGU RESI' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-8">

                                {{-- Timeline --}}
                                <div class="relative flex flex-col md:flex-row justify-between gap-y-8">

                                    {{-- Background Line --}}
                                    <div class="hidden md:block absolute top-6 left-0 w-full h-1 bg-gray-100">
                                        <div class="h-full bg-teal-500 transition-all duration-1000 ease-in-out"
                                            style="width: {{ $progressWidth }}"></div>
                                    </div>

                                    @php
                                        $steps = [
                                            [
                                                'label' => 'Dipesan',
                                                'icon' => 'bi-cart-check',
                                                'active' => true,
                                                'desc' => $item->order->created_at->format('d M H:i'),
                                            ],
                                            [
                                                'label' => 'Dibayar',
                                                'icon' => 'bi-cash-stack',
                                                'active' => $hasProof,
                                                'desc' => $hasProof ? 'Terverifikasi' : 'Belum Bayar',
                                            ],
                                            [
                                                'label' => 'Diproses',
                                                'icon' => 'bi-box-seam',
                                                'active' => $isApproved || $isShipping || $isSelesai,
                                                'desc' =>
                                                    $isApproved || $isShipping || $isSelesai
                                                        ? 'Sedang Dikemas'
                                                        : 'Menunggu',
                                            ],
                                            [
                                                'label' => 'Dikirim',
                                                'icon' => 'bi-truck',
                                                'active' => ($isShipping && $hasResi) || $isSelesai,
                                                'desc' => $hasResi ? 'Dalam Perjalanan' : 'Proses Pick-up',
                                            ],
                                            [
                                                'label' => 'Selesai',
                                                'icon' => 'bi-house-check',
                                                'active' => $isSelesai,
                                                'desc' => $isSelesai ? 'Diterima Buyer' : 'Tujuan Akhir',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($steps as $step)
                                        <div class="relative z-10 flex items-center md:flex-col gap-4 w-full md:w-auto">
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-700
                                                {{ $step['active'] ? 'bg-teal-600 text-white scale-110 ring-4 ring-teal-50' : 'bg-white text-gray-300 border border-gray-100' }}">
                                                <i class="bi {{ $step['icon'] }} text-xl"></i>
                                            </div>
                                            <div class="text-left md:text-center">
                                                <p
                                                    class="text-xs font-black uppercase tracking-tight {{ $step['active'] ? 'text-gray-800' : 'text-gray-300' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                                <p
                                                    class="text-[10px] font-medium {{ $step['active'] ? 'text-teal-600' : 'text-gray-300' }}">
                                                    {{ $step['desc'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Detail Section --}}
                                <div class="mt-12 pt-8 border-t border-gray-100">
                                    <div class="flex flex-col lg:flex-row justify-between gap-6">

                                        {{-- Address --}}
                                        <div class="flex gap-4 bg-gray-50 p-5 rounded-3xl border border-gray-100 flex-1">
                                            <div
                                                class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-teal-600">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[9px] uppercase tracking-widest text-gray-400 font-black mb-1">
                                                    Alamat Pengiriman
                                                </p>
                                                <p class="text-sm font-bold text-gray-700 leading-relaxed">
                                                    {{ $item->order->user->address }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Contact Seller --}}
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('chat.index', $item->book->user_id) }}"
                                                class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-teal-50 rounded-2xl text-teal-600 font-bold text-sm hover:bg-teal-600 hover:text-white transition-all shadow-sm">
                                                <i class="bi bi-chat-dots-fill"></i>
                                                <span>Chat Seller</span>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Product Snapshot --}}
                                    <div
                                        class="mt-6 flex items-center gap-5 bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                                class="w-20 h-20 rounded-2xl object-cover shadow-md">
                                            @if ($isSelesai)
                                                <div
                                                    class="absolute -top-2 -right-2 bg-emerald-500 text-white p-1 rounded-full shadow-lg">
                                                    <i class="bi bi-check-circle-fill text-xs"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-black text-gray-800 text-lg leading-tight mb-1">
                                                {{ $item->book->title }}
                                            </p>
                                            <div class="flex items-center gap-3">
                                                <p
                                                    class="text-sm font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-lg">
                                                    {{ $item->qty }} Item
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    Store: <span
                                                        class="font-bold text-gray-600">{{ $item->book->user->name }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total
                                                Bayar</p>
                                            <p class="text-xl font-black text-gray-900">
                                                Rp{{ number_format($item->qty * $item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="py-32 bg-white rounded-[3rem] border-4 border-dashed border-gray-50 text-center">
                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="bi bi-box-seam text-5xl text-gray-200"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Aktivitas</h3>
                            <p class="text-gray-400 max-w-xs mx-auto">
                                Paket yang sedang diproses atau dikirim akan muncul di halaman ini.
                            </p>
                        </div>
                    @endforelse

                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
