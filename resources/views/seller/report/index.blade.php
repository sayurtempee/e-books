<x-app>
    @section('title', 'Laporan Penjualan')

    @section('body-content')
        <x-sidebar>
            <div class="p-4 md:p-8 bg-[#f8fafc] min-h-screen">

                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">
                            Laporan <span class="text-teal-600">Penjualan</span>
                        </h1>
                        <p class="text-gray-500 text-sm flex items-center gap-2">
                            <span class="flex h-2 w-2 rounded-full bg-teal-500"></span>
                            Data performa real-time untuk periode terpilih.
                        </p>
                    </div>

                    <a href="{{ route('seller.reports.download', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                        class="group flex items-center gap-3 bg-white border-2 border-teal-600 text-teal-600 hover:bg-teal-600 hover:text-white px-6 py-3 rounded-2xl font-bold transition-all duration-300 shadow-sm hover:shadow-teal-200">
                        <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                        <span>Export PDF</span>
                    </a>
                </div>

                {{-- Filter Bar --}}
                <div
                    class="bg-white/70 backdrop-blur-md p-6 rounded-[2rem] border border-white shadow-xl shadow-gray-200/50 mb-10">
                    <form action="{{ route('seller.reports.index') }}" method="GET"
                        class="flex flex-col md:flex-row items-end gap-6">
                        <div class="w-full md:w-64">
                            <label class="block text-xs font-black text-teal-900 uppercase tracking-widest mb-2 ml-1">Pilih
                                Bulan</label>
                            <div class="relative">
                                <select name="month"
                                    class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-teal-500 appearance-none transition-all">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <i
                                    class="bi bi-chevron-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="w-full md:w-48">
                            <label
                                class="block text-xs font-black text-teal-900 uppercase tracking-widest mb-2 ml-1">Tahun</label>
                            <div class="relative">
                                <select name="year"
                                    class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-teal-500 appearance-none transition-all">
                                    @foreach ($yearRange as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                                <i
                                    class="bi bi-chevron-down absolute right-4 top-3.5 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex gap-3 w-full md:w-auto">
                            <button type="submit"
                                class="flex-1 md:flex-none bg-teal-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-teal-700 hover:scale-105 transition-all duration-300 shadow-lg shadow-teal-200">
                                Terapkan
                            </button>

                            <a href="{{ route('seller.reports.index') }}"
                                class="flex items-center justify-center bg-gray-100 text-gray-500 px-4 py-3 rounded-xl hover:bg-gray-200 transition-all">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Statistik Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    {{-- Omzet --}}
                    <div
                        class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                        <div class="flex justify-between items-start mb-6">
                            <div
                                class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-teal-600 group-hover:text-white transition-colors duration-500">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <span class="text-xs font-bold text-teal-500 bg-teal-50 px-3 py-1 rounded-full">Bulanan</span>
                        </div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Total Omzet</p>
                        <h3 class="text-3xl font-black text-gray-800">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h3>
                    </div>

                    {{-- Profit --}}
                    <div
                        class="group bg-teal-600 p-8 rounded-[2rem] border border-teal-500 shadow-xl shadow-teal-100 hover:shadow-teal-300 hover:-translate-y-2 transition-all duration-500">
                        <div class="flex justify-between items-start mb-6">
                            <div
                                class="w-14 h-14 bg-white/20 text-white rounded-2xl flex items-center justify-center text-2xl backdrop-blur-md">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-white/80 bg-white/10 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">Net
                                Profit</span>
                        </div>
                        <p class="text-sm text-white/70 font-medium mb-1">Keuntungan Bersih</p>
                        <h3 class="text-3xl font-black text-white">
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </h3>
                    </div>

                    {{-- Terjual --}}
                    <div
                        class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                        <div class="flex justify-between items-start mb-6">
                            <div
                                class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-full">Volume</span>
                        </div>
                        <p class="text-sm text-gray-400 font-medium mb-1">Buku Terjual</p>
                        <h3 class="text-3xl font-black text-gray-800">
                            {{ $totalSold }} <span class="text-lg font-bold text-gray-300 ml-1 uppercase">Pcs</span>
                        </h3>
                    </div>
                </div>

                {{-- Grafik Section --}}
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm mb-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800">Trend Penjualan</h2>
                            <p class="text-gray-400 text-sm">Visualisasi perbandingan Omzet dan Profit harian</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                                <span class="text-xs font-bold text-gray-500 uppercase">Profit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-teal-200"></span>
                                <span class="text-xs font-bold text-gray-500 uppercase">Omzet</span>
                            </div>
                        </div>
                    </div>

                    <div class="h-[450px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Script Chart (Tetap Sama Namun dengan Penyesuaian Visual) --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($days),
                        datasets: [{
                                label: 'Profit',
                                type: 'line',
                                data: @json($profits),
                                borderColor: '#0d9488',
                                backgroundColor: 'transparent',
                                borderWidth: 4,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#0d9488',
                                pointBorderWidth: 3,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                tension: 0.4,
                                order: 1
                            },
                            {
                                label: 'Omzet',
                                data: @json($revenues),
                                backgroundColor: 'rgba(20, 184, 166, 0.15)',
                                borderColor: 'transparent',
                                borderRadius: 12,
                                borderSkipped: false,
                                barPercentage: 0.6,
                                order: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 15,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                                    color: '#94a3b8',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#94a3b8',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
        </x-sidebar>
    @endsection
</x-app>
