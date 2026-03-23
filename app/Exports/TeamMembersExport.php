<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamMembersExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithChunkReading
{
    public function __construct(private array $filters = []) {}

    public function chunkSize(): int { return 500; }

    public function query()
    {
        $query = User::role('team_member')
            ->with('assignedTo:id,name')
            ->withCount('clients')
            ->select([
                'users.id','users.name','users.email',
                'users.employee_code','users.work_type',
                'users.assigned_to','users.is_active','users.created_at'
            ]);

        if (!empty($this->filters['filter_status'])) {
            $query->where('is_active',
                $this->filters['filter_status'] === 'Active' ? 1 : 0
            );
        }
        if (!empty($this->filters['filter_worktype'])) {
            $query->where('work_type', $this->filters['filter_worktype']);
        }
        if (!empty($this->filters['filter_assigned'])) {
            $query->whereHas('assignedTo', fn($q) =>
                $q->where('name', 'like', "%{$this->filters['filter_assigned']}%")
            );
        }

        return $query;
    }

    public function map($m): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $m->name,
            $m->email,
            $m->employee_code  ?? '—',
            $m->work_type,
            $m->assignedTo?->name ?? '—',
            $m->clients_count,
            $m->is_active ? 'Active' : 'Inactive',
            $m->created_at->format('d M Y'),
        ];
    }

    public function headings(): array
    {
        return ['S.No','Name','Email','Employee Code','Work Type','Assigned To','Clients','Status','Created'];
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
        return ['A'=>8,'B'=>25,'C'=>30,'D'=>15,'E'=>15,'F'=>20,'G'=>10,'H'=>12,'I'=>15];
    }
}