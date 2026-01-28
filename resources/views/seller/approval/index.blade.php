<x-app>
    @section('title', 'Manajemen Approval')

    @section('body-content')
        <x-sidebar>
            {{-- Tambahkan x-data di pembungkus utama --}}
            <div class="p-8 bg-gray-50 min-h-screen" x-data="{
                openShippingModal: false,
                itemName: '',
                actionUrl: '',
                currentStatus: '',
                handleStatusChange(event, id, name) {
                    const status = event.target.value;
                    if (status === 'shipping') {
                        this.openShippingModal = true;
                        this.itemName = name;
                        this.actionUrl = `/seller/approval/${id}`; // Sesuaikan dengan route update Anda
                        // Kembalikan visual ke status sebelumnya jika modal dibatalkan
                        this.currentStatus = status;
                    } else {
                        event.target.form.submit();
                    }
                }
            }">

                {{-- Header --}}
                <div class="flex justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800">
                            Approval <span class="text-teal-600">Item Pesanan</span>
                        </h1>
                        <p class="text-gray-500 text-sm">Kelola produk milik Anda.</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-xl border shadow-sm">
                        <span class="text-sm font-bold uppercase text-gray-600">{{ $items->count() }} Item</span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">Order</th>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4 text-center">Bukti Bayar</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Resi & Ekspedisi</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($items as $item)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-[10px] text-teal-600 font-bold">
                                            #ORD-{{ $item->order->id }}</div>
                                        <div class="font-bold text-gray-800">{{ $item->order->user->name }}</div>
                                        <div class="text-[11px] text-gray-500 truncate max-w-[150px]">
                                            {{ $item->order->user->address }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-700">{{ $item->book->title }}</div>
                                        <div class="text-[10px] text-gray-400">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if ($item->payment_proof)
                                            <button type="button"
                                                onclick="openModal('{{ asset('storage/' . $item->payment_proof) }}')"
                                                class="p-2 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition-all shadow-sm border border-teal-100">
                                                <i class="bi bi-receipt"></i>
                                            </button>
                                        @else
                                            <span class="text-[10px] font-bold text-gray-400 italic">Belum Upload</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center font-bold">x{{ $item->qty }}</td>

                                    <td class="px-6 py-4">
                                        <form action="{{ route('seller.approval.update', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <select name="status"
                                                @change="handleStatusChange($event, '{{ $item->id }}', '{{ $item->book->title }}')"
                                                class="text-[11px] font-bold rounded-lg border-gray-200 py-1.5 px-2 bg-white outline-none focus:ring-2 focus:ring-teal-500/20
                                                {{ $item->status === 'pending' ? 'text-amber-600' : '' }}
                                                {{ $item->status === 'approved' ? 'text-emerald-600' : '' }}
                                                {{ $item->status === 'shipping' ? 'text-blue-600' : '' }}
                                                {{ $item->status === 'refunded' ? 'text-red-600' : '' }}">
                                                @foreach (['pending', 'approved', 'shipping', 'selesai', 'refunded'] as $st)
                                                    <option value="{{ $st }}"
                                                        {{ $item->status === $st ? 'selected' : '' }}>
                                                        {{ strtoupper($st) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($item->tracking_number)
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item->expedisi_name ?? 'Kurir' }}</span>
                                                <div
                                                    class="bg-gray-100 px-2 py-1 rounded border border-gray-200 inline-block w-fit">
                                                    <span
                                                        class="font-mono text-[10px] font-bold text-gray-700 uppercase">{{ $item->tracking_number }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-[11px] italic">Belum Ada</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('chat.index', $item->order->user_id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-200 text-gray-400 hover:bg-teal-50 hover:text-teal-600 transition shadow-sm">
                                            <i class="bi bi-chat-dots"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-20 text-gray-400 italic">Belum ada item untuk
                                        diproses</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MODAL SHIPPING (ALPINES) --}}
                <template x-teleport="body">
                    <div x-show="openShippingModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="fixed inset-0 z-[100] flex items-center justify-center bg-teal-900/30 backdrop-blur-sm"
                        x-cloak>

                        <div @click.away="openShippingModal = false"
                            class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl border border-teal-50">

                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-truck text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-800">Input Resi Pengiriman</h3>
                                    <p class="text-[10px] text-gray-500 truncate max-w-[200px]" x-text="itemName"></p>
                                </div>
                            </div>

                            <form :action="actionUrl" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="shipping">

                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-teal-600/60 uppercase mb-1 ml-1 tracking-widest">Nama
                                        Ekspedisi</label>
                                    <input type="text" name="expedisi_name" required placeholder="JNT, JNE, Sicepat, dll"
                                        class="w-full h-10 px-3 text-sm border border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 outline-none transition placeholder:text-gray-300">
                                </div>

                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-teal-600/60 uppercase mb-1 ml-1 tracking-widest">Nomor
                                        Resi</label>
                                    <input type="text" name="tracking_number" required
                                        placeholder="Masukkan nomor resi..."
                                        class="w-full h-10 px-3 text-sm border border-gray-200 rounded-xl focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 outline-none font-mono tracking-widest transition placeholder:text-gray-300">
                                    @error('tracking_number')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button type="button" @click="openShippingModal = false"
                                        class="flex-1 py-2.5 text-xs font-semibold text-gray-500 hover:bg-gray-100 rounded-xl transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="flex-1 py-2.5 text-xs font-bold bg-teal-600 text-white rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-100 transition">
                                        Konfirmasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </x-sidebar>

        {{-- Modal Bukti Pembayaran (Script bawaan Anda) --}}
        @include('layouts.modal.bukti_pembayaran')

    @endsection
</x-app>
