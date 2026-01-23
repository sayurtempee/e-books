<x-app>
    @section('title', 'Login Page')

    @section('body-content')
        <div
            class="min-h-screen flex items-center justify-center
        bg-gradient-to-br
        from-green-400
        via-blue-300
        to-teal-400
        relative overflow-hidden">

            {{-- Soft blur overlay --}}
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
                    Login
                </h1>

                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    @if ($errors->has('login'))
                        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
                            {{ $errors->first('login') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" name="email"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Masukkan email" autofocus required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input type="password" name="password"
                            class="w-full mt-1 px-4 py-2 rounded-lg
                        bg-white/70 focus:bg-white
                        border border-gray-300
                        focus:ring-2 focus:ring-teal-400 focus:outline-none"
                            placeholder="Masukkan password" required>
                    </div>

                    <button type="submit"
                        class="w-full py-2 mt-4 rounded-lg
                    bg-teal-500 hover:bg-teal-600
                    text-white font-semibold
                    transition">
                        Sign In
                    </button>

                    <div class="flex justify-between text-sm mt-4">
                        <a href="{{ route('register') }}" class="text-teal-700 hover:underline">
                            Register
                        </a>
                        <a href="{{ route('forgot.password') }}" class="text-teal-700 hover:underline">
                            Forgot password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endsection
</x-app>
