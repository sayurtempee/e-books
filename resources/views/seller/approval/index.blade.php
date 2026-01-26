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

                    <div class="bg-white px-4 py-2 rounded-xl border">
                        <span class="text-sm font-bold uppercase">
                            {{ $items->count() }} Item
                        </span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white rounded-2xl border overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-xs uppercase">Order</th>
                                <th class="px-6 py-4 text-xs uppercase">Produk</th>
                                <th class="px-6 py-4 text-xs uppercase">Qty</th>
                                <th class="px-6 py-4 text-xs uppercase">Status</th>
                                <th class="px-6 py-4 text-xs uppercase">Resi</th>
                                <th class="px-6 py-4 text-xs uppercase text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($items as $item)
                                <tr>
                                    {{-- ORDER --}}
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-xs text-teal-600">
                                            #ORD-{{ $item->order->id }}
                                        </div>
                                        <div class="font-bold text-sm">
                                            {{ $item->order->user->name }}
                                        </div>
                                    </td>

                                    {{-- PRODUK --}}
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $item->book->photos_product) }}"
                                            class="w-10 h-10 rounded-lg object-cover">
                                        <div>
                                            <div class="font-bold text-sm">
                                                {{ $item->book->title }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Rp{{ number_format($item->price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- QTY --}}
                                    <td class="px-6 py-4 font-bold">
                                        x{{ $item->qty }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="px-6 py-4">
                                        <form action="{{ route('seller.approval.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <select name="status" onchange="this.form.submit()"
                                                class="text-xs rounded-lg border-gray-200">
                                                @foreach (['pending', 'approved', 'shipping', 'refunded'] as $st)
                                                    <option value="{{ $st }}"
                                                        {{ $item->status === $st ? 'selected' : '' }}>
                                                        {{ strtoupper($st) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    {{-- RESI --}}
                                    <td class="px-6 py-4">
                                        @if ($item->status === 'shipping')
                                            <span class="font-mono text-xs">
                                                {{ $item->tracking_number }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs italic">
                                                —
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('chat.index', $item->order->user_id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border hover:bg-teal-50">
                                            <i class="bi bi-chat-dots"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-16 text-gray-400">
                                        Belum ada item untuk diproses
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </x-sidebar>
    @endsection
</x-app>

