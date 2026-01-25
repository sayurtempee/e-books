<x-app>
    @section('title', 'Pesan Chat')

    @section('body-content')
        <x-sidebar>
            <div class="p-4 md:p-6 bg-[#e7ebf0] min-h-screen font-sans">
                <div
                    class="flex h-[88vh] bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 animate-slide-up">

                    {{-- SIDEBAR KONTAK (Telegram Style) --}}
                    <div class="w-1/3 border-r border-gray-100 flex flex-col bg-white">
                        {{-- Header Sidebar --}}
                        <div class="p-4 flex items-center gap-3 bg-white">
                            <button class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="bi bi-list text-xl text-gray-500"></i>
                            </button>
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="bi bi-search text-sm"></i>
                                </span>
                                <input type="text" placeholder="Search"
                                    class="w-full bg-[#f1f1f1] border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-[#008B8B]/30 transition-all">
                            </div>
                        </div>

                        {{-- List Kontak --}}
                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            @forelse($contacts as $contact)
                                @php
                                    $unreadCount = \App\Models\Message::where('sender_id', $contact->id)
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                    $isActive = isset($activeContact) && $activeContact->id == $contact->id;
                                    $initials = strtoupper(substr($contact->name, 0, 1));
                                @endphp

                                <a href="{{ route('chat.index', $contact->id) }}"
                                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 relative
                                    {{ $isActive ? 'bg-[#008B8B] text-white' : 'hover:bg-gray-50' }}">

                                    <div class="relative shrink-0">
                                        <div
                                            class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm
                                            {{ $isActive ? 'bg-white/20' : 'bg-gradient-to-tr from-[#008B8B] to-[#00a3a3]' }}">
                                            {{ $initials }}
                                        </div>
                                        @if ($contact->isOnline)
                                            <div
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 {{ $isActive ? 'border-[#008B8B]' : 'border-white' }} rounded-full">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="text-[15px] font-bold truncate">{{ $contact->name }}</h4>
                                            <span
                                                class="text-[11px] {{ $isActive ? 'text-white/70' : 'text-gray-400' }}">12:45</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-0.5">
                                            <p
                                                class="text-[13px] truncate {{ $isActive ? 'text-white/80' : 'text-gray-500' }}">
                                                {{ $contact->role }}
                                            </p>
                                            @if ($unreadCount > 0)
                                                <span
                                                    class="bg-[#008B8B] text-white text-[11px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white text-[#008B8B]' : '' }}">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="mt-20 text-center text-gray-400">
                                    <p class="text-sm">No chats yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- AREA PERCAKAPAN (Teal Theme) --}}
                    <div class="flex-1 flex flex-col bg-[#e7ebf0] relative">
                        @if (isset($activeContact))
                            {{-- Header --}}
                            <div class="px-5 py-2 bg-white flex items-center justify-between shadow-sm z-10">
                                <div
                                    class="flex items-center gap-3 cursor-pointer p-1 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div
                                        class="w-10 h-10 rounded-full bg-[#008B8B] flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($activeContact->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-[15px] font-bold text-gray-800 leading-tight">
                                            {{ $activeContact->name }}</h3>
                                        <p
                                            class="text-[12px] {{ $activeContact->isOnline ? 'text-[#008B8B] font-medium' : 'text-gray-400' }}">
                                            {{ $activeContact->isOnline ? 'online' : 'last seen recently' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-4 text-gray-400">
                                    <button class="hover:text-[#008B8B] transition-colors"><i
                                            class="bi bi-search text-lg"></i></button>
                                    <button class="hover:text-[#008B8B] transition-colors"><i
                                            class="bi bi-telephone text-lg"></i></button>
                                    <button class="hover:text-[#008B8B] transition-colors"><i
                                            class="bi bi-three-dots-vertical text-lg"></i></button>
                                </div>
                            </div>

                            {{-- Chat Body --}}
                            <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-3 custom-scrollbar" id="chatWindow">
                                @foreach ($messages as $msg)
                                    <div
                                        class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} animate-pop">
                                        <div class="relative max-w-[70%] group">
                                            <div
                                                class="px-4 py-2 rounded-2xl shadow-sm text-[14.5px] leading-relaxed
                                                {{ $msg->sender_id == auth()->id()
                                                    ? 'bg-[#effdde] text-gray-800 rounded-tr-none'
                                                    : 'bg-white text-gray-800 rounded-tl-none' }}">

                                                {{ $msg->message }}

                                                <div class="flex items-center justify-end gap-1 mt-1 -mr-1">
                                                    <span class="text-[10px] text-gray-400">
                                                        {{ $msg->created_at->format('H:i') }}
                                                    </span>
                                                    @if ($msg->sender_id == auth()->id())
                                                        <i
                                                            class="bi bi-check2-all text-[14px] {{ $msg->is_read ? 'text-[#4fc3f7]' : 'text-gray-300' }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Input Box --}}
                            <div class="p-4 bg-white md:bg-transparent">
                                <form action="{{ route('messages.send') }}" method="POST"
                                    class="max-w-4xl mx-auto flex items-end gap-2">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">

                                    <div
                                        class="flex-1 bg-white rounded-2xl shadow-md flex items-center px-4 py-1 border border-gray-100">
                                        <button type="button"
                                            class="text-gray-400 hover:text-[#008B8B] transition-colors p-2">
                                            <i class="bi bi-emoji-smile text-xl"></i>
                                        </button>
                                        <input type="text" name="message" autocomplete="off"
                                            placeholder="Write a message..."
                                            class="flex-1 border-none focus:ring-0 text-sm py-3 px-2 bg-transparent"
                                            required>
                                        <button type="button"
                                            class="text-gray-400 hover:text-[#008B8B] transition-colors p-2">
                                            <i class="bi bi-paperclip text-xl"></i>
                                        </button>
                                    </div>

                                    <button type="submit"
                                        class="w-12 h-12 bg-[#008B8B] text-white rounded-full flex items-center justify-center hover:bg-[#007373] shadow-lg transition-all active:scale-90 shrink-0">
                                        <i class="bi bi-send-fill text-lg ml-0.5"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Welcome State --}}
                            <div class="flex-1 flex flex-col items-center justify-center">
                                <div class="bg-black/5 px-4 py-1 rounded-full text-gray-500 text-sm">
                                    Select a chat to start messaging
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <style>
                /* ANIMATIONS */
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
                        transform: scale(0.95);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }

                .animate-slide-up {
                    animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
                }

                .animate-pop {
                    animation: pop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                }

                /* CUSTOM SCROLLBAR */
                .custom-scrollbar::-webkit-scrollbar {
                    width: 5px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, 0.1);
                    border-radius: 10px;
                }

                .custom-scrollbar:hover::-webkit-scrollbar-thumb {
                    background: rgba(0, 0, 0, 0.2);
                }

                /* CHAT BUBBLE TAIL (Optional subtle styling) */
                .rounded-tr-none {
                    border-top-right-radius: 4px !important;
                }

                .rounded-tl-none {
                    border-top-left-radius: 4px !important;
                }
            </style>

            <script>
                const chatWindow = document.getElementById('chatWindow');
                if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;

                const scrollToBottom = () => {
                    chatWindow.scrollTo({
                        top: chatWindow.scrollHeight,
                        behavior: 'smooth'
                    });
                }

                // Echo Listener (Pastikan Laravel Echo Terpasang)
                @if (isset($activeContact))
                    window.Echo.channel('chat.{{ auth()->id() }}')
                        .listen('.message.sent', (e) => {
                            if (e.message.sender_id == {{ $activeContact->id }}) {
                                const msgHtml = `
                                    <div class="flex justify-start animate-pop">
                                        <div class="relative max-w-[70%]">
                                            <div class="px-4 py-2 rounded-2xl shadow-sm text-[14.5px] bg-white text-gray-800 rounded-tl-none">
                                                ${e.message.message}
                                                <div class="flex items-center justify-end gap-1 mt-1 -mr-1">
                                                    <span class="text-[10px] text-gray-400 uppercase">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                chatWindow.insertAdjacentHTML('beforeend', msgHtml);
                                scrollToBottom();
                            }
                        });
                @endif
            </script>
        </x-sidebar>
    @endsection
</x-app>
