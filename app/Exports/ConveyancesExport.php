<?php

namespace App\Exports;

use App\Models\Conveyance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConveyancesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private User  $user,
        private array $filters = []
    ) {}

    public function collection()
    {
        $query = Conveyance::with(['user', 'actionedBy'])->latest();

        // Role scoping
        if (!$this->user->isSuperAdmin()) {
            $query->where('user_id', $this->user->id);
        }

        // Filters
        if (!empty($this->filters['filter_status'])) {
            $query->where('status', $this->filters['filter_status']);
        }
        if (!empty($this->filters['filter_type'])) {
            $query->where('conveyance_type', $this->filters['filter_type']);
        }
        if (!empty($this->filters['filter_member']) && $this->user->isSuperAdmin()) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->filters['filter_member']}%")
            );
        }

        return $query->get()->map(fn($c, $i) => [
            'sno'             => $i + 1,
            'team_member'     => $c->user->name ?? '—',
            'type'            => $c->conveyance_type,
            'date'            => $c->conveyance_date->format('d M Y'),
            'amount'          => '₹' . number_format($c->amount, 2),
            'remarks'         => $c->remarks ?? '—',
            'status'          => ucfirst($c->status),
            'action_remarks'  => $c->action_remarks ?? '—',
            'actioned_by'     => $c->actionedBy?->name ?? '—',
            'created_at'      => $c->created_at->format('d M Y'),
        ]);
    }

    public function headings(): array
    {
        return [
            'S.No', 'Team Member', 'Type', 'Date',
            'Amount', 'Remarks', 'Status',
            'Action Remarks', 'Actioned By', 'Created'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0e6099']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  'B' => 20, 'C' => 15,
            'D' => 15, 'E' => 15, 'F' => 25,
            'G' => 12, 'H' => 25, 'I' => 20, 'J' => 15,
        ];
    }
}