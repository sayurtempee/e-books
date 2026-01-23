<x-app>
    @section('title', 'List Buyer')

    @section('body-content')
        <x-sidebar>
            <div class="p-6">
                <h1 class="text-2xl font-bold text-teal-600 mb-6">
                    Halaman Daftar Buyer
                </h1>

                {{-- Add Buyer --}}
                <div x-data="{ openAddBuyerModal: false }">
                    @include('admin.buyer.add')
                    <button @click="openAddBuyerModal = true"
                        class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow cursor-pointer font-semibold">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Buyer
                    </button>
                </div>

                {{-- Buyer Table --}}
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-teal-200 rounded-lg overflow-hidden">
                            <thead class="bg-teal-500 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">Photo</th>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3 text-left">Email</th>
                                    <th class="px-4 py-3 text-left">Level</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-teal-100">
                                @foreach ($users as $user)
                                    @if ($user->role === 'buyer')
                                        <tr class="hover:bg-teal-50 transition">
                                            {{-- Photo Profile --}}
                                            <td class="px-4 py-3">
                                                @php
                                                    $initials = collect(explode(' ', $user->name))
                                                        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                        ->take(2)
                                                        ->implode('');

                                                    $gravatar =
                                                        'https://www.gravatar.com/avatar/' .
                                                        md5(strtolower(trim($user->email))) .
                                                        '?d=404';
                                                @endphp

                                                @if ($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                        alt="Profile Photo" class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-semibold text-sm"
                                                        title="{{ $user->name }}">
                                                        {{ $initials }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $user->name }}
                                            </td>

                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $user->email }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="px-3 py-1 text-sm rounded-full bg-teal-100 text-teal-700">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>

                                            {{-- Action Icons --}}
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-4">

                                                    {{-- Detail --}}
                                                    <div x-data="{ openDetailBuyerModal: false }">
                                                        @include('admin.buyer.detail', [
                                                            'user' => $user,
                                                        ])
                                                        <button @click="openDetailBuyerModal = true" type="button"
                                                            class="text-blue-500 hover:text-blue-600 transition"
                                                            title="Detail">
                                                            <i class="bi bi-eye cursor-pointer"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Edit --}}
                                                    <div x-data="{ openEditBuyerModal: false }">
                                                        @include('admin.buyer.edit', [
                                                            'user' => $user,
                                                        ])
                                                        <button @click="openEditBuyerModal = true" type="button"
                                                            class="text-yellow-500 hover:text-yellow-600 transition"
                                                            title="Edit">
                                                            <i class="bi bi-pencil-square cursor-pointer"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Delete --}}
                                                    <form id="deleteBuyerForm-{{ $user->id }}" method="POST"
                                                        action="{{ route('admin.buyers.delete', $user->id) }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button"
                                                            onclick="confirmDeleteBuyer({{ $user->id }}, '{{ $user->name }}')"
                                                            class="text-red-500 hover:text-red-600 transition"
                                                            title="Delete">
                                                            <i class="bi bi-trash cursor-pointer"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-sidebar>
    @endsection
</x-app>
