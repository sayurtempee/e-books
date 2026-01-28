<x-app>
    @section('title', 'Pusat Bantuan')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                {{-- Header --}}
                <div class="max-w-3xl mx-auto mb-10 text-center">
                    <h1 class="text-4xl font-black text-gray-800">
                        Pusat <span class="text-teal-600">Bantuan</span>
                    </h1>
                    <p class="text-gray-500 mt-2">Temukan jawaban untuk pertanyaan Anda seputar Miimoys E-Books</p>
                </div>

                {{-- FAQ Container --}}
                <div class="max-w-3xl mx-auto space-y-4" x-data="{ active: null }">

                    @php
                        $faqs = [
                            [
                                'q' => 'Bagaimana cara membeli e-book?',
                                'a' =>
                                    'Pilih e-book yang Anda inginkan, klik tombol "Beli Sekarang", lalu ikuti instruksi pembayaran yang tertera di layar hingga selesai.',
                            ],
                            [
                                'q' => 'Metode pembayaran apa saja yang tersedia?',
                                'a' =>
                                    'Kami mendukung berbagai metode pembayaran mulai dari Transfer Bank (BCA, Mandiri, BNI, BRI) hingga E-Wallet populer.',
                            ],
                            [
                                'q' => 'Apakah e-book bisa langsung dibaca setelah bayar?',
                                'a' =>
                                    'Ya! Setelah pembayaran berhasil dikonfirmasi oleh sistem, e-book akan otomatis muncul di menu Koleksi Saya atau Riwayat Pembelian.',
                            ],
                            [
                                'q' => 'Bagaimana jika pembayaran belum terverifikasi?',
                                'a' =>
                                    'Pastikan Anda mengunggah bukti transfer yang valid. Jika dalam 1x24 jam status belum berubah, silakan hubungi admin melalui kontak email.',
                            ],
                            [
                                'q' => 'Apakah saya bisa menjual e-book di sini?',
                                'a' =>
                                    'Bisa. Anda harus beralih akun menjadi Seller di menu profil. Setelah disetujui, Anda dapat mulai mengunggah karya Anda.',
                            ],
                        ];
                    @endphp

                    @foreach ($faqs as $index => $faq)
                        <div
                            class="border border-gray-200 rounded-2xl bg-white overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                            <button @click="active !== {{ $index }} ? active = {{ $index }} : active = null"
                                class="w-full flex items-center justify-between p-5 text-left transition-colors"
                                :class="active === {{ $index }} ? 'bg-teal-50' : ''">
                                <span class="font-bold text-gray-700"
                                    :class="active === {{ $index }} ? 'text-teal-700' : ''">
                                    {{ $faq['q'] }}
                                </span>
                                <i class="bi text-teal-600 transition-transform duration-300"
                                    :class="active === {{ $index }} ? 'bi-dash-circle-fill rotate-180' :
                                        'bi-plus-circle-fill'"></i>
                            </button>

                            <div x-show="active === {{ $index }}" x-collapse x-cloak>
                                <div class="p-5 pt-0 text-gray-600 text-sm leading-relaxed border-t border-teal-100/50">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Contact Card --}}
                    <div
                        class="mt-12 p-8 bg-gradient-to-br from-teal-600 to-teal-700 rounded-[2rem] text-white text-center shadow-xl shadow-teal-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                            <i class="bi bi-chat-quote-fill text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black mb-2">Masih punya pertanyaan?</h3>
                        <p class="text-teal-50 text-sm mb-8 max-w-sm mx-auto">
                            Tim dukungan kami siap membantu Anda melalui Email atau WhatsApp untuk respon yang lebih cepat.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            {{-- Tombol WhatsApp --}}
                            <a href="https://wa.me/6281228476372?text=Halo%20Admin%20Miimoys,%20saya%20butuh%20bantuan%20terkait..."
                                target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-emerald-500 text-white px-8 py-4 rounded-2xl font-bold hover:bg-emerald-600 transition-all active:scale-95 shadow-lg shadow-emerald-900/20">
                                <i class="bi bi-whatsapp text-xl"></i>
                                <span>Chat via WhatsApp</span>
                            </a>

                            {{-- Tombol Email --}}
                            <a href="mailto:farishilmiializa@gmail.com"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-white/10 border border-white/20 text-white px-8 py-4 rounded-2xl font-bold hover:bg-white/20 transition-all active:scale-95">
                                <i class="bi bi-envelope-fill"></i>
                                <span>Kirim Email</span>
                            </a>
                        </div>

                        <p class="mt-8 text-[10px] text-teal-200 uppercase tracking-widest font-bold">
                            Jam Operasional: 09:00 - 22:00 WIB
                        </p>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
