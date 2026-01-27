{{-- MODAL EDIT ACCOUNT --}}
<div x-show="openEditModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" style="display: none;">

    {{-- MODAL BOX - Diperkecil max-w-md --}}
    <div @click.away="openEditModal = false"
        class="relative w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-white/40">

        {{-- HEADER - Lebih compact --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-teal-100 text-teal-600">
                    <i class="bi bi-pencil-square text-sm"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-800">Edit Account</h3>
            </div>
            <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('account.update') }}" x-data="{ loading: false }" @submit="loading = true"
            class="space-y-3 text-xs">
            @csrf
            @method('PUT')

            {{-- Baris 1: NIK & Role (Readonly Grid) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-gray-500 mb-1 block">NIK</label>
                    <input type="text" readonly
                        class="w-full h-9 px-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-500 cursor-not-allowed"
                        value="{{ auth()->user()->nik }}">
                </div>
                <div>
                    <label class="text-gray-500 mb-1 block">Level</label>
                    <input type="text" readonly
                        class="w-full h-9 px-3 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 font-medium capitalize cursor-not-allowed"
                        value="{{ auth()->user()->role }}">
                </div>
            </div>

            {{-- Baris 2: Bank & Account (Readonly Grid) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-gray-500 mb-1 block">Bank Name</label>
                    <input type="text" readonly
                        class="w-full h-9 px-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-500 cursor-not-allowed"
                        value="{{ auth()->user()->bank_name ?? '-' }}">
                </div>
                <div>
                    <label class="text-gray-500 mb-1 block">Account Number</label>
                    <input type="text" readonly
                        class="w-full h-9 px-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-500 cursor-not-allowed"
                        value="{{ auth()->user()->no_rek ?? '-' }}">
                </div>
            </div>

            <hr class="border-gray-100 my-1">

            {{-- Editable Fields --}}
            <div>
                <label class="text-gray-700 font-medium mb-1 block">Full Name</label>
                <input type="text" name="name" x-ref="nameInput" x-init="$watch('openEditModal', v => v && $nextTick(() => $refs.nameInput.focus()))"
                    class="w-full h-9 px-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none"
                    value="{{ old('name', auth()->user()->name) }}">
            </div>

            <div>
                <label class="text-gray-700 font-medium mb-1 block">Email Address</label>
                <input type="email" name="email"
                    class="w-full h-9 px-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none"
                    value="{{ old('email', auth()->user()->email) }}">
            </div>

            <div>
                <label class="text-gray-700 font-medium mb-1 block">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none">{{ old('address', auth()->user()->address) }}</textarea>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="openEditModal = false"
                    class="px-4 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 text-xs font-medium rounded-lg bg-teal-600 text-white hover:bg-teal-700 disabled:opacity-60 transition flex items-center gap-2">
                    <span x-show="!loading">Update Account</span>
                    <span x-show="loading" class="flex items-center gap-1">
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
