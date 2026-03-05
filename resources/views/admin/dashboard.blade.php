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
                        <a href="{{ route('help.index') }}"
                            class="px-6 py-2.5 border-2 border-white text-white rounded-lg cursor-pointer hover:bg-white hover:text-emerald-600 transition duration-200 font-semibold">
                            Learn More Help
                        </a>
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

                <div class="flex flex-wrap items-center gap-6">
                    @php
                        // Mengambil user dengan role seller
                        $sellers = \App\Models\User::where('role', 'seller')->get();
                    @endphp

                    @forelse ($sellers as $seller)
                        @php
                            $name = $seller->name ?? 'User';
                            $initials = collect(explode(' ', $name))
                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                ->join('');
                            $initials = substr($initials, 0, 2);
                        @endphp

                        {{-- CONTAINER AVATAR VERTIKAL --}}
                        <div class="flex flex-col items-center gap-2 group cursor-pointer">

                            {{-- LINGKARAN FOTO --}}
                            <div class="relative">
                                <div
                                    class="w-16 h-16 rounded-full p-0.5 border-2 border-gray-100 group-hover:border-teal-500 transition-all duration-300">
                                    <div
                                        class="w-full h-full rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-lg overflow-hidden shadow-sm">
                                        @if ($seller->foto_profile)
                                            <img src="{{ asset('storage/' . $seller->foto_profile) }}"
                                                alt="{{ $name }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ $initials }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- STATUS DOT (Pojok Kanan Bawah) --}}
                                <span
                                    class="absolute bottom-1 right-1 w-3.5 h-3.5 rounded-full ring-2 ring-white {{ $seller->isOnline == 1 ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            </div>

                            {{-- NAMA DI BAWAH --}}
                            <div class="text-center">
                                <h4 class="text-xs font-semibold text-gray-700 group-hover:text-teal-600 transition-colors">
                                    {{ Str::limit(explode(' ', $seller->name)[0], 10) }}
                                </h4>
                                {{-- Label Online/Offline Kecil (Opsional) --}}
                                <span
                                    class="text-[9px] uppercase tracking-wider {{ $seller->isOnline == 1 ? 'text-green-500' : 'text-gray-400' }}">
                                    {{ $seller->isOnline == 1 ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>

                    @empty
                        <p class="text-gray-400 text-sm italic">No sellers found.</p>
                    @endforelse

                    {{-- TOMBOL TAMBAH / VIEW ALL (Seperti di gambar) --}}
                    <a href="{{ route('admin.sellers') }}" class="flex flex-col items-center gap-2 group">
                        <div
                            class="w-16 h-16 rounded-full border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-300 group-hover:border-teal-500 group-hover:text-teal-500 transition-all">
                            <i class="bi bi-plus-lg text-2xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 group-hover:text-teal-600">View All</span>
                    </a>
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
                            <div class="flex items-center gap-3 mb-6">
                                {{-- FOTO PROFILE / INITIALS --}}
                                <div
                                    class="w-10 h-10 rounded-xl overflow-hidden shadow-sm border border-teal-100 flex items-center justify-center bg-teal-50">
                                    @if (auth()->user()->foto_profile)
                                        <img src="{{ asset('storage/' . auth()->user()->foto_profile) }}" alt="Profile"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span class="text-teal-600 font-bold text-sm">
                                            {{ collect(explode(' ', auth()->user()->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                                        </span>
                                    @endif
                                </div>

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
