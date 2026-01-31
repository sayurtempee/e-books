@if (session('checkout_success'))
    @php
        $order = \App\Models\Order::with('items.book.user')->find(session('order_id'));
    @endphp

    @if ($order)
        <div id="invoiceModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-teal-900/40 backdrop-blur-md p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative border border-teal-100 overflow-y-auto max-h-[95vh]">

                {{-- Header Modal --}}
                <div class="text-center mb-4">
                    <div class="bg-teal-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-check-lg text-2xl text-teal-600"></i>
                    </div>
                    <h2 class="text-xl font-black text-teal-800">Checkout Berhasil!</h2>
                    <p class="text-teal-600/80 text-[10px]">Pesanan #ORD-{{ $order->id }}</p>
                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">Metode: {{ $order->payment_method }}</p>
                </div>

                <div class="space-y-4 mb-6">
                    @php $sellers = $order->items->groupBy('seller_id'); @endphp

                    @foreach ($sellers as $sellerId => $items)
                        @php
                            $seller = $items->first()->book->user;
                            $totalSeller = $items->sum(fn($i) => $i->price * $i->qty);
                            $isUploaded = $items->first()->payment_proof !== null;

                            // Logika QR Code
                            $isEwallet = in_array(strtolower($order->payment_method), ['qris', 'gopay', 'ovo', 'dana']);
                            $qrContent = "PAYMENT_{$order->id}_TO_{$seller->id}_TOTAL_{$totalSeller}";
                        @endphp

                        <div
                            class="p-4 rounded-xl border {{ $isUploaded ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">

                            {{-- Tampilkan QR Code jika metode adalah E-Wallet/QRIS --}}
                            @if ($isEwallet && !$isUploaded)
                                <div
                                    class="flex flex-col items-center mb-4 bg-white p-3 rounded-lg border border-teal-50 shadow-sm">
                                    <img src="https://image-charts.com/chart?chs=150x150&cht=qr&chl={{ urlencode($qrContent) }}"
                                        alt="QR Code" class="w-32 h-32 border border-gray-100 p-1 rounded-md">
                                    <p class="text-[8px] font-bold text-teal-600 mt-2">SCAN QR UNTUK BAYAR</p>
                                </div>
                            @endif

                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Toko:
                                        {{ $seller->name }}</p>
                                    <p class="text-sm font-black text-gray-800">
                                        {{ $seller->bank_name ?? 'E-Wallet' }} - {{ $seller->no_rek }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Total</p>
                                    <p class="text-sm font-black text-teal-600">Rp
                                        {{ number_format($totalSeller, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            @if (!$isUploaded)
                                <form action="{{ route('buyer.payment.upload', $order->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="seller_id" value="{{ $sellerId }}">

                                    {{-- Custom Upload Style --}}
                                    <div class="relative">
                                        <input type="file" name="payment_proof" required
                                            id="file-{{ $sellerId }}" class="hidden"
                                            onchange="document.getElementById('file-name-{{ $sellerId }}').innerText = this.files[0].name">
                                        <label for="file-{{ $sellerId }}"
                                            class="flex items-center justify-center gap-2 w-full py-2 border border-dashed border-teal-300 rounded-lg text-[10px] font-bold text-teal-700 bg-white cursor-pointer hover:bg-teal-50">
                                            <i class="bi bi-cloud-arrow-up"></i> <span
                                                id="file-name-{{ $sellerId }}">Pilih Bukti Bayar</span>
                                        </label>
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-teal-800 text-white py-2 rounded-lg text-[10px] font-black hover:bg-black transition-all uppercase shadow-md">
                                        Kirim Bukti Pembayaran
                                    </button>
                                </form>
                            @else
                                <div
                                    class="flex items-center justify-center gap-2 py-2 bg-emerald-100 rounded-lg text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-xs"></i>
                                    <span class="text-[10px] font-bold">BUKTI BERHASIL DIUPLOAD</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Footer Modal --}}
                <div class="space-y-2 border-t pt-4 border-gray-100">
                    <a href="{{ route('buyer.invoice.download', $order->id) }}"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-gray-50 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-100 transition border border-gray-200">
                        <i class="bi bi-file-earmark-pdf"></i> Download Invoice
                    </a>
                    <button onclick="window.location.reload()"
                        class="w-full text-[10px] font-bold text-gray-400 hover:text-teal-600 uppercase tracking-widest pt-2">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
@endif
