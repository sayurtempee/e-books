<x-app>
    @section('title', 'List Buku')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">

                {{-- Title --}}
                <h1 class="text-2xl font-bold text-teal-600 mb-6">
                    Halaman Daftar Buku
                </h1>

                {{-- Add Book --}}
                <div x-data="{ openAddBookModal: false }">
                    @include('seller.book.add')

                    <button @click="openAddBookModal = true"
                        class="inline-flex items-center gap-2
                               bg-teal-600 hover:bg-teal-700
                               text-white px-4 py-2
                               rounded-lg shadow
                               font-semibold transition">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Buku
                    </button>
                </div>


                {{-- Book Table --}}
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full border border-teal-200
                                       rounded-lg overflow-hidden">

                            {{-- Head --}}
                            <thead class="bg-teal-500 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left w-16">No</th>
                                    <th class="px-4 py-3 text-left">Judul Buku</th>
                                    <th class="px-4 py-3 text-left">Kategori Buku</th>
                                    <th class="px-4 py-3 text-left">Stok Buku</th>
                                    {{--  <th class="px-4 py-3 text-left">Harga Jual Buku</th>  --}}
                                    <th class="px-4 py-3 text-left">Harga Buku</th>
                                    {{--  <th class="px-4 py-3 text-left">Margin</th>  --}}
                                    <th class="px-4 py-3 text-left">Keuntungan</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>

                            {{-- Body --}}
                            <tbody class="bg-white divide-y divide-teal-100">
                                @foreach ($books as $index => $book)
                                    <tr class="hover:bg-teal-50 transition">

                                        <td class="px-4 py-3">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $book->title }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $book->category->title }}
                                        </td>

                                        <td class="px-4 py-3 {{ $book->stock == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $book->stock }} {{ $book->unit }}
                                        </td>

                                        {{--  <td class="px-4 py-3 text-gray-600">
                                            Rp {{ number_format($book->capital, 0, ',', '.') }}
                                        </td>  --}}

                                        <td class="px-4 py-3 text-gray-600">
                                            Rp {{ number_format($book->price, 0, ',', '.') }}
                                        </td>

                                        {{--  <td class="px-4 py-3 text-gray-600">
                                            {{ number_format($book->margin, 2, ',', '.') }}%
                                        </td>  --}}

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            <div class="flex flex-col">
                                                {{-- Keuntungan Kumulatif (Dari penjualan yang sudah terjadi) --}}
                                                <span class="text-teal-600">
                                                    Rp {{ number_format($book->total_real_profit ?? 0, 0, ',', '.') }}
                                                </span>

                                                {{-- Info tambahan: Profit per unit (Opsional) --}}
                                                <span class="text-[10px] text-gray-400 italic">
                                                    Per unit: Rp
                                                    {{ number_format($book->price - $book->capital, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-4">

                                                {{-- Detail --}}
                                                <div x-data="{ openDetailBookModal: false }">
                                                    @include('seller.book.detail', [
                                                        'book' => $book,
                                                    ])

                                                    <button @click="openDetailBookModal = true" type="button"
                                                        class="text-blue-500 hover:text-blue-600 transition" title="Detail">
                                                        <i class="bi bi-eye cursor-pointer"></i>
                                                    </button>
                                                </div>

                                                {{-- Edit --}}
                                                <div x-data="{ openEditBookModal: false }">
                                                    @include('seller.book.edit', [
                                                        'book' => $book,
                                                        'categories' => $categories,
                                                    ])

                                                    <button @click="openEditBookModal = true"
                                                        class="text-yellow-500 hover:text-yellow-600 transition"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square cursor-pointer"></i>
                                                    </button>
                                                </div>

                                                {{-- Delete --}}
                                                <form id="deleteBookForm-{{ $book->id }}" method="POST"
                                                    action="{{ route('seller.book.delete', $book->id) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDeleteBook({{ $book->id }}, '{{ $book->title }}')"
                                                        class="text-red-500 hover:text-red-600 transition" title="Delete">
                                                        <i class="bi bi-trash cursor-pointer"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
