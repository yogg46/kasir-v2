<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Shift - {{ $shift->toKasir->name }}</title>
    <style>
        @media print {
            @page {
                margin: 0.7cm;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 10px 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 11px;
            color: #333;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 5px;
        }

        th {
            background: #f3f3f3;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        td {
            border-bottom: 1px dashed #ccc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            border-top: 2px solid #000;
            background: #f9f9f9;
        }

        /* Tabel produk di bawah setiap invoice */
        .product-table {
            width: 96%;
            margin: 5px auto 8px auto;
            font-size: 11px;
            border-left: 2px solid #ccc;
        }

        .product-table td {
            padding: 3px 5px;
            border: none;
        }

        .product-table tr+tr td {
            border-top: 1px dotted #ddd;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 25px;
            font-size: 10px;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #1e40af;
        }

        .sale-items {
            margin: 2px 0 4px 8px;
            font-size: 11px;
            line-height: 1.4;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 10px;
        }

        .sale-items span {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Tombol Print --}}
        <div class="no-print" style="text-align: center; margin-bottom: 15px;">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Laporan</button>
        </div>

        {{-- Header --}}
        <div class="header">
            <h1>LAPORAN SHIFT KASIR</h1>
            <p>{{ $shift->toCabang->name ?? 'Toko' }}</p>
            <p>{{ $shift->toCabang->address ?? '' }}</p>
        </div>

        {{-- Informasi Shift --}}
        <div class="section">
            <table>
                <tr>
                    <td width="30%">Kasir</td>
                    <td>: {{ $shift->toKasir->name }}</td>
                </tr>
                <tr>
                    <td>Shift Mulai</td>
                    <td>: {{ $shift->shift_start->format('d F Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Shift Selesai</td>
                    <td>: {{ $shift->shift_end ? $shift->shift_end->format('d F Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Ringkasan Kas --}}
        <div class="section">
            <div class="section-title">Ringkasan Kas</div>
            <table>
                <tr>
                    <td>Kas Awal</td>
                    <td class="text-right">Rp {{ number_format($shift->initial_cash, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Kas Masuk (Penjualan)</td>
                    <td class="text-right">Rp {{ number_format($shift->cash_in, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Kas Keluar (Pengeluaran)</td>
                    <td class="text-right">Rp {{ number_format($shift->cash_out, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Kas Akhir</td>
                    <td class="text-right">Rp {{ number_format($shift->final_cash, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Detail Penjualan --}}
        <div class="section">
            <div class="section-title">Detail Penjualan</div>
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Waktu</th>
                        <th>Metode</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->created_at->format('H:i') }}</td>
                        <td>{{ strtoupper($sale->payment_method) }}</td>
                        <td class="text-right">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    </tr>

                    @if(isset($sale->toItems) && $sale->toItems->count())
                    <tr>
                        <td class="sales-item" colspan="4">
                            @foreach($sale->toItems as $item)
                            <span style="display:inline-block; min-width:160px; margin-right:8px;">
                                {{ $item->productName }} ({{ $item->quantity }}x) -
                                {{-- Rp {{ number_format($item->subtotal, 0, ',', '.') }} --}}
                            </span>
                            @endforeach
                        </td>

                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3">Total Penjualan</td>
                        <td class="text-right">Rp {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }}<br>
            <span style="display: block; margin-top: 6px;">Terima kasih</span>
        </div>
    </div>
</body>

</html>
