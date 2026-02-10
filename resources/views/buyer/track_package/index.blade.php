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
                        Produk dari penjual yang sama dalam satu pesanan dikirim dalam satu paket.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    @forelse ($items as $group)
                        @php
                            $firstItem = $group->first();

                            // Cek payment_proof ada di order_items
                            $hasProof = !empty($firstItem->payment_proof);

                            $status = $firstItem->status;

                            $statusFinishDate = $firstItem->updated_at;

                            $hasResi = !empty($firstItem->tracking_number);

                            // Hitung Progress lebih detail
                            $progressWidth = '0%';
                            if ($status === 'selesai') {
                                $progressWidth = '100%';
                            } elseif ($status === 'shipping' && $hasResi) {
                                $progressWidth = '80%';
                            } elseif (in_array($status, ['approved', 'shipping'])) {
                                $progressWidth = '60%';
                            } elseif ($status === 'pending' && $hasProof) {
                                $progressWidth = '40%'; // Sudah bayar tapi masih pending di seller
                            } elseif ($status === 'pending') {
                                $progressWidth = '20%'; // Baru pesan banget
                            }
                        @endphp

                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition duration-500">

                            {{-- Header Card --}}
                            <div
                                class="bg-gradient-to-r @if ($status === 'selesai') from-emerald-600 to-teal-500 @elseif($status === 'pending') from-red-400 to-red-500 @else from-teal-600 to-teal-500 @endif p-6 text-white flex flex-col md:flex-row justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                                        <i class="bi {{ $status === 'selesai' ? 'bi-check-all' : 'bi-box-seam' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-black tracking-widest opacity-80">Order
                                            #ORD-{{ $firstItem->order_id }}</p>
                                        <p class="font-bold text-lg leading-tight uppercase">
                                            {{ $firstItem->book->user->name }} {{-- Nama Seller --}}
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/20">
                                    <p class="text-[10px] uppercase tracking-widest opacity-80">Ekspedisi & Resi</p>
                                    <p class="font-mono font-bold text-lg uppercase">
                                        {{ $firstItem->expedisi_name ?? 'Kurir' }} -
                                        {{ $firstItem->tracking_number ?? 'MENUNGGU RESI' }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-8">
                                {{-- Progress Timeline --}}
                                <div class="relative flex flex-col md:flex-row justify-between gap-y-8">
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
                                                'desc' => $firstItem->order->created_at->format('d M H:i'),
                                            ],
                                            [
                                                'label' => 'Dibayar',
                                                'icon' => 'bi-cash-stack',
                                                'active' =>
                                                    $hasProof || in_array($status, ['approved', 'shipping', 'selesai']),
                                                'desc' => $hasProof ? 'Terverifikasi' : 'Menunggu Pembayaran',
                                            ],
                                            [
                                                'label' => 'Diproses',
                                                'icon' => 'bi-box-seam',
                                                // Hanya aktif jika status BUKAN pending
                                                'active' => in_array($status, ['approved', 'shipping', 'selesai']),
                                                'desc' =>
                                                    $status === 'pending' ? 'Menunggu Penjual' : 'Penjual Memproses',
                                            ],
                                            [
                                                'label' => 'Dikirim',
                                                'icon' => 'bi-truck',
                                                'active' =>
                                                    ($status === 'shipping' && $hasResi) || $status === 'selesai',
                                                'desc' => $hasResi ? 'Dalam Perjalanan' : 'Belum Dikirim',
                                            ],
                                            [
                                                'label' => 'Selesai',
                                                'icon' => 'bi-house-check',
                                                'active' => $status === 'selesai',
                                                'desc' => [
                                                    $status === 'selesai' ? 'Diterima' : 'Tujuan',
                                                    $status === 'selesai' && $statusFinishDate
                                                        ? $statusFinishDate->format('d M H:i')
                                                        : '-',
                                                ],
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($steps as $step)
                                        <div class="relative z-10 flex items-center md:flex-col gap-4 w-full md:w-auto">
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-700 {{ $step['active'] ? 'bg-teal-600 text-white scale-110 ring-4 ring-teal-50' : 'bg-white text-gray-300 border border-gray-100' }}">
                                                <i class="bi {{ $step['icon'] }} text-xl"></i>
                                            </div>
                                            <div class="text-left md:text-center">
                                                <p
                                                    class="text-xs font-black uppercase tracking-tight {{ $step['active'] ? 'text-gray-800' : 'text-gray-300' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                                <p
                                                    class="text-[10px] font-medium {{ $step['active'] ? 'text-teal-600' : 'text-gray-300' }}">
                                                    {{-- @dd($step['desc']) --}}
                                                    {!! is_array($step['desc']) ? implode('<br>', $step['desc']) : $step['desc'] !!}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Daftar Produk dalam Paket --}}
                                <div class="mt-12 pt-8 border-t border-gray-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-4">Isi Paket
                                        ({{ $group->count() }} Produk)</p>
                                    <div class="space-y-4">
                                        @foreach ($group as $subItem)
                                            <div
                                                class="flex items-center gap-5 bg-gray-50/50 p-4 rounded-3xl border border-gray-100">
                                                <img src="{{ asset('storage/' . $subItem->book->photos_product) }}"
                                                    class="w-16 h-16 rounded-2xl object-cover shadow-md">
                                                <div class="flex-1">
                                                    <p class="font-black text-gray-800 text-base leading-tight">
                                                        {{ $subItem->book->title }}
                                                    </p>
                                                    <p class="text-xs font-bold text-teal-600 uppercase">
                                                        {{ $subItem->qty }} x
                                                        Rp{{ number_format($subItem->price, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-black text-gray-900 italic">
                                                        Rp{{ number_format($subItem->qty * $subItem->price, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Footer Total --}}
                                    <div
                                        class="mt-8 flex flex-col lg:flex-row justify-between items-center gap-6 pt-6 border-t border-dashed">
                                        <div class="flex gap-4 items-center">
                                            <div
                                                class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <p class="text-sm font-bold text-gray-600 max-w-xs">{{ Auth::user()->address }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-6">
                                            <div class="text-right">
                                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                                                    Total Harga Paket</p>
                                                <p class="text-2xl font-black text-teal-600">
                                                    Rp{{ number_format($group->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <a href="{{ route('chat.index', $firstItem->seller_id) }}"
                                                class="px-6 py-3 bg-white border-2 border-teal-600 rounded-2xl text-teal-600 font-bold text-sm hover:bg-teal-600 hover:text-white transition-all">
                                                Chat Seller
                                            </a>
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
