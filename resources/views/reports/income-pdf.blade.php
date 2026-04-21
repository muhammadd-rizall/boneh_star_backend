<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
        .total { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENDAPATAN</h2>
        <p>Periode: {{ $month ? date('F', mktime(0, 0, 0, $month, 10)) : '' }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Metode</th>
                <th>Tipe</th>
                <th>Status</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->payment_date->format('d/m/Y') }}</td>
                <td>{{ $item->transaction->invoice_number ?? '-' }}</td>
                <td>{{ $item->paymentMethod->name ?? '-' }}</td>
                <td>{{ $item->payment_type }}</td>
                <td>{{ $item->status }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @php $total += $item->amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="6" style="text-align: right;">TOTAL PENDAPATAN</td>
                <td style="text-align: right;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
