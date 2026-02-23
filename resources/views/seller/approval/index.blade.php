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

                <div class="flex justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800">Approval <span class="text-teal-600">Pesanan</span></h1>
                        <p class="text-gray-500 text-sm">Kelola pengiriman berdasarkan pesanan masuk.</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-xl border shadow-sm">
                        <span class="text-sm font-bold uppercase text-gray-600">{{ $groupedItems->count() }} Pesanan</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">Order & Pembeli</th>
                                <th class="px-6 py-4">Daftar Produk</th>
                                <th class="px-6 py-4">Bukti Bayar</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4">Status Group</th>
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
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-[10px] text-teal-600 font-bold">#ORD-{{ $orderId }}
                                        </div>
                                        <div class="font-bold text-gray-800">
                                            {{ $firstItem->order->user->name ?? 'Nama User Terhapus' }}</div>
                                        <div class="text-[11px] text-gray-500 truncate max-w-[150px]">
                                            {{ $firstItem->order->user->address ?? 'tidak menerapkan lokasi' }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @foreach ($orderItems as $oi)
                                            <div
                                                class="text-gray-700 font-medium text-xs border-l-2 border-teal-500 pl-2 mb-1">
                                                {{ $oi->book->title ?? 'judul buku tidak ada' }}
                                            </div>
                                        @endforeach
                                        {{-- Tampilkan alasan jika ada --}}
                                        @if ($firstItem->cancel_reason)
                                            <div
                                                class="mt-2 text-[10px] p-1.5 bg-red-50 text-red-600 rounded border border-red-100 leading-tight">
                                                <strong>Alasan:</strong> {{ $firstItem->cancel_reason }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($firstItem->payment_proof)
                                            <button type="button"
                                                onclick="openModal('{{ asset('storage/' . $firstItem->payment_proof) }}')"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-teal-50 text-teal-700 border border-teal-200 rounded-xl text-xs font-bold hover:bg-teal-600 hover:text-white transition-all shadow-sm">
                                                <i class="bi bi-file-earmark-check-fill text-sm"></i> Lihat Bukti
                                            </button>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 text-gray-400 border border-gray-200 rounded-xl text-xs font-medium italic">
                                                <i class="bi bi-x-circle text-sm"></i> Belum Ada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center font-bold">x{{ $orderItems->sum('qty') }}</td>

                                    <td class="px-6 py-4">
                                        <form action="{{ route('seller.approval.update', $orderId) }}" method="POST">
                                            @csrf @method('PUT')
                                            <select name="status"
                                                @change="handleStatusChange($event, '{{ $orderId }}', '{{ $allTitles }}')"
                                                class="text-[11px] font-bold rounded-lg border-gray-200 py-1.5 px-2 bg-white outline-none focus:ring-2 focus:ring-teal-500/20">
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
                                                    class="text-[9px] font-bold text-gray-400 uppercase">{{ $firstItem->expedisi_name }}</span>
                                                <span
                                                    class="font-mono text-[10px] font-bold text-teal-700">{{ $firstItem->tracking_number }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-[11px] italic">Belum dikirim</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('chat.index', $firstItem->order->user_id) }}"
                                            class="p-2 text-gray-400 hover:text-teal-600"><i
                                                class="bi bi-chat-dots"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-20 text-gray-400">Tidak ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MODAL SHIPPING --}}
                <template x-teleport="body">
                    <div x-show="openShippingModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
                        <div @click.away="openShippingModal = false"
                            class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                            <h3 class="text-lg font-bold mb-4">Kirim Pesanan</h3>
                            <form :action="actionUrl" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="shipping">
                                <input type="text" name="expedisi_name" required placeholder="Nama Ekspedisi (JNT, JNE)"
                                    class="w-full border rounded-xl p-2 text-sm">
                                <input type="text" name="tracking_number" placeholder="Isi Nomor Resi Pengiriman"
                                    required class="w-full border rounded-xl p-2 text-sm font-mono">
                                <div class="flex gap-2">
                                    <button type="button" @click="openShippingModal = false"
                                        class="flex-1 py-2 text-gray-500">Batal</button>
                                    <button type="submit"
                                        class="flex-1 py-2 bg-teal-600 text-white rounded-xl font-bold">Konfirmasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- MODAL REJECT / REFUND (TAMBAHAN) --}}
                <template x-teleport="body">
                    <div x-show="openRejectModal"
                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
                        <div @click.away="openRejectModal = false"
                            class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl border-t-4"
                            :class="selectedStatus === 'tolak' ? 'border-red-500' : 'border-orange-500'">
                            <h3 class="text-lg font-bold mb-2"
                                x-text="selectedStatus === 'tolak' ? 'Tolak Pesanan' : 'Refund Pesanan'"></h3>
                            <p class="text-[11px] text-gray-500 mb-4 italic" x-text="itemName"></p>
                            <form :action="actionUrl" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" :value="selectedStatus">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 uppercase">Alasan Penolakan/Refund</label>
                                    <textarea name="cancel_reason" required placeholder="Berikan alasan yang jelas kepada pembeli..."
                                        class="w-full border rounded-xl p-3 text-sm focus:ring-2 focus:ring-teal-500 outline-none" rows="3"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="openRejectModal = false"
                                        class="flex-1 py-2 text-gray-500 font-medium">Batal</button>
                                    <button type="submit"
                                        :class="selectedStatus === 'tolak' ? 'bg-red-600' : 'bg-orange-600'"
                                        class="flex-1 py-2 text-white rounded-xl font-bold shadow-sm transition-transform active:scale-95">
                                        Konfirmasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

            </div>

            @include('layouts.modal.bukti_pembayaran')

            <script>
                function openModal(imageSrc) {
                    const modal = document.getElementById('imageModal');
                    const modalContent = document.getElementById('modalContent');
                    const modalImg = document.getElementById('modalImage');
                    modalImg.src = imageSrc;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }

                function closeModal() {
                    const modal = document.getElementById('imageModal');
                    const modalContent = document.getElementById('modalContent');
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 300);
                }
            </script>
        </x-sidebar>
    @endsection
</x-app>
