<x-app>
    @section('title', 'Welcome')

    @section('body-content')
        <section
            class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-green-400 via-teal-400 to-emerald-500 overflow-hidden">

            {{-- BLUR DECORATION --}}
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-white/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-300/30 rounded-full blur-3xl"></div>

            {{-- MAIN CONTAINER --}}
            <div
                class="relative max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-12 items-center px-6 py-14 bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl">

                {{-- LEFT CONTENT --}}
                <div class="space-y-6">
                    <span class="inline-block px-4 py-1 rounded-full bg-teal-100 text-teal-700 text-sm font-semibold">
                        📚 Platform Edukasi Digital
                    </span>

                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 leading-tight">
                        Buka Dunia <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-emerald-500">
                            Pengetahuan Lewat E-BOOKS
                        </span>
                    </h1>

                    <p class="text-gray-600 text-lg">
                        Akses ratusan e-book pilihan, belajar lebih fleksibel,
                        dan tingkatkan wawasanmu kapan saja & di mana saja.
                    </p>

                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('login') }}"
                            class="group px-6 py-3 rounded-xl bg-teal-500 text-white font-semibold shadow-lg hover:bg-teal-600 transition flex items-center gap-2">
                            Login
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <a href="{{ route('register') }}"
                            class="px-6 py-3 rounded-xl border-2 border-teal-500 text-teal-600 font-semibold hover:bg-teal-50 transition">
                            Register
                        </a>
                    </div>
                </div>

                {{-- RIGHT IMAGE --}}
                <div class="flex justify-center">
                    <img src="{{ asset('image/people-image.svg') }}" alt="Reading Illustration"
                        class="w-full max-w-md drop-shadow-2xl hover:scale-105 transition duration-500">
                </div>

            </div>
        </section>
    @endsection
</x-app>
