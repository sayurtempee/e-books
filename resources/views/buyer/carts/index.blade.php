<x-app>
    @section('title', 'List Keranjang')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                <h1 class="text-3xl font-extrabold text-gray-800 border-l-4 border-teal-600 pl-4 mb-8">
                    Keranjang Belanja
                </h1>

                @if (session('cart') && count(session('cart')) > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="p-4 font-semibold text-gray-600">Produk</th>
                                    <th class="p-4 font-semibold text-gray-600">Harga</th>
                                    <th class="p-4 font-semibold text-gray-600 text-center">Jumlah</th>
                                    <th class="p-4 font-semibold text-gray-600 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0 @endphp
                                @foreach (session('cart') as $id => $details)
                                    @php $total += $details['price'] * $details['qty'] @endphp
                                    <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 flex items-center gap-4">
                                            <img src="{{ asset('storage/' . $details['photos_product']) }}"
                                                class="w-16 h-16 object-cover rounded-lg border">
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $details['title'] }}</div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-gray-600">
                                            Rp {{ number_format($details['price'], 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                {{-- Tombol Kurangi (-) --}}
                                                <form action="{{ route('buyer.carts.destroy', $id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center bg-rose-100 text-rose-600 rounded-full hover:bg-rose-200 transition-colors shadow-sm"
                                                        title="Kurangi">
                                                        <i class="bi bi-dash-lg"></i>
                                                    </button>
                                                </form>

                                                {{-- Angka Jumlah --}}
                                                <span class="w-10 text-center font-bold text-gray-800">
                                                    {{ $details['qty'] }}
                                                </span>

                                                {{-- Tombol Tambah (+) --}}
                                                <form action="{{ route('buyer.carts.store') }}" method="POST">
                                                    @csrf
                                                    {{-- Data disesuaikan dengan key session cart Anda --}}
                                                    <input type="hidden" name="book_id" value="{{ $id }}">
                                                    <input type="hidden" name="title" value="{{ $details['title'] }}">
                                                    <input type="hidden" name="price" value="{{ $details['price'] }}">
                                                    <input type="hidden" name="photo"
                                                        value="{{ $details['photos_product'] }}">

                                                    <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center bg-teal-100 text-teal-600 rounded-full hover:bg-teal-200 transition-colors shadow-sm"
                                                        title="Tambah">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right font-bold text-teal-600">
                                            Rp {{ number_format($details['price'] * $details['qty'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Ringkasan Total & Form Checkout --}}
                        <div class="p-8 bg-teal-50">
                            <form action="{{ route('buyer.checkout') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                    {{-- Bagian Alamat --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                                            Alamat Pengiriman
                                        </label>
                                        <textarea name="address" required
                                            class="w-full border-teal-200 rounded-xl shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm p-4"
                                            placeholder="Tuliskan alamat lengkap Anda..." rows="3">{{ old('address', auth()->user()->address) }}</textarea>

                                        @if (auth()->user()->address)
                                            <p class="text-[10px] text-gray-400 mt-1 italic">
                                                *Menggunakan alamat dari profil Anda. Silakan ubah jika ingin kirim ke
                                                tempat lain.
                                            </p>
                                        @else
                                            <p class="text-[10px] text-rose-500 mt-1 italic">
                                                *Alamat profil kosong. Silakan isi alamat pengiriman di atas.
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Bagian Metode Pembayaran --}}
                                    <div x-data="{ paymentMethod: '' }">
                                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                                            Metode Pembayaran
                                        </label>

                                        <select name="payment_method" x-model="paymentMethod" required
                                            class="w-full border-teal-200 rounded-xl shadow-sm focus:ring-teal-500 focus:border-teal-500 text-sm p-4">
                                            <option value="" disabled selected>Pilih Metode Pembayaran</option>
                                            <option value="gopay">GoPay</option>
                                            <option value="ovo">OVO</option>
                                            <option value="qris">QRIS</option>
                                            <option value="transfer">Bank Transfer (Manual)</option>
                                        </select>

                                        <div x-show="paymentMethod === 'transfer'"
                                            class="mt-4 p-4 bg-teal-50 border border-teal-200 rounded-xl">
                                            <p class="text-xs text-teal-700 font-bold uppercase mb-2">
                                                Bank Transfer (Marketplace)
                                            </p>
                                            <p class="text-[11px] text-gray-600">
                                                • Rekening tujuan akan ditampilkan setelah checkout
                                                • Jika membeli dari beberapa seller, sistem akan menampilkan
                                                <span class="font-semibold">rekening masing-masing seller</span>
                                                di halaman pembayaran.
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-teal-600 mt-3 flex items-center gap-1">
                                        <i class="bi bi-info-circle-fill"></i>
                                        Instruksi pembayaran akan muncul setelah Anda klik Checkout.
                                    </p>
                                </div>
                        </div>

                        <div
                            class="flex flex-col md:flex-row justify-between items-center gap-6 border-t border-teal-200 pt-8">
                            <div>
                                <span class="text-gray-500 font-medium">Total Estimasi Pembayaran:</span>
                                <div class="text-3xl font-black text-teal-700">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('buyer.orders.index') }}"
                                    class="px-6 py-3 bg-white border-2 border-teal-600 text-teal-600 rounded-xl hover:bg-teal-50 transition-all font-bold">
                                    Lanjut Belanja
                                </a>
                                <button type="submit"
                                    class="px-10 py-3 bg-teal-600 text-white rounded-xl hover:bg-teal-700 shadow-xl shadow-teal-200 transition-all font-black uppercase tracking-widest transform hover:-translate-y-1">
                                    Checkout Sekarang
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
            </div>
        @else
            {{-- Tampilan Jika Keranjang Kosong --}}
            <div class="bg-white p-12 rounded-xl border border-dashed border-gray-300 text-center">
                <div class="text-5xl mb-4">🛒</div>
                <h2 class="text-xl font-bold text-gray-800">Keranjangmu masih kosong</h2>
                <p class="text-gray-500 mb-6">Ayo cari buku favoritmu dan mulai belanja!</p>
                <a href="{{ route('buyer.orders.index') }}"
                    class="inline-block bg-teal-600 text-white px-8 py-3 rounded-full font-bold hover:bg-teal-700 transition-all">
                    Lihat Koleksi Buku
                </a>
            </div>
            @endif
            </div>

            {{--  Modal payment  --}}
            @if (session('checkout_success'))
                @php
                    $order = \App\Models\Order::with('items.book.user')->find(session('order_id'));
                @endphp

                @if ($order)
                    @include('buyer.payment.index')
                @endif
            @endif
        </x-sidebar>
    @endsection
</x-app>
