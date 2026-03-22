@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name . '!')

@section('content')

{{-- ═══ STAT TILES ═══ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Tile 1: Total Clients --}}
    <div class="bg-white rounded-card p-5 shadow-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-[12px] bg-primary-light flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs text-crm-gray font-semibold mb-1">Total Clients</div>
            <div class="text-2xl font-extrabold text-dark" id="statTotalClients">
                <div class="h-7 w-20 bg-crm-border rounded animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Tile 2: Total AUM --}}
    <div class="bg-white rounded-card p-5 shadow-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-[12px] flex items-center justify-center flex-shrink-0"
             style="background:#fff7ed">
            <svg class="w-5 h-5 fill-none stroke-2" style="stroke:#f97316" viewBox="0 0 24 24">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs text-crm-gray font-semibold mb-1">Total AUM</div>
            <div class="text-2xl font-extrabold text-dark" id="statTotalAum">
                <div class="h-7 w-24 bg-crm-border rounded animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Tile 3: SIP Clients --}}
    <div class="bg-white rounded-card p-5 shadow-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-[12px] flex items-center justify-center flex-shrink-0"
             style="background:#f0fdf4">
            <svg class="w-5 h-5 fill-none stroke-2" style="stroke:#10b981" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs text-crm-gray font-semibold mb-1">SIP Clients</div>
            <div class="text-2xl font-extrabold text-dark" id="statSipClients">
                <div class="h-7 w-16 bg-crm-border rounded animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Tile 4: Total SIP --}}
    <div class="bg-white rounded-card p-5 shadow-card flex items-center gap-4">
        <div class="w-12 h-12 rounded-[12px] flex items-center justify-center flex-shrink-0"
             style="background:#eff6ff">
            <svg class="w-5 h-5 fill-none stroke-2" style="stroke:#3b82f6" viewBox="0 0 24 24">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs text-crm-gray font-semibold mb-1">Total SIP</div>
            <div class="text-2xl font-extrabold text-dark" id="statTotalSip">
                <div class="h-7 w-24 bg-crm-border rounded animate-pulse"></div>
            </div>
        </div>
    </div>

</div>

