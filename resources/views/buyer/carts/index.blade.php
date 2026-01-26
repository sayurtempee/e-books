<x-app>
    @section('title', 'Keranjang Belanja')

    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-800">Keranjang Belanja</h1>
                    <p class="text-sm text-gray-500">Kelola item pilihanmu sebelum checkout.</p>
                </div>

                @if (session('cart') && count(session('cart')) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        {{-- List Item (Sisi Kiri) --}}
                        <div class="lg:col-span-2 space-y-8">
                            @php $total = 0 @endphp

                            {{-- PENGELOMPOKAN BERDASARKAN SELLER --}}
                            @foreach (collect(session('cart'))->groupBy('seller_name') as $sellerName => $items)
                                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                                    {{-- Header Toko --}}
                                    <div class="bg-gray-50/50 px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                                        <i class="bi bi-shop text-teal-600"></i>
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Toko:
                                            {{ $sellerName }}</span>
                                    </div>

                                    <div class="divide-y divide-gray-50">
                                        @foreach ($items as $id => $details)
                                            @php $total += $details['price'] * $details['qty'] @endphp
                                            <div class="p-4 flex items-center gap-4 hover:bg-gray-50/30 transition">
                                                <img src="{{ asset('storage/' . $details['photos_product']) }}"
                                                    class="w-16 h-16 object-cover rounded-lg border">

                                                <div class="flex-1">
                                                    <h3 class="font-bold text-gray-800 text-sm">{{ $details['title'] }}</h3>
                                                    <p class="text-teal-600 font-bold text-xs">Rp
                                                        {{ number_format($details['price'], 0, ',', '.') }}</p>
                                                </div>

                                                <div
                                                    class="flex items-center gap-3 bg-white rounded-lg p-1 border border-gray-200">
                                                    {{-- Tombol Kurangi --}}
                                                    <form action="{{ route('buyer.carts.destroy', $details['id']) }}"
                                                        method="POST">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-rose-600 transition">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                    </form>

                                                    <span
                                                        class="text-xs font-bold text-gray-700 w-4 text-center">{{ $details['qty'] }}</span>

                                                    {{-- Tombol Tambah --}}
                                                    <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $details['id'] }}">
                                                        <button
                                                            class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-teal-600 transition">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Ringkasan & Form (Sisi Kanan) --}}
                        <div class="lg:col-span-1">
                            <div class="bg-white p-6 rounded-xl border border-gray-200 sticky top-6">
                                <h2 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Ringkasan Order</h2>

                                <form action="{{ route('buyer.checkout') }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        {{-- Alamat --}}
                                        <div x-data="{ address: '{{ old('address', auth()->user()->address) }}' }">
                                            <label
                                                class="flex items-center gap-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                                Alamat Pengiriman
                                                {{-- Span Merah: Muncul hanya jika address kosong --}}
                                                <span x-show="!address.trim()" x-transition
                                                    class="lowercase italic font-normal text-rose-500 normal-case">
                                                    (wajib diisi*)
                                                </span>
                                            </label>

                                            <textarea name="address" x-model="address" {{-- Menghubungkan textarea ke state 'address' --}} required rows="3"
                                                class="w-full mt-1 p-3 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 outline-none transition-all"
                                                :class="!address.trim() ? 'border-rose-200 bg-rose-50/30' : 'border-gray-200'"
                                                placeholder="Tuliskan alamat lengkap pengiriman...">{{ old('address', auth()->user()->address) }}</textarea>

                                            {{-- Keterangan Tambahan --}}
                                            <p class="text-[10px] text-gray-400 mt-1">
                                                <i class="bi bi-info-circle"></i>
                                                Pastikan alamat sudah lengkap (Nama Jalan, No. Rumah, RT/RW).
                                            </p>
                                        </div>

                                        {{-- Pembayaran --}}
                                        <div x-data="{ method: '' }">
                                            <label
                                                class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Metode
                                                Pembayaran</label>
                                            <select name="payment_method" x-model="method" required
                                                class="w-full mt-1 p-3 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 outline-none">
                                                <option value="" disabled selected>Pilih Pembayaran</option>
                                                <option value="gopay">GoPay</option>
                                                <option value="ovo">OVO</option>
                                                <option value="qris">QRIS</option>
                                                <option value="transfer">Bank Transfer</option>
                                            </select>

                                            <div x-show="method === 'transfer'"
                                                class="mt-2 p-3 bg-blue-50 text-[10px] text-blue-700 rounded-lg">
                                                Instruksi rekening akan muncul setelah checkout.
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-gray-100">
                                            <div class="flex justify-between items-center mb-4">
                                                <span class="text-gray-500 text-sm">Total Tagihan</span>
                                                <span class="text-xl font-bold text-teal-600">Rp
                                                    {{ number_format($total, 0, ',', '.') }}</span>
                                            </div>

                                            <button type="submit"
                                                class="w-full py-3 bg-teal-600 text-white rounded-lg font-bold text-sm hover:bg-teal-700 transition shadow-lg shadow-teal-100">
                                                Checkout Sekarang
                                            </button>

                                            <a href="{{ route('buyer.orders.index') }}"
                                                class="block text-center mt-3 text-xs text-gray-400 hover:text-teal-600 font-medium transition">
                                                <i class="bi bi-arrow-left"></i> Kembali Belanja
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="bg-white p-16 rounded-xl border border-gray-200 text-center">
                        <div class="text-4xl mb-3">🛒</div>
                        <h2 class="font-bold text-gray-800">Keranjang Kosong</h2>
                        <p class="text-sm text-gray-500 mb-6">Sepertinya Anda belum memilih buku apa pun.</p>
                        <a href="{{ route('buyer.orders.index') }}"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 transition">
                            Cari Buku Sekarang
                        </a>
                    </div>
                @endif
            </div>

            {{-- Modal Sukses (Tetap Sama) --}}
            @if (session('checkout_success'))
                @php $order = \App\Models\Order::with('items.book.user')->find(session('order_id')); @endphp
                @if ($order)
                    @include('buyer.payment.index')
                @endif
            @endif
        </x-sidebar>
    @endsection
</x-app>
