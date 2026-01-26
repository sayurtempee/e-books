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

                            $progressWidth = '33%';
                            if ($isShipping && $hasResi) {
                                $progressWidth = '100%';
                            } elseif ($isApproved || $hasResi) {
                                $progressWidth = '66%';
                            }
                        @endphp

                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition">

                            {{-- Header Card --}}
                            <div
                                class="bg-gradient-to-r from-teal-600 to-teal-500 p-6 text-white flex flex-col md:flex-row justify-between gap-4">

                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                                        <i class="bi bi-box-seam"></i>
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
                                        Resi (JNE)
                                    </p>
                                    <p class="font-mono font-bold text-lg">
                                        {{ $item->tracking_number ?? 'MENUNGGU RESI' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-8">

                                {{-- Timeline --}}
                                <div class="relative flex flex-col md:flex-row justify-between">

                                    {{-- Line --}}
                                    <div class="hidden md:block absolute top-5 left-0 w-full h-1 bg-gray-100">
                                        <div class="h-full bg-teal-500 transition-all duration-700"
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
                                                'active' => (bool) $item->order->payment_proof,
                                                'desc' => $item->order->payment_proof ? 'Terverifikasi' : 'Menunggu',
                                            ],
                                            [
                                                'label' => 'Diproses',
                                                'icon' => 'bi-box-seam',
                                                'active' => $isApproved || $isShipping,
                                                'desc' => $item->approved_at
                                                    ? $item->approved_at->format('d M')
                                                    : 'Diproses Seller',
                                            ],
                                            [
                                                'label' => 'Dikirim',
                                                'icon' => 'bi-truck',
                                                'active' => $isShipping && $hasResi,
                                                'desc' => $hasResi ? 'Dalam Pengiriman' : 'Belum Dikirim',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($steps as $step)
                                        <div class="relative z-10 flex items-center md:flex-col gap-3 w-full md:w-auto">
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center shadow
                                                {{ $step['active'] ? 'bg-teal-600 text-white scale-110' : 'bg-gray-100 text-gray-400' }}">
                                                <i class="bi {{ $step['icon'] }}"></i>
                                            </div>
                                            <div class="text-left md:text-center">
                                                <p
                                                    class="text-xs font-black uppercase {{ $step['active'] ? 'text-gray-800' : 'text-gray-400' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                                <p class="text-[10px] text-gray-400">
                                                    {{ $step['desc'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Detail --}}
                                <div class="mt-10 pt-8 border-t border-gray-100">

                                    <div class="flex flex-col lg:flex-row justify-between gap-6">

                                        {{-- Address --}}
                                        <div class="flex gap-4 bg-gray-50 p-4 rounded-2xl border flex-1">
                                            <div
                                                class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-teal-600">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <div>
                                                <p class="text-[9px] uppercase tracking-widest text-gray-400 font-black">
                                                    Alamat Tujuan
                                                </p>
                                                <p class="text-sm font-bold text-gray-700">
                                                    {{ $item->order->user->address }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Action --}}
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('chat.index', $item->book->user_id) }}"
                                                class="w-12 h-12 flex items-center justify-center bg-white border-2 border-teal-100 rounded-xl text-teal-600 hover:bg-teal-50">
                                                <i class="bi bi-chat-dots-fill"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Item --}}
                                    <div class="mt-6 flex items-center gap-4 bg-white p-4 rounded-2xl border">
                                        <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                            class="w-20 h-20 rounded-xl object-cover">
                                        <div class="flex-1">
                                            <p class="font-black text-gray-800">
                                                {{ $item->book->title }}
                                            </p>
                                            <p class="text-sm font-bold text-teal-600">
                                                {{ $item->qty }} x Rp{{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Seller: {{ $item->book->user->name }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="py-20 bg-white rounded-[2rem] border border-dashed text-center">
                            <i class="bi bi-box-seam text-6xl text-gray-200 mb-4"></i>
                            <p class="text-gray-400 font-bold">
                                Belum ada paket yang bisa dilacak
                            </p>
                        </div>
                    @endforelse

                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
