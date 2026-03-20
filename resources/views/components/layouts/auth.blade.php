<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $title ?? 'Investment CRM' }}</title>
    {{-- Vite compiles Tailwind CSS + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans min-h-screen flex bg-crm-bg overflow-x-hidden">

    {{-- LEFT PANEL — passed as a named slot --}}
    {{ $left }}

    {{-- RIGHT PANEL — main content slot --}}
    <div class="w-[55%] flex items-center justify-center px-16 py-16 bg-white overflow-y-auto">
        {{ $slot }}
    </div>

    @stack('scripts')
</body>
</html>
</html>