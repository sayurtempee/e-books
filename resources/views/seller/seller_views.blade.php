<x-app>
    @section('title', 'Daftar Rekan Seller')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Profile</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nama Seller
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($sellers as $seller)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- Kolom Foto Profile --}}
                                    <td class="px-6 py-4">
                                        @php
                                            $initials = collect(explode(' ', $seller->name))
                                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                ->take(2)
                                                ->implode('');
                                        @endphp

                                        @if ($seller->profile_photo_path)
                                            <img src="{{ asset('storage/' . $seller->profile_photo_path) }}"
                                                class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-sm"
                                                title="{{ $seller->name }}">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Nama --}}
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $seller->name }}
                                        @if ($seller->id === auth()->id())
                                            <span
                                                class="ml-2 text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Anda</span>
                                        @endif
                                    </td>

                                    {{-- Email --}}
                                    <td class="px-6 py-4 text-gray-600 italic">
                                        {{ $seller->email }}
                                    </td>

                                    {{-- Role/Level --}}
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 uppercase">
                                            {{ $seller->role }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
