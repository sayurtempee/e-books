<x-app>
    @section('title', 'Koleksi Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                    <div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">
                            Koleksi <span class="text-teal-600">Buku</span>
                        </h1>
                        <p class="text-gray-500 font-medium">Temukan jendela dunia melalui ribuan literatur pilihan.</p>
                    </div>
                    <div class="bg-teal-100 px-4 py-2 rounded-full border border-teal-200 shadow-sm">
                        <span class="text-sm text-teal-700 font-bold uppercase tracking-widest">
                            Total: {{ $books->count() }} Produk
                        </span>
                    </div>
                </div>

                {{-- Search & Filter Section --}}
                <div class="mb-10" x-data="{ openFilter: false }">
                    <form action="{{ route('buyer.orders.index') }}" method="GET"
                        class="flex flex-wrap md:flex-nowrap gap-3 items-center bg-white p-3 rounded-2xl shadow-sm border border-gray-100">

                        {{-- Search Input --}}
                        <div class="flex-1 relative group">
                            <i
                                class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-600 transition-colors"></i>
                            <input type="text" name="search" placeholder="Cari judul buku favoritmu..."
                                value="{{ request('search') }}"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-teal-500 transition-all text-sm">
                        </div>

                        {{-- Category Filter --}}
                        <div class="relative">
                            <button type="button" @click="openFilter = !openFilter"
                                class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-all font-semibold text-sm text-gray-600">
                                <i class="bi bi-filter-left text-lg"></i>
                                <span>Kategori</span>
                            </button>

                            <div x-show="openFilter" @click.outside="openFilter = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute right-0 mt-3 w-64 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 p-2">

                                <button type="submit" name="category" value=""
                                    class="w-full text-left px-4 py-3 text-sm rounded-xl hover:bg-teal-50 transition-colors flex justify-between items-center {{ request('category') == '' ? 'bg-teal-50 text-teal-700 font-bold' : 'text-gray-600' }}">
                                    Semua Kategori
                                    @if (request('category') == '')
                                        <i class="bi bi-check-circle-fill"></i>
                                    @endif
                                </button>

                                <div class="my-2 border-t border-gray-100"></div>

                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @foreach ($categories as $category)
                                        <button type="submit" name="category" value="{{ $category->id }}"
                                            class="w-full text-left px-4 py-3 text-sm rounded-xl hover:bg-teal-50 transition-colors flex justify-between items-center {{ request('category') == $category->id ? 'bg-teal-50 text-teal-700 font-bold' : 'text-gray-600' }}">
                                            {{ $category->title }}
                                            @if (request('category') == $category->id)
                                                <i class="bi bi-check-circle-fill"></i>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="px-8 py-3 bg-teal-600 text-white rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-200 transition-all font-bold text-sm uppercase tracking-wider">
                            Terapkan
                        </button>
                    </form>
                </div>

                {{-- Grid Koleksi Buku --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($books as $book)
                        <div
                            class="group bg-white rounded-[2rem] border border-gray-100 hover:border-teal-200 shadow-sm hover:shadow-2xl hover:shadow-teal-100 transition-all duration-500 overflow-hidden flex flex-col">

                            {{-- Image Preview --}}
                            <div class="relative bg-gray-50 h-72 flex justify-center items-center p-8 overflow-hidden">
                                <img src="{{ asset('storage/' . $book->photos_product) }}" alt="{{ $book->title }}"
                                    class="h-full w-auto object-contain transition-transform duration-700 group-hover:scale-110 drop-shadow-2xl">

                                {{-- Floating Badge --}}
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="px-3 py-1 text-[10px] uppercase tracking-widest font-black rounded-full shadow-sm backdrop-blur-md {{ $book->stock > 0 ? 'bg-teal-500/10 text-teal-600 border border-teal-200' : 'bg-rose-500/10 text-rose-600 border border-rose-200' }}">
                                        {{ $book->stock > 0 ? 'Ready Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Book Details --}}
                            <div class="p-6 flex-grow">
                                <div class="flex items-center gap-2 mb-2">
                                    <span
                                        class="text-[10px] font-bold text-teal-600 uppercase tracking-tighter bg-teal-50 px-2 py-0.5 rounded">
                                        {{ $book->category->title ?? 'Umum' }}
                                    </span>
                                </div>
                                <h3
                                    class="text-lg font-extrabold text-gray-800 line-clamp-1 mb-1 group-hover:text-teal-600 transition-colors">
                                    {{ $book->title }}
                                </h3>

                                <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-4">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="font-medium">Seller: <span
                                            class="text-gray-600">{{ $book->user->name }}</span></span>
                                </div>

                                <p
                                    class="text-sm text-gray-500 text-justify line-clamp-2 leading-relaxed mb-4 min-h-[40px]">
                                    {{ $book->description }}
                                </p>

                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Harga</p>
                                        <div class="text-2xl font-black text-gray-900">
                                            Rp{{ number_format($book->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Sisa</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $book->stock }} <span
                                                class="text-[10px] text-gray-400">Pcs</span></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="px-6 pb-6 pt-2 flex gap-3">
                                <form action="{{ route('buyer.carts.store') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="hidden" name="title" value="{{ $book->title }}">
                                    <input type="hidden" name="price" value="{{ $book->price }}">
                                    <input type="hidden" name="photo" value="{{ $book->photos_product }}">

                                    <button type="submit"
                                        class="w-full py-3 bg-teal-600 text-white rounded-xl hover:bg-gray-900 transition-all duration-300 font-bold text-sm flex justify-center items-center gap-2 shadow-lg shadow-teal-100 group-hover:shadow-none">
                                        <i class="bi bi-basket-fill"></i>
                                        + Keranjang
                                    </button>
                                </form>

                                <a href="{{ route('chat.index', $book->user->id) }}" title="Chat Seller"
                                    class="flex-1 w-full py-3 bg-white text-teal-600 border-2 border-teal-600 rounded-xl hover:bg-teal-600 hover:text-white transition-all duration-300 shadow-sm font-bold text-sm flex justify-center items-center gap-2">
                                    <i class="bi bi-chat-text-fill"></i>
                                    Chat Seller
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    width: 4px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #0d9488;
                    border-radius: 10px;
                }
            </style>
        </x-sidebar>
    @endsection
</x-app>
