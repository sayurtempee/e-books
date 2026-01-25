{{-- MODAL EDIT CATEGORY --}}
<div x-show="openEditCategoryModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-black/30 backdrop-blur-sm"
    style="display: none;">

    {{-- MODAL BOX --}}
    <div @click.away="openEditCategoryModal = false"
        class="relative w-full max-w-lg
               bg-white/90 backdrop-blur-xl
               rounded-3xl
               p-7 md:p-8
               shadow-2xl
               border border-white/40">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div
                    class="flex items-center justify-center
                           w-9 h-9 rounded-xl
                           bg-teal-100 text-teal-600">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Edit Kategori
                </h3>
            </div>

            <button @click="openEditCategoryModal = false"
                class="p-2 rounded-xl
                       text-gray-400
                       hover:text-gray-600
                       hover:bg-gray-100
                       transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" x-data="{ loading: false }"
            @submit="loading = true" class="space-y-5 text-sm">
            @csrf
            @method('PUT')

            {{-- TITLE --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">
                    Title
                </label>
                <input type="text" name="title" value="{{ old('title', $category->title) }}" required
                    class="w-full h-11 px-4 rounded-xl
                           border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- CREATED AT --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">
                    Created At
                </label>
                <input type="date" name="created_at"
                    value="{{ old('created_at', $category->created_at->format('Y-m-d')) }}" required
                    class="w-full h-11 px-4 rounded-xl
                           bg-gray-100/80
                           border border-gray-200
                           focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEditCategoryModal = false"
                    class="px-5 py-2.5 text-sm rounded-xl
                           border border-gray-300
                           text-gray-600
                           hover:bg-gray-100
                           transition">
                    Cancel
                </button>

                <button type="submit" :disabled="loading"
                    class="px-5 py-2.5 rounded-xl
                           bg-teal-600 text-white
                           hover:bg-teal-700
                           disabled:opacity-60
                           disabled:cursor-not-allowed">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
