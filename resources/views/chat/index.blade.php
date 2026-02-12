<x-app>
    @section('title', 'Halaman Chat')
    @section('body-content')
        <x-sidebar>
            {{-- Container Utama Chat --}}
            <div
                class="flex h-[calc(100vh-2rem)] bg-white rounded-2xl shadow-2xl overflow-hidden m-4 border border-gray-100">

                {{-- SIDEBAR KIRI: Daftar Percakapan --}}
                <div class="w-1/3 border-r border-gray-100 flex flex-col bg-white z-20">
                    <div class="p-5 border-b bg-gray-50/50">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-teal-600">Messages</h2>
                            <button class="text-teal-600 hover:bg-teal-50 p-2 rounded-full transition">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="text" placeholder="Cari pesan..."
                                class="w-full bg-white border border-gray-200 rounded-xl py-2.5 px-10 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all shadow-sm">
                            <span class="absolute left-3.5 top-3 text-gray-400">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        @foreach ($conversations as $chat)
                            @php
                                $user = $chat->interlocutor;
                                $isActive = isset($activeChat) && $activeChat->id == $chat->id;
                            @endphp
                            <a href="{{ route('chat.index', $user->id) }}"
                                class="flex items-center p-4 cursor-pointer transition-all border-l-4 {{ $isActive ? 'bg-teal-50 border-teal-500' : 'hover:bg-gray-50 border-transparent' }}">
                                <div class="relative mr-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-tr from-teal-500 to-emerald-400 rounded-full flex items-center justify-center text-white font-bold uppercase shadow-md">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    @if ($user->isOnline)
                                        <span
                                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                    @endif
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div class="flex justify-between items-center mb-1">
                                        <h4 class="font-bold text-gray-800 truncate">{{ $user->name }}</h4>
                                        <span
                                            class="text-[10px] font-medium text-gray-400">{{ $chat->updated_at->format('H:i') }}</span>
                                    </div>
                                    <p
                                        class="text-xs {{ $isActive ? 'text-teal-600 font-medium' : 'text-gray-500' }} truncate">
                                        {{ $chat->messages->last()->body ?? 'Belum ada pesan' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- KOLOM KANAN: Area Chat --}}
                <div class="flex-1 flex flex-col relative overflow-hidden bg-[#f0f2f5]">
                    @if ($activeChat)
                        @php
                            $activeUser =
                                $activeChat->sender_id == auth()->id() ? $activeChat->receiver : $activeChat->sender;
                        @endphp

                        {{-- Header Chat --}}
                        <div
                            class="p-4 bg-white/90 backdrop-blur-md shadow-sm flex items-center justify-between z-10 border-b border-gray-100">
                            <div class="flex items-center">
                                <div
                                    class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-3 uppercase shadow-md">
                                    {{ substr($activeUser->name, 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 leading-tight">{{ $activeUser->name }}</h4>
                                    <p
                                        class="text-[11px] {{ $activeUser->isOnline ? 'text-teal-500 font-bold' : 'text-gray-400' }}">
                                        {{ $activeUser->isOnline ? '● Online' : 'Offline' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-3 text-gray-400">
                                <button class="hover:text-teal-600 p-2 rounded-full transition"><i
                                        class="bi bi-telephone-fill"></i></button>
                                <button class="hover:text-teal-600 p-2 rounded-full transition"><i
                                        class="bi bi-three-dots-vertical"></i></button>
                            </div>
                        </div>

                        {{-- Area Pesan (Dibuat bg-transparent agar wallpaper terlihat) --}}
                        <div id="chat-messages"
                            class="flex-1 overflow-y-auto p-6 space-y-4 z-10 custom-scrollbar relative bg-transparent">
                            @foreach ($activeChat->messages as $message)
                                <div
                                    class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }} animate-fade-in">
                                    <div
                                        class="max-w-[75%] px-4 py-2 shadow-md rounded-2xl relative
                                        {{ $message->user_id == auth()->id()
                                            ? 'bg-teal-600 text-white rounded-br-none'
                                            : 'bg-white text-gray-800 rounded-bl-none border border-gray-100' }}">

                                        <p class="text-[13.5px] leading-relaxed">{{ $message->body }}</p>

                                        <div class="flex items-center justify-end gap-1 mt-1">
                                            <span
                                                class="text-[9px] {{ $message->user_id == auth()->id() ? 'text-teal-100' : 'text-gray-400' }}">
                                                {{ $message->created_at->format('H:i') }}
                                            </span>
                                            @if ($message->user_id == auth()->id())
                                                <i class="bi bi-check2-all text-[12px] text-teal-200"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Input Pesan --}}
                        <div class="p-3 z-20">
                            <form id="chat-form" data-url="{{ route('chat.send', $activeChat->id ?? 0) }}"
                                class="bg-transparent flex items-center gap-2 max-w-4xl mx-auto">
                                @csrf

                                <div
                                    class="flex flex-1 items-center bg-white/90 backdrop-blur-sm border border-gray-200 rounded-full px-3 py-1">
                                    <button type="button" class="p-1.5 text-gray-500 hover:text-teal-600 transition">
                                        <i class="bi bi-emoji-smile text-xl"></i>
                                    </button>

                                    <input type="text" id="message-input" name="body" placeholder="Tulis pesan..."
                                        required
                                        class="flex-1 bg-transparent border-none focus:ring-0 text-base py-1.5 px-2 text-gray-700">

                                    <button type="button" class="p-1.5 text-gray-500 hover:text-teal-600 transition">
                                        <i class="bi bi-paperclip text-xl"></i>
                                    </button>
                                </div>

                                <button type="submit"
                                    class="bg-teal-600 hover:bg-teal-700 text-white w-11 h-11 rounded-full flex items-center justify-center shadow-md transition-all active:scale-90 shrink-0">
                                    <i class="bi bi-send-fill text-lg"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- Welcome Screen --}}
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-400 z-10 relative">
                            <div
                                class="w-24 h-24 bg-white rounded-full shadow-xl flex items-center justify-center mb-6 animate-bounce">
                                <i class="bi bi-chat-dots-fill text-4xl text-teal-500"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-700">Miimoys Chat</h3>
                            <p class="text-sm">Pilih teman untuk mulai mengobrol</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-sidebar>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #14b8a6;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }

            #chat-messages {
                background-image: url('{{ asset('image/wallpaper-chat.png') }}');
                background-color: #EBF4F6;
                background-blend-mode: overlay;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
            }
        </style>

        <script>
            // Logika JavaScript tetap sama seperti sebelumnya, pastikan ID elemen sesuai.
            document.addEventListener('DOMContentLoaded', function() {
                const chatForm = document.getElementById('chat-form');
                const messageInput = document.getElementById('message-input');
                const chatMessages = document.getElementById('chat-messages');

                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                if (chatForm) {
                    chatForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const body = messageInput.value.trim();
                        if (!body) return;

                        const url = this.getAttribute('data-url');
                        const token = document.querySelector('input[name="_token"]').value;
                        messageInput.value = '';

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    body: body
                                })
                            });
                            const data = await response.json();
                            if (data.status === 'success') {
                                appendMessage(data.message.body, data.time);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        } catch (error) {
                            console.error(error);
                        }
                    });
                }

                function appendMessage(text, time) {
                    const html = `<div class="flex items-end justify-end animate-fade-in">
                        <div class="bg-teal-600 text-white p-3 rounded-2xl rounded-br-none shadow-md max-w-md">
                            <p class="text-sm">${text}</p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-[9px] text-teal-100">${time}</span>
                                <i class="bi bi-check2-all text-[12px] text-teal-200"></i>
                            </div>
                        </div>
                    </div>`;
                    chatMessages.insertAdjacentHTML('beforeend', html);
                }

                // Echo Logic
                const activeChatId = "{{ $activeChat->id ?? 0 }}";
                if (activeChatId > 0 && window.Echo) {
                    window.Echo.private(`chat.${activeChatId}`)
                        .listen('.MessageSent', (e) => {
                            if (e.message.user_id != "{{ auth()->id() }}") {
                                const incomingHtml = `<div class="flex items-end animate-fade-in">
                                    <div class="bg-white text-gray-800 p-3 rounded-2xl rounded-bl-none shadow-md max-w-md border border-gray-100">
                                        <p class="text-sm">${e.message.body}</p>
                                        <span class="text-[10px] text-gray-400 mt-1 block text-left">Baru saja</span>
                                    </div>
                                </div>`;
                                chatMessages.insertAdjacentHTML('beforeend', incomingHtml);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        });
                }

                const userId = "{{ Auth::id() }}";
                if (window.Echo) {
                    window.Echo.leave(`App.Models.User.${userId}`);

                    window.Echo.private(`App.Models.User.${userId}`)
                        .notification((notification) => {
                            if (typeof activeChatId !== 'undefined') {
                                console.log("User sedang di room chat, skip notifikasi lonceng.");
                                return;
                            }

                            console.log("Notifikasi Baru Diterima:", notification);

                            const badge = document.getElementById('notification-count');
                            if (badge) {
                                let currentCount = parseInt(badge.innerText) || 0;
                                badge.innerText = currentCount + 1;
                                badge.classList.remove('hidden');
                            }

                            const notificationList = document.querySelector('.divide-y');
                            if (notificationList) {
                                // Hapus state kosong
                                const emptyState = notificationList.querySelector('.py-12');
                                if (emptyState) emptyState.remove();

                                const readUrl = '/notifications/read/${notification.id}';
                                const deleteUrl = '/notifications/delete/${notification.id}';

                                const newNotificationHtml = `
    <div class="group relative flex items-start gap-3 px-5 py-4 hover:bg-teal-50 transition">
        <a href="${readUrl}" class="flex flex-1 gap-3">
            <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center bg-teal-100 text-teal-600">
                ${notification.icon || '🔔'}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 font-bold text-teal-900">
                    ${notification.title || 'Pesan Baru'}
                </p>
                <p class="text-[11px] text-gray-500 line-clamp-2">
                    ${notification.message || ''}
                </p>
                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                    <i class="bi bi-clock"></i> Baru saja
                </p>
            </div>
        </a>
        <form action="${deleteUrl}" method="POST" class="opacity-0 group-hover:opacity-100 transition">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="text-gray-400 hover:text-red-500 p-1">
                <i class="bi bi-trash3 text-sm"></i>
            </button>
        </form>
    </div>`;

                                notificationList.insertAdjacentHTML('afterbegin', newNotificationHtml);
                            }
                        });
                }
            });
        </script>
    @endsection
</x-app>
