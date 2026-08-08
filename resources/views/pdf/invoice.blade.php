<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $transaction->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.8cm 1cm 0.8cm 1cm;
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            line-height: 1.3; 
            color: #0B3558; 
            margin: 0; 
            padding: 0; 
        }
        
        /* Kop Surat (Letterhead) */
        .kop-table { 
            width: 100%; 
            border-bottom: 3px double #0B3558; 
            padding-bottom: 8px; 
            margin-bottom: 12px; 
        }
        .kop-logo { 
            font-size: 22px; 
            font-weight: bold; 
            color: #0B3558; 
            line-height: 1.1;
        }
        .kop-details { 
            text-align: right; 
            font-size: 9px; 
            color: #476788; 
            line-height: 1.3;
        }
        
        .title-table { 
            width: 100%; 
            margin-bottom: 12px; 
        }
        .invoice-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #006BFF; 
        }
        .invoice-meta { 
            text-align: right; 
            font-size: 10px; 
        }
        
        .info-table { 
            width: 100%; 
            margin-bottom: 12px; 
        }
        .info-cell { 
            width: 50%; 
            vertical-align: top; 
            font-size: 10px; 
        }
        .info-title { 
            font-weight: bold; 
            color: #0B3558; 
            margin-bottom: 4px; 
            border-bottom: 1px solid #D4E0ED; 
            padding-bottom: 2px; 
            width: 90%; 
        }
        
        /* Items Table */
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
            font-size: 10px; 
        }
        .items-table th { 
            background-color: #0B3558; 
            color: #FFFFFF; 
            font-weight: bold; 
            padding: 6px 8px; 
            text-align: left; 
        }
        .items-table td { 
            padding: 6px 8px; 
            border-bottom: 1px solid #D4E0ED; 
            vertical-align: top; 
        }
        .items-table tr:nth-child(even) { 
            background-color: #F8F9FB; 
        }
        
        /* Totals */
        .totals-table { 
            width: 100%; 
            margin-bottom: 15px; 
        }
        .totals-label { 
            text-align: right; 
            font-weight: bold; 
            padding: 3px 10px; 
            font-size: 10px; 
        }
        .totals-value { 
            text-align: right; 
            padding: 3px 8px; 
            font-size: 10px; 
            font-weight: bold; 
            color: #0B3558; 
            width: 130px; 
            border-bottom: 1px solid #D4E0ED; 
        }
        
        /* Payment info & Footer */
        .payment-info { 
            background-color: #F8F9FB; 
            border-left: 4px solid #006BFF; 
            padding: 8px 12px; 
            font-size: 9px; 
            margin-bottom: 12px; 
        }
        .payment-info h4 { 
            margin-top: 0; 
            margin-bottom: 4px; 
            color: #0B3558; 
        }
        .payment-method-item { 
            margin-bottom: 2px; 
        }
        
        .footer { 
            margin-top: 15px; 
            text-align: center; 
            font-size: 8px; 
            color: #A6BBD1; 
            border-top: 1px solid #E7EDF6; 
            padding-top: 4px; 
        }
        
        /* Status Badge */
        .status-badge { 
            display: inline-block; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-size: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .status-paid { 
            background-color: #E7EDF6; 
            color: #004EBA; 
        }
        .status-pending { 
            background-color: #FFF3CD; 
            color: #856404; 
        }
        .status-cancelled { 
            background-color: #F8D7DA; 
            color: #721C24; 
        }
    </style>
</head>
<body>
    @php
        // $profile is injected by HistoryController — no extra DB query inside the view
        if (!$profile) {
            $profile = (object) [
                'company_name' => 'PO BONEH STAR',
                'address'      => 'Jl. Raya Utama No. 123, Jakarta',
                'whatsapp'     => [],
                'email'        => 'info@bonehstar.com',
                'logo'         => null,
            ];
        }

        $whatsappNum = '-';
        if (!empty($profile->whatsapp)) {
            $whatsappData = is_string($profile->whatsapp) ? json_decode($profile->whatsapp, true) : $profile->whatsapp;
            if (is_array($whatsappData)) {
                $numbers = [];
                foreach ($whatsappData as $item) {
                    if (is_array($item))        { $numbers[] = $item['number'] ?? ($item['label'] ?? ''); }
                    elseif (is_object($item))   { $numbers[] = $item->number  ?? ($item->label  ?? ''); }
                    elseif (is_string($item))   { $numbers[] = $item; }
                }
                $numbers = array_filter(array_map('trim', $numbers));
                if (count($numbers) > 0) { $whatsappNum = implode(', ', $numbers); }
            } elseif (is_string($profile->whatsapp)) {
                $whatsappNum = trim($profile->whatsapp);
            }
        }

        // Resolve logo to absolute local path (DomPDF cannot fetch remote URLs)
        $logoSrc = null;
        if (!empty($profile->logo)) {
            $clean = ltrim(preg_replace('#^/?storage/#', '', $profile->logo), '/');
            foreach ([
                public_path('storage/' . $clean),
                storage_path('app/public/' . $clean),
            ] as $candidate) {
                if (file_exists($candidate)) { $logoSrc = $candidate; break; }
            }
        }
    @endphp

    <div class="invoice-box">
        <!-- Kop Surat -->
        <table class="kop-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                @if($logoSrc)
                    <td style="width: 70px; vertical-align: middle; padding-right: 12px;">
                        <img src="{{ $logoSrc }}" style="max-height: 50px; max-width: 70px; object-fit: contain;">
                    </td>
                @endif
                <td style="vertical-align: middle; text-align: left;">
                    <span class="kop-logo">{{ strtoupper($profile->company_name ?? 'PO BONEH STAR') }}</span><br>
                    <span style="font-size: 10px; color: #476788; font-weight: normal; letter-spacing: 0.5px;">Jasa Penyewaan Bus Pariwisata Premium</span>
                </td>
                <td class="kop-details" style="vertical-align: middle; text-align: right;">
                    <strong>Kantor Pusat:</strong> {{ $profile->address }}<br>
                    <strong>WhatsApp:</strong> {{ $whatsappNum }}<br>
                    <strong>Email:</strong> {{ $profile->email }}
                </td>
            </tr>
        </table>

        <!-- Invoice Title -->
        <table class="title-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <span class="invoice-title">INVOICE</span>
                </td>
                <td class="invoice-meta">
                    <strong>Nomor:</strong> {{ $transaction->invoice_number }}<br>
                    <strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        <!-- Info Table (Customer vs Company) -->
        <table class="info-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="info-cell">
                    <div class="info-title">DITAGIHKAN KEPADA:</div>
                    <strong>{{ $customer->name }}</strong><br>
                    {{ $customer->email }}<br>
                    {{ $customer->phone }}<br>
                    {{ $customer->address }}
                </td>
                <td class="info-cell">
                    <div class="info-title">PENGIRIM:</div>
                    <strong>{{ $profile->company_name ?? 'PO Boneh Star' }}</strong><br>
                    Layanan Sewa Bus Pariwisata<br>
                    Tipe Armada: Big Bus, Medium Bus<br>
                    Rute Dalam & Luar Kota
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Deskripsi Layanan</th>
                    <th style="width: 30%;">Detail Perjalanan</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Penyewaan Bus Pariwisata</strong><br>
                        Tipe: Sewa armada pariwisata premium ({{ $booking->bus_count_requested }} Unit)<br>
                        Aktivitas: {{ $booking->activity->name }}
                    </td>
                    <td>
                        <strong>Rute:</strong> {{ $booking->origin }} - {{ $booking->full_route }}<br>
                        <strong>Keberangkatan:</strong> {{ $booking->departure_date->format('d/m/Y') }}<br>
                        <strong>Kepulangan:</strong> {{ $booking->return_date ? $booking->return_date->format('d/m/Y') : '1 Hari Perjalanan' }}
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totals Table -->
        <table class="totals-table" align="right" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="totals-label">Total Tagihan:</td>
                <td class="totals-value">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="totals-label">Uang Muka (DP) Paid:</td>
                <td class="totals-value">Rp {{ number_format($transaction->dp_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="totals-label">Sisa Pembayaran:</td>
                <td class="totals-value" style="color: #006BFF;">Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="totals-label">Status Tagihan:</td>
                <td class="totals-value">
                    @if(strtolower($transaction->status) === 'paid' || strtolower($transaction->status) === 'verified' || strtolower($transaction->status) === 'completed')
                        <span class="status-badge status-paid">{{ $transaction->status }}</span>
                    @elseif(strtolower($transaction->status) === 'cancelled')
                        <span class="status-badge status-cancelled">{{ $transaction->status }}</span>
                    @else
                        <span class="status-badge status-pending">{{ $transaction->status }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <!-- Payment Methods -->
        <div class="payment-info">
            <h4>METODE PEMBAYARAN:</h4>
            <p style="margin-bottom: 8px;">Silakan melakukan transfer ke salah satu rekening bank resmi kami di bawah ini:</p>
            @foreach($payment_methods as $method)
                <div class="payment-method-item">
                    <strong>{{ $method->bank_name }}</strong>: {{ $method->account_number }} a.n {{ $method->account_name }}
                </div>
            @endforeach
            <p style="margin-top: 10px; font-size: 9px; color: #476788; font-style: italic;">* Harap unggah bukti transfer di portal pelanggan setelah melakukan pembayaran untuk proses verifikasi cepat.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ $profile->company_name ?? 'PO Boneh Star' }} - Jasa Penyewaan Bus Pariwisata Premium | &copy; {{ date('Y') }} All rights reserved.
        </div>
    </div>
</body>
</html>
