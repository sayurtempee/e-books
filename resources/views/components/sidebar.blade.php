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

            {{-- Navbar Menu --}}
            @auth
                @include('layouts.sidebar.' . auth()->user()->role)
            @endauth
        </div>

        {{-- FOOTER SIDEBAR --}}
        <div class="flex flex-col gap-1 p-4 border-t border-teal-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout()"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                               text-red-500 hover:bg-red-100 transition">
                    <i class="bi bi-box-arrow-left text-lg"></i>
                    <span>Sign Out</span>
                </button>
            </form>

            <a href="{{ route('help.index') }}"
                class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                {{ request()->routeIs('help.index') ? 'bg-teal-100 text-teal-700' : 'text-teal-600 hover:bg-teal-100' }}
                font-semibold transition">
                <i class="bi bi-question-circle-fill text-lg"></i>
                <span>Help</span>
            </a>
        </div>
    </aside>

    {{-- CONTENT --}}
    <div class="flex-1 p-6 md:p-8">
        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">
            {{-- LEFT --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden flex flex-col justify-center gap-1.5 w-8 h-8 p-1 rounded hover:bg-gray-200">
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                    <span class="block h-0.5 w-full bg-gray-800"></span>
                </button>
                <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
            </div>

            @php
                $name = auth()->user()->name ?? 'User';
                $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                $initials = substr($initials, 0, 2);
                $unreadCount = $notifications->whereNull('read_at')->count();
            @endphp

            {{-- USER + NOTIFICATION --}}
            <div class="relative" x-data="{
                openProfile: false,
                unreadCount: {{ $unreadCount }},
                init() {
                    // MENGGUNAKAN WEBSOCKET (REVERB) UNTUK NOTIFIKASI
                    window.Echo.private('App.Models.User.' + {{ auth()->id() }})
                        .notification((notification) => {
                            this.unreadCount++;
                            // Opsional: Mainkan suara notifikasi di sini
                            // alert('Notifikasi baru: ' + notification.title);
                        });
                }
            }">
                {{-- PROFILE BUTTON --}}
                <button @click="openProfile = !openProfile" class="flex items-center gap-3 focus:outline-none">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-gray-800">{{ $name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role ?? 'User' }}</p>
                    </div>

                    <div
                        class="relative w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-white">
                        {{ $initials }}
                        {{-- RED DOT NOTIF (Hanya muncul jika unreadCount > 0) --}}
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] items-center justify-center border border-white"
                                    x-text="unreadCount"></span>
                            </span>
                        </template>
                    </div>
                </button>

                {{-- DROPDOWN NOTIFICATION --}}
                <div x-show="openProfile" @click.away="openProfile = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute right-0 mt-4 w-80 bg-white rounded-2xl shadow-2xl border overflow-hidden text-sm z-50">

                    {{-- HEADER --}}
                    <div class="bg-gradient-to-r from-teal-500 to-emerald-500 px-5 py-4 text-white">
                        <div class="flex justify-between items-center">
                            <p class="font-semibold text-base">🔔 Notifikasi</p>
                            <span x-show="unreadCount > 0"
                                class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Baru</span>
                        </div>
                        <p class="text-xs text-teal-100"
                            x-text="unreadCount > 0 ? 'Kamu punya ' + unreadCount + ' pesan belum dibaca' : 'Tidak ada aktivitas baru'">
                        </p>
                    </div>

                    {{-- LIST NOTIFIKASI --}}
                    <div class="divide-y max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($notifications as $notification)
                            <div
                                class="group relative flex items-start gap-3 px-5 py-4 hover:bg-teal-50 transition {{ $notification->read_at ? 'opacity-60' : '' }}">

                                {{-- Link Klik Notifikasi --}}
                                <a href="{{ route('notifications.readSingle', $notification->id) }}"
                                    class="flex flex-1 gap-3">
                                    <div
                                        class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100 text-gray-400' : 'bg-teal-100 text-teal-600' }}">
                                        {!! $notification->data['icon'] ?? '🔔' !!}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="font-medium text-gray-800 {{ !$notification->read_at ? 'font-bold text-teal-900' : '' }}">
                                            {{ $notification->data['title'] }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 line-clamp-2">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                            <i class="bi bi-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>

                                {{-- Tombol Hapus Satuan --}}
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                    class="opacity-0 group-hover:opacity-100 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition p-1">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <div class="text-4xl mb-2">empty</div>
                                <p class="text-gray-400 text-xs">Belum ada notifikasi untukmu.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- FOOTER / ACTION BUTTONS --}}
                    @if ($notifications->count() > 0)
                        <div class="grid grid-cols-2 border-t text-center divide-x">
                            {{-- Tombol Tandai Semua Dibaca --}}
                            <a href="{{ route('notifications.markAllRead') }}"
                                class="py-3 text-[11px] font-semibold text-teal-600 hover:bg-teal-50 transition uppercase tracking-wider">
                                <i class="bi bi-check2-all mr-1"></i> Tandai Dibaca
                            </a>

                            {{-- Tombol Hapus Semua (Gunakan Form karena Method DELETE) --}}
                            <form action="{{ route('notifications.clearAll') }}" method="POST"
                                onsubmit="return confirm('Hapus semua riwayat notifikasi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-3 text-[11px] font-semibold text-red-500 hover:bg-red-50 transition uppercase tracking-wider">
                                    <i class="bi bi-trash3 mr-1"></i> Bersihkan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <main>{{ $slot }}</main>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #14b8a6;
        border-radius: 10px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Echo !== 'undefined') {
            // Listen ke channel private user
            window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                .notification((notification) => {
                    console.log('Notifikasi baru diterima:', notification);

                    // 1. Update Titik Merah via Alpine.js
                    // Mencari elemen yang memiliki x-data dengan hasNotification
                    const profileContainer = document.querySelector('[x-data*="hasNotification"]');
                    if (profileContainer) {
                        const alpineData = Alpine.$data(profileContainer);
                        alpineData.hasNotification = true;
                        // Opsional: Jika ingin mengupdate teks counter "Kamu punya X pesan"
                        // Anda bisa memanggil alpineData.refreshCount();
                    }

                    // 2. Masukkan Item Baru ke Dropdown List secara Real-time
                    const listContainer = document.querySelector('.divide-y.max-h-96');
                    if (listContainer) {
                        // Hapus pesan "Belum ada notifikasi" jika ada
                        const emptyState = listContainer.querySelector('.italic');
                        if (emptyState) emptyState.remove();

                        const newNotifHtml = `
                            <div class="group relative flex items-start gap-3 px-5 py-4 hover:bg-teal-50 transition">
                                <a href="${notification.url}" class="flex flex-1 gap-3">
                                    <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center ${notification.color}">
                                        ${notification.icon}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-black text-teal-900">${notification.title}</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-2">${notification.message}</p>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase">Baru saja</p>
                                    </div>
                                </a>
                                <div class="flex flex-col items-center gap-2">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                                </div>
                            </div>
                        `;
                        // Sisipkan di posisi paling atas dropdown
                        listContainer.insertAdjacentHTML('afterbegin', newNotifHtml);
                    }
                });
        }
    });
</script>
