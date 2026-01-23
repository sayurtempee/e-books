<x-app>
    @section('title', 'Manajemen Approval')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">

                {{-- Title --}}
                <h1 class="text-2xl font-bold text-teal-600 mb-6">
                    List Approval Pesanan
                </h1>

                {{-- Stats / Badge --}}
                <div class="mb-6">
                    <span
                        class="bg-teal-100 text-teal-700 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm border border-teal-200">
                        <i class="bi bi-clipboard-check mr-1"></i> Total: {{ $orders->count() }} Pesanan
                    </span>
                </div>

                {{-- Table Container --}}
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-teal-200 rounded-lg overflow-hidden">

                            {{-- Head (Konsisten dengan Daftar Buku) --}}
                            <thead class="bg-teal-500 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">Pembeli & Alamat</th>
                                    <th class="px-4 py-3 text-left">Metode & Judul Buku</th>
                                    <th class="px-4 py-3 text-left">Total</th>
                                    <th class="px-4 py-3 text-center">Bukti</th>
                                    <th class="px-4 py-3 text-left">Status & Resi</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>

                            {{-- Body --}}
                            <tbody class="bg-white divide-y divide-teal-100">
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-teal-50 transition">
                                        {{-- Pembeli & Alamat --}}
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">{{ $order->user->name ?? 'User' }}</div>
                                            <div class="text-[10px] text-teal-600 font-semibold uppercase">
                                                {{ $order->payment_method ?? 'Manual Transfer' }}</div>
                                            <div class="text-[11px] text-gray-500 mt-1 max-w-[180px] leading-tight italic">
                                                <i
                                                    class="bi bi-geo-alt-fill mr-1"></i>{{ $order->user->address ?? 'Alamat tidak tersedia' }}
                                            </div>
                                            <div class="text-[9px] text-gray-400 font-mono mt-1">#ORD-{{ $order->id }}
                                            </div>
                                        </td>

                                        {{-- Judul & Qty --}}
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-1">
                                                @foreach ($order->items as $item)
                                                    <div
                                                        class="text-gray-700 text-xs border-b border-gray-50 last:border-0 pb-1">
                                                        <span class="font-medium">{{ $item->book->title ?? '-' }}</span>
                                                        <span class="text-teal-600 font-bold">(x{{ $item->qty }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- ... kolom total, bukti, status tetap sama seperti kode sebelumnya ... --}}

                                        {{-- Total --}}
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>

                                        {{-- Bukti --}}
                                        <td class="px-4 py-3 text-center">
                                            @include('layouts.modal.bukti_pembayaran')
                                            @if ($order->payment_proof)
                                                <button type="button"
                                                    onclick="openModal('{{ asset('storage/' . $order->payment_proof) }}')"
                                                    class="text-teal-600">
                                                    <i class="bi bi-file-earmark-image text-xl"></i>
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                            @endif
                                        </td>

                                        {{-- Form Status --}}
                                        <td class="px-4 py-3">
                                            <form id="update-form-{{ $order->id }}"
                                                action="{{ route('seller.approval.update', $order->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <select name="status" onchange="this.form.submit()"
                                                    class="border border-teal-200 rounded-md text-xs p-1">
                                                    @foreach (['pending', 'approved', 'shipping', 'refunded'] as $st)
                                                        <option value="{{ $st }}"
                                                            {{ $order->status == $st ? 'selected' : '' }}>
                                                            {{ ucfirst($st) }}</option>
                                                    @endforeach
                                                </select>
                                                @if (in_array($order->status, ['approved', 'shipping']))
                                                    <div class="relative flex items-center mt-2">
                                                        <span
                                                            class="absolute left-2 text-[9px] font-bold text-gray-400">JNE</span>
                                                        <input type="text" name="tracking_number"
                                                            value="{{ str_replace('JNE', '', $order->tracking_number) }}"
                                                            class="w-full border border-teal-200 rounded-md pl-8 pr-2 py-1 text-xs">
                                                    </div>
                                                @endif
                                            </form>
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-4">
                                                @if (in_array($order->status, ['approved', 'shipping']))
                                                    <button type="submit" form="update-form-{{ $order->id }}"
                                                        class="text-teal-600">
                                                        <i class="bi bi-save2-fill text-lg"></i>
                                                    </button>
                                                @endif

                                                @if ($order->status === 'refunded')
                                                    <form id="deleteOrderForm-{{ $order->id }}"
                                                        action="{{ route('seller.approval.delete', $order->id) }}"
                                                        method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDeleteOrder({{ $order->id }})"
                                                            class="text-red-500">
                                                            <i class="bi bi-trash text-lg"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
