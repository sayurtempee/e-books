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
            margin-bottom: 30px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th {
            background-color: #0d9488;
            color: white;
            padding: 10px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            word-wrap: break-word;
            vertical-align: top;
        }

        .product-item {
            margin-bottom: 4px;
            color: #555;
            font-size: 10px;
        }

        .product-qty {
            font-weight: bold;
            color: #0d9488;
        }

        .stats-container {
            margin-top: 30px;
            width: 100%;
        }

        .top-products {
            width: 55%;
            float: left;
        }

        .summary-box {
            width: 35%;
            float: right;
            padding: 15px;
            background-color: #f0fdfa;
            border: 1px solid #0d9488;
            border-radius: 8px;
        }

        .summary-table {
            margin-top: 0;
            width: 100%;
        }

        .summary-table td {
            border: none;
            padding: 5px 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0d9488;
            border-bottom: 1px solid #0d9488;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            padding: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">MIIMOYS E-BOOKS</div>
        <div style="font-weight: bold; font-size: 14px;">LAPORAN PENJUALAN SELLER</div>
        <div style="font-size: 10px; color: #666; margin-top: 5px;">
            Periode: {{ now()->translatedFormat('F Y') }} |
            Dicetak: {{ now()->format('d/m/Y H:i') }}
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

    <div class="stats-container">
        {{-- Section: Top 5 Produk Terlaris --}}
        <div class="top-products">
            <div class="section-title">TOP 5 PRODUK TERLARIS ANDA</div>
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
                        <td style="padding: 5px 0; border-bottom: 1px dashed #ddd;">{{ $summary['title'] }}</td>
                        <td
                            style="padding: 5px 0; border-bottom: 1px dashed #ddd; text-align: right; font-weight: bold;">
                            {{ $summary['total_qty'] }} Pcs
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Section: Ringkasan Keuangan --}}
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
                    <td style="padding-top: 10px;">PROFIT BERSIH</td>
                    <td style="padding-top: 10px; text-align: right;">
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
