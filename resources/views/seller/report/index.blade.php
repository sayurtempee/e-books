<x-app>
    @section('title', 'Laporan Penjualan')

    @section('body-content')
        <x-sidebar>
            <div class="min-h-screen bg-[#f8fafc] px-6 md:px-10 py-10 space-y-10">

                {{-- Header Section --}}
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div>
                        <nav class="flex mb-2" aria-label="Breadcrumb">
                            <ol
                                class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                <li>Analitik</li>
                                <li><i class="bi bi-chevron-right text-[10px]"></i></li>
                                <li class="text-teal-600">Laporan Penjualan</li>
                            </ol>
                        </nav>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                            Performa <span class="text-teal-600">Bisnis</span>
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 italic">
                            Data komprehensif untuk evaluasi pertumbuhan toko Anda.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <form id="pdfDownloadForm" action="{{ route('seller.reports.download') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <input type="hidden" name="year" value="{{ $selectedYear }}">
                            <input type="hidden" name="chart_image" id="chart_image_input">

                            <button type="button" onclick="submitPdfWithChart()"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-slate-200 hover:bg-slate-800 transition active:scale-95">
                                <i class="bi bi-cloud-arrow-down-fill text-lg"></i>
                                Ekspor Laporan PDF
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Filter Card --}}
                <div class="bg-white rounded-3xl border border-slate-200/60 p-2 shadow-sm">
                    <form method="GET" action="{{ route('seller.reports.index') }}"
                        class="flex flex-col md:flex-row items-center gap-2">

                        <div class="grid grid-cols-2 gap-2 w-full md:w-auto flex-1">
                            <div class="relative">
                                <select name="month"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-none bg-slate-50 focus:ring-2 focus:ring-teal-500 font-bold text-slate-700 text-sm appearance-none">
                                    <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>Semua Bulan
                                    </option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}"
                                            {{ $selectedMonth != 'all' && $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="bi bi-calendar4-event absolute left-4 top-4 text-teal-600"></i>
                            </div>

                            <div class="relative">
                                <select name="year"
                                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-none bg-slate-50 focus:ring-2 focus:ring-teal-500 font-bold text-slate-700 text-sm appearance-none">
                                    @foreach ($yearRange as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-hash absolute left-4 top-4 text-teal-600"></i>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto p-1">
                            <button type="submit"
                                class="flex-1 md:flex-none rounded-2xl bg-teal-600 px-8 py-3.5 text-sm font-bold text-white hover:bg-teal-700 transition shadow-lg shadow-teal-100">
                                Filter Data
                            </button>
                            <a href="{{ route('seller.reports.index', ['month' => 'all', 'year' => date('Y')]) }}"
                                class="rounded-2xl bg-white border border-slate-200 px-4 py-3.5 text-slate-400 hover:text-rose-500 hover:border-rose-100 transition shadow-sm">
                                <i class="bi bi-arrow-counterclockwise text-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Stats Grid --}}
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Omzet --}}
                    <div
                        class="relative overflow-hidden bg-white rounded-3xl border border-slate-200/60 p-8 shadow-sm group">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-teal-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                        </div>
                        <div class="relative">
                            <div
                                class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-6">
                                <i class="bi bi-currency-dollar text-xl"></i>
                            </div>
                            <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Omzet Periode Ini
                            </p>
                            <p class="text-3xl font-black text-slate-800">
                                <span class="text-sm font-medium text-slate-400">Rp</span>
                                {{ number_format($totalRevenue, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Profit --}}
                    <div class="relative overflow-hidden bg-teal-600 rounded-3xl p-8 shadow-xl shadow-teal-100 group">
                        <div
                            class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-125 transition-transform">
                        </div>
                        <div class="relative">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center mb-6 backdrop-blur-md">
                                <i class="bi bi-graph-up-arrow text-xl"></i>
                            </div>
                            <p class="text-xs font-black uppercase tracking-widest text-teal-100 mb-1">Profit Bersih</p>
                            <p class="text-3xl font-black text-white">
                                <span class="text-sm font-medium text-teal-200">Rp</span>
                                {{ number_format($totalProfit, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Terjual --}}
                    <div
                        class="relative overflow-hidden bg-white rounded-3xl border border-slate-200/60 p-8 shadow-sm group">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                        </div>
                        <div class="relative">
                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6">
                                <i class="bi bi-box-seam text-xl"></i>
                            </div>
                            <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Volume Penjualan</p>
                            <p class="text-3xl font-black text-slate-800">
                                {{ $totalSold }} <span
                                    class="text-sm font-medium text-slate-400 uppercase tracking-tighter">Produk
                                    Terjual</span>
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Chart Section --}}
                <section class="bg-white rounded-[2rem] border border-slate-200/60 p-8 md:p-12 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Grafik Fluktuasi</h2>
                            <p class="text-sm text-slate-400 mt-1">
                                Visualisasi {{ $selectedMonth == 'all' ? 'bulanan' : 'harian' }} omzet terhadap profit.
                            </p>
                        </div>
                        <div class="flex items-center p-1 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-2 px-4 py-2">
                                <span class="w-3 h-3 rounded-full bg-teal-600"></span>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Profit</span>
                            </div>
                            <div class="flex items-center gap-2 px-4 py-2 border-l border-slate-200">
                                <span class="w-3 h-3 rounded-full bg-teal-200"></span>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Omzet</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-[450px] w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </section>

                {{-- All Time Summary --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-4">
                    <div class="bg-slate-900 rounded-[2rem] p-8 text-white flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Grand Total
                                Omzet</p>
                            <p class="text-2xl font-black tracking-tight italic">Rp
                                {{ number_format($grandTotalRevenue, 0, ',', '.') }}</p>
                        </div>
                        <i class="bi bi-layers text-4xl text-slate-700"></i>
                    </div>
                    <div
                        class="bg-white rounded-[2rem] border-2 border-dashed border-slate-200 p-8 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Grand Total
                                Profit</p>
                            <p class="text-2xl font-black tracking-tight text-teal-600 italic">Rp
                                {{ number_format($grandTotalProfit, 0, ',', '.') }}</p>
                        </div>
                        <i class="bi bi-trophy text-4xl text-slate-200"></i>
                    </div>
                </div>
            </div>

            {{-- Chart JS --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('salesChart');

                    window.myChart = new Chart(ctx, {
                        data: {
                            labels: @json($days),
                            datasets: [{
                                    type: 'line',
                                    label: 'Profit',
                                    data: @json($profits),
                                    borderColor: '#0f766e',
                                    backgroundColor: '#0f766e',
                                    borderWidth: 3,
                                    tension: .4,
                                    pointRadius: 4,
                                    fill: false
                                },
                                {
                                    type: 'bar',
                                    label: 'Omzet',
                                    data: @json($revenues),
                                    backgroundColor: 'rgba(15,118,110,.25)',
                                    borderRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false, // Wajib false agar capture instan
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        },
                        plugins: [{
                            beforeDraw: (chart) => {
                                const {
                                    ctx
                                } = chart;
                                ctx.save();
                                ctx.globalCompositeOperation = 'destination-over';
                                ctx.fillStyle = 'white';
                                ctx.fillRect(0, 0, chart.width, chart.height);
                                ctx.restore();
                            }
                        }]
                    });
                });

                // NAMA FUNGSI INI HARUS SAMA DENGAN ONCLICK DI TOMBOL
                function submitPdfWithChart() {
                    if (window.myChart) {
                        // 1. Ambil gambar dari chart
                        const chartBase64 = window.myChart.toBase64Image('image/png', 1);

                        // 2. Isi hidden input
                        document.getElementById('chart_image_input').value = chartBase64;

                        // 3. Submit form dengan ID yang benar (pdfDownloadForm)
                        document.getElementById('pdfDownloadForm').submit();
                    } else {
                        alert('Grafik belum siap, silakan tunggu sebentar.');
                    }
                }
            </script>
        </x-sidebar>
    @endsection
</x-app>
