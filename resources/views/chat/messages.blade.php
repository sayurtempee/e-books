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

    <div class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }} animate-pop">
        <div
            class="max-w-[75%] shadow-sm {{ $msg->sender_id == auth()->id() ? 'bg-teal-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white text-gray-800 rounded-r-2xl rounded-tl-2xl border border-gray-100' }} px-4 py-2 relative">
            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
            <div class="flex justify-end items-center gap-1 mt-1">
                <span class="text-[9px] {{ $msg->sender_id == auth()->id() ? 'text-teal-100' : 'text-gray-400' }}">
                    {{ $msg->created_at->format('H:i') }}
                </span>
                @if ($msg->sender_id == auth()->id())
                    <i class="bi bi-check2-all text-xs {{ $msg->is_read ? 'text-sky-300' : 'text-teal-200' }}"></i>
                @endif
            </div>
        </div>
    </div>
@endforeach
