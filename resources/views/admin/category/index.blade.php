<x-app>
    @section('title', 'List Kategori')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">

                <h1 class="text-2xl font-bold text-teal-600 mb-6">
                    Halaman Kategori
                </h1>

                {{-- Add Category --}}
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
                                            class="px-4 py-3 {{ $category->books->count() == 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $category->books->count() }}
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-4">

                                                {{-- Edit --}}
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