{{-- ═══ TARGETS + CHART ROW ═══ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    {{-- Target Progress Card --}}
    <div class="bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">Monthly Targets</h3>
                <p class="text-xs text-crm-gray mt-0.5">{{ now()->format('F Y') }}</p>
            </div>
            <div class="w-8 h-8 rounded-[8px] bg-primary-light flex items-center justify-center">
                <svg class="w-4 h-4 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>

        {{-- SIP Target --}}
        <div class="mb-5" id="targetSipBlock">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-crm-gray">SIP Target</span>
                <span class="text-xs font-bold text-primary" id="targetSipPct">—</span>
            </div>
            <div class="h-2 bg-crm-border rounded-full overflow-hidden">
                <div id="targetSipBar"
                     class="h-full rounded-full transition-all duration-700"
                     style="width:0%; background:linear-gradient(90deg,#0e6099,#00a8e8)">
                </div>
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-[10px] text-crm-gray" id="targetSipAchieved">Achieved: —</span>
                <span class="text-[10px] text-crm-gray" id="targetSipGoal">Target: —</span>
            </div>
        </div>

        {{-- Lumpsum Target --}}
        <div id="targetLumpsumBlock">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-crm-gray">Lumpsum Target</span>
                <span class="text-xs font-bold text-primary" id="targetLumpsumPct">—</span>
            </div>
            <div class="h-2 bg-crm-border rounded-full overflow-hidden">
                <div id="targetLumpsumBar"
                     class="h-full rounded-full transition-all duration-700"
                     style="width:0%; background:linear-gradient(90deg,#10b981,#4fc3f7)">
                </div>
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-[10px] text-crm-gray" id="targetLumpsumAchieved">Achieved: —</span>
                <span class="text-[10px] text-crm-gray" id="targetLumpsumGoal">Target: —</span>
            </div>
        </div>

        {{-- No targets message --}}
        <div id="noTargetsMsg" class="hidden text-center py-4">
            <p class="text-xs text-crm-gray">No targets set for this month.</p>
        </div>
    </div>

    {{-- AUM Trend Chart --}}
    <div class="xl:col-span-2 bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">AUM Growth Trend</h3>
                <p class="text-xs text-crm-gray mt-0.5">Last 6 months</p>
            </div>
            <div class="w-8 h-8 rounded-[8px] bg-primary-light flex items-center justify-center">
                <svg class="w-4 h-4 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
                </svg>
            </div>
        </div>
        <div class="relative h-[200px]">
            <canvas id="aumChart"></canvas>
            {{-- Chart skeleton --}}
            <div id="aumChartSkeleton"
                 class="absolute inset-0 flex items-center justify-center bg-white rounded">
                <div class="flex items-end gap-2 h-32">
                    @foreach([60,80,50,90,70,100] as $h)
                    <div class="w-8 rounded-t animate-pulse bg-crm-border"
                         style="height:{{ $h }}%"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══ BIRTHDAYS + MEETINGS ROW ═══ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

    {{-- 🎂 Today's Birthdays --}}
    <div class="bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">🎂 Birthdays Today</h3>
                <p class="text-xs text-crm-gray mt-0.5">{{ now()->format('d F Y') }}</p>
            </div>
            <div class="w-8 h-8 rounded-[8px] flex items-center justify-center"
                 style="background:#fff0f6">
                <svg class="w-4 h-4 fill-none stroke-2" style="stroke:#ec4899" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
        </div>
        <div id="birthdaysList" class="space-y-3">
            {{-- Skeleton --}}
            @foreach(range(1,3) as $i)
            <div class="flex items-center gap-3 p-3 rounded-[10px] bg-crm-light animate-pulse">
                <div class="w-8 h-8 rounded-full bg-crm-border flex-shrink-0"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-3 bg-crm-border rounded w-3/4"></div>
                    <div class="h-2.5 bg-crm-border rounded w-1/2"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 📅 Today's Meetings --}}
    <div class="bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">📅 Today's Meetings</h3>
                <p class="text-xs text-crm-gray mt-0.5">{{ now()->format('d F Y') }}</p>
            </div>
            <div class="w-8 h-8 rounded-[8px] flex items-center justify-center"
                 style="background:#eff6ff">
                <svg class="w-4 h-4 fill-none stroke-2" style="stroke:#3b82f6" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>
        <div id="meetingsList" class="space-y-3">
            {{-- Skeleton --}}
            @foreach(range(1,3) as $i)
            <div class="flex items-center gap-3 p-3 rounded-[10px] bg-crm-light animate-pulse">
                <div class="w-8 h-8 rounded-full bg-crm-border flex-shrink-0"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-3 bg-crm-border rounded w-3/4"></div>
                    <div class="h-2.5 bg-crm-border rounded w-1/2"></div>
                </div>
                <div class="h-3 bg-crm-border rounded w-16"></div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ═══ RECENT ACTIVITY + PENDING CONVEYANCES ═══ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    {{-- Recent Activities --}}
    <div class="bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">Recent Activities</h3>
                <p class="text-xs text-crm-gray mt-0.5">Latest client interactions</p>
            </div>
            <a href="#" class="text-xs text-primary font-semibold hover:underline">View All</a>
        </div>

        <div id="recentActivitiesList" class="space-y-3">
            {{-- Skeleton --}}
            @foreach(range(1,4) as $i)
            <div class="flex items-center gap-3 p-3 rounded-[10px] bg-crm-light animate-pulse">
                <div class="w-8 h-8 rounded-full bg-crm-border flex-shrink-0"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-3 bg-crm-border rounded w-3/4"></div>
                    <div class="h-2.5 bg-crm-border rounded w-1/2"></div>
                </div>
                <div class="h-3 bg-crm-border rounded w-16"></div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pending Conveyances (Super Admin only) --}}
    @can('approve conveyance')
    <div class="bg-white rounded-card p-6 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-dark">Pending Conveyances</h3>
                <p class="text-xs text-crm-gray mt-0.5">Awaiting your approval</p>
            </div>
            <a href="#" class="text-xs text-primary font-semibold hover:underline">View All</a>
        </div>

        <div id="pendingConveyancesList" class="space-y-3">
            {{-- Skeleton --}}
            @foreach(range(1,4) as $i)
            <div class="flex items-center gap-3 p-3 rounded-[10px] bg-crm-light animate-pulse">
                <div class="w-8 h-8 rounded-full bg-crm-border flex-shrink-0"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="h-3 bg-crm-border rounded w-3/4"></div>
                    <div class="h-2.5 bg-crm-border rounded w-1/2"></div>
                </div>
                <div class="h-3 bg-crm-border rounded w-16"></div>
            </div>
            @endforeach
        </div>
    </div>
    @endcan

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadTargets();
    loadAumTrend();
    loadRecentActivities();
    loadPendingConveyances();
    loadTodayBirthdays();
    loadTodayMeetings();
});

