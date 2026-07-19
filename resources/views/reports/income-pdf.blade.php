<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 11px; 
            color: #0B3558; 
            margin: 20px;
        }
        .kop-table { 
            width: 100%; 
            border-bottom: 2px solid #0B3558; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
        }
        .kop-logo { 
            font-size: 20px; 
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
        .report-header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .report-title { 
            font-size: 15px; 
            font-weight: bold; 
            color: #006BFF; 
            margin: 0 0 5px 0; 
            text-transform: uppercase; 
        }
        .report-period { 
            font-size: 10px; 
            color: #476788; 
            margin: 0; 
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        .data-table th { 
            background-color: #0B3558; 
            color: #FFFFFF; 
            font-weight: bold; 
            padding: 8px; 
            border: 1px solid #D4E0ED; 
        }
        .data-table td { 
            padding: 8px; 
            border: 1px solid #D4E0ED; 
        }
        .data-table tr:nth-child(even) { 
            background-color: #F8F9FB; 
        }
        .total-row { 
            font-weight: bold; 
            background-color: #E7EDF6 !important; 
            color: #006BFF; 
        }
        .footer { 
            margin-top: 30px; 
            text-align: right; 
            font-size: 9px; 
            color: #A6BBD1; 
            border-top: 1px solid #E7EDF6; 
            padding-top: 10px; 
        }
    </style>
</head>
<body>
    @php
        // $profile is injected by the controller — no extra DB query needed here
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
                <strong>WhatsApp:</strong> {{ $whatsappNum }} | <strong>Email:</strong> {{ $profile->email }}
            </td>
        </tr>
    </table>

    <div class="report-header">
        <h2 class="report-title">LAPORAN PENDAPATAN</h2>
        <p class="report-period">Periode: 
            <strong>
                @if($month)
                    {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }}
                @endif
                {{ $year }}
            </strong>
        </p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 13%; text-align: center;">Tanggal</th>
                <th style="width: 18%;">No. Invoice</th>
                <th style="width: 18%;">Customer</th>
                <th style="width: 13%; text-align: center;">Metode</th>
                <th style="width: 11%; text-align: center;">Tipe</th>
                <th style="width: 10%; text-align: center;">Status</th>
                <th style="width: 12%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->payment_date->format('d/m/Y') }}</td>
                <td style="font-weight: bold;">{{ $item->transaction->invoice_number ?? '-' }}</td>
                <td>{{ $item->transaction->booking->customer->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->paymentMethod->name ?? '-' }}</td>
                <td style="text-align: center; text-transform: uppercase;">{{ $item->payment_type }}</td>
                <td style="text-align: center; text-transform: uppercase;">{{ $item->status }}</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @php $total += $item->amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" style="text-align: right; padding: 8px;">TOTAL PENDAPATAN</td>
                <td style="text-align: right; padding: 8px;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | {{ $profile->company_name ?? 'PO Boneh Star' }}
    </div>
</body>
</html>
