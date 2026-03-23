<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithChunkReading
{
    public function __construct(private array $filters = []) {}

    // ── Chunk size — processes 500 rows at a time ────
    public function chunkSize(): int { return 500; }

    // ── Query — no ->get() — lazy loaded ─────────────
    public function query()
    {
        $user  = auth()->user();
        $query = Client::query()
            ->with('assignedTo:id,name')
            ->select([
                'id','client_name','client_pan',
                'client_mobile','client_email',
                'client_type','assigned_to',
                'is_active','created_at'
            ]);

        if (!$user->isSuperAdmin()) {
            $query->where('assigned_to', $user->id);
        }
        if (!empty($this->filters['filter_type'])) {
            $query->where('client_type', $this->filters['filter_type']);
        }
        if (!empty($this->filters['filter_status'])) {
            $query->where('is_active',
                $this->filters['filter_status'] === 'Active' ? 1 : 0
            );
        }
        if (!empty($this->filters['filter_assigned']) && $user->isSuperAdmin()) {
            $query->whereHas('assignedTo', fn($q) =>
                $q->where('name', 'like', "%{$this->filters['filter_assigned']}%")
            );
        }

        return $query;
    }

    // ── Map each row — no manual collection loop ──────
    public function map($client): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $client->client_name,
            $client->client_pan    ?? '—',
            $client->client_mobile ?? '—',
            $client->client_email  ?? '—',
            $client->client_type   ?? '—',
            $client->assignedTo?->name ?? '—',
            $client->is_active ? 'Active' : 'Inactive',
            $client->created_at->format('d M Y'),
        ];
    }

    public function headings(): array
    {
        return ['S.No','Name','PAN','Mobile','Email','Type','Assigned To','Status','Created'];
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
        return ['A'=>8,'B'=>25,'C'=>15,'D'=>15,'E'=>25,'F'=>18,'G'=>20,'H'=>12,'I'=>15];
    }
}