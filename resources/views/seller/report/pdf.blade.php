<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan Miimoys</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .chart-section {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .chart-section img {
            width: 100%;
            max-height: 350px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
        }

        th {
            background-color: #0d9488;
            color: white;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            word-wrap: break-word;
            vertical-align: top;
        }

        .product-item {
            margin-bottom: 3px;
            color: #555;
            font-size: 10px;
        }

        .product-qty {
            font-weight: bold;
            color: #0d9488;
        }

        .stats-container {
            margin-top: 20px;
            width: 100%;
            display: block;
            clear: both;
        }

        .top-products {
            width: 50%;
            float: left;
        }

        .summary-box {
            width: 40%;
            float: right;
            padding: 12px;
            background-color: #f0fdfa;
            border: 1px solid #0d9488;
            border-radius: 8px;
        }

        .summary-table td {
            border: none;
            padding: 4px 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0d9488;
            border-bottom: 1px solid #0d9488;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            padding-bottom: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">MIIMOYS E-BOOKS</div>
        <div style="font-weight: bold; font-size: 14px;">LAPORAN PENJUALAN SELLER</div>
        <div style="font-size: 10px; color: #666; margin-top: 5px;">
            Periode: {{ $periode }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Tabel Transaksi Utama --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">ID Order</th>
                <th style="width: 35%;">Rincian Produk</th>
                <th style="width: 20%;">Pembeli</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $orderId => $items)
                @php
                    $firstItem = $items->first();
                    $orderData = $firstItem->order;
                    $orderSubtotal = $items->sum(fn($i) => $i->price * $i->qty);
                @endphp
                <tr>
                    <td><strong>#ORD-{{ $orderId }}</strong></td>
                    <td>
                        @foreach ($items as $item)
                            <div class="product-item">
                                • {{ $item->book->title }}
                                <span class="product-qty">({{ $item->qty }}x)</span>
                            </div>
                        @endforeach
                    </td>
                    <td>{{ $orderData->user->name }}</td>
                    <td>{{ $orderData->created_at->format('d/m/Y') }}</td>
                    <td style="font-weight: bold;">
                        Rp {{ number_format($orderSubtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- BAGIAN GRAFIK --}}
    @if ($chartImage)
        <div class="chart-section">
            <div class="section-title">VISUALISASI TREN PENJUALAN</div>
            <img src="{{ $chartImage }}" style="width: 100%; height: auto;">
        </div>
    @endif

    <div class="stats-container">
        {{-- Section: Top 5 Produk --}}
        <div class="top-products">
            <div class="section-title">TOP 5 PRODUK TERLARIS</div>
            <table style="margin-top: 5px;">
                @php
                    $productSummary = $orders
                        ->flatten()
                        ->groupBy('book_id')
                        ->map(function ($items) {
                            return [
                                'title' => $items->first()->book->title,
                                'total_qty' => $items->sum('qty'),
                            ];
                        })
                        ->sortByDesc('total_qty')
                        ->take(5);
                @endphp
                @foreach ($productSummary as $summary)
                    <tr>
                        <td style="padding: 4px 0; border-bottom: 1px dashed #ddd;">{{ $summary['title'] }}</td>
                        <td
                            style="padding: 4px 0; border-bottom: 1px dashed #ddd; text-align: right; font-weight: bold;">
                            {{ $summary['total_qty'] }} Pcs
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Section: Ringkasan --}}
        <div class="summary-box">
            <div class="section-title">RINGKASAN KEUANGAN</div>
            <table class="summary-table">
                <tr>
                    <td>Total Pesanan</td>
                    <td style="text-align: right;">{{ $orders->count() }} Order</td>
                </tr>
                <tr>
                    <td>Total Omzet</td>
                    <td style="text-align: right;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-size: 13px; font-weight: bold; color: #059669;">
                    <td style="padding-top: 8px;">PROFIT BERSIH</td>
                    <td style="padding-top: 8px; text-align: right;">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem akuntansi Miimoys E-Books.<br>
        Laporan ini hanya mencakup data penjualan untuk seller yang bersangkutan.
    </div>
</body>

</html>
