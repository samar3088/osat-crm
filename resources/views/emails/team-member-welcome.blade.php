<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f7ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(14,96,153,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0a4a78, #0e6099);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0 0 8px;
        }
        .header p {
            color: rgba(255,255,255,0.75);
            font-size: 13px;
            margin: 0;
        }
        .body {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 15px;
        }
        .text {
            font-size: 14px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .credentials {
            background: #e8f4fd;
            border-left: 4px solid #0e6099;
            border-radius: 8px;
            padding: 20px 25px;
            margin: 25px 0;
        }
        .credentials h3 {
            color: #0e6099;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 15px;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(14,96,153,0.1);
            font-size: 14px;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { color: #64748b; font-weight: 600; }
        .cred-value { color: #0f172a; font-weight: bold; }
        .btn {
            display: inline-block;
            background: #0e6099;
            color: #ffffff !important;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0 25px;
        }
        .warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: #c2410c;
            margin-top: 20px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OSAT Wealth CRM</h1>
            <p>Your Ultimate Wealth Partner</p>
        </div>
        <div class="body">
            <div class="greeting">Welcome, {{ $user->name }}! 👋</div>
            <p class="text">
                Your account has been created on the <strong>Investment CRM</strong> platform.
                You can now login and start managing your clients, activities and targets.
            </p>

            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <div class="cred-row">
                    <span class="cred-label">Name</span>
                    <span class="cred-value">{{ $user->name }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Email</span>
                    <span class="cred-value">{{ $user->email }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value">{{ $password }}</span>
                </div>
                @if($user->employee_code)
                <div class="cred-row">
                    <span class="cred-label">Employee Code</span>
                    <span class="cred-value">{{ $user->employee_code }}</span>
                </div>
                @endif
            </div>

            <center>
                <a href="{{ url('/login') }}" class="btn">Login to Dashboard →</a>
            </center>

            <div class="warning">
                ⚠️ Please change your password after first login for security purposes.
            </div>
        </div>
        <div class="footer">
            Investment CRM — Confidential · Do not share your credentials
        </div>
    </div>
</body>
</html>