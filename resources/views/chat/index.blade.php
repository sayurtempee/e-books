<x-app>
    @section('title', 'Pesan Chat')

    @section('body-content')
        <x-sidebar>
            <div class="min-h-screen bg-[#f8fafc] p-4 md:p-6 font-sans">

                {{-- WRAPPER --}}
                <div
                    class="flex h-[88vh]
                           rounded-2xl shadow-2xl overflow-hidden
                           border border-gray-200 animate-slide-up
                           bg-transparent">

                    {{-- ================= SIDEBAR ================= --}}
                    <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">

                        {{-- Search --}}
                        <div class="p-4 flex items-center gap-3">
                            <button class="p-2 rounded-full hover:bg-slate-100 transition">
                                <i class="bi bi-list text-xl text-gray-500"></i>
                            </button>

                            <div class="relative flex-1">
                                <i
                                    class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2
                                           text-gray-400 text-sm"></i>
                                <input type="text" placeholder="Search chat"
                                    class="w-full bg-slate-100 rounded-full py-2.5 pl-9 pr-4
                                           text-sm focus:ring-2 focus:ring-teal-500/30 border-none">
                            </div>
                        </div>

                        {{-- Contacts --}}
                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            @forelse($contacts as $contact)
                                @php
                                    $unreadCount = \App\Models\Message::where('sender_id', $contact->id)
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                    $isActive = isset($activeContact) && $activeContact->id == $contact->id;
                                @endphp

                                <a href="{{ route('chat.index', $contact->id) }}"
                                    class="flex items-center gap-3 px-4 py-3.5 transition
                                    {{ $isActive ? 'bg-gradient-to-r from-teal-600 to-teal-500 text-white' : 'hover:bg-slate-50' }}">

                                    {{-- Avatar --}}
                                    <div class="relative">
                                        <div
                                            class="w-11 h-11 rounded-full flex items-center justify-center
                                                   font-bold text-lg text-white
                                                   bg-gradient-to-br from-teal-600 to-emerald-500">
                                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                                        </div>

                                        @if ($contact->isOnline)
                                            <span
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500
                                                       ring-2 {{ $isActive ? 'ring-teal-600' : 'ring-white' }}
                                                       rounded-full"></span>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between">
                                            <h4 class="font-semibold text-sm truncate">
                                                {{ $contact->name }}
                                            </h4>
                                            <span
                                                class="text-[11px]
                                                {{ $isActive ? 'text-white/70' : 'text-gray-400' }}">
                                                {{ $contact->last_message_time ?? '—' }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center mt-0.5">
                                            <p
                                                class="text-xs truncate
                                                {{ $isActive ? 'text-white/80' : 'text-gray-500' }}">
                                                {{ $contact->role }}
                                            </p>

                                            @if ($unreadCount > 0)
                                                <span
                                                    class="px-2 py-0.5 text-[11px] font-bold rounded-full
                                                    {{ $isActive ? 'bg-white text-teal-600' : 'bg-teal-600 text-white' }}">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="text-center text-sm text-gray-400 mt-20">
                                    No conversations
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- ================= CHAT AREA ================= --}}
                    <div class="flex-1 flex flex-col relative chat-wallpaper">

                        @if (isset($activeContact))
                            {{-- Header --}}
                            <div
                                class="px-5 py-3 bg-white/80 backdrop-blur
                                       border-b border-gray-200
                                       flex items-center justify-between relative z-10">

                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br
                                               from-teal-600 to-emerald-500
                                               flex items-center justify-center
                                               text-white font-bold">
                                        {{ strtoupper(substr($activeContact->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-bold text-gray-800">
                                            {{ $activeContact->name }}
                                        </h3>
                                        <p
                                            class="text-xs
                                            {{ $activeContact->isOnline ? 'text-teal-600' : 'text-gray-400' }}">
                                            {{ $activeContact->isOnline ? 'online' : 'last seen recently' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4 text-gray-400">
                                    <i class="bi bi-search hover:text-teal-600 cursor-pointer"></i>
                                    <i class="bi bi-telephone hover:text-teal-600 cursor-pointer"></i>
                                    <i class="bi bi-three-dots-vertical hover:text-teal-600 cursor-pointer"></i>
                                </div>
                            </div>

                            {{-- Messages --}}
                            <div id="chatWindow"
                                class="flex-1 overflow-y-auto p-6 space-y-3 custom-scrollbar relative z-10">

                                @foreach ($messages as $msg)
                                    <div
                                        class="flex
                                        {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}
                                        animate-pop">

                                        <div class="max-w-[70%]">
                                            <div
                                                class="px-4 py-2 rounded-2xl text-sm leading-relaxed
                                                {{ $msg->sender_id == auth()->id()
                                                    ? 'bg-emerald-100/90 rounded-tr-md'
                                                    : 'bg-white/90 border border-gray-200 rounded-tl-md' }}">

                                                {{ $msg->message }}

                                                <div class="flex justify-end items-center gap-1 mt-1">
                                                    <span class="text-[10px] text-gray-400">
                                                        {{ $msg->created_at->format('H:i') }}
                                                    </span>

                                                    @if ($msg->sender_id == auth()->id())
                                                        <i
                                                            class="bi bi-check2-all text-xs
                                                            {{ $msg->is_read ? 'text-sky-500' : 'text-gray-300' }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Input --}}
                            <div class="p-4 relative z-10">
                                <form action="{{ route('messages.send') }}" method="POST"
                                    class="max-w-4xl mx-auto flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">

                                    <div
                                        class="flex-1 flex items-center bg-white/90 backdrop-blur
                                               rounded-full px-4 py-1
                                               shadow border border-gray-200">

                                        <i class="bi bi-emoji-smile text-gray-400 text-xl"></i>

                                        <input type="text" name="message" required placeholder="Type a message…" autofocus
                                            class="flex-1 px-3 py-2 bg-transparent border-none focus:ring-0 text-sm">

                                        <i class="bi bi-paperclip text-gray-400 text-xl"></i>
                                    </div>

                                    <button type="submit"
                                        class="w-11 h-11 rounded-full bg-teal-600 text-white
                                               flex items-center justify-center
                                               hover:bg-teal-700 transition active:scale-90">
                                        <i class="bi bi-send-fill text-sm ml-0.5"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex-1 flex items-center justify-center">
                                <span class="text-sm text-gray-400">
                                    Select a chat to start messaging
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= STYLE ================= --}}
            <style>
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes pop {
                    from {
                        opacity: 0;
                        transform: scale(0.96);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }

                .animate-slide-up {
                    animation: slideUp .5s cubic-bezier(.22, 1, .36, 1);
                }

                .animate-pop {
                    animation: pop .25s cubic-bezier(.22, 1, .36, 1);
                }

                .custom-scrollbar::-webkit-scrollbar {
                    width: 5px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, .2);
                    border-radius: 10px;
                }

                /* CHAT WALLPAPER */
                .chat-wallpaper {
                    position: relative;
                    background-image: url('{{ asset('image/wallpaper-chat.png') }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                }

                .chat-wallpaper::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: rgba(255, 255, 255, .20);
                    backdrop-filter: blur(1px);
                    z-index: 0;
                }

                .chat-wallpaper>* {
                    position: relative;
                    z-index: 1;
                }
            </style>

            <script>
                const chatWindow = document.getElementById('chatWindow');
                if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;
            </script>
        </x-sidebar>
    @endsection
</x-app>
