<x-app>
    @section('title', 'Halaman Chat')
    @section('body-content')
        <x-sidebar>
            <div class="flex h-[calc(100vh-2rem)] bg-white rounded-xl shadow-lg overflow-hidden m-4 border border-gray-100">

                <div class="w-1/3 border-r border-gray-100 flex flex-col bg-white">
                    <div class="p-4 border-b">
                        <h2 class="text-xl font-bold text-teal-600 mb-4">Messages</h2>
                        <div class="relative">
                            <input type="text" placeholder="Cari pesan..."
                                class="w-full bg-gray-100 border-none rounded-full py-2 px-10 text-sm focus:ring-2 focus:ring-teal-500">
                            <span class="absolute left-4 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        @foreach ($conversations as $chat)
                            @php
                                // Menentukan siapa lawan bicara
                                $user = $chat->interlocutor;
                                $isActive = isset($activeChat) && $activeChat->id == $chat->id;
                            @endphp
                            <a href="{{ route('chat.index', $user->id) }}"
                                class="flex items-center p-4 cursor-pointer transition-all border-l-4 {{ $isActive ? 'bg-teal-50 border-teal-500' : 'hover:bg-gray-50 border-transparent' }}">
                                <div
                                    class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold mr-3 uppercase">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-semibold text-gray-800 truncate">{{ $user->name }}</h4>
                                        <span
                                            class="text-[10px] text-gray-500">{{ $chat->updated_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-sm {{ $isActive ? 'text-teal-600' : 'text-gray-500' }} truncate">
                                        {{ $chat->messages->last()->body ?? 'Belum ada pesan' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex-1 flex flex-col bg-[#f0f4f4]">
                    @if ($activeChat)
                        @php
                            $activeUser =
                                $activeChat->sender_id == auth()->id() ? $activeChat->receiver : $activeChat->sender;
                        @endphp
                        <div class="p-4 bg-white shadow-sm flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="w-10 h-10 bg-teal-500 rounded-full flex items-center justify-center text-white font-bold mr-3 uppercase">
                                    {{ substr($activeUser->name, 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $activeUser->name }}</h4>
                                    <p class="text-xs {{ $activeUser->isOnline ? 'text-teal-500' : 'text-gray-400' }}">
                                        {{ $activeUser->isOnline ? 'Online' : 'Offline' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 bg-opacity-50">
                            @foreach ($activeChat->messages as $message)
                                @if ($message->user_id == auth()->id())
                                    <div class="flex items-end justify-end">
                                        <div
                                            class="bg-teal-500 text-white p-3 rounded-2xl rounded-br-none shadow-sm max-w-md">
                                            <p class="text-sm">{{ $message->body }}</p>
                                            <span
                                                class="text-[10px] text-teal-100 mt-1 block text-right">{{ $message->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-end">
                                        <div
                                            class="bg-white text-gray-800 p-3 rounded-2xl rounded-bl-none shadow-sm max-w-md">
                                            <p class="text-sm">{{ $message->body }}</p>
                                            <span
                                                class="text-[10px] text-gray-400 mt-1 block text-left">{{ $message->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <form id="chat-form" data-url="{{ route('chat.send', $activeChat->id ?? 0) }}"
                            class="p-4 bg-white border-t border-gray-100">
                            @csrf
                            <div class="flex items-center space-x-3">
                                <input type="text" id="message-input" name="body" placeholder="Tulis pesan..."
                                    required
                                    class="flex-1 bg-gray-50 border-none rounded-full py-2.5 px-5 focus:ring-1 focus:ring-teal-500 text-sm">
                                <button type="submit"
                                    class="bg-teal-500 hover:bg-teal-600 text-white p-2.5 rounded-full shadow-md transition-transform active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                            <div class="bg-white p-6 rounded-full shadow-inner mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-teal-200" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p>Pilih pesan untuk mulai mengobrol</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-sidebar>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chatForm = document.getElementById('chat-form');
                const messageInput = document.getElementById('message-input');
                const chatMessages = document.getElementById('chat-messages');

                // Scroll otomatis ke bawah saat halaman dimuat
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

                        // 1. Kosongkan input segera untuk UX yang cepat
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
                                // 2. Tambahkan bubble chat baru ke UI secara instan
                                appendMessage(data.message.body, data.time);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        } catch (error) {
                            console.error('Gagal mengirim pesan:', error);
                            alert('Gagal mengirim pesan, coba lagi.');
                        }
                    });
                }

                function appendMessage(text, time) {
                    const messageHtml = `
                        <div class="flex items-end justify-end">
                            <div class="bg-teal-500 text-white p-3 rounded-2xl rounded-br-none shadow-sm max-w-md">
                                <p class="text-sm">${text}</p>
                                <span class="text-[10px] text-teal-100 mt-1 block text-right">${time}</span>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                }
            });

            // Pastikan Laravel Echo sudah terinstall dan terkonfigurasi di resources/js/app.js
            // Biasanya Reverb otomatis terkonfigurasi di sana.

            document.addEventListener('DOMContentLoaded', function() {
                const activeChatId = "{{ $activeChat->id ?? 0 }}";
                const authId = "{{ auth()->id() }}";

                if (activeChatId > 0) {
                    // Mendengarkan Private Channel
                    window.Echo.private(`chat.${activeChatId}`)
                        .listen('MessageSent', (e) => {
                            // Jangan tambah bubble jika pesan itu dikirim oleh diri sendiri (sudah ditangani AJAX)
                            if (e.message.user_id != authId) {
                                appendIncomingMessage(e.message.body, 'Baru saja');
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        });
                }

                // Fungsi khusus untuk pesan masuk (Lawan Bicara)
                function appendIncomingMessage(text, time) {
                    const messageHtml = `
            <div class="flex items-end">
                <div class="bg-white text-gray-800 p-3 rounded-2xl rounded-bl-none shadow-sm max-w-md">
                    <p class="text-sm">${text}</p>
                    <span class="text-[10px] text-gray-400 mt-1 block text-left">${time}</span>
                </div>
            </div>
        `;
                    document.getElementById('chat-messages').insertAdjacentHTML('beforeend', messageHtml);
                }
            });
        </script>
    @endsection
</x-app>