// ── 1. Stat Tiles ────────────────────────────────────
async function loadStats() {
    const res = await ajaxGet('{{ route("dashboard.stats") }}');
    if (!res.success) return;
    const d = res.data;
    document.getElementById('statTotalClients').textContent = d.total_clients;
    document.getElementById('statTotalAum').textContent     = d.total_aum;
    document.getElementById('statSipClients').textContent   = d.sip_clients;
    document.getElementById('statTotalSip').textContent     = d.total_sip;
}

// ── 2. Target Progress Bars ──────────────────────────
async function loadTargets() {
    const res = await ajaxGet('{{ route("dashboard.targets") }}');
    if (!res.success) return;
    const d = res.data;

    // SIP
    document.getElementById('targetSipPct').textContent      = d.sip.percentage + '%';
    document.getElementById('targetSipBar').style.width      = d.sip.percentage + '%';
    document.getElementById('targetSipAchieved').textContent = 'Achieved: ' + d.sip.achieved;
    document.getElementById('targetSipGoal').textContent     = 'Target: ' + d.sip.target;

    // Lumpsum
    document.getElementById('targetLumpsumPct').textContent      = d.lumpsum.percentage + '%';
    document.getElementById('targetLumpsumBar').style.width      = d.lumpsum.percentage + '%';
    document.getElementById('targetLumpsumAchieved').textContent = 'Achieved: ' + d.lumpsum.achieved;
    document.getElementById('targetLumpsumGoal').textContent     = 'Target: ' + d.lumpsum.target;
}

// ── 3. AUM Trend Chart ───────────────────────────────
async function loadAumTrend() {
    const res = await ajaxGet('{{ route("dashboard.aum-trend") }}');
    if (!res.success) return;

    // Hide skeleton
    document.getElementById('aumChartSkeleton').classList.add('hidden');

    const ctx = document.getElementById('aumChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: res.data.labels,
            datasets: [{
                label: 'AUM (₹ Cr)',
                data: res.data.data,
                borderColor: '#0e6099',
                backgroundColor: 'rgba(14,96,153,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#0e6099',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '₹ ' + ctx.parsed.y + ' Cr'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e2e8f0' },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 11 },
                        callback: val => '₹' + val + ' Cr'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                }
            }
        }
    });
}

// ── 4. Recent Activities ─────────────────────────────
async function loadRecentActivities() {
    const res  = await ajaxGet('{{ route("dashboard.recent-activities") }}');
    const list = document.getElementById('recentActivitiesList');
    if (!res.success) { list.innerHTML = '<p class="text-xs text-crm-gray text-center py-4">Could not load activities.</p>'; return; }

    if (res.data.length === 0) {
        list.innerHTML = `
            <div class="text-center py-8">
                <p class="text-xs text-crm-gray">No activities recorded yet.</p>
            </div>`;
        return;
    }

    list.innerHTML = res.data.map(a => `
        <div class="flex items-start gap-3 p-3 rounded-[10px] hover:bg-crm-light transition-all">
            <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="text-xs font-bold text-primary">${a.client_name.charAt(0).toUpperCase()}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-dark truncate">${a.client_name}</div>
                <div class="text-xs text-crm-gray mt-0.5">
                    ${a.transaction ?? '—'} · ${a.amount}
                </div>
            </div>
            <div class="text-[10px] text-crm-gray whitespace-nowrap">${a.date}</div>
        </div>
    `).join('');
}

