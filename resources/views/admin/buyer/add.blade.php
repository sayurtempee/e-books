{{-- MODAL ADD BUYER --}}
<div x-show="openAddBuyerModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-black/30 backdrop-blur-sm"
    style="display: none;">
    {{-- MODAL BOX --}}
    <div @click.away="openAddBuyerModal = false"
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
                    <i class="bi bi-shop"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Tambah Buyer
                </h3>
            </div>

            <button @click="openAddBuyerModal = false"
                class="p-2 rounded-xl
                       text-gray-400
                       hover:text-gray-600
                       hover:bg-gray-100
                       transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('admin.buyers.create') }}" method="POST" x-data="{ loading: false }"
            @submit="loading = true" class="space-y-5 text-sm" enctype="multipart/form-data">
            @csrf

            {{-- FOTO PROFILE --}}
            <div>
                <label class="block text-gray-600 mb-1">Foto Profile</label>
                <input type="file" name="foto_profile" accept="image/*"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-full file:border-0
                   file:text-sm file:font-semibold
                   file:bg-teal-50 file:text-teal-700
                   hover:file:bg-teal-100">
                <p class="text-[10px] text-gray-400 mt-1">*Format: jpg, png, max 2MB</p>
            </div>

            {{-- NAME --}}
            <div>
                <label class="block text-gray-600 mb-1">Nama</label>
                <input type="text" name="name" required autofocus
                    class="w-full h-11 px-4 rounded-xl border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Your name">
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-gray-600 mb-1">Email</label>
                <input type="email" name="email" required
                    class="w-full h-11 px-4 rounded-xl border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500"
                    placeholder="you@example.com">
            </div>

            {{-- NIK --}}
            <div>
                <label class="block text-gray-600 mb-1">NIK</label>
                <input type="text" name="nik" required
                    class="w-full h-11 px-4 rounded-xl
                           bg-gray-100/80
                           border border-gray-200
                           focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Your NIK">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- BANK NAME --}}
                <div>
                    <label class="block text-gray-600 mb-1">Bank Name</label>
                    <select name="bank_name"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500 bg-white">
                        <option value="" disabled selected>Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                    </select>
                </div>

                {{-- NO REKENING --}}
                <div>
                    <label class="block text-gray-600 mb-1">Nomor Rekening</label>
                    <input type="text" name="no_rek"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500"
                        placeholder="Contoh: 12345678">
                </div>
            </div>

            {{-- PASSWORD --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full h-11 px-4 rounded-xl border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500"
                        placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label class="block text-gray-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full h-11 px-4 rounded-xl border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500"
                        placeholder="Repeat password">
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-gray-600 mb-1">Alamat</label>
                <input type="text" name="address"
                    class="w-full h-11 px-4 rounded-xl
                           bg-gray-100/80
                           border border-gray-200
                           focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Your address">
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openAddBuyerModal = false"
                    class="px-5 py-2.5 text-sm rounded-xl
                           border border-gray-300
                           text-gray-600
                           hover:bg-gray-100
                           transition">
                    Cancel
                </button>

                <button type="submit" :disabled="loading"
                    class="px-5 py-2.5 rounded-xl bg-teal-600 text-white
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
