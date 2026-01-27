{{-- MODAL EDIT ACCOUNT --}}
<div x-show="openEditModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-[2px]" style="display: none;">

    {{-- MODAL BOX - Diperkecil ke max-w-md --}}
    <div @click.away="openEditModal = false"
        class="relative w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-gray-100">

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

        {{-- FORM --}}
        <form method="POST" action="{{ route('account.update') }}" x-data="{ loading: false }" @submit="loading = true"
            class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            {{-- INFORMASI AKUN (READONLY GRID) --}}
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
                <div class="col-span-1 mt-1 border-t border-gray-200 pt-2">
                    <label class="text-gray-400 block uppercase text-[10px] font-bold tracking-tight">Bank</label>
                    <p class="text-gray-600 truncate">{{ auth()->user()->bank_name ?? '-' }}</p>
                </div>
                <div class="col-span-1 mt-1 border-t border-gray-200 pt-2">
                    <label class="text-gray-400 block uppercase text-[10px] font-bold tracking-tight">No.
                        Rekening</label>
                    <p class="text-gray-600 truncate tracking-wider">{{ auth()->user()->no_rek ?? '-' }}</p>
                </div>
            </div>

            {{-- EDITABLE FIELDS --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-gray-600 font-semibold mb-1 ml-0.5">Nama Lengkap</label>
                    <input type="text" name="name" x-ref="nameInput" x-init="$watch('openEditModal', v => v && $nextTick(() => $refs.nameInput.focus()))"
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
                    class="px-4 py-2 font-medium rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    Batal
                </button>

                <button type="submit" :disabled="loading"
                    class="px-5 py-2 rounded-lg bg-teal-600 text-white font-bold shadow-sm hover:bg-teal-700 disabled:opacity-50 transition flex items-center gap-2">
                    <span x-show="!loading">Update Profil</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-3 w-3 mr-2 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
