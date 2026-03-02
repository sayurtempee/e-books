<x-app>
    @section('title', 'Lacak Paket')

    @section('body-content')
        <x-sidebar>
            <div class="p-4 md:p-10 bg-gray-50 min-h-screen">
                {{-- Header Section --}}
                <div class="mb-12">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                        <div>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                                Lacak <span class="text-teal-600">Paket</span>
                            </h1>
                            <p class="text-gray-500 mt-2 flex items-center gap-2">
                                <span class="w-8 h-1 bg-teal-500 rounded-full"></span>
                                Pantau status pengiriman belanjaan Anda
                            </p>
                        </div>

                        {{-- Search Form --}}
                        <div class="w-full lg:w-1/3">
                            <form action="{{ route('buyer.orders.tracking') }}" method="GET" class="relative group">
                                <input type="text" name="tracking_number" value="{{ request('tracking_number') }}"
                                    placeholder="Cari No. Resi..."
                                    class="w-full pl-12 pr-24 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-4 focus:ring-teal-500/10 transition-all text-sm font-medium">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="bi bi-search text-lg"></i>
                                </div>
                                <button type="submit"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-teal-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-teal-700 transition-all">
                                    CARI
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    @forelse ($items as $group)
                        @php
                            $firstItem = $group->first();
                            $hasProof = !empty($firstItem->payment_proof);
                            $status = $firstItem->status; // 'pending', 'approved', 'shipping', 'selesai', 'rejected'
                            $hasResi = !empty($firstItem->tracking_number);

                            // Logic Progress Bar & Color
                            $isRejected = $status === 'rejected';

                            $progressWidth = match ($status) {
                                'selesai' => '100%',
                                'shipping' => $hasResi ? '80%' : '60%',
                                'approved' => '50%',
                                'pending' => $hasProof ? '30%' : '10%',
                                'rejected' => '100%', // Full tapi nanti warnanya merah
                                default => '5%',
                            };

                            $themeColor = $isRejected ? 'red' : 'teal';
                            $statusBg = $isRejected ? 'bg-red-600' : 'bg-teal-600';
                        @endphp

                        {{-- START CARD PER ORDER --}}
                        <div
                            class="bg-white rounded-[2rem] shadow-sm border {{ $isRejected ? 'border-red-100' : 'border-gray-100' }} overflow-hidden hover:shadow-xl transition-all duration-300 mb-8">

                            {{-- Card Top Info --}}
                            <div
                                class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 {{ $isRejected ? 'bg-red-50/50' : 'bg-gray-50/50' }} border-b border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="p-3 {{ $statusBg }} rounded-2xl text-white shadow-lg {{ $isRejected ? 'shadow-red-200' : 'shadow-teal-200' }}">
                                        <i
                                            class="bi {{ $isRejected ? 'bi-x-circle-fill' : 'bi-box-seam-fill' }} text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-black text-gray-900 leading-none">
                                                #ORD-{{ $firstItem->order_id }}</h3>
                                            @if ($isRejected)
                                                <span
                                                    class="px-3 py-1 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded-full tracking-widest">Pesanan
                                                    Dibatalkan</span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">Toko:
                                            {{ $firstItem->book->user->name }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col md:items-end">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ekspedisi /
                                        Resi</span>
                                    <p class="font-mono font-bold text-gray-700">
                                        {{ $firstItem->expedisi_name ?? 'Kurir Internal' }}
                                        <span
                                            class="{{ $isRejected ? 'text-red-600' : 'text-teal-600' }} ml-2">{{ $firstItem->tracking_number ?? '---' }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="p-6 md:p-10">
                                {{-- Timeline Status (Hidden if Rejected for a simpler Alert view, or kept with Red style) --}}
                                @if (!$isRejected)
                                    <div class="relative mb-12 px-4">
                                        <div
                                            class="absolute top-5 left-0 w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-teal-500 transition-all duration-1000"
                                                style="width: {{ $progressWidth }}"></div>
                                        </div>
                                        {{-- ... (Steps logic same as before) ... --}}
                                        <div class="relative z-10 flex justify-between">
                                            @php
                                                $steps = [
                                                    [
                                                        'label' => 'Dipesan',
                                                        'icon' => 'bi-cart-check',
                                                        'active' => true, // Selalu active karena sudah masuk list
                                                    ],
                                                    [
                                                        'label' => 'Dibayar',
                                                        'icon' => 'bi-cash-stack',
                                                        // Active jika sudah ada bukti ATAU sudah di-approve/lebih lanjut
                                                        'active' =>
                                                            $hasProof ||
                                                            in_array($status, ['approved', 'shipping', 'selesai']),
                                                    ],
                                                    [
                                                        'label' => 'Diproses',
                                                        'icon' => 'bi-gear-wide-connected',
                                                        // Active jika status sudah approved ke atas
                                                        'active' => in_array($status, [
                                                            'approved',
                                                            'shipping',
                                                            'selesai',
                                                        ]),
                                                    ],
                                                    [
                                                        'label' => 'Dikirim',
                                                        'icon' => 'bi-truck',
                                                        // Active jika status shipping (dan idealnya punya resi)
                                                        'active' => in_array($status, ['shipping', 'selesai']),
                                                    ],
                                                    [
                                                        'label' => 'Selesai',
                                                        'icon' => 'bi-house-heart',
                                                        'active' => $status === 'selesai',
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($steps as $step)
                                                <div class="flex flex-col items-center">
                                                    <div
                                                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 {{ $step['active'] ? 'bg-teal-600 text-white ring-4 ring-teal-50' : 'bg-white text-gray-300 border border-gray-100' }}">
                                                        <i class="bi {{ $step['icon'] }} text-sm"></i>
                                                    </div>
                                                    <p
                                                        class="hidden md:block mt-3 text-[10px] font-black uppercase tracking-tighter {{ $step['active'] ? 'text-gray-800' : 'text-gray-300' }}">
                                                        {{ $step['label'] }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Tampilan Khusus Jika Ditolak --}}
                                    <div
                                        class="mb-10 p-6 bg-red-50 rounded-3xl border border-red-100 flex items-center gap-5">
                                        <div
                                            class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center shrink-0">
                                            <i class="bi bi-exclamation-octagon-fill text-2xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-red-800 text-sm uppercase tracking-tight">Mohon Maaf,
                                                Pesanan Ditolak</h4>
                                            <p class="text-xs text-red-600 mt-1">Pesanan Anda tidak dapat diproses oleh
                                                penjual. Silakan hubungi toko melalui chat untuk informasi lebih lanjut atau
                                                dana Anda akan dikembalikan sesuai kebijakan.</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Item List --}}
                                <div class="space-y-4">
                                    @foreach ($group as $subItem)
                                        <div
                                            class="flex items-center gap-5 p-4 rounded-2xl border border-gray-50 {{ $isRejected ? 'grayscale' : 'bg-gray-50/30' }}">
                                            <img src="{{ asset('storage/' . $subItem->book->photos_product) }}"
                                                onerror="this.src='{{ asset('image/default-buku.avif') }}'"
                                                class="w-16 h-16 rounded-xl object-cover shadow-sm">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 leading-tight">
                                                    {{ $subItem->book->title }}</h4>
                                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase">
                                                    {{ $subItem->qty }} x
                                                    Rp{{ number_format($subItem->price, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-black text-gray-900">
                                                    Rp{{ number_format($subItem->qty * $subItem->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Footer Card --}}
                                <div
                                    class="mt-8 pt-8 border-t border-dashed border-gray-200 flex flex-col lg:flex-row justify-between items-center gap-6">
                                    <div class="flex gap-4 items-center w-full lg:w-auto">
                                        <div
                                            class="w-10 h-10 {{ $isRejected ? 'bg-red-50 text-red-400' : 'bg-orange-50 text-orange-600' }} rounded-xl flex items-center justify-center shrink-0">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="max-w-xs">
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Alamat
                                                Pengiriman</p>
                                            <p class="text-xs font-bold text-gray-600 truncate">{{ Auth::user()->address }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-center gap-6 w-full lg:w-auto">
                                        <div class="text-center sm:text-right">
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Total
                                                Pembayaran</p>
                                            <p
                                                class="text-2xl font-black {{ $isRejected ? 'text-gray-400 line-through' : 'text-teal-600' }} tracking-tighter">
                                                <span
                                                    class="text-sm mr-0.5">Rp</span>{{ number_format($group->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap justify-center gap-2">
                                            <a href="{{ route('chat.index', $firstItem->seller_id) }}"
                                                class="px-4 py-3 border-2 {{ $isRejected ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-gray-100 text-gray-600 hover:bg-gray-100' }} rounded-xl font-bold text-xs transition-all flex items-center gap-2">
                                                <i class="bi bi-chat-dots-fill"></i> Hubungi Toko
                                            </a>

                                            @if (!$isRejected)
                                                @if (in_array($status, ['approved', 'shipping', 'selesai']))
                                                    <a href="{{ route('buyer.invoice.download', $firstItem->order_id) }}"
                                                        class="px-4 py-3 bg-gray-900 text-white rounded-xl font-bold text-xs hover:bg-black transition-all flex items-center gap-2">
                                                        <i class="bi bi-file-earmark-pdf"></i> Invoice
                                                    </a>
                                                @endif

                                                @if (!$hasProof)
                                                    <button
                                                        onclick="openUploadModal('{{ $firstItem->order_id }}', '{{ $firstItem->seller_id }}')"
                                                        class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold text-xs hover:bg-red-700 shadow-lg shadow-red-100 transition-all">
                                                        BAYAR SEKARANG
                                                    </button>
                                                @else
                                                    <button
                                                        onclick="viewPaymentProof('{{ asset('storage/' . $firstItem->payment_proof) }}')"
                                                        class="px-4 py-3 bg-teal-50 text-teal-600 rounded-xl font-bold text-xs hover:bg-teal-100 transition-all flex items-center gap-2">
                                                        <i class="bi bi-eye"></i> Bukti Bayar
                                                    </button>
                                                @endif
                                            @endif

                                            {{-- Tombol Lacak Paket --}}
                                            @if ($firstItem->status === 'shipping')
                                                <a href="https://www.jne.co.id/" target="_blank"
                                                    class="px-4 py-2 bg-blue-500 text-white rounded-xl text-xs font-bold hover:bg-blue-600 transition-all flex items-center gap-2">
                                                    <i class="bi bi-geo-fill"></i>
                                                    Lacak Paket
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-32 bg-white rounded-[3rem] border-4 border-dashed border-gray-100 text-center">
                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="bi bi-box-seam text-4xl text-gray-200"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Tidak ada paket ditemukan</h3>
                            <p class="text-gray-400 text-sm mt-2">Coba cari dengan nomor resi yang berbeda</p>
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
                    <input type="hidden" name="seller_id" id="modal_seller_id">

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
