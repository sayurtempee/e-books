<x-app>
    @section('title', 'List Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-extrabold text-gray-800 border-l-4 border-teal-600 pl-4">
                        Koleksi Buku
                    </h1>
                    <span class="text-sm text-gray-500 font-medium">Total: {{ $books->count() }} Produk</span>
                </div>

                {{--  Search dan filter category  --}}
                <div class="mb-6" x-data="{ openFilter: false }">
                    <form action="{{ route('buyer.orders.index') }}" method="GET"
                        class="flex flex-wrap md:flex-nowrap gap-2 items-center">

                        {{-- Search --}}
                        <input type="text" name="search" placeholder="Cari buku berdasarkan judul ..."
                            value="{{ request('search') }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-teal-600">

                        {{-- Filter Category (Three Dots) --}}
                        <div class="relative">
                            <button type="button" @click="openFilter = !openFilter"
                                class="p-2 rounded-lg border border-gray-300
                                       hover:bg-gray-100 focus:ring-2 focus:ring-teal-600">
                                <i class="bi bi-three-dots-vertical text-lg"></i>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="openFilter" @click.outside="openFilter = false" x-transition
                                class="absolute right-0 mt-2 w-52 bg-white
                                        border border-gray-200 rounded-lg shadow-lg z-50">

                                {{-- Semua kategori --}}
                                <button type="submit" name="category" value=""
                                    class="w-full text-left px-4 py-2 text-sm
                                           hover:bg-teal-50 {{ request('category') == '' ? 'bg-teal-100 font-semibold' : '' }}">
                                    Semua Kategori
                                </button>

                                <div class="border-t my-1"></div>

                                {{-- List category dari database --}}
                                @foreach ($categories as $category)
                                    <button type="submit" name="category" value="{{ $category->id }}"
                                        class="w-full text-left px-4 py-2 text-sm
                                               hover:bg-teal-50
                                               {{ request('category') == $category->id ? 'bg-teal-100 font-semibold' : '' }}">
                                        {{ $category->title }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Button Cari --}}
                        <button type="submit"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg
                                   hover:bg-teal-700 transition-colors
                                   font-medium flex items-center gap-2">
                            <i class="bi bi-search"></i>
                            Cari
                        </button>
                    </form>
                </div>
                {{--  End Search dan filter category  --}}

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($books as $book)
                        <div
                            class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col">

                            {{-- Container Gambar (Dikecilkan & Berjarak) --}}
                            <div class="relative bg-gray-100 flex justify-center items-center p-6 h-64 overflow-hidden">
                                <img src="{{ asset('storage/' . $book->photos_product) }}" alt="{{ $book->title }}"
                                    class="h-full w-auto object-contain transition-transform duration-500 group-hover:scale-105 shadow-sm">

                                {{-- Badge Stok di Pojok Kiri --}}
                                <div class="absolute top-2 left-2">
                                    <span
                                        class="px-2 py-1 text-[10px] uppercase tracking-wider font-bold rounded bg-white/90 backdrop-blur-sm border {{ $book->stock > 0 ? 'text-teal-600 border-teal-200' : 'text-red-500 border-red-200' }}">
                                        {{ $book->stock > 0 ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Konten Teks --}}
                            <div class="p-4 flex-grow text-center">
                                <h3 class="text-base font-bold text-gray-800 line-clamp-1 mb-1 group-hover:text-teal-600">
                                    {{ $book->title }}
                                </h3>
                                <div class="text-xs text-gray-400 mb-3 italic">Stok: {{ $book->stock }} pcs</div>

                                <div class="description-container">
                                    <p id="desc-{{ $book->id }}"
                                        class="text-sm text-gray-500 text-justify line-clamp-2 mb-1 h-10 leading-snug overflow-hidden">
                                        {{ $book->description }}
                                    </p>

                                    @if (strlen($book->description) > 100)
                                        <button type="button" onclick="toggleDescription({{ $book->id }}, this)"
                                            class="text-xs font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">
                                            Baca selengkapnya
                                        </button>
                                    @endif
                                </div>

                                <div class="text-lg font-bold text-teal-600">
                                    Rp {{ number_format($book->price, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Tombol Aksi (Dibuat Lebih Kompak) --}}
                            <div class="p-4 pt-0 flex gap-2 justify-center">
                                {{-- Form Tambah ke Keranjang --}}
                                <form action="{{ route('buyer.carts.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="hidden" name="title" value="{{ $book->title }}">
                                    <input type="hidden" name="price" value="{{ $book->price }}">
                                    <input type="hidden" name="photo" value="{{ $book->photos_product }}">

                                    <button type="submit" title="Keranjang"
                                        class="w-10 h-10 flex justify-center items-center bg-amber-400 text-white rounded-full hover:bg-amber-500 transition-colors shadow-sm">
                                        <i class="bi bi-basket"></i>
                                    </button>
                                </form>
                                <button title="Chat"
                                    class="w-10 h-10 flex justify-center items-center bg-white text-blue-500 border border-blue-500 rounded-full hover:bg-blue-50 transition-colors shadow-sm">
                                    <i class="bi bi-chat-dots"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
