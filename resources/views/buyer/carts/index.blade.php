<x-app>
    @section('title', 'Keranjang Belanja')
    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-800">Keranjang Belanja</h1>
                    <p class="text-sm text-gray-500">Kelola item pilihanmu sebelum checkout.</p>
                </div>

                @if (session('cart') && count(session('cart')) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-8">
                            @php $total = 0 @endphp
                            @foreach (collect(session('cart'))->groupBy('seller_name') as $sellerName => $items)
                                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                                    <div class="bg-gray-50/50 px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                                        <i class="bi bi-shop text-teal-600"></i>
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Toko:
                                            {{ $sellerName }}</span>
                                    </div>
                                    <div class="divide-y divide-gray-50">
                                        @foreach ($items as $id => $details)
                                            @php $total += $details['price'] * $details['qty'] @endphp
                                            <div class="p-4 flex items-center gap-4">
                                                <img src="{{ asset('storage/' . $details['photos_product']) }}"
                                                    class="w-16 h-16 object-cover rounded-lg border">
                                                <div class="flex-1">
                                                    <h3 class="font-bold text-gray-800 text-sm">{{ $details['title'] }}</h3>
                                                    <p class="text-teal-600 font-bold text-xs">Rp
                                                        {{ number_format($details['price'], 0, ',', '.') }}</p>
                                                </div>
                                                <div
                                                    class="flex items-center gap-3 bg-white rounded-lg p-1 border border-gray-200">
                                                    <form action="{{ route('buyer.carts.destroy', $details['id']) }}"
                                                        method="POST">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-rose-600"><i
                                                                class="bi bi-dash"></i></button>
                                                    </form>
                                                    <span
                                                        class="text-xs font-bold text-gray-700 w-4 text-center">{{ $details['qty'] }}</span>
                                                    <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $details['id'] }}">
                                                        <button
                                                            class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-teal-600"><i
                                                                class="bi bi-plus"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-white p-6 rounded-xl border border-gray-200 sticky top-6">
                                <h2 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Ringkasan Order</h2>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center mb-6">
                                        <span class="text-gray-500 text-sm">Total Tagihan</span>
                                        <span class="text-xl font-bold text-teal-600">Rp
                                            {{ number_format($total, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('buyer.checkout.confirm') }}"
                                        class="w-full block text-center py-3 bg-teal-600 text-white rounded-lg font-bold text-sm hover:bg-teal-700 transition shadow-lg shadow-teal-100">
                                        Lanjut ke Konfirmasi
                                    </a>
                                    <a href="{{ route('buyer.orders.index') }}"
                                        class="block text-center mt-3 text-xs text-gray-400 hover:text-teal-600 font-medium">
                                        <i class="bi bi-arrow-left"></i> Kembali Belanja
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white p-16 rounded-xl border border-gray-200 text-center">
                        <div class="text-4xl mb-3">🛒</div>
                        <h2 class="font-bold text-gray-800">Keranjang Kosong</h2>
                        <a href="{{ route('buyer.orders.index') }}"
                            class="mt-4 inline-block px-6 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold">Cari
                            Buku Sekarang</a>
                    </div>
                @endif
            </div>

            @if (session('checkout_success'))
                @php $order = \App\Models\Order::with('items.book.user')->find(session('order_id')); @endphp
                @if ($order)
                    @include('buyer.payment.index')
                @endif
            @endif
        </x-sidebar>
    @endsection
</x-app>
