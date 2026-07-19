<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IncomeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
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
            'Customer',
            'Metode Pembayaran',
            'Tipe',
            'Status',
            'Jumlah',
        ];
    }

    public function map($payment): array
    {
        // Resolve payment method name safely
        $methodName = '-';
        if ($payment->paymentMethod) {
            $methodName = $payment->paymentMethod->name ?? '-';
        } elseif ($payment->payment_method_id) {
            // Fallback: load directly if relation wasn't eager-loaded
            $method = \App\Models\PaymentMethod::find($payment->payment_method_id);
            $methodName = $method->name ?? '-';
        }

        return [
            $payment->payment_date->format('d/m/Y'),
            $payment->transaction->invoice_number ?? '-',
            $payment->transaction->booking->customer->name ?? '-',
            $methodName,
            strtoupper($payment->payment_type),
            strtoupper($payment->status),
            (float) $payment->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '"Rp"#,##0', // Kolom G = Jumlah
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;

                // Label TOTAL (merge A–F)
                $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL PENDAPATAN');

                // SUM kolom G (Jumlah)
                $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$lastRow})");

                // Format kolom G total sama seperti data
                $sheet->getStyle("G{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp"#,##0');

                // Style baris TOTAL — warna gelap navy (sama dengan header, tidak menyakiti mata)
                $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 11,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0B3558'], // Navy — sama dengan header
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '0B3558'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Jumlah rata kanan
                $sheet->getStyle("G{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getRowDimension($totalRow)->setRowHeight(24);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style Header Row (A1:G1)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B3558'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style Data Rows
        if ($lastRow > 1) {
            $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D4E0ED'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal
            $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Invoice
            $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Customer
            $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Metode
            $sheet->getStyle('E2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tipe & Status
            $sheet->getStyle('G2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);  // Jumlah
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        return [];
    }
}
