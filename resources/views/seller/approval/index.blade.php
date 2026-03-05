<x-app>
    @section('title', 'Manajemen Approval')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen" x-data="{
                openShippingModal: false,
                openRejectModal: false,
                itemName: '',
                actionUrl: '',
                selectedStatus: '',
                handleStatusChange(event, id, name) {
                    const status = event.target.value;
                    this.selectedStatus = status;
                    this.itemName = name;
                    this.actionUrl = `/seller/approval/${id}`;
            
                    if (status === 'shipping') {
                        this.openShippingModal = true;
                    } else if (status === 'tolak' || status === 'refunded') {
                        this.openRejectModal = true;
                    } else {
                        event.target.form.submit();
                    }
                }
            }">

                {{-- HEADER & TITLE --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white shadow-lg">
                            <i class="bi bi-box-seam text-xl"></i>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black text-gray-800">
                                Approval <span class="text-teal-600">Pesanan</span>
                            </h1>
                            <p class="text-gray-500 text-sm">
                                Kelola pengiriman dan status pesanan masuk.
                            </p>
                        </div>
                    </div>

                    {{-- SEARCH & FILTER BAR --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('seller.approval.index') }}" method="GET" class="flex items-center gap-2">
                            {{-- Input Search --}}
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari Order / Pembeli / Buku..."
                                    class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-teal-500 outline-none w-64 shadow-sm transition-all">
                                <i class="bi bi-search absolute left-3.5 top-3 text-gray-400"></i>
                            </div>

                            {{-- Select Filter Status --}}
                            <select name="status" onchange="this.form.submit()"
                                class="py-2.5 px-4 rounded-xl border border-gray-200 bg-white text-sm font-bold text-gray-600 focus:ring-2 focus:ring-teal-500 outline-none shadow-sm cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="tolak" {{ request('status') == 'tolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded
                                </option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                            </select>

                            @if (request('search') || request('status'))
                                <a href="{{ route('seller.approval.index') }}"
                                    class="p-2.5 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- TABLE SECTION --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr class="text-[10px] uppercase text-gray-400 font-black tracking-widest">
                                    <th class="px-6 py-4">Order & Pembeli</th>
                                    <th class="px-6 py-4">Daftar Produk</th>
                                    <th class="px-6 py-4">Bukti Bayar</th>
                                    <th class="px-6 py-4 text-center">Qty</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Info Pengiriman</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse ($groupedItems as $orderId => $orderItems)
                                    @php
                                        $firstItem = $orderItems->first();
                                        $allTitles = $orderItems->pluck('book.title')->filter()->join(', ');
                                    @endphp
                                    <tr class="hover:bg-teal-50/20 transition group">
                                        <td class="px-6 py-4">
                                            <div class="font-mono text-[10px] text-teal-600 font-bold mb-1">
                                                #ORD-{{ $orderId }}</div>
                                            <div class="font-bold text-gray-800">
                                                {{ $firstItem->order->user->name ?? 'User Terhapus' }}</div>
                                            <div class="text-[11px] text-gray-400 truncate max-w-[150px]">
                                                {{ $firstItem->order->user->address ?? '-' }}</div>
                                        </td>

                                        <td class="px-6 py-4">
                                            @foreach ($orderItems as $oi)
                                                <div class="text-gray-700 font-medium text-xs mb-1 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                                                    {{ $oi->book->title ?? 'Judul Tidak Ada' }}
                                                </div>
                                            @endforeach
                                            @if ($firstItem->cancel_reason)
                                                <div
                                                    class="mt-2 text-[10px] p-2 bg-rose-50 text-rose-600 rounded-lg border border-rose-100 italic">
                                                    <strong>Alasan:</strong> {{ $firstItem->cancel_reason }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if ($firstItem->payment_proof)
                                                <button type="button"
                                                    onclick="openModal('{{ asset('storage/' . $firstItem->payment_proof) }}')"
                                                    class="p-2 bg-white border rounded-xl shadow-sm text-teal-600 hover:bg-teal-600 hover:text-white transition-all">
                                                    <i class="bi bi-image"></i>
                                                </button>
                                            @else
                                                <span class="text-gray-300 italic text-xs">Kosong</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center font-black text-gray-700 italic">
                                            x{{ $orderItems->sum('qty') }}</td>

                                        <td class="px-6 py-4">
                                            <form action="{{ route('seller.approval.update', $orderId) }}" method="POST">
                                                @csrf @method('PUT')
                                                <select name="status"
                                                    @change="handleStatusChange($event, '{{ $orderId }}', '{{ $allTitles }}')"
                                                    class="text-[10px] font-black uppercase rounded-lg border-gray-200 py-1.5 px-2 bg-white outline-none focus:ring-2 focus:ring-teal-500/20 cursor-pointer
                                                    {{ $firstItem->status == 'tolak' ? 'text-red-600' : ($firstItem->status == 'selesai' ? 'text-green-600' : 'text-gray-700') }}">
                                                    @foreach (['tolak', 'pending', 'approved', 'shipping', 'selesai', 'refunded'] as $st)
                                                        <option value="{{ $st }}"
                                                            {{ $firstItem->status === $st ? 'selected' : '' }}>
                                                            {{ strtoupper($st) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($firstItem->tracking_number)
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[9px] font-black text-teal-600 uppercase">{{ $firstItem->expedisi_name }}</span>
                                                    <span
                                                        class="font-mono text-[10px] text-gray-500">{{ $firstItem->tracking_number }}</span>
                                                </div>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 bg-gray-100 text-gray-400 text-[9px] rounded font-bold uppercase">Di
                                                    {{ $firstItem->status }}</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('chat.index', $firstItem->order->user_id) }}"
                                                class="w-9 h-9 inline-flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-teal-50 hover:text-teal-600 transition-all shadow-sm">
                                                <i class="bi bi-chat-dots-fill text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-20">
                                            <i class="bi bi-inbox text-5xl text-gray-200 mb-4 block"></i>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Pesanan
                                                tidak ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- MODAL SHIPPING --}}
                <template x-teleport="body">
                    <div x-show="openShippingModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
                        <div @click.away="openShippingModal = false"
                            class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-2 bg-teal-500"></div>
                            <h3 class="text-xl font-black text-gray-800 mb-2">Konfirmasi Pengiriman</h3>
                            <p class="text-sm text-gray-500 mb-6 italic">Masukkan informasi pelacakan untuk pesanan ini.</p>

                            <form :action="actionUrl" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="shipping">
                                <div>
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Ekspedisi</label>
                                    <input type="text" name="expedisi_name" required
                                        placeholder="Contoh: JNE, J&T, SiCepat"
                                        class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Nomor
                                        Resi</label>
                                    <input type="text" name="tracking_number" placeholder="Contoh: JX123456789"
                                        required
                                        class="w-full border-gray-200 rounded-xl p-3 text-sm font-mono focus:ring-2 focus:ring-teal-500 outline-none transition-all">
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="openShippingModal = false"
                                        class="flex-1 py-3 text-gray-400 font-bold text-sm">Batal</button>
                                    <button type="submit"
                                        class="flex-1 py-3 bg-teal-600 text-white rounded-xl font-black shadow-lg shadow-teal-200 active:scale-95 transition-all">Kirim
                                        Sekarang</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- MODAL REJECT / REFUND --}}
                <template x-teleport="body">
                    <div x-show="openRejectModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
                        x-cloak>
                        <div @click.away="openRejectModal = false"
                            class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-2"
                                :class="selectedStatus === 'tolak' ? 'bg-rose-500' : 'bg-orange-500'"></div>
                            <h3 class="text-xl font-black text-gray-800 mb-2"
                                x-text="selectedStatus === 'tolak' ? 'Tolak Pesanan' : 'Proses Refund'"></h3>
                            <p class="text-[11px] text-gray-400 mb-6 italic" x-text="itemName"></p>

                            <form :action="actionUrl" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" :value="selectedStatus">
                                <div>
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Alasan
                                        Pembatalan</label>
                                    <textarea name="cancel_reason" required placeholder="Berikan alasan yang jelas agar pembeli dapat memahami..."
                                        class="w-full border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition-all"
                                        rows="4"></textarea>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="openRejectModal = false"
                                        class="flex-1 py-3 text-gray-400 font-bold text-sm">Batal</button>
                                    <button type="submit"
                                        :class="selectedStatus === 'tolak' ? 'bg-rose-600' : 'bg-orange-600'"
                                        class="flex-1 py-3 text-white rounded-xl font-black shadow-lg active:scale-95 transition-all">Konfirmasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

            </div>

            @include('layouts.modal.bukti_pembayaran')

        </x-sidebar>
    @endsection
</x-app>
