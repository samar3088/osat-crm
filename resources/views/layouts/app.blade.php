<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — OSAT Wealth CRM</title>

    {{-- jQuery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"/>

    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body class="font-sans bg-[#f0f6fb] text-dark" x-data="{ sidebarOpen: true }">

{{-- ═══ GLOBAL LOADER ═══ --}}
<div id="globalLoader"
     class="hidden fixed inset-0 bg-black/30 z-[9999] flex items-center justify-center">
    <div class="bg-white rounded-card p-6 flex items-center gap-3 shadow-card">
        <div class="w-6 h-6 border-[3px] border-primary border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm font-semibold text-dark">Please wait...</span>
    </div>
</div>

{{-- ═══ TOAST NOTIFICATION ═══ --}}
<div id="toast"
     class="hidden fixed top-5 right-5 z-[9998] flex items-center gap-3 px-5 py-4 rounded-card shadow-card min-w-[280px] max-w-sm">
    <div id="toastIcon" class="w-5 h-5 flex-shrink-0"></div>
    <span id="toastMsg" class="text-sm font-semibold"></span>
</div>

<div class="flex min-h-screen">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside id="sidebar" class="min-h-screen bg-dark-2 fixed left-0 top-0 flex flex-col z-50 transition-all duration-300" style="width:260px">

        {{-- Logo + Toggle --}}
        <div class="flex items-center gap-3 px-5 py-[22px] border-b border-white/[0.07] relative">
            <div class="w-[42px] h-[42px] bg-primary rounded-[11px] flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div id="sidebarLogoText">
                <div class="text-sm font-extrabold text-white leading-tight whitespace-nowrap">OSAT Wealth CRM</div>
                <div class="text-[10px] text-white/40 font-medium whitespace-nowrap">Wealth Management</div>
            </div>

            {{-- Toggle Arrow Button --}}
            <button id="sidebarToggle"
                    onclick="toggleSidebar()"
                    class="absolute -right-3 top-1/2 -translate-y-1/2
                        w-6 h-6 bg-primary rounded-full
                        flex items-center justify-center
                        shadow-lg border-2 border-dark-2
                        hover:bg-primary-dark transition-all z-10">
                <svg id="sidebarArrow"
                    class="w-3 h-3 stroke-white fill-none stroke-[2.5] transition-transform duration-300"
                    viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            {{-- Main --}}
            <div class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2 sidebar-text sidebar-section">Main</div>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
            title="Dashboard"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all
                    {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-white/60 hover:bg-white/[0.07] hover:text-white' }}">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Management --}}
            <div class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2 mt-4 sidebar-text sidebar-section">Management</div>

            @can('view users')
            <a href="{{ route('team-members.index') }}"
                title="Team Members"
                class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all
                        {{ request()->routeIs('team-members.*') ? 'bg-primary text-white' : 'text-white/60 hover:bg-white/[0.07] hover:text-white' }}">
                    <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Team Members</span>
                </a>
            @endcan

            @can('view customers')
            <a href="#"
            title="Customers"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all text-white/60 hover:bg-white/[0.07] hover:text-white">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Customers</span>
            </a>
            @endcan

            @can('view activities')
            <a href="#"
            title="Activities"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all text-white/60 hover:bg-white/[0.07] hover:text-white">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Activities</span>
            </a>
            @endcan

            @can('view conveyance')
            <a href="#"
            title="Conveyance"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all text-white/60 hover:bg-white/[0.07] hover:text-white">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Conveyance</span>
            </a>
            @endcan

            {{-- Reports --}}
            <div class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2 mt-4 sidebar-text sidebar-section">Reports</div>

            @can('view reports')
            <a href="#"
            title="Reports"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all text-white/60 hover:bg-white/[0.07] hover:text-white">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Reports</span>
            </a>
            @endcan

            {{-- Tools --}}
            <div class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2 mt-4 sidebar-text sidebar-section">Tools</div>

            <a href="{{ route('calculators') }}"
            title="Calculators"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all
                    {{ request()->routeIs('calculators') ? 'bg-primary text-white' : 'text-white/60 hover:bg-white/[0.07] hover:text-white' }}">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <rect x="4" y="2" width="16" height="20" rx="2"/>
                    <line x1="8" y1="6" x2="16" y2="6"/>
                    <line x1="8" y1="10" x2="8" y2="10"/>
                    <line x1="12" y1="10" x2="12" y2="10"/>
                    <line x1="16" y1="10" x2="16" y2="10"/>
                    <line x1="8" y1="14" x2="8" y2="14"/>
                    <line x1="12" y1="14" x2="12" y2="14"/>
                    <line x1="16" y1="14" x2="16" y2="14"/>
                    <line x1="8" y1="18" x2="12" y2="18"/>
                    <line x1="16" y1="18" x2="16" y2="18"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Calculators</span>
            </a>

            {{-- Admin Only --}}
            @can('manage settings')
            <div class="text-[10px] font-bold text-white/30 tracking-widest uppercase px-3 mb-2 mt-4 sidebar-text sidebar-section">Admin</div>


            {{-- Audit Logs --}}
            <a href="#"
            title="Audit Logs"
            class="nav-link flex items-center justify-start gap-3 px-3 py-2.5 rounded-[10px] mb-1 transition-all text-white/60 hover:bg-white/[0.07] hover:text-white">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span class="text-sm font-semibold sidebar-text whitespace-nowrap">Audit Logs</span>
            </a>
            @endcan

        </nav>        

    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div id="mainContent" class="flex-1 flex flex-col min-h-screen transition-all duration-300" style="margin-left:260px">

        {{-- Top Header --}}
        <header class="h-[68px] bg-white border-b border-crm-border flex items-center justify-between px-6 sticky top-0 z-40">

            {{-- Left: Page Title --}}
            <div>
                <h1 class="text-base font-extrabold text-dark">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-crm-gray">@yield('page-subtitle', 'Welcome back, ' . auth()->user()->name)</p>
            </div>

            {{-- Right: Notification + Profile Dropdown --}}
            <div class="flex items-center gap-3">

                {{-- Date --}}
                <div class="text-xs text-crm-gray font-medium hidden md:block">
                    {{ now()->format('d M Y') }}
                </div>

                {{-- Notification Bell --}}
                <button class="relative w-9 h-9 rounded-[10px] bg-crm-light flex items-center justify-center hover:bg-primary-light transition-all">
                    <svg class="w-4 h-4 stroke-crm-gray fill-none stroke-2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span id="notifBadge"
                        class="hidden absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full
                                text-[9px] text-white font-bold flex items-center justify-center">
                    </span>
                </button>

                {{-- Profile Dropdown --}}
                <div class="relative" id="profileDropdownWrap">

                    {{-- Trigger Button --}}
                    <button onclick="toggleProfileDropdown()"
                            class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-[10px]
                                hover:bg-crm-light transition-all border border-transparent
                                hover:border-crm-border">
                        {{-- Avatar --}}
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-extrabold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                        {{-- Name + Role --}}
                        <div class="hidden md:block text-left">
                            <div class="text-xs font-bold text-dark leading-tight">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="text-[10px] text-crm-gray capitalize leading-tight">
                                {{ str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'User') }}
                            </div>
                        </div>
                        {{-- Chevron --}}
                        <svg id="dropdownChevron"
                            class="w-3.5 h-3.5 stroke-crm-gray fill-none stroke-2 transition-transform duration-200"
                            viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="profileDropdown"
                        class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-card
                                shadow-card border border-crm-border z-50 overflow-hidden">

                        {{-- User Info Header --}}
                        <div class="px-4 py-3 border-b border-crm-border bg-crm-light">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-extrabold text-white">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-sm font-bold text-dark truncate">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="text-[10px] text-crm-gray truncate">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Items --}}
                        <div class="py-1">
                            {{-- My Profile --}}
                            <a href="{{ route('profile') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-dark
                                    hover:bg-primary-light hover:text-primary transition-all">
                                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span class="font-semibold">My Profile</span>
                            </a>

                            {{-- Change Password --}}
                            <a href="{{ route('profile') }}#change-password"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-dark
                                    hover:bg-primary-light hover:text-primary transition-all">
                                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <span class="font-semibold">Change Password</span>
                            </a>

                            @can('manage settings')
                            {{-- Settings --}}
                            <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-dark
                                    hover:bg-primary-light hover:text-primary transition-all">
                                <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                <span class="font-semibold">Settings</span>
                            </a>
                            @endcan
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-crm-border"></div>

                        {{-- Logout --}}
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                            text-red-500 hover:bg-red-50 transition-all">
                                    <svg class="w-4 h-4 stroke-current fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    <span class="font-semibold">Logout</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
                {{-- End Profile Dropdown --}}

            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

