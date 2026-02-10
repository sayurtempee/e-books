<x-app>
    @section('title', 'Laporan Penjualan')

    @section('body-content')
        <x-sidebar>
            <div class="min-h-screen bg-slate-50 px-6 md:px-10 py-10 space-y-14">

                {{-- Header --}}
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                            Laporan <span class="text-teal-600">Penjualan</span>
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Data performa untuk periode terpilih
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('seller.reports.download', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-teal-600 px-5 py-3 text-sm font-bold text-teal-700 hover:bg-teal-600 hover:text-white transition">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF (Filter)
                        </a>

                        <a href="{{ route('reports.downloadAll') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-teal-700 transition">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            Download Semua Waktu
                        </a>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <form method="GET" action="{{ route('seller.reports.index') }}"
                        class="flex flex-col md:flex-row md:items-end gap-6">

                        {{-- Input Hidden untuk memastikan parameter selalu terkirim jika diperlukan --}}
                        <div class="w-full md:w-56">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Filter
                                Bulan</label>
                            <select name="month"
                                class="mt-2 w-full rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500 font-bold text-slate-700">
                                <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>✨ Semua Bulan
                                </option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}"
                                        {{ $selectedMonth != 'all' && $selectedMonth == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-40">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Tahun</label>
                            <select name="year"
                                class="mt-2 w-full rounded-xl border-slate-200 focus:ring-teal-500 focus:border-teal-500 font-bold text-slate-700">
                                @foreach ($yearRange as $y)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="rounded-xl bg-teal-600 px-8 py-3 text-sm font-bold text-white hover:bg-teal-700 transition shadow-lg shadow-teal-100 flex items-center gap-2">
                                <i class="bi bi-filter"></i>
                                Terapkan Filter
                            </button>

                            {{-- Tombol Reset diarahkan kembali ke link default --}}
                            <a href="{{ route('seller.reports.index', ['month' => 'all', 'year' => '2026']) }}"
                                class="rounded-xl bg-slate-100 px-4 py-3 text-slate-500 hover:bg-slate-200 transition flex items-center justify-center"
                                title="Reset Filter">
                                <i class="bi bi-arrow-clockwise text-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Statistik Terfilter --}}
                <section>
                    <div class="mb-4">
                        <h2 class="text-xs font-extrabold tracking-widest uppercase text-slate-400">
                            @if ($selectedMonth == 'all')
                                Statistik Tahun {{ $selectedYear }}
                            @else
                                Statistik
                                {{ \Carbon\Carbon::createFromDate(null, $selectedMonth, 1)->locale('id')->translatedFormat('F') }}
                                {{ $selectedYear }}
                            @endif
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-2xl border border-slate-100 p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-500">Omzet Periode Ini</span>
                            </div>
                            <p class="text-3xl font-extrabold text-slate-800">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="bg-teal-600 rounded-2xl p-8 text-white">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <span class="text-sm font-semibold text-white/80">Profit Periode Ini</span>
                            </div>
                            <p class="text-3xl font-extrabold">
                                Rp {{ number_format($totalProfit, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-100 p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-500">Buku Terjual</span>
                            </div>
                            <p class="text-3xl font-extrabold text-slate-800">
                                {{ $totalSold }} <span class="text-base text-slate-400">pcs</span>
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Chart --}}
                <section class="bg-white rounded-3xl border border-slate-100 p-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-800">Trend Penjualan</h2>
                            <p class="text-sm text-slate-400">
                                {{ $selectedMonth == 'all' ? 'Data bulanan sepanjang tahun' : 'Data harian pada bulan terpilih' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4 mt-4 md:mt-0">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                                <span class="text-xs font-semibold text-slate-500 uppercase">Profit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-300"></span>
                                <span class="text-xs font-semibold text-slate-500 uppercase">Omzet</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-[420px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </section>

                <hr class="border-slate-200">

                {{-- Ringkasan Seluruh Waktu (Pindahkan ke bawah agar tidak membingungkan) --}}
                <section>
                    <h2 class="text-xs font-extrabold tracking-widest uppercase text-slate-400 mb-4">
                        Akumulasi Seluruh Waktu
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-white/50 rounded-2xl border border-dashed border-slate-200 p-6 flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-400 font-bold">Total Omzet (All Time)</p>
                                <p class="text-xl font-extrabold text-slate-600">
                                    Rp {{ number_format($grandTotalRevenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="bg-white/50 rounded-2xl border border-dashed border-slate-200 p-6 flex items-center gap-4">
                            <div
                                class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="bi bi-piggy-bank"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-400 font-bold">Total Profit (All Time)</p>
                                <p class="text-xl font-extrabold text-slate-600">
                                    Rp {{ number_format($grandTotalProfit, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Chart JS --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                new Chart(document.getElementById('salesChart'), {
                    data: {
                        labels: @json($days),
                        datasets: [{
                                type: 'line',
                                label: 'Profit',
                                data: @json($profits),
                                borderColor: '#0f766e',
                                borderWidth: 3,
                                tension: .4,
                                pointRadius: 4
                            },
                            {
                                type: 'bar',
                                label: 'Omzet',
                                data: @json($revenues),
                                backgroundColor: 'rgba(15,118,110,.15)',
                                borderRadius: 10
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + v.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            </script>
        </x-sidebar>
    @endsection
</x-app>
