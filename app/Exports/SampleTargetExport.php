<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SampleTargetExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['EMP-001', 'Monthly', '1', 'Equity', 'SIP', '10', '500000'],
            ['EMP-002', 'Monthly', '1', 'Debt', 'Lumpsum', '5', '1000000'],
            ['EMP-003', 'Quarterly', '1', 'Hybrid', 'SIP', '8', '750000'],
        ];
    }

    public function headings(): array
    {
        return [
            'Employee Code',
            'Frequency (Monthly/Quarterly/Half Yearly/Annual)',
            'Month (1-12)',
            'Category Type (Equity/Debt/Hybrid/Blended)',
            'Target Type (SIP/Lumpsum)',
            'Target Clients',
            'Target Amount',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0e6099']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 45, 'C' => 15,
            'D' => 35, 'E' => 25, 'F' => 15, 'G' => 15,
        ];
    }
}