</div>

{{-- Global JS --}}
<script>

    // ── Profile Dropdown ─────────────────────────────────
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        const chevron  = document.getElementById('dropdownChevron');
        const isHidden = dropdown.classList.contains('hidden');

        dropdown.classList.toggle('hidden', !isHidden);
        chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('profileDropdownWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
        }
    });

    // ── Loader ──────────────────────────────────
    window.showLoader = () => document.getElementById('globalLoader').classList.remove('hidden');
    window.hideLoader = () => document.getElementById('globalLoader').classList.add('hidden');

    // ── Toast ───────────────────────────────────
    window.showToast = (message, type = 'success') => {
        const toast   = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        const toastIcon = document.getElementById('toastIcon');

        toastMsg.textContent = message;

        if (type === 'success') {
            toast.className = toast.className.replace(/bg-\S+/g, '');
            toast.classList.add('bg-green-50', 'border', 'border-green-200');
            toastMsg.classList.add('text-green-700');
            toastIcon.innerHTML = `<svg class="w-5 h-5 stroke-green-500 fill-none stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>`;
        } else {
            toast.className = toast.className.replace(/bg-\S+/g, '');
            toast.classList.add('bg-red-50', 'border', 'border-red-200');
            toastMsg.classList.add('text-red-700');
            toastIcon.innerHTML = `<svg class="w-5 h-5 stroke-red-500 fill-none stroke-2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
        }

        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3500);
    };

    // ── AJAX Helper ─────────────────────────────
    window.ajaxGet = async (url) => {
        showLoader();
        try {
            const res  = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            hideLoader();
            return data;
        } catch (err) {
            hideLoader();
            showToast('Something went wrong. Please try again.', 'error');
            throw err;
        }
    };

    window.ajaxPost = async (url, payload) => {
        showLoader();
        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            hideLoader();
            return data;
        } catch (err) {
            hideLoader();
            showToast('Something went wrong. Please try again.', 'error');
            throw err;
        }
    };
</script>

<script>
    let sidebarOpen = true;

    function toggleSidebar() {
        const sidebar      = document.getElementById('sidebar');
        const mainContent  = document.getElementById('mainContent');
        const arrow        = document.getElementById('sidebarArrow');
        const logoText     = document.getElementById('sidebarLogoText');
        const userInfo     = document.getElementById('sidebarUserInfo');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const navLinks     = document.querySelectorAll('.nav-link');
        const sectionLabels = document.querySelectorAll('.sidebar-section');

        sidebarOpen = !sidebarOpen;

        if (sidebarOpen) {
            // ── Expand ──
            sidebar.style.width          = '260px';
            mainContent.style.marginLeft = '260px';
            arrow.style.transform        = 'rotate(0deg)';
            logoText.classList.remove('hidden');
            userInfo.classList.remove('hidden');
            sidebarTexts.forEach(el => {
                el.classList.remove('hidden');
                el.classList.remove('opacity-0');
            });
            sectionLabels.forEach(el => el.classList.remove('hidden'));
            navLinks.forEach(el => {
                el.classList.remove('justify-center');
                el.classList.add('justify-start');
            });
        } else {
            // ── Collapse ──
            sidebar.style.width          = '70px';
            mainContent.style.marginLeft = '70px';
            arrow.style.transform        = 'rotate(180deg)';
            logoText.classList.add('hidden');
            userInfo.classList.add('hidden');
            sidebarTexts.forEach(el => {
                el.classList.add('hidden');
                el.classList.add('opacity-0');
            });
            sectionLabels.forEach(el => el.classList.add('hidden'));
            navLinks.forEach(el => {
                el.classList.add('justify-center');
                el.classList.remove('justify-start');
            });
        }

        localStorage.setItem('sidebarOpen', sidebarOpen);
    }

    // Restore on page load
    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('sidebarOpen');
        if (saved === 'false') {
            sidebarOpen = true;
            toggleSidebar();
        }
    });
</script>

@stack('scripts')

</body>
</html>