<x-app>
    @section('title', 'List Kategori')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">

                <h1 class="text-2xl font-bold text-teal-600 mb-6">
                    Halaman Kategori
                </h1>

                {{-- Container Add Category + Search --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    {{-- Sisi Kiri: Tombol Tambah --}}
                    <div x-data="{ openAddCategoryModal: false }">
                        @include('admin.category.add')

                        <button @click="openAddCategoryModal = true"
                            class="inline-flex items-center gap-2
                                   bg-teal-600 hover:bg-teal-700
                                   text-white px-4 py-2
                                   rounded-lg shadow
                                   cursor-pointer font-semibold">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Kategori
                        </button>
                    </div>

                    {{-- Sisi Kanan: Search Bar --}}
                    <div class="w-full md:w-72">
                        <form action="{{ route('admin.categories') }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Category..."
                                class="w-full pl-10 pr-10 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="bi bi-search"></i>
                            </div>
                            @if (request('search'))
                                <button type="button"
                                    onclick="document.querySelector('input[name=\'search\']').value=''; this.closest('form').submit()"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            @endif
                            <button type="submit" class="hidden">Search</button>
                        </form>
                    </div>
                </div>

                {{-- Category Table --}}
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-teal-200 rounded-lg overflow-hidden">

                            <thead class="bg-teal-500 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left w-16">No</th>
                                    <th class="px-4 py-3 text-left">Title</th>
                                    <th class="px-4 py-3 text-left">Created Date</th>
                                    <th class="px-4 py-3 text-left">Product Book All</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-teal-100">
                                @foreach ($categories as $index => $category)
                                    <tr class="hover:bg-teal-50 transition">

                                        <td class="px-4 py-3">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $category->title }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $category->created_at->format('d M Y') }}
                                        </td>

                                        <td
                                            class="px-4 py-3 {{ $category->book->count() == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $category->book->count() }}
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-4">
                                                {{-- Edit --}}
                                                @if ($category->book->count() == 0)
                                                    <div x-data="{ openEditCategoryModal: false }">
                                                        @include('admin.category.edit', [
                                                            'category' => $category,
                                                        ])

                                                        <button @click="openEditCategoryModal = true" type="button"
                                                            class="text-yellow-500 hover:text-yellow-600 transition"
                                                            title="Edit">
                                                            <i class="bi bi-pencil-square cursor-pointer"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Delete --}}
                                                    <form id="deleteCategoryForm-{{ $category->id }}"
                                                        action="{{ route('admin.categories.delete', $category->id) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button"
                                                            onclick="confirmDeleteCategory({{ $category->id }}, '{{ $category->title }}')"
                                                            class="text-red-500 hover:text-red-600 transition"
                                                            title="Delete">
                                                            <i class="bi bi-trash cursor-pointer"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Opsional: Tampilkan icon abu-abu jika tidak bisa diedit --}}
                                                    <span class="text-gray-300" title="Tidak ada stok">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </span>
                                                    <span class="text-gray-300" title="Tidak ada stok">
                                                        <i class="bi bi-trash"></i>
                                                    </span>
                                                @endif
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