// ── 5. Pending Conveyances ───────────────────────────
async function loadPendingConveyances() {
    const el = document.getElementById('pendingConveyancesList');
    if (!el) return; // Not visible for non-admins

    const res = await ajaxGet('{{ route("dashboard.pending-conveyances") }}');
    if (!res.success) { el.innerHTML = '<p class="text-xs text-crm-gray text-center py-4">Could not load.</p>'; return; }

    if (res.data.length === 0) {
        el.innerHTML = `
            <div class="text-center py-8">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 stroke-green-500 fill-none stroke-2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <p class="text-xs text-crm-gray">All conveyances are cleared!</p>
            </div>`;
        return;
    }

    el.innerHTML = res.data.map(c => `
        <div class="flex items-center gap-3 p-3 rounded-[10px] hover:bg-crm-light transition-all">
            <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-orange-500">${c.user_name.charAt(0).toUpperCase()}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-dark truncate">${c.user_name}</div>
                <div class="text-xs text-crm-gray mt-0.5">${c.conveyance_type} · ${c.amount}</div>
            </div>
            <div class="flex flex-col items-end gap-1">
                <span class="text-[10px] bg-orange-50 text-orange-500 font-bold px-2 py-0.5 rounded-full">Pending</span>
                <span class="text-[10px] text-crm-gray">${c.date}</span>
            </div>
        </div>
    `).join('');
}

// ── 6. Today's Birthdays ─────────────────────────────
async function loadTodayBirthdays() {
    const res  = await ajaxGet('{{ route("dashboard.today-birthdays") }}');
    const list = document.getElementById('birthdaysList');
    if (!res.success) { list.innerHTML = '<p class="text-xs text-crm-gray text-center py-4">Could not load.</p>'; return; }

    if (res.data.length === 0) {
        list.innerHTML = `
            <div class="text-center py-6">
                <div class="text-3xl mb-2">🎂</div>
                <p class="text-xs text-crm-gray">No birthdays today.</p>
            </div>`;
        return;
    }

    list.innerHTML = res.data.map(b => `
        <div class="flex items-center gap-3 p-3 rounded-[10px] hover:bg-pink-50 transition-all border border-pink-100">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-lg"
                 style="background:#fff0f6">
                🎂
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-dark truncate">${b.client_name}</div>
                <div class="text-xs text-crm-gray mt-0.5">
                    ${b.client_mobile ?? '—'}
                    ${b.age ? ' · Turning ' + b.age : ''}
                </div>
            </div>
            <a href="tel:${b.client_mobile}"
               class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 transition-all hover:scale-110"
               style="background:#fff0f6">
                <svg class="w-3.5 h-3.5 fill-none stroke-2" style="stroke:#ec4899" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.84a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/>
                </svg>
            </a>
        </div>
    `).join('');
}

// ── 7. Today's Meetings ──────────────────────────────
async function loadTodayMeetings() {
    const res  = await ajaxGet('{{ route("dashboard.today-meetings") }}');
    const list = document.getElementById('meetingsList');
    if (!res.success) { list.innerHTML = '<p class="text-xs text-crm-gray text-center py-4">Could not load.</p>'; return; }

    if (res.data.length === 0) {
        list.innerHTML = `
            <div class="text-center py-6">
                <div class="text-3xl mb-2">📅</div>
                <p class="text-xs text-crm-gray">No meetings scheduled today.</p>
            </div>`;
        return;
    }

    list.innerHTML = res.data.map(m => `
        <div class="flex items-start gap-3 p-3 rounded-[10px] hover:bg-blue-50 transition-all border border-blue-100">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                 style="background:#eff6ff">
                <svg class="w-4 h-4 fill-none stroke-2" style="stroke:#3b82f6" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-dark truncate">${m.client_name}</div>
                <div class="text-xs text-crm-gray mt-0.5">
                    📍 ${m.location}
                </div>
                ${m.notes ? `<div class="text-xs text-crm-gray mt-0.5 truncate">📝 ${m.notes}</div>` : ''}
            </div>
            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                <span class="text-xs font-bold text-blue-600">${m.meeting_time}</span>
                <span class="text-[10px] text-crm-gray">${m.assigned_to}</span>
            </div>
        </div>
    `).join('');
}

</script>
@endpush