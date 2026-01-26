<x-app>
    @section('title', 'Manajemen Approval')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
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
                                <th class="px-6 py-4">Qty</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">No. Resi</th> {{-- Kolom Resi Kembali --}}
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-[10px] text-teal-600 font-bold">
                                            #ORD-{{ $item->order->id }}</div>
                                        <div class="font-bold text-sm text-gray-800">{{ $item->order->user->name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-[180px]">
                                            {{ $item->order->user->address }}</div>
                                    </td>

                                    <td class="px-6 py-4 font-medium text-sm text-gray-700">
                                        {{ $item->book->title }}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <button onclick="openModal('{{ asset('storage/' . $item->order->payment_proof) }}')"
                                            class="p-2 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition-all active:scale-90 shadow-sm border border-teal-100">
                                            <i class="bi bi-receipt text-lg"></i>
                                        </button>
                                    </td>

                                    <td class="px-6 py-4 font-bold text-sm">x{{ $item->qty }}</td>

                                    <td class="px-6 py-4">
                                        <form action="{{ route('seller.approval.update', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-[11px] font-bold rounded-lg border-gray-200 focus:ring-teal-500 focus:border-teal-500 py-1 px-2 bg-white">
                                                @foreach (['pending', 'approved', 'shipping', 'refunded'] as $st)
                                                    <option value="{{ $st }}"
                                                        {{ $item->status === $st ? 'selected' : '' }}>
                                                        {{ strtoupper($st) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    {{-- BAGIAN RESI --}}
                                    <td class="px-6 py-4">
                                        @if ($item->tracking_number)
                                            <div class="bg-gray-100 px-2 py-1 rounded border border-gray-200 inline-block">
                                                <span class="font-mono text-[11px] font-bold text-gray-700 uppercase">
                                                    {{ $item->tracking_number }}
                                                </span>
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
            </div>
        </x-sidebar>

        {{-- Modal Tetap di Luar --}}
        @include('layouts.modal.bukti_pembayaran')

    @endsection
</x-app>
