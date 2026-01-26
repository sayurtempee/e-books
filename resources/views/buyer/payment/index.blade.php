@if (session('checkout_success'))
    @php
        $order = \App\Models\Order::with('items.book.user')->find(session('order_id'));
    @endphp

    @if ($order)
        <div id="invoiceModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-teal-900/40 backdrop-blur-md p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative border border-teal-100 overflow-y-auto max-h-[95vh]">

                <div class="text-center mb-4">
                    <div class="bg-teal-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-check-lg text-2xl text-teal-600"></i>
                    </div>
                    <h2 class="text-xl font-black text-teal-800">Checkout Berhasil!</h2>
                    <p class="text-teal-600/80 text-[10px]">Pesanan #ORD-{{ $order->id }}</p>
                </div>

                <div class="space-y-4 mb-6">
                    @php $sellers = $order->items->groupBy('seller_id'); @endphp

                    @foreach ($sellers as $sellerId => $items)
                        @php
                            $seller = $items->first()->book->user;
                            $totalSeller = $items->sum(fn($i) => $i->price * $i->qty);
                            // Cek apakah item milik seller ini sudah diupload buktinya
                            $isUploaded = $items->first()->payment_proof !== null;
                        @endphp

                        <div
                            class="p-4 rounded-xl border {{ $isUploaded ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Toko: {{ $seller->name }}
                                    </p>
                                    <p class="text-sm font-black text-gray-800">{{ $seller->bank_name }} -
                                        {{ $seller->no_rek }}</p>
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
                                    <input type="file" name="payment_proof" required
                                        class="block w-full text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:bg-teal-600 file:text-white cursor-pointer">
                                    <button type="submit"
                                        class="w-full bg-teal-800 text-white py-2 rounded-lg text-[10px] font-bold hover:bg-black transition-all">
                                        KIRIM BUKTI PEMBAYARAN
                                    </button>
                                </form>
                            @else
                                <div
                                    class="flex items-center justify-center gap-2 py-2 bg-emerald-100 rounded-lg text-emerald-700">
                                    <i class="bi bi-check-circle-fill text-xs"></i>
                                    <span class="text-[10px] font-bold">BUKTI BERHASIL DIUPLOAD</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2">
                    <a href="{{ route('buyer.invoice.download', $order->id) }}"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-bold text-xs hover:bg-gray-200 transition">
                        <i class="bi bi-file-earmark-pdf"></i> Download Invoice
                    </a>
                    <button onclick="window.location.reload()"
                        class="w-full text-[10px] font-bold text-gray-400 hover:text-teal-600 uppercase">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
@endif
