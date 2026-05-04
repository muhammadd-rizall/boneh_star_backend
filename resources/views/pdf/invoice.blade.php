<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .company-info { text-align: right; }
        .customer-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; border: 1px solid #eee; text-align: left; }
        table th { background-color: #f9f9f9; }
        .total-box { text-align: right; }
        .payment-methods { margin-top: 30px; border-top: 2px solid #eee; padding-top: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p><strong>Nomor:</strong> {{ $transaction->invoice_number }}</p>
                <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="company-info">
                <h2>Boneh Star</h2>
                <p>Jasa Penyewaan Bus Pariwisata</p>
            </div>
        </div>

        <div class="customer-info">
            <h3>Tagihan Kepada:</h3>
            <p><strong>{{ $customer->name }}</strong></p>
            <p>{{ $customer->email }}</p>
            <p>{{ $customer->phone }}</p>
            <p>{{ $customer->address }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Detail Perjalanan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Penyewaan Bus ({{ $booking->bus_count_requested }} Unit)</td>
                    <td>
                        {{ $booking->activity->name }}<br>
                        {{ $booking->origin }} - {{ $booking->full_route }}<br>
                        {{ $booking->departure_date->format('d/m/Y') }} s/d {{ $booking->return_date->format('d/m/Y') }}
                    </td>
                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <p><strong>Total Tagihan:</strong> Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
            <p><strong>DP Dibayar:</strong> Rp {{ number_format($transaction->dp_paid, 0, ',', '.') }}</p>
            <p><strong>Sisa Pembayaran:</strong> Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ strtoupper($transaction->status) }}</p>
        </div>

        <div class="payment-methods">
            <h3>Metode Pembayaran Transfer Bank:</h3>
            @foreach($payment_methods as $method)
                <p>{{ $method->bank_name }}: {{ $method->account_number }} a.n {{ $method->account_name }}</p>
            @endforeach
        </div>

        <div class="footer">
            <p>Terima kasih telah memilih Boneh Star untuk perjalanan Anda.</p>
        </div>
    </div>
</body>
</html>
