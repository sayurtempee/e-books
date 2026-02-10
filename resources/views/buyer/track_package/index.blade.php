<x-app>
    @section('title', 'Lacak Paket')

    @section('body-content')
        <x-sidebar>
            <div class="p-4 md:p-8 bg-gray-50 min-h-screen">
                {{-- Header Section --}}
                <div class="mb-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div>
                            <h1
                                class="text-3xl font-black text-gray-800 border-l-4 border-teal-600 pl-4 uppercase tracking-tight">
                                Lacak <span class="text-teal-600">Paket</span>
                            </h1>
                            <p class="text-gray-500 text-sm ml-5 mt-1">
                                Kelola pengiriman dan pantau status pesanan Anda secara real-time.
                            </p>
                        </div>

                        {{-- Search Form --}}
                        <div class="w-full lg:w-1/2">
                            <form action="{{ route('buyer.orders.tracking') }}" method="GET" class="relative">
                                <div
                                    class="flex gap-2 p-1.5 bg-white rounded-2xl shadow-sm border border-gray-100 focus-within:border-teal-500 focus-within:ring-4 focus-within:ring-teal-50 transition-all duration-300">
                                    <div class="flex items-center pl-4 text-gray-400">
                                        <i class="bi bi-search"></i>
                                    </div>
                                    <input type="text" name="tracking_number" value="{{ request('tracking_number') }}"
                                        placeholder="Cari No. Resi atau Ekspedisi..."
                                        class="w-full border-none focus:ring-0 text-sm font-medium text-gray-700 bg-transparent py-3">

                                    @if (request('tracking_number'))
                                        <a href="{{ route('buyer.orders.tracking') }}"
                                            class="flex items-center px-3 text-gray-400 hover:text-red-500">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </a>
                                    @endif

                                    <button type="submit"
                                        class="bg-teal-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-teal-700 transition-all shadow-lg shadow-teal-100">
                                        Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-10">
                    @forelse ($items as $group)
                        @php
                            $firstItem = $group->first();
                            $hasProof = !empty($firstItem->payment_proof);
                            $status = $firstItem->status;
                            $hasResi = !empty($firstItem->tracking_number);

                            // Logic Progress Bar
                            $progressWidth = match ($status) {
                                'selesai' => '100%',
                                'shipping' => $hasResi ? '80%' : '60%',
                                'approved' => '60%',
                                'pending' => $hasProof ? '40%' : '20%',
                                default => '10%',
                            };

                            // Warna Header berdasarkan Status
                            $headerGradient = match ($status) {
                                'selesai' => 'from-emerald-600 to-teal-500',
                                'pending' => 'from-orange-500 to-amber-500',
                                default => 'from-teal-600 to-teal-500',
                            };
                        @endphp

                        <div
                            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-teal-900/5 transition-all duration-500">

                            {{-- Card Header --}}
                            <div
                                class="bg-gradient-to-r {{ $headerGradient }} p-6 md:p-8 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                                        <i class="bi {{ $status === 'selesai' ? 'bi-check-all' : 'bi-box-seam-fill' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-black tracking-[0.2em] opacity-70">Order ID</p>
                                        <h2 class="font-bold text-xl tracking-tight italic">#ORD-{{ $firstItem->order_id }}
                                        </h2>
                                        <p class="text-xs font-medium opacity-90 mt-1">Toko: <span
                                                class="font-bold uppercase">{{ $firstItem->book->user->name }}</span></p>
                                    </div>
                                </div>

                                <div
                                    class="bg-black/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/20 w-full md:w-auto">
                                    <p class="text-[10px] uppercase tracking-widest opacity-70 mb-1">Logistik & Resi</p>
                                    <p class="font-mono font-black text-lg">
                                        {{ $firstItem->expedisi_name ?? 'Internal' }}
                                        <span class="mx-2 opacity-30">|</span>
                                        <span class="text-yellow-300">{{ $firstItem->tracking_number ?? 'PENDING' }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="p-6 md:p-10">
                                {{-- Progress Timeline --}}
                                <div class="relative mb-16">
                                    <div
                                        class="absolute top-6 left-0 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-teal-500 transition-all duration-[1.5s] ease-out"
                                            style="width: {{ $progressWidth }}"></div>
                                    </div>

                                    <div class="relative z-10 flex justify-between">
                                        @php
                                            $steps = [
                                                ['label' => 'Dipesan', 'icon' => 'bi-cart-check', 'active' => true],
                                                [
                                                    'label' => 'Dibayar',
                                                    'icon' => 'bi-cash-stack',
                                                    'active' =>
                                                        $hasProof ||
                                                        in_array($status, ['approved', 'shipping', 'selesai']),
                                                ],
                                                [
                                                    'label' => 'Diproses',
                                                    'icon' => 'bi-gear-wide-connected',
                                                    'active' => in_array($status, ['approved', 'shipping', 'selesai']),
                                                ],
                                                [
                                                    'label' => 'Dikirim',
                                                    'icon' => 'bi-truck',
                                                    'active' =>
                                                        ($status === 'shipping' && $hasResi) || $status === 'selesai',
                                                ],
                                                [
                                                    'label' => 'Selesai',
                                                    'icon' => 'bi-house-heart',
                                                    'active' => $status === 'selesai',
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($steps as $step)
                                            <div class="flex flex-col items-center group">
                                                <div
                                                    class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 shadow-md {{ $step['active'] ? 'bg-teal-600 text-white ring-4 ring-teal-50 scale-110' : 'bg-white text-gray-300 border border-gray-100' }}">
                                                    <i class="bi {{ $step['icon'] }} text-xl"></i>
                                                </div>
                                                <p
                                                    class="hidden md:block mt-4 text-[10px] font-black uppercase tracking-tighter {{ $step['active'] ? 'text-gray-800' : 'text-gray-300' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Products List --}}
                                <div class="space-y-4">
                                    @foreach ($group as $subItem)
                                        <div
                                            class="flex items-center gap-5 bg-gray-50/50 p-4 rounded-3xl border border-gray-100 hover:bg-white hover:shadow-md transition-all">
                                            <img src="{{ asset('storage/' . $subItem->book->photos_product) }}"
                                                class="w-20 h-20 rounded-2xl object-cover shadow-sm">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 text-lg leading-tight">
                                                    {{ $subItem->book->title }}</h4>
                                                <p class="text-xs font-bold text-teal-600 mt-1 uppercase tracking-wider">
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

                                {{-- Card Footer: Total & Actions --}}
                                <div
                                    class="mt-10 pt-8 border-t border-dashed border-gray-200 flex flex-col lg:flex-row justify-between items-center gap-8">

                                    {{-- Address Info --}}
                                    <div class="flex gap-4 items-start w-full lg:w-1/3">
                                        <div
                                            class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600 shrink-0">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat
                                                Pengiriman</p>
                                            <p class="text-xs font-bold text-gray-600 leading-relaxed">
                                                {{ Auth::user()->address }}</p>
                                        </div>
                                    </div>

                                    {{-- Total & Buttons --}}
                                    <div class="flex flex-col sm:flex-row items-center gap-6 w-full lg:w-auto">
                                        <div class="text-center sm:text-right">
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em] mb-1">
                                                Total Pembayaran</p>
                                            <p class="text-3xl font-black text-teal-600 tracking-tighter">
                                                <span
                                                    class="text-sm mr-1">Rp</span>{{ number_format($group->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap justify-center gap-3">
                                            {{-- Button Group --}}
                                            <div class="flex gap-2">
                                                <a href="{{ route('chat.index', $firstItem->seller_id) }}"
                                                    class="px-5 py-3 bg-white border-2 border-teal-600 rounded-2xl text-teal-600 font-bold text-xs hover:bg-teal-600 hover:text-white transition-all flex items-center gap-2">
                                                    <i class="bi bi-chat-left-dots-fill"></i>
                                                    Chat
                                                </a>

                                                @if (in_array($status, ['approved', 'shipping', 'selesai']))
                                                    <a href="{{ route('buyer.invoice.download', $firstItem->order_id) }}"
                                                        class="px-4 py-3 bg-gray-50 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-100 transition-all border border-gray-200 flex items-center gap-2">
                                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                                        <span class="hidden sm:inline">Invoice</span>
                                                    </a>
                                                @endif
                                            </div>

                                            @if (!$hasProof)
                                                <button onclick="openUploadModal('{{ $firstItem->order_id }}')"
                                                    class="px-8 py-3 bg-red-500 text-white rounded-2xl font-black text-xs hover:bg-red-600 transition-all shadow-lg shadow-red-100 flex items-center gap-2 animate-pulse hover:animate-none">
                                                    <i class="bi bi-cloud-arrow-up-fill text-base"></i>
                                                    BAYAR SEKARANG
                                                </button>
                                            @else
                                                <button
                                                    onclick="viewPaymentProof('{{ asset('storage/' . $firstItem->payment_proof) }}')"
                                                    class="px-6 py-3 bg-gray-800 text-white rounded-2xl font-bold text-xs hover:bg-black transition-all flex items-center gap-2">
                                                    <i class="bi bi-eye-fill"></i>
                                                    Lihat Bukti
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State Tetap Sama --}}
                        <div class="py-32 bg-white rounded-[3rem] border-4 border-dashed border-gray-100 text-center">
                            <i class="bi bi-box-seam text-6xl text-gray-200 mb-6 block"></i>
                            <h3 class="text-xl font-bold text-gray-800">Tidak ada paket ditemukan</h3>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-sidebar>

        {{-- MODAL UPLOAD BUKTI --}}
        <div id="uploadModal"
            class="fixed inset-0 z-[999] hidden items-center justify-center bg-teal-900/60 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeUploadModal()">

            <div id="uploadModalContent"
                class="relative w-full max-w-xl mx-4 bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 overflow-hidden"
                onclick="event.stopPropagation()">

                <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                    <h3 class="font-bold text-teal-800 uppercase tracking-tight text-sm">Upload Bukti Transfer</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-red-500 transition">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <form id="uploadForm" action="{{ route('buyer.checkout.upload_proof') }}" method="POST"
                    enctype="multipart/form-data" class="p-6">
                    @csrf
                    <input type="hidden" name="order_id" id="modal_order_id">

                    <div class="mb-6">
                        <div class="relative group">
                            <input type="file" name="payment_proof" id="input_payment_proof" required
                                accept="image/*" onchange="previewImage(this)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">

                            <div id="preview_container"
                                class="border-4 border-dashed border-gray-100 rounded-[2rem] p-4 group-hover:border-teal-200 group-hover:bg-teal-50/30 transition-all text-center min-h-[250px] flex flex-col items-center justify-center overflow-hidden">

                                <div id="placeholder_view" class="py-10">
                                    <i
                                        class="bi bi-cloud-arrow-up text-5xl text-gray-300 group-hover:text-teal-400 transition-colors"></i>
                                    <p
                                        class="text-xs font-bold text-gray-400 mt-3 group-hover:text-teal-600 uppercase tracking-widest">
                                        Klik untuk pilih bukti transfer
                                    </p>
                                </div>

                                <img id="upload_preview_img" src="" alt="Preview"
                                    class="hidden max-h-[40vh] rounded-xl shadow-lg object-contain border-4 border-white">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-teal-600 text-white rounded-2xl font-black text-sm hover:bg-teal-700 shadow-lg shadow-teal-100 transition-all uppercase tracking-widest">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
        </div>

        {{-- MODAL LIHAT BUKTI (Image Preview) --}}
        <div id="imageModal"
            class="fixed inset-0 z-[999] hidden items-center justify-center bg-teal-900/60 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal()">
            <div id="modalContent"
                class="relative w-full max-w-xl mx-4 bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 overflow-hidden"
                onclick="event.stopPropagation()">
                <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                    <h3 class="font-bold text-teal-800">Bukti Pembayaran</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
                <div class="p-4 flex justify-center bg-gray-100">
                    <img id="modalImage" src="" alt="Bukti"
                        class="max-h-[70vh] rounded-lg shadow-md object-contain">
                </div>
            </div>
        </div>
    @endsection
</x-app>
