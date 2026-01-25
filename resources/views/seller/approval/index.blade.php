<x-app>
    @section('title', 'Manajemen Approval')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">

                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 tracking-tight">
                            Approval <span class="text-teal-600">Pesanan</span>
                        </h1>
                        <p class="text-gray-500 text-sm">Validasi pembayaran produk Anda dari berbagai pesanan.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-3 h-3 bg-teal-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-bold text-gray-700 tracking-wide uppercase">
                            {{ $orders->count() }} Pesanan Masuk
                        </span>
                    </div>
                </div>

                {{-- Table Card Premium --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                        Detail Transaksi</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                        Produk Anda</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                        Bukti Bayar</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                        Tagihan & Status</th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                        Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-50">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-teal-50/20 transition-all group">
                                        {{-- Pembeli & ID --}}
                                        <td class="px-6 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[10px] font-mono text-teal-600 font-bold mb-1">#ORD-{{ $order->id }}</span>
                                                <div class="font-bold text-gray-800 text-base leading-tight">
                                                    {{ $order->user->name ?? 'User' }}</div>
                                                <div
                                                    class="flex items-center gap-1 text-[10px] text-gray-400 font-bold uppercase mt-1">
                                                    <i class="bi bi-geo-alt-fill text-rose-400"></i>
                                                    <span
                                                        class="truncate max-w-[150px]">{{ $order->address ?? $order->user->address }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Produk Pesanan (Hanya milik Seller ini) --}}
                                        <td class="px-6 py-6">
                                            <div class="space-y-2">
                                                @foreach ($order->items as $item)
                                                    <div
                                                        class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl border border-gray-100">
                                                        <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                                            class="w-8 h-8 rounded-lg object-cover">
                                                        <div>
                                                            <div
                                                                class="font-bold text-gray-700 text-[11px] truncate max-w-[140px]">
                                                                {{ $item->book->title }}</div>
                                                            <span
                                                                class="text-[9px] bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded-full font-black">x{{ $item->qty }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- Bukti Bayar --}}
                                        <td class="px-6 py-6 text-center">
                                            @if ($order->payment_proof)
                                                <button type="button"
                                                    onclick="openModal('{{ asset('storage/' . $order->payment_proof) }}')"
                                                    class="w-12 h-12 bg-white border-2 border-teal-100 rounded-2xl hover:border-teal-500 transition-all shadow-sm flex items-center justify-center relative group/img">
                                                    <i class="bi bi-image text-teal-600 text-xl"></i>
                                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                        <span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
                                                    </span>
                                                </button>
                                            @else
                                                <div class="text-gray-300 italic text-[10px] font-bold">BELUM UPLOAD</div>
                                            @endif
                                        </td>

                                        {{-- Status & Tagihan --}}
                                        <td class="px-6 py-6">
                                            <form id="update-form-{{ $order->id }}"
                                                action="{{ route('seller.approval.update', $order->id) }}" method="POST"
                                                class="space-y-2">
                                                @csrf @method('PUT')
                                                <div class="text-sm font-black text-gray-900 mb-1">
                                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}</div>

                                                <select name="status" onchange="this.form.submit()"
                                                    class="w-full bg-gray-50 border-none rounded-xl text-[10px] font-black py-2 px-3 focus:ring-2 focus:ring-teal-500 transition-all uppercase tracking-tighter">
                                                    @foreach (['pending', 'approved', 'shipping', 'refunded'] as $st)
                                                        <option value="{{ $st }}"
                                                            {{ $order->status == $st ? 'selected' : '' }}>
                                                            {{ $st }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if (in_array($order->status, ['approved', 'shipping']))
                                                    <div class="relative group">
                                                        <span
                                                            class="absolute left-2 top-1/2 -translate-y-1/2 text-[8px] font-black text-teal-600 bg-white px-1 rounded border border-teal-100">JNE</span>
                                                        <input type="text" name="tracking_number"
                                                            placeholder="Input Resi"
                                                            value="{{ str_replace('JNE', '', $order->tracking_number) }}"
                                                            class="w-full border border-gray-100 rounded-xl pl-9 pr-2 py-1.5 text-[10px] font-bold focus:border-teal-500 bg-white">
                                                    </div>
                                                @endif
                                            </form>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-6 py-6">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="submit" form="update-form-{{ $order->id }}"
                                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-teal-600 text-white hover:bg-black transition-all shadow-lg shadow-teal-100">
                                                    <i class="bi bi-send-check"></i>
                                                </button>

                                                <a href="{{ route('chat.index', $order->user_id) }}"
                                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-blue-500 transition-all">
                                                    <i class="bi bi-chat-dots"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-24 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="bi bi-collection text-5xl text-gray-200 mb-4"></i>
                                                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Belum
                                                    ada pesanan untuk disetujui</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('layouts.modal.bukti_pembayaran')
        </x-sidebar>
    @endsection
</x-app>
