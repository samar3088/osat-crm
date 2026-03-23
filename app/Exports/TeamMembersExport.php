<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamMembersExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        $query = User::role('team_member')
            ->with('assignedTo')
            ->withCount('clients')
            ->latest();

        if (!empty($this->filters['filter_status'])) {
            $active = $this->filters['filter_status'] === 'Active' ? 1 : 0;
            $query->where('is_active', $active);
        }
        if (!empty($this->filters['filter_worktype'])) {
            $query->where('work_type', $this->filters['filter_worktype']);
        }
        if (!empty($this->filters['filter_assigned'])) {
            $query->whereHas('assignedTo', fn($q) =>
                $q->where('name', 'like', "%{$this->filters['filter_assigned']}%")
            );
        }

        return $query->get()->map(fn($m, $i) => [
            'sno'           => $i + 1,
            'name'          => $m->name,
            'email'         => $m->email,
            'employee_code' => $m->employee_code ?? '—',
            'work_type'     => $m->work_type,
            'assigned_to'   => $m->assignedTo?->name ?? '—',
            'clients'       => $m->clients_count,
            'status'        => $m->is_active ? 'Active' : 'Inactive',
            'created_at'    => $m->created_at->format('d M Y'),
        ]);
    }

    public function headings(): array
    {
        return ['S.No', 'Name', 'Email', 'Employee Code', 'Work Type', 'Assigned To', 'Clients', 'Status', 'Created'];
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
        return ['A' => 8, 'B' => 25, 'C' => 30, 'D' => 15, 'E' => 15, 'F' => 20, 'G' => 10, 'H' => 12, 'I' => 15];
    }
}