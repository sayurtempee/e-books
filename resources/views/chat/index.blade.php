<x-app>
    @section('title', 'Pesan Chat')

    @section('body-content')
        <x-sidebar>
            <div class="p-4 md:p-8 bg-[#f0f2f5] min-h-screen">
                <div class="flex h-[85vh] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">

                    {{-- SIDEBAR KONTAK (WhatsApp Style) --}}
                    <div class="w-1/3 border-r border-gray-200 flex flex-col bg-white">
                        <div class="p-5 bg-[#f0f2f5] flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Chat</h2>
                            <div class="flex gap-4 text-gray-600">
                                <i class="bi bi-chat-left-text-fill cursor-pointer"></i>
                                <i class="bi bi-three-dots-vertical cursor-pointer"></i>
                            </div>
                        </div>

                        {{-- Search Bar --}}
                        <div class="p-3 bg-white border-b">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" placeholder="Cari atau mulai chat baru"
                                    class="w-full bg-[#f0f2f5] border-none rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-1 focus:ring-teal-500">
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            @forelse($contacts as $contact)
                                @php
                                    // Hitung pesan belum dibaca khusus dari orang ini untuk saya
                                    $unreadCount = \App\Models\Message::where('sender_id', $contact->id)
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();

                                    $isActive = isset($activeContact) && $activeContact->id == $contact->id;
                                @endphp

                                <a href="{{ route('chat.index', $contact->id) }}"
                                    class="group relative flex items-center gap-4 px-4 py-3 border-b border-gray-50 transition-all
                                    {{ $isActive ? 'bg-teal-50/70 border-l-4 border-teal-600' : 'hover:bg-gray-50' }}">

                                    {{-- Avatar & Status Indicator --}}
                                    <div class="relative shrink-0">
                                        @php
                                            $words = explode(' ', $contact->name);
                                            $initials = strtoupper(
                                                substr($words[0], 0, 1) .
                                                    (isset($words[1]) ? substr($words[1], 0, 1) : ''),
                                            );
                                        @endphp
                                        <div
                                            class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center text-white font-bold text-sm tracking-tighter shadow-inner border border-white/20 group-hover:shadow-teal-200/50 transition-all duration-300">
                                            {{ $initials }}
                                        </div>

                                        {{-- Dot Hijau Unread --}}
                                        @if ($unreadCount > 0)
                                            <span
                                                class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-teal-500 border-2 border-white rounded-full shadow-sm animate-pulse"></span>
                                        @endif
                                    </div>

                                    {{-- Name & Message Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center">
                                            <p
                                                class="text-[15px] {{ $unreadCount > 0 ? 'font-bold text-gray-900' : 'font-semibold text-gray-700' }} truncate pr-2">
                                                {{ $contact->name }}
                                            </p>

                                            {{-- Badge Angka --}}
                                            @if ($unreadCount > 0)
                                                <span
                                                    class="bg-teal-600 text-white text-[10px] min-w-[18px] h-[18px] flex items-center justify-center rounded-full font-bold px-1 shadow-sm">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Role Tag --}}
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span
                                                class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-bold uppercase tracking-wider">
                                                {{ $contact->role }}
                                            </span>
                                            @if ($unreadCount > 0)
                                                <span class="text-[10px] text-teal-600 font-medium">Pesan baru</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    <i class="bi bi-chat-dots text-3xl opacity-20 block mb-2"></i>
                                    <p class="text-xs italic">Belum ada percakapan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- AREA PERCAKAPAN (Elegant Background) --}}
                    <div class="flex-1 flex flex-col relative bg-[#efe7de]"
                        style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-blend-mode: overlay;">

                        @if ($activeContact)
                            {{-- Header Chat --}}
                            <div class="px-5 py-3 bg-[#f0f2f5] flex items-center justify-between shadow-sm z-10 border-b">
                                <div class="flex items-center gap-3">
                                    @php
                                        $wordsActive = explode(' ', $activeContact->name);
                                        $initialsActive = strtoupper(
                                            substr($wordsActive[0], 0, 1) .
                                                (isset($wordsActive[1]) ? substr($wordsActive[1], 0, 1) : ''),
                                        );

                                        // LOGIKA ONLINE: Anggap online jika aktivitas terakhir kurang dari 2 menit yang lalu
                                        $isOnline =
                                            $activeContact->last_seen &&
                                            $activeContact->last_seen->diffInMinutes(now()) < 2;
                                    @endphp

                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-white font-bold text-xs shadow-md border-2 border-white">
                                        {{ $initialsActive }}
                                    </div>

                                    <div>
                                        <p class="text-[15px] font-bold text-gray-800 leading-tight">
                                            {{ $activeContact->name }}
                                        </p>

                                        <div class="flex items-center gap-1.5">
                                            @if ($isOnline)
                                                {{-- Status Online dengan animasi ping --}}
                                                <span class="relative flex h-2 w-2">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                </span>
                                                <p class="text-[11px] text-green-600 font-bold uppercase tracking-tighter">
                                                    Online</p>
                                            @else
                                                {{-- Status Offline dengan keterangan waktu --}}
                                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                                <p class="text-[11px] text-gray-500 font-medium italic">
                                                    Terakhir dilihat
                                                    {{ $activeContact->last_seen ? $activeContact->last_seen->diffForHumans() : 'tidak diketahui' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-5 text-gray-500">
                                    <i class="bi bi-search cursor-pointer hover:text-teal-600 transition-colors"></i>
                                    <i class="bi bi-paperclip cursor-pointer hover:text-teal-600 transition-colors"></i>
                                    <i
                                        class="bi bi-three-dots-vertical cursor-pointer hover:text-teal-600 transition-colors"></i>
                                </div>
                            </div>

                            {{-- Isi Chat --}}
                            <div class="flex-1 overflow-y-auto p-6 space-y-2 custom-scrollbar" id="chatWindow">
                                <div class="flex justify-center mb-4">
                                    <span
                                        class="bg-[#d9fdd3] text-[#54656f] text-[11px] px-3 py-1 rounded-lg shadow-sm font-medium">HARI
                                        INI</span>
                                </div>

                                @foreach ($messages as $msg)
                                    <div
                                        class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} mb-1">
                                        <div
                                            class="relative max-w-[65%] px-3 py-2 rounded-xl shadow-sm
                                            {{ $msg->sender_id == auth()->id()
                                                ? 'bg-[#d9fdd3] text-[#111b21] rounded-tr-none'
                                                : 'bg-white text-[#111b21] rounded-tl-none' }}">

                                            <p class="text-[14px] leading-tight pr-10">{{ $msg->message }}</p>

                                            <div class="absolute bottom-1 right-2 flex items-center gap-1 opacity-60">
                                                <span class="text-[9px]">{{ $msg->created_at->format('H:i') }}</span>
                                                @if ($msg->sender_id == auth()->id())
                                                    <i class="bi bi-check2-all text-[12px] text-blue-500"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Input Box (WhatsApp Modern) --}}
                            <div class="px-4 py-3 bg-[#f0f2f5] flex items-center gap-3 border-t">
                                <button class="text-gray-500 text-xl"><i class="bi bi-emoji-smile"></i></button>
                                <button class="text-gray-500 text-xl"><i class="bi bi-plus-lg"></i></button>

                                <form action="{{ route('messages.send') }}" method="POST"
                                    class="flex-1 flex items-center gap-3">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">
                                    <input type="text" name="message" autocomplete="off" placeholder="Ketik pesan"
                                        class="flex-1 bg-white border-none rounded-xl px-4 py-2.5 text-sm focus:ring-0 shadow-sm transition-all"
                                        required>

                                    <button type="submit"
                                        class="w-10 h-10 bg-teal-600 text-white rounded-full flex items-center justify-center hover:bg-teal-700 shadow transition-transform active:scale-90">
                                        <i class="bi bi-send-fill text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Welcome State (WhatsApp Desktop Style) --}}
                            <div
                                class="flex-1 flex flex-col items-center justify-center text-center bg-[#f0f2f5] border-b-8 border-teal-500">
                                <div class="w-64 h-64 mb-8 opacity-40">
                                    <img src="https://abs.twimg.com/errors/logo-error-800x400.png"
                                        class="grayscale brightness-150" alt="">
                                </div>
                                <h3 class="text-2xl font-light text-gray-600">Miimoys Web</h3>
                                <p class="text-sm text-gray-500 max-w-sm mt-2">Kirim dan terima pesan untuk mendiskusikan
                                    buku favoritmu tanpa harus keluar dari aplikasi.</p>
                                <div class="mt-8 flex items-center gap-2 text-gray-400 text-xs tracking-widest uppercase">
                                    <i class="bi bi-lock-fill"></i> Terenkripsi secara End-to-End
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <style>
                /* Styling scrollbar agar mirip WA */
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, 0.1);
                    border-radius: 10px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: rgba(0, 0, 0, 0.2);
                }

                /* Animasi Fade In */
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .animate-fade-in {
                    animation: fadeIn 0.3s ease-out;
                }
            </style>

            <script>
                // Auto scroll ke bawah
                const chatWindow = document.getElementById('chatWindow');
                const scrollToBottom = () => {
                    if (chatWindow) {
                        chatWindow.scrollTop = chatWindow.scrollHeight;
                    }
                };

                scrollToBottom();

                // Listen Pusher/Echo (Penting: Struktur HTML di sini juga disamakan dengan gaya WA)
                Echo.channel('chat.{{ auth()->id() }}')
                    .listen('.message.sent', (e) => {
                        chatWindow.insertAdjacentHTML('beforeend', `
                            <div class="flex justify-start mb-1 animate-fade-in">
                                <div class="relative max-w-[65%] px-3 py-2 rounded-xl shadow-sm bg-white text-[#111b21] rounded-tl-none">
                                    <p class="text-[14px] leading-tight pr-10">${e.message.message}</p>
                                    <div class="absolute bottom-1 right-2 flex items-center gap-1 opacity-60">
                                        <span class="text-[9px] font-medium">${new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</span>
                                    </div>
                                </div>
                            </div>
                        `);
                        scrollToBottom();
                    });
            </script>
        </x-sidebar>
    @endsection
</x-app>
