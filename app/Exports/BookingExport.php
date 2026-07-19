<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BookingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $bookings;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    public function collection()
    {
        return $this->bookings;
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Customer',
            'Asal',
            'Keberangkatan',
            'Kepulangan',
            'Jumlah Penumpang',
            'Jumlah Bus',
            'Status',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->booking_code,
            $booking->customer->name ?? '-',
            $booking->origin,
            $booking->departure_date->format('d/m/Y H:i'),
            $booking->return_date ? $booking->return_date->format('d/m/Y H:i') : '-',
            $booking->passenger_count,
            $booking->bus_count_requested,
            $booking->status,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $sheet->getHighestRow();
                $totalRow = $lastRow + 1;

                // Label TOTAL (merge kolom A–E)
                $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');

                // Rumus SUM
                $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$lastRow})"); // Total Penumpang
                $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$lastRow})"); // Total Bus
                $sheet->setCellValue("H{$totalRow}", $this->bookings->count() . ' Booking');

                // Style baris TOTAL
                $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
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

                $sheet->getRowDimension($totalRow)->setRowHeight(24);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style Header Row (A1:H1)
        $sheet->getStyle('A1:H1')->applyFromArray([
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

        // Style Data Rows (A2:H{lastRow})
        if ($lastRow > 1) {
            $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
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

            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        return [];
    }
}
