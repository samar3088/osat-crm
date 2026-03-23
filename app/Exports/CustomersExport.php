<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        $user  = Auth::user();
        $query = Client::with('assignedTo')->latest();

        if (!$user->isSuperAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        if (!empty($this->filters['filter_type'])) {
            $query->where('client_type', $this->filters['filter_type']);
        }
        if (!empty($this->filters['filter_status'])) {
            $query->where('is_active', $this->filters['filter_status'] === 'Active' ? 1 : 0);
        }
        if (!empty($this->filters['filter_assigned'])) {
            $query->whereHas('assignedTo', fn($q) =>
                $q->where('name', 'like', "%{$this->filters['filter_assigned']}%")
            );
        }

        return $query->get()->map(fn($c, $i) => [
            'sno'           => $i + 1,
            'client_name'   => $c->client_name,
            'client_pan'    => $c->client_pan    ?? '—',
            'client_mobile' => $c->client_mobile ?? '—',
            'client_email'  => $c->client_email  ?? '—',
            'client_type'   => $c->client_type   ?? '—',
            'assigned_to'   => $c->assignedTo?->name ?? '—',
            'status'        => $c->is_active ? 'Active' : 'Inactive',
            'created_at'    => $c->created_at->format('d M Y'),
        ]);
    }

    public function headings(): array
    {
        return ['S.No', 'Name', 'PAN', 'Mobile', 'Email', 'Type', 'Assigned To', 'Status', 'Created'];
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
        return ['A' => 8, 'B' => 25, 'C' => 15, 'D' => 15, 'E' => 25, 'F' => 18, 'G' => 20, 'H' => 12, 'I' => 15];
    }
}