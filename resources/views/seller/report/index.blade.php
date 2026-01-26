<x-app>
    @section('title', 'Laporan Penjualan')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">

                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 tracking-tight">
                            Laporan Penjualan
                        </h1>
                        <p class="text-gray-500 text-sm">
                            Analisis performa penjualan buku Anda secara real-time.
                        </p>
                    </div>

                    <a href="{{ route('seller.reports.download') }}"
                        class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold shadow">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        Download Laporan (PDF)
                    </a>
                </div>

                {{-- Statistik --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                    {{-- Omzet --}}
                    <div class="bg-white p-6 rounded-2xl border shadow-sm">
                        <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Omzet</p>
                        <h3 class="text-2xl font-black">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h3>
                    </div>

                    {{-- Profit --}}
                    <div class="bg-white p-6 rounded-2xl border shadow-sm border-l-4 border-emerald-500">
                        <div
                            class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Keuntungan</p>
                        <h3 class="text-2xl font-black text-emerald-600">
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </h3>
                    </div>

                    {{-- Terjual --}}
                    <div class="bg-white p-6 rounded-2xl border shadow-sm">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-book"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Buku Terjual</p>
                        <h3 class="text-2xl font-black">
                            {{ $totalSold }}
                            <span class="text-sm font-normal text-gray-400">Pcs</span>
                        </h3>
                    </div>

                </div>

                {{-- Grafik --}}
                <div class="bg-white p-8 rounded-2xl border shadow-sm">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-xl font-bold">Grafik Penjualan Mingguan</h2>
                        <span class="text-xs bg-gray-100 px-3 py-1 rounded-full text-gray-500">
                            Status: Approved / Shipping
                        </span>
                    </div>

                    <div class="h-[400px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Chart --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: @json($days),
                        datasets: [{
                            label: 'Profit',
                            data: @json($profits),
                            borderColor: '#0d9488',
                            backgroundColor: 'rgba(13,148,136,.15)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + v.toLocaleString()
                                }
                            }
                        }
                    }
                });
            </script>

        </x-sidebar>
    @endsection
</x-app>
