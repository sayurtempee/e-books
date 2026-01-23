<x-app>
    @section('title', 'Reset Password')

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
                    Reset Password
                </h1>

                @error('email')
                    <p class="text-center text-sm text-red-600 mb-4">
                        {{ $message }}
                    </p>
                @enderror

                <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" name="email" value="{{ $email }}" readonly
                            class="w-full mt-1 px-4 py-2 rounded-lg
                            bg-gray-100
                            border border-gray-300">
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                            bg-white/70 focus:bg-white
                            border border-gray-300
                            focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Minimum 8 characters" autofocus required>
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

                    <button type="submit"
                        class="w-full py-2 mt-4 rounded-lg
                        bg-teal-500 hover:bg-teal-600
                        text-white font-semibold transition">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    @endsection
</x-app>
