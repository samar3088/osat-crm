<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #0f172a; }
        h1 { color: #0e6099; font-size: 18px; margin-bottom: 5px; }
        p { color: #64748b; font-size: 11px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #0e6099; color: white;
            padding: 8px 10px; text-align: left;
            font-size: 11px; font-weight: bold;
        }
        td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge-active   { color: #10b981; font-weight: bold; }
        .badge-inactive { color: #ef4444; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Team Members Report</h1>
    <p>Generated on {{ now()->format('d M Y, h:i A') }} · Total: {{ $members->count() }} members</p>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee Code</th>
                <th>Work Type</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $i => $member)
            <tr>
                <td>{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ $member->employee_code ?? '—' }}</td>
                <td>{{ $member->work_type }}</td>
                <td>{{ $member->assignedTo?->name ?? '—' }}</td>
                <td class="{{ $member->is_active ? 'badge-active' : 'badge-inactive' }}">
                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                </td>
                <td>{{ $member->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Investment CRM — Confidential Report</div>
</body>
</html>