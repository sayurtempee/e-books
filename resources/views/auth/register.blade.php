<x-app>
    @section('title', 'Register Page')

    @section('body-content')
        <div
            class="min-h-screen flex items-center justify-center
        bg-gradient-to-br
        from-green-400
        via-blue-300
        to-teal-400
        relative overflow-hidden">

            {{-- Blur overlay --}}
            <div class="absolute inset-0 backdrop-blur-3xl"></div>

            {{-- Back Button --}}
            <a href="{{ route('welcome') }}"
                class="absolute top-6 left-6 z-20
                flex items-center gap-2
                px-4 py-2 rounded-xl
                bg-white/40 backdrop-blur-lg
                text-gray-800 font-semibold
                shadow-md border border-white/50
                hover:bg-white/60 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>

            {{-- Card --}}
            <div
                class="relative w-full max-w-md p-8 rounded-2xl
            bg-white/30 backdrop-blur-xl
            shadow-xl border border-white/40">

                <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
                    Create Account
                </h1>

                <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- NIK --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            NIK
                        </label>
                        <input type="text" name="nik"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Your NIK" autofocus required>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Name
                        </label>
                        <input type="text" name="name"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Your name" required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" name="email"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="you@example.com" required>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Minimum 8 characters" required>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Repeat password" required>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Address
                        </label>
                        <textarea name="address"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Your address"></textarea>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Role
                        </label>

                        <div class="relative">
                            <!-- Icon kiri -->
                            <span id="roleIcon" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="bi bi-people text-lg"></i>
                            </span>

                            <!-- Select -->
                            <select name="role"
                                class="w-full pl-11 pr-4 py-2 rounded-lg
                   bg-white/70 focus:bg-white
                   border border-gray-300
                   focus:ring-2 focus:ring-teal-400
                   focus:border-teal-400
                   focus:outline-none transition">
                                <option value="" disabled selected>Pilih Role</option>
                                <option value="seller">Seller</option>
                                <option value="buyer">Buyer</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-2 mt-4 rounded-lg
                    bg-teal-500 hover:bg-teal-600
                    text-white font-semibold transition">
                        Sign Up
                    </button>

                    <p class="text-center text-sm text-gray-700 mt-4">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-teal-700 font-medium hover:underline">
                            Login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    @endsection
</x-app>
