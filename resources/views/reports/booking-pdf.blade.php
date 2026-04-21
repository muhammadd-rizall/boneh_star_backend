<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemesanan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMESANAN BUS</h2>
        <p>Periode: {{ $start_date }} s/d {{ $end_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Customer</th>
                <th>Asal</th>
                <th>Keberangkatan</th>
                <th>Jumlah Bus</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->booking_code }}</td>
                <td>{{ $item->customer->name ?? '-' }}</td>
                <td>{{ $item->origin }}</td>
                <td>{{ $item->departure_date->format('d/m/Y H:i') }}</td>
                <td>{{ $item->bus_count_requested }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
