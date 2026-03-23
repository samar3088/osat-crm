<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { color: #0e6099; font-size: 16px; margin-bottom: 5px; }
        p  { color: #64748b; font-size: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0e6099; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .active   { color: #10b981; font-weight: bold; }
        .inactive { color: #ef4444; font-weight: bold; }
        .footer   { margin-top: 20px; text-align: center; color: #94a3b8; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Customers Report</h1>
    <p>Generated: {{ now()->format('d M Y, h:i A') }} · Total: {{ $clients->count() }}</p>
    <table>
        <thead>
            <tr>
                <th>S.No</th><th>Name</th><th>PAN</th>
                <th>Mobile</th><th>Type</th><th>Assigned To</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $i => $c)
            <tr>
                <td>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
                <td>{{ $c->client_name }}</td>
                <td>{{ $c->client_pan ?? '—' }}</td>
                <td>{{ $c->client_mobile ?? '—' }}</td>
                <td>{{ $c->client_type ?? '—' }}</td>
                <td>{{ $c->assignedTo?->name ?? '—' }}</td>
                <td class="{{ $c->is_active ? 'active' : 'inactive' }}">
                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Investment CRM — Confidential Report</div>
</body>
</html>