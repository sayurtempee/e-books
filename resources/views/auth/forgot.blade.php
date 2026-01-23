<x-app>
    @section('title', 'Forgot Password')

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
                    Forgot Password
                </h1>

                @if (session('success'))
                    <p class="text-center text-sm text-green-700 mb-4">
                        {{ session('success') }}
                    </p>
                @endif

                @error('email')
                    <p class="text-center text-sm text-red-600 mb-4">
                        {{ $message }}
                    </p>
                @enderror

                <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                    @csrf

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
                            placeholder="you@example.com" autofocus required>
                    </div>

                    <button type="button" onclick="confirmForgetPassword()"
                        class="w-full py-2 mt-4 rounded-lg
                        bg-teal-500 hover:bg-teal-600
                        text-white font-semibold transition">
                        Send Reset Link
                    </button>

                    <p class="text-center text-sm text-gray-700 mt-4">
                        Remember your password?
                        <a href="{{ route('login.page') }}" class="text-teal-700 font-medium hover:underline">
                            Login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    @endsection
</x-app>
