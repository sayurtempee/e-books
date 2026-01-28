<x-app>
    @section('title', 'Koleksi Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Koleksi Buku</h1>
                        <p class="text-sm text-gray-500">Menampilkan {{ $books->count() }} buku pilihan terbaik.</p>
                    </div>

                    {{-- Search & Filter Bar --}}
                    <form action="{{ route('buyer.orders.index') }}" method="GET"
                        class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" placeholder="Cari buku..." value="{{ request('search') }}"
                                class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm transition-all">
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="p-2.5 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition shadow-sm">
                                <i class="bi bi-filter-left text-xl"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-xl z-50 py-2">
                                <button type="submit" name="category" value=""
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-teal-50 {{ request('category') == '' ? 'text-teal-600 font-bold' : '' }}">
                                    Semua Kategori
                                </button>
                                @foreach ($categories as $category)
                                    <button type="submit" name="category" value="{{ $category->id }}"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-teal-50 {{ request('category') == $category->id ? 'text-teal-600 font-bold' : '' }}">
                                        {{ $category->title }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Grid Buku --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($books as $book)
                        <div
                            class="group bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300">

                            {{-- Container Foto & Overlay --}}
                            <div
                                class="relative aspect-[1/1] bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                                <img src="{{ asset('storage/' . $book->photos_product) }}" alt="{{ $book->title }}"
                                    class="h-full object-contain transform group-hover:scale-110 transition-transform duration-500">

                                {{-- Overlay Keranjang (Posisi Bawah) --}}
                                <div
                                    class="absolute inset-0 bg-black/5 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-3">
                                    <form action="{{ route('buyer.carts.store') }}" method="POST"
                                        class="w-full translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button
                                            class="w-full py-2 bg-teal-600/90 hover:bg-teal-600 text-white rounded-lg text-xs font-bold shadow-lg transition-colors flex items-center justify-center gap-2">
                                            <i class="bi bi-cart-plus text-sm"></i> Tambah Keranjang
                                        </button>
                                    </form>
                                </div>

                                {{-- Badge Stok --}}
                                <div
                                    class="absolute top-2 left-2 px-2 py-0.5 bg-white/80 backdrop-blur text-[9px] font-bold text-gray-600 rounded-md border border-gray-100">
                                    Stok: {{ $book->stock }}
                                </div>
                            </div>

                            {{-- Info Konten --}}
                            <div class="p-4 flex-grow">
                                <div class="text-[9px] font-bold text-teal-600 uppercase mb-1 tracking-wider">
                                    {{ $book->category->title ?? 'Umum' }}
                                </div>
                                <h3
                                    class="font-bold text-gray-800 text-sm line-clamp-1 group-hover:text-teal-600 transition-colors">
                                    {{ $book->title }}
                                </h3>

                                <div x-data="{ expand: false }" class="mt-2">
                                    <p :class="expand ? '' : 'line-clamp-2'"
                                        class="text-[11px] text-gray-500 leading-relaxed">
                                        {{ $book->description }}
                                    </p>
                                    @if (strlen($book->description) > 50)
                                        <button @click="expand = !expand" type="button"
                                            class="text-teal-600 text-[9px] font-bold mt-1 uppercase tracking-tighter">
                                            <span x-show="!expand">Selengkapnya</span>
                                            <span x-show="expand">Tutup</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Card --}}
                            <div class="px-4 pb-4 pt-3 flex justify-between items-center border-t border-gray-50 mt-auto">
                                <div>
                                    <p class="font-black text-teal-600 text-base">
                                        Rp{{ number_format($book->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <a href="{{ route('chat.index', $book->user->id) }}"
                                    class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-400 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition-all border border-transparent hover:border-teal-100"
                                    title="Chat Seller">
                                    <i class="bi bi-chat-dots text-base"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
