<x-app>
    @section('title', 'Pesan Chat')

    @section('body-content')
        <x-sidebar>
            <div class="min-h-screen bg-[#f8fafc] p-4 md:p-6 font-sans">
                <div
                    class="flex h-[88vh] rounded-2xl shadow-2xl overflow-hidden border border-gray-200 animate-slide-up bg-transparent">

                    {{-- SIDEBAR CONTACTS --}}
                    <div class="w-full md:w-1/3 bg-white border-r border-gray-200 flex flex-col">
                        <div class="p-4 flex items-center gap-3 bg-white">
                            <div class="relative flex-1">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="contactSearch" placeholder="Cari pesan..."
                                    class="w-full bg-slate-100 rounded-full py-2.5 pl-9 pr-4 text-sm focus:ring-2 focus:ring-teal-500/30 border-none">
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            @forelse($contacts as $contact)
                                @php
                                    $unreadCount = \App\Models\Message::where('sender_id', $contact->id)
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                    $isActive = isset($activeContact) && $activeContact->id == $contact->id;
                                    $roleColor = [
                                        'admin' => 'bg-red-100 text-red-600',
                                        'seller' => 'bg-teal-100 text-teal-600',
                                        'buyer' => 'bg-blue-100 text-blue-600',
                                    ];
                                @endphp

                                <a href="{{ route('chat.index', $contact->id) }}"
                                    class="contact-item flex items-center gap-3 px-4 py-3.5 transition border-b border-gray-50
                                    {{ $isActive ? 'bg-gradient-to-r from-teal-600 to-teal-500 text-white' : 'hover:bg-slate-50' }}">

                                    <div class="relative">
                                        <div
                                            class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-lg text-white bg-gradient-to-br from-teal-600 to-emerald-500">
                                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                                        </div>
                                        @if (Cache::has('user-is-online-' . $contact->id))
                                            <span
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 ring-2 {{ $isActive ? 'ring-teal-600' : 'ring-white' }} rounded-full"></span>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline">
                                            <h4 class="font-semibold text-sm truncate contact-name">{{ $contact->name }}
                                            </h4>
                                            <span class="text-[10px] {{ $isActive ? 'text-white/70' : 'text-gray-400' }}">
                                                {{ \App\Models\Message::where(function ($q) use ($contact) {
                                                    $q->where('sender_id', $contact->id)->orWhere('receiver_id', $contact->id);
                                                })->latest()->first()
                                                    ?->created_at->diffForHumans() ?? '' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center mt-0.5">
                                            <span
                                                class="text-[10px] px-2 rounded-md uppercase font-bold {{ $isActive ? 'bg-white/20 text-white' : $roleColor[$contact->role] ?? 'bg-gray-100' }}">
                                                {{ $contact->role }}
                                            </span>
                                            @if ($unreadCount > 0)
                                                <span
                                                    class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $isActive ? 'bg-white text-teal-600' : 'bg-teal-600 text-white' }}">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center mt-20 p-4">
                                    <i class="bi bi-chat-dots text-4xl text-gray-200"></i>
                                    <p class="text-sm text-gray-400 mt-2">Belum ada kontak tersedia</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- CHAT AREA --}}
                    <div class="hidden md:flex flex-1 flex flex-col relative chat-wallpaper">
                        @if (isset($activeContact))
                            <div
                                class="px-5 py-3 bg-white/90 backdrop-blur border-b border-gray-200 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-600 to-emerald-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($activeContact->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-800">{{ $activeContact->name }}</h3>
                                        <p
                                            class="text-[11px] {{ Cache::has('user-is-online-' . $activeContact->id) ? 'text-teal-600' : 'text-gray-400' }}">
                                            {{ Cache::has('user-is-online-' . $activeContact->id) ? 'Online' : 'Offline' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div id="chatWindow"
                                class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar relative z-10">
                                @php $lastDate = null; @endphp
                                @foreach ($messages as $msg)
                                    @php $msgDate = $msg->created_at->format('Y-m-d'); @endphp
                                    @if ($lastDate !== $msgDate)
                                        <div class="flex justify-center my-4">
                                            <span
                                                class="bg-gray-200/50 backdrop-blur px-3 py-1 rounded-full text-[10px] text-gray-600 uppercase tracking-wider">
                                                {{ $msg->created_at->isToday() ? 'Hari Ini' : $msg->created_at->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                        @php $lastDate = $msgDate; @endphp
                                    @endif

                                    <div
                                        class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} animate-pop">
                                        <div
                                            class="max-w-[75%] shadow-sm {{ $msg->sender_id == auth()->id() ? 'bg-teal-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white text-gray-800 rounded-r-2xl rounded-tl-2xl border border-gray-100' }} px-4 py-2 relative">
                                            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                                            <div class="flex justify-end items-center gap-1 mt-1">
                                                <span
                                                    class="text-[9px] {{ $msg->sender_id == auth()->id() ? 'text-teal-100' : 'text-gray-400' }}">
                                                    {{ $msg->created_at->format('H:i') }}
                                                </span>
                                                @if ($msg->sender_id == auth()->id())
                                                    <i
                                                        class="bi bi-check2-all text-xs {{ $msg->is_read ? 'text-sky-300' : 'text-teal-200' }}"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-4 bg-white/50 backdrop-blur relative z-10">
                                <form action="{{ route('messages.send') }}" method="POST" id="chatForm"
                                    class="max-w-4xl mx-auto flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $activeContact->id }}">
                                    <div
                                        class="flex-1 flex items-center bg-white rounded-full px-4 py-1 shadow-md border border-gray-200">
                                        <input type="text" name="message" id="messageInput" required autocomplete="off"
                                            placeholder="Tulis pesan..."
                                            class="flex-1 px-3 py-2 bg-transparent border-none focus:ring-0 text-sm">
                                    </div>
                                    <button type="submit"
                                        class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center hover:bg-teal-700 transition shadow-lg">
                                        <i class="bi bi-send-fill text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center bg-white/60 backdrop-blur">
                                <div class="w-24 h-24 bg-teal-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-chat-left-text text-4xl text-teal-500"></i>
                                </div>
                                <h3 class="text-gray-800 font-bold">Pesan Anda</h3>
                                <p class="text-sm text-gray-500 mt-1">Pilih teman chat untuk memulai percakapan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <style>
                .chat-wallpaper {
                    background-color: #e5e7eb;
                    background-image: url("{{ asset('image/wallpaper-chat.png') }}");
                }

                .custom-scrollbar::-webkit-scrollbar {
                    width: 4px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 10px;
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const chatWindow = document.getElementById('chatWindow');
                    if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;

                    @if (isset($activeContact))
                        // 1. Logika Aktivitas (Sudah ada di kode Anda)
                        const reportActivity = () => {
                            fetch("{{ route('chat.activity') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    receiver_id: {{ $activeContact->id }}
                                })
                            });
                        };
                        reportActivity();
                        setInterval(reportActivity, 30000);

                        // 2. LOGIKA REAL-TIME CHAT (TAMBAHKAN INI)
                        if (typeof Echo !== 'undefined') {
                            window.Echo.channel(`chat.{{ auth()->id() }}`)
                                .listen('.message.sent', (e) => {
                                    // Hanya munculkan jika pengirimnya adalah orang yang sedang kita ajak chat
                                    if (e.message.sender_id == {{ $activeContact->id }}) {
                                        const messageHtml = `
                                            <div class="flex justify-start animate-pop">
                                                <div class="max-w-[75%] shadow-sm bg-white text-gray-800 rounded-r-2xl rounded-tl-2xl border border-gray-100 px-4 py-2 relative">
                                                    <p class="text-sm leading-relaxed">${e.message.message}</p>
                                                    <div class="flex justify-end items-center gap-1 mt-1">
                                                        <span class="text-[9px] text-gray-400">Baru saja</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        chatWindow.insertAdjacentHTML('beforeend', messageHtml);
                                        chatWindow.scrollTop = chatWindow.scrollHeight;
                                    } else {
                                        // Jika pesan dari orang lain, biarkan notifikasi sidebar yang bekerja
                                        console.log('Pesan masuk dari kontak lain');
                                    }
                                });
                        }
                    @endif
                });
            </script>
        </x-sidebar>
    @endsection
</x-app>
