<x-app>
    @section('title', 'Laporan Penjualan')

    @section('body-content')
        <x-sidebar>
            <div class="p-8 bg-gray-50 min-h-screen">

                {{-- Header & Download Button --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 tracking-tight">Laporan Penjualan</h1>
                        <p class="text-gray-500 text-sm">Analisis performa penjualan buku Anda secara real-time.</p>
                    </div>

                    {{-- Button Download Laporan --}}
                    <a href="{{ route('seller.reports.download') }}"
                        class="flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-teal-200 transition-all transform hover:-translate-y-1">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        Download Laporan (PDF)
                    </a>
                </div>

                {{-- Statistik Ringkas (Cards) --}}
                @php
                    // Ambil data pesanan yang sudah sukses (Approved/Shipping)
                    $orders = \App\Models\Order::whereIn('status', ['approved', 'shipping'])
                        ->with('items')
                        ->get();
                    $totalRevenue = $orders->sum('total_price');
                    $totalProfit = $orders->flatMap->items->sum('profit');
                    $totalSold = $orders->flatMap->items->sum('qty');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-currency-dollar text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Total Omzet</p>
                        <h3 class="text-2xl font-black text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
                        <div
                            class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-graph-up-arrow text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Total Keuntungan</p>
                        <h3 class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </h3>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-book text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Buku Terjual</p>
                        <h3 class="text-2xl font-black text-gray-800">{{ $totalSold }} <span
                                class="text-sm font-normal text-gray-400">Pcs</span></h3>
                    </div>
                </div>

                {{-- Grafik Statistik --}}
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Grafik Penjualan Mingguan</h2>
                        <span class="text-xs bg-gray-100 px-3 py-1 rounded-full text-gray-500 font-medium">Status: Approved
                            Only</span>
                    </div>
                    <div class="h-[400px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Script Chart.js --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('salesChart').getContext('2d');

                    // Data Dummy (Nanti bisa diganti dengan data dari Controller)
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($days) !!}, // Mengambil array hari dari Controller
                            datasets: [{
                                label: 'Keuntungan (Rp)',
                                data: {!! json_encode($profits) !!}, // Mengambil array profit dari Controller
                                borderColor: '#0d9488',
                                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                                borderWidth: 4,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#0d9488'
                            }]
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
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </x-sidebar>
    @endsection
</x-app>
