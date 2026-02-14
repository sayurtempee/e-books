{{-- MODAL EDIT SELLER --}}
<div x-show="openEditSellerModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" style="display: none;">

    {{-- MODAL BOX --}}
    <div @click.away="openEditSellerModal = false"
        class="relative w-full max-w-lg bg-white/90 backdrop-blur-xl rounded-3xl p-7 md:p-8 shadow-2xl border border-white/40 max-h-[90vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-teal-100 text-teal-600">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Edit Seller</h3>
            </div>
            <button @click="openEditSellerModal = false"
                class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        {{-- Inisialisasi photoPreview untuk live preview gambar --}}
        <form action="{{ route('admin.sellers.update', $user->id) }}" method="POST" enctype="multipart/form-data"
            x-data="{ loading: false, photoPreview: null }" @submit="loading = true" class="space-y-5 text-sm">
            @csrf
            @method('PUT')

            {{-- EDIT FOTO PROFILE DENGAN PREVIEW --}}
            <div
                class="flex flex-col items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                <div class="relative">
                    {{-- Preview Foto Baru --}}
                    <template x-if="photoPreview">
                        <img :src="photoPreview"
                            class="w-24 h-24 rounded-2xl object-cover shadow-md border-2 border-teal-500">
                    </template>

                    {{-- Foto Lama (Jika tidak ada preview baru) --}}
                    <template x-if="!photoPreview">
                        @if ($user->foto_profile)
                            <img src="{{ asset('storage/' . $user->foto_profile) }}"
                                class="w-24 h-24 rounded-2xl object-cover shadow-sm border-2 border-white">
                        @else
                            <div
                                class="w-24 h-24 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center text-2xl font-bold border-2 border-white shadow-sm">
                                {{ collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('') }}
                            </div>
                        @endif
                    </template>
                </div>

                <div class="w-full">
                    <label class="block text-gray-600 mb-1.5 font-medium text-center">Ganti Foto Profile</label>
                    <input type="file" name="foto_profile" accept="image/*"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            }
                        "
                        class="w-full text-xs text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0
                               file:text-xs file:font-semibold
                               file:bg-teal-50 file:text-teal-700
                               hover:file:bg-teal-100 transition cursor-pointer">
                    @error('foto_profile')
                        <p class="text-red-500 text-[10px] mt-1 text-center">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- NIK --}}
                <div class="md:col-span-2">
                    <label class="block text-gray-600 mb-1 text-left font-medium">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" required
                        class="w-full h-11 px-4 rounded-xl bg-gray-100/80 border border-gray-200 focus:border-teal-500 focus:ring-teal-500 transition-all">
                </div>

                {{-- NAME --}}
                <div>
                    <label class="block text-gray-600 mb-1 text-left font-medium">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:border-teal-500 focus:ring-teal-500 transition-all">
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block text-gray-600 mb-1 text-left font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:border-teal-500 focus:ring-teal-500 transition-all">
                </div>

                {{-- BANK NAME --}}
                <div>
                    <label class="block text-gray-600 mb-1 text-left font-medium">Bank Name</label>
                    <select name="bank_name"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none bg-white">
                        <option value="" disabled {{ !$user->bank_name ? 'selected' : '' }}>Pilih Bank</option>
                        @foreach (['BCA', 'Mandiri', 'BNI', 'BRI'] as $bank)
                            <option value="{{ $bank }}" {{ $user->bank_name == $bank ? 'selected' : '' }}>
                                {{ $bank }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- NO REKENING --}}
                <div>
                    <label class="block text-gray-600 mb-1 text-left font-medium">No. Rekening</label>
                    <input type="text" name="no_rek" value="{{ old('no_rek', $user->no_rek) }}"
                        class="w-full h-11 px-4 rounded-xl border border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono transition-all">
                </div>
            </div>

            {{-- ADDRESS --}}
            <div>
                <label class="block text-gray-600 mb-1 text-left font-medium">Alamat</label>
                <textarea name="address" rows="2"
                    class="w-full p-4 rounded-xl bg-gray-100/80 border border-gray-200 focus:border-teal-500 focus:ring-teal-500 transition-all">{{ old('address', $user->address) }}</textarea>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="openEditSellerModal = false"
                    class="px-5 py-2.5 text-sm rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button type="submit" :disabled="loading"
                    class="px-5 py-2.5 rounded-xl bg-teal-600 text-white hover:bg-teal-700 disabled:opacity-60 disabled:cursor-not-allowed transition shadow-md shadow-teal-200">
                    <span x-show="!loading">Update Seller</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Proses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
