@php
    $activeClass = 'bg-teal-500 text-white font-semibold';
    $inactiveClass = 'text-gray-700 hover:bg-gray-100';
@endphp

<div x-data="{ sidebarOpen: false }" class="min-h-screen flex bg-gray-100 font-lato">

    {{-- OVERLAY MOBILE --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-30 md:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg
               flex flex-col justify-between
               transform transition-transform duration-300
               md:relative md:translate-x-0">

        {{-- TOP --}}
        <div>
            <div class="px-6 py-6 text-xl font-bold text-teal-600">
                Miimoys E-Books
            </div>

            {{--  Navbar  --}}
            @auth
                @include('layouts.sidebar.' . auth()->user()->role)
            @endauth
        </div>

        {{-- FOOTER --}}
        <div class="flex flex-col gap-1 p-4 border-t border-teal-100">

            {{-- LOGOUT --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout()"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                               text-red-500 hover:bg-red-100">
                    <i class="bi bi-box-arrow-left text-lg"></i>
                    <span>Sign Out</span>
                </button>
            </form>

            {{-- HELP --}}
            <button type="button" onclick="openHelpModal()"
                class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                           text-teal-600 hover:bg-teal-100 font-semibold">
                <i class="bi bi-question-circle-fill text-lg"></i>
                <span>Help</span>
            </button>
        </div>
    </aside>

    {{-- CONTENT --}}
    <div class="flex-1 p-6 md:p-8">
        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            {{-- LEFT --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden flex flex-col justify-center gap-1.5
                   w-8 h-8 p-1 rounded hover:bg-gray-200">
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                </button>

                {{-- TITLE TETAP --}}
                <h1 class="text-2xl font-bold text-gray-800">
                    @yield('title')
                </h1>
            </div>

            {{-- USER + NOTIFICATION --}}
            @php
                $name = auth()->user()->name ?? 'User';
                $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                $initials = substr($initials, 0, 2);

                // Menggunakan variabel $notifications dari AppServiceProvider
                $unreadCount = $notifications->whereNull('read_at')->count();
            @endphp

            <div class="relative" x-data="{
                openProfile: false,
                {{-- Dot hijau akan muncul jika ada notifikasi yang belum dibaca --}}
                hasNotification: {{ $unreadCount > 0 ? 'true' : 'false' }}
            }">

                {{-- PROFILE BUTTON --}}
                <button @click="openProfile = !openProfile" class="flex items-center gap-3">

                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-800">{{ $name }}</p>
                        <p class="text-xs text-gray-500 capitalize">
                            {{ auth()->user()->role ?? 'Admin' }}
                        </p>
                    </div>

                    {{-- AVATAR --}}
                    <div
                        class="relative w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm">
                        {{ $initials }}

                        {{-- GREEN DOT --}}
                        <span x-show="hasNotification"
                            class="absolute top-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full ring-2 ring-white">
                        </span>
                    </div>
                </button>

                {{-- DROPDOWN NOTIFICATION --}}
                <div x-show="openProfile" @click.away="openProfile = false" x-transition
                    class="absolute right-0 mt-4 w-80 bg-white rounded-2xl shadow-2xl border overflow-hidden text-sm z-50">

                    {{-- HEADER --}}
                    <div class="bg-gradient-to-r from-teal-500 to-emerald-500 px-5 py-4 text-white">
                        <p class="font-semibold text-base">🔔 Notifikasi</p>
                        <p class="text-xs text-teal-100">
                            {{ $unreadCount > 0 ? "Kamu punya $unreadCount pesan baru" : 'Tidak ada aktivitas baru' }}
                        </p>
                    </div>

                    {{-- LIST --}}
                    <div class="divide-y max-h-96 overflow-y-auto">
                        @forelse($notifications as $notification)
                            <div
                                class="group relative flex items-start gap-3 px-5 py-4 hover:bg-teal-50 transition {{ $notification->read_at ? 'opacity-60' : '' }}">

                                {{-- Link ke URL Notifikasi --}}
                                <a href="{{ route('notifications.readSingle', $notification->id) }}"
                                    class="flex flex-1 gap-3">
                                    <div
                                        class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center {{ $notification->data['color'] ?? 'bg-teal-100 text-teal-600' }}">
                                        {!! $notification->data['icon'] ?? '🔔' !!}
                                    </div>

                                    <div class="flex-1">
                                        <p
                                            class="font-medium text-gray-800 {{ !$notification->read_at ? 'font-bold' : '' }}">
                                            {{ $notification->data['title'] }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>

                                {{-- Action Buttons (Dot & Delete) --}}
                                <div class="flex flex-col items-center gap-2">
                                    @if (!$notification->read_at)
                                        <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                                    @endif

                                    {{-- Tombol Hapus 1 Notifikasi --}}
                                    <form action="{{ route('notifications.destroy', $notification->id) }}"
                                        method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="bi bi-x-circle-fill text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-gray-400 italic text-xs">
                                Belum ada notifikasi.
                            </div>
                        @endforelse
                    </div>

                    {{-- FOOTER --}}
                    <div class="bg-gray-50 px-5 py-3 flex justify-between items-center border-t">
                        {{-- Tombol Mark Read (Gunakan Tag <a> untuk Route GET) --}}
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ route('markNotificationsRead') }}"
                                class="text-[10px] font-bold text-teal-600 hover:text-teal-700 uppercase">
                                Tandai Dibaca
                            </a>
                        @endif

                        {{-- Tombol Hapus Semua (Gunakan Form untuk Route DELETE) --}}
                        @if (auth()->user()->notifications->count() > 0)
                            <form action="{{ route('notifications.clearAll') }}" method="POST"
                                onsubmit="return confirm('Hapus semua riwayat notifikasi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase flex items-center gap-1">
                                    <i class="bi bi-trash3-fill"></i> Hapus Semua
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE SLOT --}}
        {{ $slot }}

    </div>
</div>
