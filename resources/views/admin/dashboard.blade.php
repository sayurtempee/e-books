<x-app>
    @section('title', 'Dashboard Admin')

    @section('body-content')
        <x-sidebar>
            {{-- BANNER CARD --}}
            <div
                class="bg-gradient-to-r from-teal-400 to-emerald-500 rounded-2xl p-8 text-white flex justify-between items-center mb-8 shadow-lg shadow-teal-100">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Miimoys E-Books</h2>
                    <p class="max-w-md opacity-90">selalu di depan melayani kebutuhan anda</p>

                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('admin.categories') }}"
                            class="px-6 py-2.5 bg-white text-emerald-600 rounded-lg cursor-pointer font-semibold hover:bg-emerald-50 shadow-md transition duration-200">
                            Get Started
                        </a>
                        <button
                            class="px-6 py-2.5 border-2 border-white text-white rounded-lg cursor-pointer hover:bg-white hover:text-emerald-600 transition duration-200 font-semibold"
                            onclick="openHelpModal()">
                            Learn More
                        </button>
                    </div>
                </div>
                <img src="{{ asset('image/people-image.svg') }}" class="w-32 hidden md:block drop-shadow-lg">
            </div>

            {{-- SECTION SELLER ACTIVE --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-6 bg-teal-500 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800">Seller Status & Activity</h3>
                    </div>
                    <span
                        class="text-[10px] bg-teal-50 text-teal-600 border border-teal-100 px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                        Role: Seller Only
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                        // Mengambil user dengan role seller sesuai struktur database Anda
                        $sellers = \App\Models\User::where('role', 'seller')->get();
                    @endphp

                    @forelse ($sellers as $seller)
                        @php
                            // Generate inisial dari nama masing-masing seller
                            $name = $seller->name ?? 'User';
                            $initials = collect(explode(' ', $name))
                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                ->join('');
                            $initials = substr($initials, 0, 2);
                        @endphp

                        <div
                            class="bg-white border border-gray-100 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center gap-4">

                                {{-- AVATAR DENGAN INISIAL & STATUS --}}
                                <div
                                    class="relative w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                    {{ $initials }}

                                    {{-- GREEN/GRAY DOT (Berdasarkan kolom isOnline) --}}
                                    @if ($seller->isOnline == 1)
                                        <span
                                            class="absolute top-0 right-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-white animate-pulse shadow-sm">
                                        </span>
                                    @else
                                        <span
                                            class="absolute top-0 right-0 w-3 h-3 bg-gray-300 rounded-full ring-2 ring-white shadow-sm">
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-sm font-black text-gray-800 leading-tight mb-0.5">
                                        {{ $seller->name }}
                                    </h4>
                                    <p class="text-[11px] text-gray-400 font-medium truncate max-w-[110px]">
                                        {{ $seller->email }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                @if ($seller->isOnline == 1)
                                    <span
                                        class="text-[9px] font-black text-green-600 bg-green-50 px-2.5 py-1 rounded-lg border border-green-100">
                                        ONLINE
                                    </span>
                                @else
                                    <span
                                        class="text-[9px] font-bold text-gray-400 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-100">
                                        OFFLINE
                                    </span>
                                    <div class="text-[9px] text-gray-400 mt-1.5 font-medium italic">
                                        {{ $seller->last_activity_at ? $seller->last_activity_at->diffForHumans() : 'Last seen unknown' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-10 text-center">
                            <i class="bi bi-people text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-gray-400 text-sm font-medium italic">No sellers registered yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- MY ACCOUNT --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    My Account
                </h3>

                <div
                    class="relative bg-white rounded-2xl p-6 md:p-7
               border border-gray-200
               shadow-sm">

                    <div x-data="{ openEditModal: false }">
                        @include('layouts.modal.edit.admin')
                        {{-- EDIT BUTTON (VISUAL ONLY) --}}
                        <button @click="openEditModal = true" type="button"
                            class="absolute top-4 right-4 p-2 rounded-lg
                            text-gray-400 hover:text-teal-600
                            hover:bg-teal-50 transition cursor-pointer">
                            <i class="bi bi-pencil-square text-lg"></i>
                        </button>

                        {{-- HEADER --}}
                        <div class="flex items-center gap-2 mb-6">
                            <i class="bi bi-person-circle text-teal-600 text-xl"></i>
                            <h4 class="text-base font-semibold text-gray-800">
                                Account Information
                            </h4>
                        </div>

                        {{-- CONTENT --}}
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">NIK</span>
                                <span class="font-medium text-gray-800">
                                    {{ substr(auth()->user()->nik, 0, 8) . str_repeat('*', strlen(auth()->user()->nik) - 8) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Name</span>
                                <span class="font-medium text-gray-800">
                                    {{ auth()->user()->name }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Email</span>
                                <span class="font-medium text-gray-800">
                                    {{ auth()->user()->email }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Bank</span>
                                <span class="font-medium text-gray-800">
                                    {{ auth()->user()->bank_name ?? 'Not set' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Account Number</span>
                                <span class="font-medium text-gray-800 font-mono">
                                    {{ auth()->user()->no_rek ?? 'Not set' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Level</span>
                                <span class="font-medium text-gray-800 capitalize">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Address</span>
                                <span class="font-medium text-gray-800 capitalize">
                                    {{ auth()->user()->address }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
