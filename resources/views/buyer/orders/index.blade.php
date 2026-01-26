<x-app>
    @section('title', 'Koleksi Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Koleksi Buku</h1>
                        <p class="text-sm text-gray-500">Menampilkan {{ $books->count() }} buku pilihan.</p>
                    </div>

                    {{-- Search & Filter Bar --}}
                    <form action="{{ route('buyer.orders.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-64">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" placeholder="Cari buku..." value="{{ request('search') }}"
                                class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-teal-500 outline-none text-sm">
                        </div>

                        {{-- Category Filter Icon Only --}}
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="p-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                                <i class="bi bi-filter-left text-xl"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1">
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
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden flex flex-col hover:shadow-sm transition">
                            {{-- Foto --}}
                            <div class="relative aspect-[4/5] bg-gray-50 flex items-center justify-center p-4">
                                <img src="{{ asset('storage/' . $book->photos_product) }}" alt="{{ $book->title }}" class="h-full object-contain">
                            </div>

                            {{-- Info --}}
                            <div class="p-4 flex-grow">
                                <div class="text-[10px] font-bold text-teal-600 uppercase mb-1">{{ $book->category->title ?? 'Umum' }}</div>
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $book->title }}</h3>

                                {{-- Read More Minimalis --}}
                                <div x-data="{ expand: false }" class="mt-2 mb-4">
                                    <p :class="expand ? '' : 'line-clamp-2'" class="text-xs text-gray-500 leading-relaxed">
                                        {{ $book->description }}
                                    </p>
                                    @if(strlen($book->description) > 50)
                                        <button @click="expand = !expand" type="button" class="text-teal-600 text-[10px] font-bold mt-1">
                                            <span x-show="!expand text-xs">Selengkapnya</span>
                                            <span x-show="expand text-xs">Tutup</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="flex justify-between items-end pt-2 border-t border-gray-50">
                                    <p class="font-bold text-gray-900">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-gray-400">Stok: {{ $book->stock }}</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="px-4 pb-4 flex gap-2">
                                <form action="{{ route('buyer.carts.store') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <button class="w-full py-2 bg-teal-600 text-white rounded-md text-xs font-bold hover:bg-teal-700 transition">
                                        + Keranjang
                                    </button>
                                </form>
                                <a href="{{ route('chat.index', $book->user->id) }}" class="p-2 border border-gray-200 text-gray-500 rounded-md hover:text-teal-600 transition">
                                    <i class="bi bi-chat-dots"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
