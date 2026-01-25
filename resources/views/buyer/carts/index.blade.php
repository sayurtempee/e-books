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
                                    @php
                                        $total = 0;
                                        $cart = session('cart', []);
                                        $seller = null;

                                        if (count($cart) > 0) {
                                            // Ambil ID buku pertama
                                            $firstBookId = array_key_first($cart);

                                            // Cari buku. Jika session simpan ID sebagai key, find($firstBookId) sudah benar.
                                            $book = \App\Models\Book::find($firstBookId);

                                            if ($book && $book->user) {
                                                // Kita ambil user-nya dulu tanpa cek role di awal untuk testing
                                                $potentialSeller = $book->user;

                                                // Cek apakah rolenya benar 'seller' (sesuai isi database di gambar kamu)
                                                if (trim(strtolower($potentialSeller->role)) === 'seller') {
                                                    $seller = $potentialSeller;
                                                }
                                            }
                                        }
                                    @endphp
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

                                        {{-- Tampilan Rekening Seller --}}
                                        <div x-show="paymentMethod === 'transfer'"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                                            x-transition:enter-end="opacity-100 transform translate-y-0"
                                            class="mt-4 p-5 bg-teal-50 border border-teal-200 rounded-xl">

                                            <h4 class="text-[10px] font-black text-teal-700 uppercase tracking-widest mb-4">
                                                <i class="bi bi-bank me-1"></i> Informasi Rekening Tujuan (Seller)
                                            </h4>

                                            @if ($seller)
                                                <div class="space-y-3">
                                                    <div
                                                        class="flex justify-between items-center p-3 bg-white rounded-lg border border-teal-100">
                                                        <div>
                                                            <p class="text-[9px] text-gray-400 uppercase font-bold">Bank</p>
                                                            <p class="text-sm font-bold text-gray-800">
                                                                {{ $seller->bank_name ?? 'BCA' }}</p>
                                                        </div>
                                                        <i class="bi bi-wallet2 text-teal-300"></i>
                                                    </div>

                                                    <div
                                                        class="flex justify-between items-center p-3 bg-white rounded-lg border border-teal-100">
                                                        <div>
                                                            <p class="text-[9px] text-gray-400 uppercase font-bold">Nomor
                                                                Rekening</p>
                                                            <p
                                                                class="text-lg font-mono font-black text-teal-700 tracking-wider">
                                                                {{ $seller->no_rek ?? '-' }}</p>
                                                        </div>
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ $seller->no_rek }}')"
                                                            class="text-[10px] bg-teal-100 text-teal-600 px-2 py-1 rounded hover:bg-teal-200 transition-all font-bold">
                                                            SALIN
                                                        </button>
                                                    </div>

                                                    <div class="p-3 bg-white rounded-lg border border-teal-100">
                                                        <p class="text-[9px] text-gray-400 uppercase font-bold">Atas Nama
                                                        </p>
                                                        <p class="text-sm font-bold text-gray-800">{{ $seller->name }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs text-rose-500 italic">Data rekening seller tidak
                                                    ditemukan.</p>
                                            @endif
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

            {{--  Invoice Modal --}}
            @if (session('checkout_success'))
                @php
                    // Ambil data order untuk menentukan instruksi
                    $currentOrder = \App\Models\Order::find(session('order_id'));
                @endphp

                <div id="invoiceModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-teal-900/40 backdrop-blur-md p-4">
                    <div
                        class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 relative border border-teal-100 overflow-y-auto max-h-[95vh]">

                        <div class="text-center mb-6">
                            <div
                                class="bg-teal-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                                <i class="bi bi-check-lg text-3xl text-teal-600"></i>
                            </div>
                            <h2 class="text-2xl font-black text-teal-800">Checkout Berhasil!</h2>
                            <p class="text-teal-600/80 text-sm">Pesanan #ORD-{{ session('order_id') }}</p>
                        </div>

                        {{-- TOTAL YANG HARUS DIBAYAR --}}
                        <div class="mb-4 text-center p-3 bg-teal-50 rounded-xl border border-teal-100">
                            <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest">Total yang harus
                                dibayar</p>
                            <p class="text-2xl font-black text-teal-900">Rp
                                {{ number_format($currentOrder->total_price, 0, ',', '.') }}</p>
                        </div>

                        {{-- INSTRUKSI PEMBAYARAN DINAMIS --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl border-2 border-dashed border-teal-200">
                            <h3 class="text-center text-xs font-bold text-teal-700 uppercase mb-3">Instruksi Pembayaran
                                ({{ strtoupper($currentOrder->payment_method) }})</h3>

                            <div class="flex flex-col items-center justify-center">
                                @if (in_array($currentOrder->payment_method, ['qris', 'gopay', 'ovo']))
                                    {{-- Dummy QR Code --}}
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=DUMMY_PAYMENT_{{ session('order_id') }}_TOTAL_{{ $currentOrder->total_price }}"
                                        alt="QR Payment" class="rounded-lg shadow-md mb-2">
                                    <p class="text-[10px] text-gray-500 italic text-center px-4">Silakan scan dan masukkan
                                        nominal sesuai total di atas melalui aplikasi
                                        {{ ucfirst($currentOrder->payment_method) }} Anda</p>
                                @else
                                    @php
                                        $order = \App\Models\Order::with('user')->findOrFail(session('order_id'));
                                    @endphp

                                    @if ($order->payment_method === 'transfer')
                                        <div class="text-center">
                                            <p class="text-xs font-bold">TRANSFER DARI</p>
                                            <p class="text-2xl font-mono font-black">
                                                {{ $order->user->no_rek }}
                                            </p>
                                            <p class="text-xs font-bold">
                                                BANK {{ $order->user->bank_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                A/N {{ $order->user->name }}
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            {{-- Tombol Download PDF --}}
                            <a href="{{ route('buyer.invoice.download', session('order_id')) }}"
                                class="flex items-center justify-between p-4 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all shadow-lg group">
                                <span class="font-bold">Download Invoice (PDF)</span>
                                <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                            </a>

                            {{-- Form Upload Bukti --}}
                            <div class="p-5 bg-white rounded-xl border border-teal-100 shadow-sm">
                                <form action="{{ route('buyer.payment.upload', session('order_id')) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <label
                                        class="block text-[10px] font-bold text-teal-700 uppercase mb-2 text-center underline">Klik
                                        di bawah untuk Upload Bukti Transfer</label>
                                    <input type="file" name="payment_proof" required
                                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                                    <button type="submit"
                                        class="w-full mt-4 bg-teal-800 text-white py-3 rounded-xl font-black text-xs hover:bg-black transition-all">
                                        KIRIM BUKTI PEMBAYARAN
                                    </button>
                                </form>
                            </div>
                        </div>

                        <button onclick="document.getElementById('invoiceModal').classList.add('hidden')"
                            class="mt-4 w-full text-center text-gray-400 text-[10px] font-bold hover:text-teal-600 tracking-widest uppercase">
                            Tutup & Selesaikan Nanti
                        </button>
                    </div>
                </div>
            @endif
        </x-sidebar>
    @endsection
</x-app>
