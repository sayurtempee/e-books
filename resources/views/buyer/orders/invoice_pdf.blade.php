<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #ORD-{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.4;
        }

        .invoice-box {
            border: 1px solid #eee;
            padding: 30px;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #0d9488;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .title {
            color: #0d9488;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        /* Status Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #f59e0b;
        }

        /* Kuning/Amber */
        .status-approved {
            background-color: #10b981;
        }

        /* Hijau */
        .status-shipping {
            background-color: #3b82f6;
        }

        /* Biru */
        .status-refunded {
            background-color: #ef4444;
        }

        /* Merah */

        /* Tables */
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 10px;
        }

        .total-box {
            margin-top: 20px;
            text-align: right;
        }

        .total-amount {
            font-size: 20px;
            color: #0d9488;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .tracking-box {
            margin-top: 15px;
            padding: 10px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">INVOICE</h1>
                    <p style="margin: 5px 0;">ID Pesanan: <strong>#ORD-{{ $order->id }}</strong></p>
                    <p style="margin: 0;">Waktu Transaksi: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <span class="badge status-{{ $order->status }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td>
                    <strong style="color: #0d9488;">DITERBITKAN UNTUK:</strong><br>
                    <strong>{{ $order->user->name }}</strong><br>
                    {{ $order->user->email }}
                </td>
                <td>
                    <strong style="color: #0d9488;">ALAMAT PENGIRIMAN:</strong><br>
                    {{ $order->user->address }}
                </td>
            </tr>
        </table>

        <p><strong>Metode Pembayaran:</strong> {{ strtoupper($order->payment_method) }}</p>

        @if ($order->status == 'shipping' && $order->tracking_number)
            <div class="tracking-box">
                <strong>Nomor Resi Pengiriman:</strong> {{ $order->tracking_number }}
            </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th>Judul Buku</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->book->title ?? 'Buku dihapus' }}</td>
                        <td style="text-align: center;">{{ $item->qty }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <p style="margin-bottom: 5px;">Total Tagihan:</p>
            <div class="total-amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Bookstore!</p>
            <p>Invoice ini dihasilkan secara otomatis dan sah tanpa tanda tangan basah.</p>
        </div>
    </div>
</body>

</html>
