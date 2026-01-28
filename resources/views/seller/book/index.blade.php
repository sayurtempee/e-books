<x-app>
    @section('title', 'Manajemen Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">

                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 tracking-tight">
                            Manajemen <span class="text-teal-600">Buku</span>
                        </h1>
                        <p class="text-gray-500 text-sm">Kelola stok dan pantau keuntungan penjualan Anda.</p>
                    </div>

                    <div x-data="{ openAddBookModal: false }">
                        @include('seller.book.add')
                        <button @click="openAddBookModal = true"
                            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl shadow-lg shadow-teal-200 font-bold transition-all active:scale-95">
                            <i class="bi bi-plus-circle-fill"></i>
                            Tambah Produk Baru
                        </button>
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">
                                        No</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">
                                        Informasi Buku</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">
                                        Kategori</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Stok
                                    </th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Harga
                                        Modal</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Harga
                                        Jual</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">
                                        Estimasi Profit</th>
                                    <th
                                        class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">
                                        Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-50">
                                @forelse ($books as $index => $book)
                                    <tr class="hover:bg-teal-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-400">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ asset('storage/' . $book->photos_product) }}"
                                                    class="w-12 h-12 rounded-lg object-cover border border-gray-100 shadow-sm">
                                                <span
                                                    class="font-bold text-gray-800 group-hover:text-teal-600 transition-colors">
                                                    {{ $book->title }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase rounded-full">
                                                {{ $book->category->title }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold {{ $book->stock <= 5 ? 'text-rose-600' : 'text-gray-700' }}">
                                                    {{ $book->stock }} {{ $book->unit }}
                                                </span>
                                                @if ($book->stock <= 5)
                                                    <span
                                                        class="text-[9px] text-rose-400 font-bold uppercase tracking-tighter italic">Stok
                                                        Hampir Habis!</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            Rp {{ number_format($book->capital, 0, ',', '.') }}
                                        </td>

                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            Rp{{ number_format($book->price, 0, ',', '.') }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                {{-- Menampilkan harga dari pesanan terbaru, jika belum ada tampilkan harga default buku --}}
                                                <div class="flex flex-col">
                                                    {{-- Total Keuntungan yang sudah masuk dari buku ini --}}
                                                    <span class="text-teal-600 font-black">
                                                        @php
                                                            $totalProfitBuku = $book
                                                                ->item()
                                                                ->whereIn('status', ['approved', 'shipping', 'selesai'])
                                                                ->sum('profit');
                                                        @endphp
                                                        Rp{{ number_format($totalProfitBuku, 0, ',', '.') }}
                                                    </span>

                                                    <span class="text-[10px] text-gray-400 font-medium italic">
                                                        Profit & Margin:
                                                        Rp{{ number_format($book->price - $book->capital, 0, ',', '.') }}
                                                        ({{ $book->margin }}%)
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Detail --}}
                                                <div x-data="{ openDetailBookModal: false }">
                                                    @include('seller.book.detail', ['book' => $book])
                                                    <button @click="openDetailBookModal = true"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </div>

                                                {{-- Edit --}}
                                                <div x-data="{ openEditBookModal: false }">
                                                    @include('seller.book.edit', [
                                                        'book' => $book,
                                                        'categories' => $categories,
                                                    ])
                                                    <button @click="openEditBookModal = true"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-all">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                </div>

                                                {{-- Delete --}}
                                                <form id="deleteBookForm-{{ $book->id }}" method="POST"
                                                    action="{{ route('seller.book.delete', $book->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDeleteBook({{ $book->id }}, '{{ $book->title }}')"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="bi bi-journal-x text-5xl text-gray-200 mb-4"></i>
                                                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Belum
                                                    ada buku yang Anda jual</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
