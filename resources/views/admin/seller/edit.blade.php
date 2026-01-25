{{-- MODAL EDIT SELLER --}}
<div x-show="openEditSellerModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-black/30 backdrop-blur-sm"
    style="display: none;">
    {{-- MODAL BOX --}}
    <div @click.away="openEditSellerModal = false"
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
                    Edit Seller
                </h3>
            </div>

            <button @click="openEditSellerModal = false"
                class="p-2 rounded-xl
                       text-gray-400
                       hover:text-gray-600
                       hover:bg-gray-100
                       transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('admin.sellers.update', $user->id) }}" method="POST" x-data="{ loading: false }"
            @submit="loading = true" class="space-y-5 text-sm">
            @csrf
            @method('PUT')

            {{-- NIK --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">NIK</label>
                <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" required
                    class="w-full h-11 px-4 rounded-xl
                   bg-gray-100/80
                   border border-gray-200
                   focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- NAME --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full h-11 px-4 rounded-xl border border-gray-300
                   focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full h-11 px-4 rounded-xl border border-gray-300
                   focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- GRID UNTUK INFO BANK (READONLY) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- BANK NAME --}}
                <div>
                    <label class="block text-gray-500 mb-1 text-left">Bank Name</label>
                    <input type="text" readonly
                        class="w-full h-11 px-4 rounded-xl
                   bg-gray-100/80
                   border border-gray-200
                   text-gray-500
                   cursor-not-allowed"
                        value="{{ $user->bank_name ?? 'Not set' }}">
                </div>

                {{-- NO REKENING --}}
                <div>
                    <label class="block text-gray-500 mb-1 text-left">No. Rekening</label>
                    <input type="text" readonly
                        class="w-full h-11 px-4 rounded-xl
                   bg-gray-100/80
                   border border-gray-200
                   text-gray-500
                   font-mono
                   cursor-not-allowed"
                        value="{{ $user->no_rek ?? 'Not set' }}">
                </div>
            </div>

            {{-- ADDRESS --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">Alamat</label>
                <input type="text" name="address" value="{{ old('address', $user->address ?? '-') }}"
                    class="w-full h-11 px-4 rounded-xl
                   bg-gray-100/80
                   border border-gray-200
                   focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEditSellerModal = false"
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
