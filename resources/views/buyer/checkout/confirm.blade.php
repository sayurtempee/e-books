<x-app>
    @section('title', 'Konfirmasi Pesanan')
    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                <div class="mb-6 flex items-center gap-4">
                    <a href="{{ route('buyer.carts.index') }}"
                        class="w-10 h-10 flex items-center justify-center bg-white border rounded-full text-gray-400 hover:text-teal-600"><i
                            class="bi bi-chevron-left"></i></a>
                    <h1 class="text-2xl font-bold text-gray-800">Selesaikan Pesanan</h1>
                </div>

                <form action="{{ route('buyer.checkout') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2"><i
                                        class="bi bi-geo-alt text-teal-600"></i> Alamat Pengiriman</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100"><label
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama
                                            Penerima</label>
                                        <p class="text-sm font-bold text-gray-700">{{ auth()->user()->name }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100"><label
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kontak</label>
                                        <p class="text-sm font-bold text-gray-700">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <textarea name="address" required rows="3"
                                    class="w-full p-4 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 outline-none">{{ old('address', auth()->user()->address) }}</textarea>
                            </div>

                            <div class="space-y-4">
                                @foreach (collect($cart)->groupBy('seller_name') as $sellerName => $items)
                                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                                        <div
                                            class="bg-teal-50/50 px-5 py-2 border-b border-teal-100 flex items-center gap-2">
                                            <i class="bi bi-shop text-teal-600"></i><span
                                                class="text-xs font-black text-teal-800">PENJUAL: {{ $sellerName }}</span>
                                        </div>
                                        @foreach ($items as $item)
                                            <div class="p-4 flex gap-4 border-b last:border-0 items-center">
                                                <img src="{{ asset('storage/' . $item['photos_product']) }}"
                                                    class="w-12 h-12 object-cover rounded-lg border">
                                                <div class="flex-1 text-sm font-bold text-gray-800">{{ $item['title'] }}
                                                </div>
                                                <div class="text-xs text-teal-600 font-bold">Rp
                                                    {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase mb-4 tracking-widest">Data Penjual
                                </h3>
                                @foreach ($sellers as $seller)
                                    <div class="flex items-center gap-3 p-3 mb-2 bg-gray-50 rounded-xl">
                                        <div
                                            class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold">
                                            {{ substr($seller->name, 0, 1) }}</div>
                                        <div class="overflow-hidden">
                                            <p class="text-xs font-bold truncate">{{ $seller->name }}</p>
                                            <p class="text-[9px] text-gray-500 italic">
                                                {{ $seller->bank_name ?? 'Transfer' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="bg-white p-6 rounded-2xl border-2 border-teal-600 shadow-xl">
                                <h3 class="font-bold text-gray-800 mb-4">Metode Pembayaran</h3>
                                <select name="payment_method" required
                                    class="w-full p-4 text-sm border border-gray-200 rounded-xl mb-6 outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="" disabled selected>Pilih Pembayaran</option>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                                <div class="flex justify-between font-black text-xl mb-6">
                                    <span class="text-sm font-normal text-gray-500">Total Tagihan</span>
                                    <span class="text-teal-600">Rp
                                        {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['qty']), 0, ',', '.') }}</span>
                                </div>
                                <button type="submit"
                                    class="w-full py-4 bg-teal-600 text-white rounded-xl font-black text-sm hover:bg-teal-700 shadow-lg">CHECKOUT
                                    SEKARANG</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </x-sidebar>
    @endsection
</x-app>
