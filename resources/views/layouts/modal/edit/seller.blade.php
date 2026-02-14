{{-- MODAL EDIT ACCOUNT --}}
<div x-show="openEditModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-[2px]" style="display: none;">

    {{-- MODAL BOX --}}
    <div @click.away="openEditModal = false"
        class="relative w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-gray-100 max-h-[95vh] overflow-y-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-teal-50 text-teal-600">
                    <i class="bi bi-pencil-square text-sm"></i>
                </div>
                <h3 class="text-base font-bold text-gray-800">Edit Account</h3>
            </div>
            <button @click="openEditModal = false" class="text-gray-400 hover:text-red-500 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM UTAMA --}}
        <form method="POST" action="{{ route('account.update') }}" x-data="{ loading: false, photoPreview: null }" @submit="loading = true"
            class="space-y-4 text-xs" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- UPLOAD FOTO PROFILE --}}
            <div
                class="flex flex-col items-center gap-3 p-3 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <div class="relative group">
                    {{-- Preview Foto Baru (Saat Upload) --}}
                    <template x-if="photoPreview">
                        <div class="relative">
                            <img :src="photoPreview"
                                class="w-20 h-20 rounded-2xl object-cover shadow-md border-2 border-teal-500">
                            {{-- Tombol Batal Preview --}}
                            <button type="button"
                                @click="photoPreview = null; document.getElementById('foto_input').value = ''"
                                class="absolute -top-2 -right-2 bg-gray-800 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-gray-900 transition">
                                <i class="bi bi-x text-sm"></i>
                            </button>
                        </div>
                    </template>

                    {{-- Foto Lama / Inisial --}}
                    <template x-if="!photoPreview">
                        <div class="relative">
                            @if (auth()->user()->foto_profile)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profile) }}"
                                    class="w-20 h-20 rounded-2xl object-cover shadow-sm border-2 border-white">

                                {{-- TOMBOL HAPUS FOTO PERMANEN --}}
                                <button type="button"
                                    onclick="if(confirm('Hapus foto profil permanen?')) { document.getElementById('delete-photo-form').submit(); }"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-600 transition border-2 border-white">
                                    <i class="bi bi-trash text-[10px]"></i>
                                </button>
                            @else
                                <div
                                    class="w-20 h-20 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center text-2xl font-bold border-2 border-white shadow-sm">
                                    {{ collect(explode(' ', auth()->user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('') }}
                                </div>
                            @endif
                        </div>
                    </template>
                </div>

                <div class="w-full">
                    <label class="block text-gray-500 text-center mb-1 font-medium">Ubah Foto Profil</label>
                    <input type="file" id="foto_input" name="foto_profile" accept="image/*"
                        @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            }
                        "
                        class="w-full text-[10px] text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition cursor-pointer">
                </div>
            </div>

            {{-- INFORMASI AKUN (READONLY) --}}
            <div class="grid grid-cols-2 gap-x-4 gap-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="col-span-1">
                    <label class="text-gray-400 block uppercase text-[10px] font-bold tracking-tight">NIK</label>
                    <p class="text-gray-700 font-medium truncate">{{ auth()->user()->nik }}</p>
                </div>
                <div class="col-span-1">
                    <label class="text-gray-400 block uppercase text-[10px] font-bold tracking-tight">Level</label>
                    <span class="px-1.5 py-0.5 rounded bg-teal-100 text-teal-700 font-bold capitalize text-[10px]">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

            {{-- EDITABLE FIELDS --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-gray-600 font-semibold mb-1 ml-0.5">Nama Lengkap</label>
                    <input type="text" name="name"
                        class="w-full h-9 px-3 rounded-lg border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition"
                        value="{{ old('name', auth()->user()->name) }}">
                </div>
                <div>
                    <label class="block text-gray-600 font-semibold mb-1 ml-0.5">Email</label>
                    <input type="email" name="email"
                        class="w-full h-9 px-3 rounded-lg border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition"
                        value="{{ old('email', auth()->user()->email) }}">
                </div>
                <div>
                    <label class="block text-gray-600 font-semibold mb-1 ml-0.5">Alamat</label>
                    <textarea name="address" rows="2"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition"
                        placeholder="Alamat lengkap...">{{ old('address', auth()->user()->address) }}</textarea>
                </div>
            </div>

            {{-- FOOTER ACTION --}}
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-50">
                <button type="button" @click="openEditModal = false"
                    class="px-4 py-2 font-medium rounded-lg text-gray-500 hover:bg-gray-100 transition">Batal</button>
                <button type="submit" :disabled="loading"
                    class="px-5 py-2 rounded-lg bg-teal-600 text-white font-bold shadow-sm hover:bg-teal-700 disabled:opacity-50 transition flex items-center gap-2">
                    <span x-show="!loading">Update Profil</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-3 w-3 mr-2 text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>

        {{-- PINDAHKAN FORM DELETE KE SINI (DI LUAR FORM UTAMA) --}}
        <form id="delete-photo-form" action="{{ route('account.delete-photo') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
