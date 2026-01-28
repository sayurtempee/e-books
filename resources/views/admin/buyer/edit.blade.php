{{-- MODAL EDIT BUYER --}}
<div x-show="openEditBuyerModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-black/30 backdrop-blur-sm"
    style="display: none;">
    {{-- MODAL BOX --}}
    <div @click.away="openEditBuyerModal = false"
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
                    Edit Buyer
                </h3>
            </div>

            <button @click="openEditBuyerModal = false"
                class="p-2 rounded-xl
                       text-gray-400
                       hover:text-gray-600
                       hover:bg-gray-100
                       transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('admin.buyers.update', $user->id) }}" method="POST" x-data="{ loading: false }"
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

            {{-- GRID UNTUK INFO BANK (EDITABLE) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- BANK NAME (SELECT OPTION) --}}
                <div>
                    <label for="bank_name" class="block text-gray-700 font-medium mb-1 text-left">Bank Name</label>
                    <select name="bank_name" id="bank_name"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                        <option value="" disabled {{ !$user->bank_name ? 'selected' : '' }}>Pilih Bank</option>
                        <option value="BCA" {{ $user->bank_name == 'BCA' ? 'selected' : '' }}>BCA</option>
                        <option value="Mandiri" {{ $user->bank_name == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="BNI" {{ $user->bank_name == 'BNI' ? 'selected' : '' }}>BNI</option>
                        <option value="BRI" {{ $user->bank_name == 'BRI' ? 'selected' : '' }}>BRI</option>
                    </select>
                    @error('bank_name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- NO REKENING (VARCHAR / TEXT) --}}
                <div>
                    <label for="no_rek" class="block text-gray-700 font-medium mb-1 text-left">No. Rekening</label>
                    <input type="text" name="no_rek" id="no_rek"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono transition-all outline-none @error('no_rek') border-red-500 @enderror"
                        placeholder="Contoh: 1234567890" maxlength="20" value="{{ old('no_rek', $user->no_rek) }}">
                    @error('no_rek')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- ADDRESS --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left">Alamat</label>
                <input type="text" name="address"
                    value="{{ old('address', $user->address ?? 'Address has not been entered') }}"
                    class="w-full h-11 px-4 rounded-xl
                   bg-gray-100/80
                   border border-gray-200
                   focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEditBuyerModal = false"
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
