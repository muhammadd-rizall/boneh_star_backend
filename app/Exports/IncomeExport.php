<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pembayaran',
            'No. Invoice',
            'Metode Pembayaran',
            'Jumlah',
            'Tipe',
            'Status',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->payment_date->format('d/m/Y'),
            $payment->transaction->invoice_number ?? '-',
            $payment->paymentMethod->name ?? '-',
            number_format($payment->amount, 2, ',', '.'),
            $payment->payment_type,
            $payment->status,
        ];
    }
}
