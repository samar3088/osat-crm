<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body { font-family: Arial, sans-serif; background:#f0f7ff; margin:0; padding:20px; }
        .container { max-width:580px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(14,96,153,0.1); }
        .header { background:linear-gradient(135deg,#0a4a78,#0e6099); padding:40px 30px; text-align:center; }
        .header h1 { color:#fff; font-size:22px; margin:0 0 8px; }
        .header p { color:rgba(255,255,255,0.75); font-size:13px; margin:0; }
        .body { padding:35px 30px; }
        .status-badge { display:inline-block; padding:8px 20px; border-radius:50px; font-weight:bold; font-size:14px; margin:15px 0; }
        .approved { background:#d1fae5; color:#065f46; }
        .rejected { background:#fee2e2; color:#991b1b; }
        .details { background:#f8fafc; border-radius:10px; padding:20px; margin:20px 0; }
        .detail-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:13px; }
        .detail-row:last-child { border-bottom:none; }
        .label { color:#64748b; font-weight:600; }
        .value { color:#0f172a; font-weight:bold; }
        .remarks-box { background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; padding:12px 16px; font-size:13px; color:#c2410c; margin-top:15px; }
        .footer { background:#f8fafc; padding:20px; text-align:center; font-size:11px; color:#94a3b8; border-top:1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OSAT Wealth CRM</h1>
            <p>Conveyance Claim Update</p>
        </div>
        <div class="body">
            <p style="font-size:16px;font-weight:bold;color:#0f172a;">
                Hello {{ $conveyance->user->name }},
            </p>
            <p style="color:#64748b;font-size:14px;">
                Your conveyance claim has been
                <span class="status-badge {{ $conveyance->status }}">
                    {{ ucfirst($conveyance->status) }}
                </span>
            </p>

            <div class="details">
                <div class="detail-row">
                    <span class="label">Type</span>
                    <span class="value">{{ $conveyance->conveyance_type }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Date</span>
                    <span class="value">{{ $conveyance->conveyance_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Amount</span>
                    <span class="value">₹{{ number_format($conveyance->amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Status</span>
                    <span class="value">{{ ucfirst($conveyance->status) }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Actioned By</span>
                    <span class="value">{{ $conveyance->actionedBy?->name ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Actioned On</span>
                    <span class="value">{{ $conveyance->actioned_at?->format('d M Y') }}</span>
                </div>
            </div>

            @if($conveyance->action_remarks)
            <div class="remarks-box">
                <strong>Remarks:</strong> {{ $conveyance->action_remarks }}
            </div>
            @endif
        </div>
        <div class="footer">
            Investment CRM — Confidential
        </div>
    </div>
</body>
</html>