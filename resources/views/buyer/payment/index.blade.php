{{-- Invoice Modal --}}
@if (session('checkout_success'))

    <div id="invoiceModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-teal-900/40 backdrop-blur-md p-4">

        <div
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 relative border border-teal-100 overflow-y-auto max-h-[95vh]">

            {{-- HEADER --}}
            <div class="text-center mb-6">
                <div class="bg-teal-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-check-lg text-3xl text-teal-600"></i>
                </div>
                <h2 class="text-2xl font-black text-teal-800">Checkout Berhasil!</h2>
                <p class="text-teal-600/80 text-sm">Pesanan #ORD-{{ $order->id }}</p>
            </div>

            {{-- TOTAL --}}
            <div class="mb-4 text-center p-3 bg-teal-50 rounded-xl border border-teal-100">
                <p class="text-[10px] font-bold text-teal-600 uppercase">Total yang harus dibayar</p>
                <p class="text-2xl font-black text-teal-900">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </p>
            </div>

            {{-- INSTRUKSI --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border-2 border-dashed border-teal-200">
                <h3 class="text-center text-xs font-bold text-teal-700 uppercase mb-3">
                    Instruksi Pembayaran ({{ strtoupper($order->payment_method) }})
                </h3>

                @if ($order->payment_method === 'transfer')

                    @php
                        $sellers = $order->items->groupBy(fn($item) => $item->book->user->id);
                    @endphp

                    <div class="space-y-5">
                        @foreach ($sellers as $sellerItems)
                            @php
                                $seller = $sellerItems->first()->book->user;
                                $totalSeller = $sellerItems->sum(fn($i) => $i->price * $i->qty);
                            @endphp

                            <div class="border rounded-xl p-4 bg-slate-50 text-center">
                                <p class="text-xs font-bold text-gray-500">TRANSFER KE</p>

                                <p class="text-2xl font-mono font-black">
                                    {{ $seller->no_rek }}
                                </p>

                                <p class="text-xs font-bold">
                                    BANK {{ $seller->bank_name }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    A/N {{ $seller->name }}
                                </p>

                                <p class="mt-2 text-sm font-semibold text-emerald-600">
                                    Rp {{ number_format($totalSeller, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- QRIS / E-Wallet --}}
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ORDER_{{ $order->id }}"
                        class="mx-auto rounded shadow">
                @endif
            </div>

            {{-- ACTION --}}
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
                class="w-full text-xs font-bold text-gray-400 hover:text-teal-600 uppercase">
                Tutup
            </button>
        </div>
    </div>
@endif
