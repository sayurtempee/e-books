{{-- MODAL DETAIL SELLER - DESIGN MODERN CARD STYLE --}}
<div x-show="openDetailSellerModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" style="display: none;"
    @keydown.escape.window="openDetailSellerModal = false">

    {{-- MODAL BOX WITH CARD DESIGN --}}
    <div @click.away="openDetailSellerModal = false" class="relative w-full max-w-sm mx-auto">

        {{-- CLOSE BUTTON (FLOATING TOP RIGHT) --}}
        <button @click="openDetailSellerModal = false"
            class="absolute -top-3 -right-3 z-10
                       w-10 h-10 rounded-full
                       bg-white shadow-lg
                       flex items-center justify-center
                       text-gray-600 hover:text-gray-800
                       hover:shadow-xl hover:scale-110
                       transition-all duration-200">
            <i class="bi bi-x-lg text-lg"></i>
        </button>

        {{-- CARD CONTAINER --}}
        <div class="relative overflow-hidden rounded-3xl shadow-2xl">

            {{-- BACKGROUND GRADIENT (TOP SECTION) --}}
            <div class="relative h-32 bg-gradient-to-br from-teal-500 via-teal-600 to-cyan-600">
                {{-- DECORATIVE CIRCLES --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2">
                </div>
            </div>

            {{-- CONTENT SECTION --}}
            <div class="relative bg-white px-6 pb-8 pt-2">

                {{-- AVATAR (OVERLAPPING) --}}
                @php
                    $initials = collect(explode(' ', $user->name))
                        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                        ->take(2)
                        ->implode('');
                @endphp

                <div class="flex justify-center -mt-14 mb-4">
                    <div class="relative">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                class="w-28 h-28 rounded-full object-cover
                                        border-4 border-white shadow-xl"
                                alt="Profile Photo">
                        @else
                            <div class="w-28 h-28 rounded-full
                                       bg-gradient-to-br from-teal-400 to-cyan-500
                                       border-4 border-white shadow-xl
                                       flex items-center justify-center
                                       text-3xl font-bold text-white"
                                title="{{ $user->name }}">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- USER INFO --}}
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-1">
                        {{ $user->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-2">
                        {{ ucfirst($user->nik) }}
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 bg-gradient-to-r from-teal-500 to-cyan-500
                                 text-white text-xs font-semibold shadow-md">
                        <i class="bi bi-shield-check"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                {{-- DETAIL ITEMS --}}
                <div class="space-y-3">

                    {{-- EMAIL --}}
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-lg
                                    bg-gradient-to-br from-blue-400 to-blue-500
                                    flex items-center justify-center shadow-sm">
                            <i class="bi bi-envelope-fill text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 font-medium mb-0.5">Email</p>
                            <p class="text-sm text-gray-800 font-medium truncate">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    {{-- ADDRESS --}}
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-lg
                                    bg-gradient-to-br from-purple-400 to-purple-500
                                    flex items-center justify-center shadow-sm">
                            <i class="bi bi-geo-alt-fill text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 font-medium mb-0.5">Alamat</p>
                            <p class="text-sm text-gray-800 leading-relaxed">
                                {{ $user->address ?? 'Address has not been entered' }}
                            </p>
                        </div>
                    </div>

                    {{-- BANK INFO --}}
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-lg
                            bg-gradient-to-br from-emerald-400 to-emerald-500
                            flex items-center justify-center shadow-sm">
                            <i class="bi bi-bank2 text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 font-medium mb-0.5">Informasi Bank</p>
                            <p class="text-sm text-gray-800 font-medium">
                                {{ $user->bank_name ?? '-' }}
                            </p>
                            <p class="text-xs text-teal-600 font-mono tracking-wider">
                                {{ $user->no_rek ?? 'Belum diatur' }}
                            </p>
                        </div>
                    </div>

                    {{-- JOINED DATE (optional) --}}
                    @if ($user->created_at ?? false)
                        <div
                            class="flex items-start gap-3 p-3 rounded-xl bg-gray-100 hover:bg-gray-300 transition-colors">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-lg
                                    bg-gradient-to-br from-orange-400 to-orange-500
                                    flex items-center justify-center shadow-sm">
                                <i class="bi bi-calendar-check-fill text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 font-medium mb-0.5">Bergabung Sejak</p>
                                <p class="text-sm text-gray-800 font-medium">
                                    {{ $user->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- FOOTER INFO (OPTIONAL STATS) --}}
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-10 text-center">
                        <div>
                            {{-- Menggunakan Accessor getBooksCountAttribute --}}
                            <p class="text-lg font-bold text-teal-600">{{ $user->books_count }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Total Buku</p>
                        </div>

                        <div class="w-px h-8 bg-gray-200"></div>

                        <div>
                            {{-- Menggunakan Accessor getSoldCountAttribute --}}
                            <p class="text-lg font-bold text-cyan-600">{{ $user->sold_count }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Item Terjual</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
