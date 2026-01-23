<x-app>
    @section('title', 'Dashboard Seller')

    @section('body-content')
        <x-sidebar>
            {{-- BANNER CARD --}}
            <div
                class="bg-gradient-to-r from-teal-400 to-emerald-500 rounded-2xl p-8 text-white flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        Miimoys E-Books
                    </h2>
                    <p class="max-w-md">
                        selalu di depan melayani kebutuhan anda
                    </p>

                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('seller.book.index') }}"
                            class="px-4 py-2 bg-white text-teal-600 rounded-lg font-semibold">
                            Get Started
                        </a>
                        <button class="px-4 py-2 border border-white rounded-lg cursor-pointer" onclick="openHelpModal()">
                            Learn More
                        </button>
                    </div>
                </div>

                <img src="{{ asset('image/people-image.svg') }}" class="w-32 hidden md:block">
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
                        @include('layouts.modal.edit.seller')
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
                                <span class="text-gray-500">Level</span>
                                <span class="font-medium text-gray-800 capitalize">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Address</span>
                                <span class="font-medium text-gray-800 capitalize">
                                    {{ auth()->user()->address ?? 'Address has not been entered' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--  Category Section  --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Your Category Book
                </h3>
                <div class="flex gap-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('seller.book.index', ['category' => $category->id]) }}"
                            class="flex flex-col items-center bg-white rounded-xl px-4 py-3 shadow hover:shadow-md hover:bg-teal-50 transition cursor-pointer">
                            <i class="bi bi-{{ $category->getIconClass() }} text-teal-600 text-2xl mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $category->title }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
