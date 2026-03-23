<?php

namespace App\Exports;

use App\Models\Conveyance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConveyancesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithChunkReading
{
    public function __construct(
        private User  $user,
        private array $filters = []
    ) {}

    public function chunkSize(): int { return 500; }

    public function query()
    {
        $query = Conveyance::query()
            ->with([
                'user:id,name',
                'actionedBy:id,name'
            ])
            ->select([
                'id','user_id','conveyance_type',
                'conveyance_date','amount','remarks',
                'status','action_remarks','actioned_by','created_at'
            ]);

        if (!$this->user->isSuperAdmin()) {
            $query->where('user_id', $this->user->id);
        }
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

        return $query->latest();
    }

    public function map($c): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $c->user->name         ?? '—',
            $c->conveyance_type,
            $c->conveyance_date->format('d M Y'),
            '₹' . number_format($c->amount, 2),
            $c->remarks            ?? '—',
            ucfirst($c->status),
            $c->action_remarks     ?? '—',
            $c->actionedBy?->name  ?? '—',
            $c->created_at->format('d M Y'),
        ];
    }

    public function headings(): array
    {
        return ['S.No','Team Member','Type','Date','Amount','Remarks','Status','Action Remarks','Actioned By','Created'];
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
        return ['A'=>8,'B'=>20,'C'=>15,'D'=>15,'E'=>15,'F'=>25,'G'=>12,'H'=>25,'I'=>20,'J'=>15];
    }
}