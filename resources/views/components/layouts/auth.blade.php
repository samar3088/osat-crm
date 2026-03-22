<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $title ?? 'OSAT Wealth CRM' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans min-h-screen flex bg-crm-bg" style="overflow-x:hidden;">

    {{-- LEFT PANEL --}}
    {{ $left }}

    {{-- RIGHT PANEL --}}
    <div class="flex-1 flex items-center justify-center bg-white min-h-screen overflow-y-auto py-12 px-10">
        {{ $slot }}
    </div>

    @stack('scripts')
</body>
</html>