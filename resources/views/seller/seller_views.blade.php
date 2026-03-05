<x-app>
    @section('title', 'Daftar Rekan Seller')

    @section('body-content')
        <x-sidebar>
            <div class="p-6 bg-gray-50 min-h-screen">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    {{-- Header Section --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Daftar Rekan Seller</h2>
                        <p class="text-gray-500 text-sm">Kelola dan pantau aktivitas semua seller di platform Anda.</p>
                    </div>

                    <div class="w-full md:w-72">
                        <form action="{{ route('seller.list.sellers') }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari seller..."
                                class="w-full pl-10 pr-10 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="bi bi-search"></i>
                            </div>
                            @if (request('search'))
                                <button type="button"
                                    onclick="document.querySelector('input[name=\'search\']').value=''; this.closest('form').submit()"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            @endif
                            <button type="submit" class="hidden">Search</button>
                        </form>
                    </div>
                </div>

                {{-- Grid Container --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($sellers as $seller)
                        @php
                            // Logika Sensor NIK (6 digit depan + bintang)
                            $maskedNik = $seller->nik ? substr($seller->nik, 0, 8) . '********' : 'NIK Tidak Tersedia';

                            // Logika Inisial Fallback
                            $initials = collect(explode(' ', $seller->name))
                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp

                        <div
                            class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 relative group">

                            {{-- Status Indicator (Dot) --}}
                            <div class="absolute top-5 right-5">
                                <div class="flex items-center gap-1.5">
                                    <span class="relative flex h-2.5 w-2.5">
                                        @if ($seller->isOnline == 1)
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                        @else
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                        @endif
                                    </span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider {{ $seller->isOnline == 1 ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $seller->isOnline == 1 ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                {{-- Profile Picture --}}
                                <div class="flex-shrink-0">
                                    @if ($seller->foto_profile)
                                        <img src="{{ asset('storage/' . $seller->foto_profile) }}"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-gray-50 shadow-sm"
                                            alt="{{ $seller->name }}">
                                    @else
                                        <div
                                            class="w-16 h-16 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-xl shadow-inner">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </div>

                                {{-- User Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-bold text-gray-800 truncate mb-0.5 flex items-center gap-2">
                                        {{ $seller->name }}
                                        @if ($seller->id === auth()->id())
                                            <span
                                                class="text-[9px] bg-blue-50 text-blue-500 px-2 py-0.5 rounded-full border border-blue-100">ANDA</span>
                                        @endif
                                    </h4>

                                    <p class="text-xs text-gray-500 font-medium mb-3 flex items-center gap-1">
                                        <i class="bi bi-geo-alt-fill text-teal-500"></i>
                                        {{ $seller->address ?? 'Alamat belum diatur' }}
                                    </p>

                                    {{-- NIK & Email Badges --}}
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span
                                            class="px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-600 text-[10px] font-semibold rounded-lg">
                                            ID: {{ $maskedNik }}
                                        </span>
                                        <span
                                            class="px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-600 text-[10px] font-semibold rounded-lg truncate max-w-full">
                                            {{ $seller->email }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Card: Last Activity --}}
                            <div class="mt-5 pt-4 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-[10px] text-gray-400 font-medium italic">
                                    @if ($seller->isOnline == 1)
                                        Sedang aktif sekarang
                                    @else
                                        Terakhir terlihat
                                        {{ $seller->last_activity_at ? \Carbon\Carbon::parse($seller->last_activity_at)->diffForHumans() : 'tidak diketahui' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty State --}}
                @if ($sellers->isEmpty())
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <div class="text-gray-300 text-6xl mb-4">📭</div>
                        <h3 class="text-gray-500 font-medium">Belum ada seller yang terdaftar.</h3>
                    </div>
                @endif
            </div>
        </x-sidebar>
    @endsection
</x-app>
