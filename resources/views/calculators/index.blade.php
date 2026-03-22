@extends('layouts.app')

@section('page-title', 'Calculators')
@section('page-subtitle', 'Financial planning tools')

@section('content')

{{-- ═══ SEARCH BAR ═══ --}}
<div class="mb-6">
    <div class="relative w-full">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 stroke-crm-gray fill-none stroke-2"
             viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text"
               id="calcSearch"
               oninput="filterCalculators(this.value)"
               class="crm-input pl-10"
               placeholder="Search calculators..."/>
    </div>
</div>

{{-- ═══ CALCULATOR GRID ═══ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" id="calcGrid">

    @foreach($calculators as $calc)
    <div class="calc-card bg-white rounded-card shadow-card p-6 cursor-pointer
                hover:shadow-btn hover:-translate-y-1 transition-all duration-200"
         data-tags="{{ implode(',', $calc['tags']) }}"
         data-name="{{ strtolower($calc['name']) }}"
         onclick="openCalculator('{{ $calc['id'] }}')">

        {{-- Icon --}}
        <div class="w-12 h-12 rounded-[12px] flex items-center justify-center mb-4
                    bg-{{ $calc['color'] }}-50">
            @include('calculators.partials.icon', ['icon' => $calc['icon'], 'color' => $calc['color']])
        </div>

        {{-- Name + Description --}}
        <h3 class="text-sm font-extrabold text-dark mb-1">{{ $calc['name'] }}</h3>
        <p class="text-xs text-crm-gray leading-relaxed">{{ $calc['description'] }}</p>

        {{-- Open button --}}
        <div class="flex items-center gap-1 mt-4 text-xs font-bold text-primary">
            <span>Open Calculator</span>
            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </div>

    </div>
    @endforeach

</div>

{{-- No results --}}
<div id="noResults" class="hidden text-center py-16">
    <div class="text-4xl mb-3">🔍</div>
    <p class="text-sm text-crm-gray">No calculators found for your search.</p>
</div>

{{-- ═══ CALCULATOR MODAL ═══ --}}
<div id="calcModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-lg max-h-[90vh] overflow-y-auto">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <div>
                <h2 id="modalTitle" class="text-base font-extrabold text-dark"></h2>
                <p id="modalDesc" class="text-xs text-crm-gray mt-0.5"></p>
            </div>
            <button onclick="closeCalculator()"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body — loaded dynamically --}}
        <div id="modalBody" class="p-6"></div>

    </div>
</div>

@endsection

@push('scripts')
<script>

// ── Calculator Registry (matches controller) ─────────
const calculators = @json($calculators);

// ── Search Filter ────────────────────────────────────
function filterCalculators(query) {
    const q     = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.calc-card');
    let   shown = 0;

    cards.forEach(card => {
        const name = card.dataset.name;
        const tags = card.dataset.tags;
        const match = !q || name.includes(q) || tags.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) shown++;
    });

    document.getElementById('noResults').classList.toggle('hidden', shown > 0);
}

// ── Open Calculator Modal ────────────────────────────
function openCalculator(id) {
    const calc = calculators.find(c => c.id === id);
    if (!calc) return;

    document.getElementById('modalTitle').textContent = calc.name;
    document.getElementById('modalDesc').textContent  = calc.description;
    document.getElementById('modalBody').innerHTML    = getCalculatorHTML(id);
    document.getElementById('calcModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// ── Close Modal ──────────────────────────────────────
function closeCalculator() {
    document.getElementById('calcModal').classList.add('hidden');
    document.body.style.overflow = '';
    // Reset search
    document.getElementById('calcSearch').value = '';
    filterCalculators('');
}

// Close on backdrop click
document.getElementById('calcModal').addEventListener('click', function(e) {
    if (e.target === this) closeCalculator();
});

// ── Format Currency ───────────────────────────────────
function formatINR(amount) {
    if (amount >= 10000000) return '₹' + (amount/10000000).toFixed(2) + ' Cr';
    if (amount >= 100000)   return '₹' + (amount/100000).toFixed(2) + ' L';
    return '₹' + amount.toLocaleString('en-IN', {maximumFractionDigits:2});
}

// ── Result Card HTML ──────────────────────────────────
function resultCard(items) {
    return `
        <div class="mt-5 p-4 rounded-card border border-primary-light bg-primary-light">
            ${items.map(item => `
                <div class="flex justify-between items-center py-2
                            ${item.highlight ? 'border-t border-primary/20 mt-1 pt-3' : ''}">
                    <span class="text-xs ${item.highlight ? 'font-bold text-dark' : 'text-crm-gray'}">${item.label}</span>
                    <span class="text-sm ${item.highlight ? 'font-extrabold text-primary' : 'font-semibold text-dark'}">${item.value}</span>
                </div>
            `).join('')}
        </div>`;
}

// ── Input HTML Helper ─────────────────────────────────
function calcInput(id, label, placeholder, prefix='', suffix='') {
    return `
        <div class="mb-4">
            <label class="crm-label">${label}</label>
            <div class="relative">
                ${prefix ? `<span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-crm-gray">${prefix}</span>` : ''}
                <input type="number" id="${id}" min="0"
                       class="crm-input ${prefix ? 'pl-8' : ''} ${suffix ? 'pr-16' : ''}"
                       placeholder="${placeholder}"/>
                ${suffix ? `<span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-crm-gray">${suffix}</span>` : ''}
            </div>
        </div>`;
}

function calcBtn(calcFn, resultId) {
    return `
        <div class="flex gap-3 mt-2">
            <button onclick="${calcFn}"
                    class="crm-btn-primary flex-1">
                Calculate
            </button>
            <button onclick="resetCalc('${resultId}')"
                    class="flex-1 py-4 px-6 bg-crm-light text-crm-gray
                           rounded-input border border-crm-border text-sm font-bold
                           cursor-pointer transition-all duration-200
                           hover:bg-crm-border hover:text-dark">
                Reset
            </button>
        </div>`;
}

// ── Calculator HTML Definitions ───────────────────────
function getCalculatorHTML(id) {
    switch(id) {

        // ── SIP ──────────────────────────────────────
        case 'sip': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                M = P × {[(1+r)ⁿ – 1] / r} × (1+r)
            </div>
            ${calcInput('sipAmount',    'Monthly Investment (₹)', '5000', '₹')}
            ${calcInput('sipRate',      'Expected Annual Return (%)', '12', '', '% p.a.')}
            ${calcInput('sipYears',     'Time Period', '10', '', 'Years')}
            ${calcBtn('calcSIP()', 'sipResult')}
            <div id="sipResult"></div>`;

        // ── Lumpsum ──────────────────────────────────
        case 'lumpsum': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                A = P × (1 + r)ⁿ
            </div>
            ${calcInput('lsAmount',  'Investment Amount (₹)', '100000', '₹')}
            ${calcInput('lsRate',    'Expected Annual Return (%)', '12', '', '% p.a.')}
            ${calcInput('lsYears',   'Time Period', '10', '', 'Years')}
            ${calcBtn('calcLumpsum()', 'lsResult')}
            <div id="lsResult"></div>`;

        // ── FD ───────────────────────────────────────
        case 'fd': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                A = P × (1 + r/n)^(n×t)
            </div>
            ${calcInput('fdAmount',  'Principal Amount (₹)', '100000', '₹')}
            ${calcInput('fdRate',    'Annual Interest Rate (%)', '7', '', '% p.a.')}
            ${calcInput('fdYears',   'Time Period', '5', '', 'Years')}
            <div class="mb-4">
                <label class="crm-label">Compounding Frequency</label>
                <select id="fdFreq" class="crm-input">
                    <option value="1">Annually</option>
                    <option value="2">Half Yearly</option>
                    <option value="4" selected>Quarterly</option>
                    <option value="12">Monthly</option>
                </select>
            </div>
            ${calcBtn('calcFD()', 'fdResult')}
            <div id="fdResult"></div>`;

        // ── Simple Interest ───────────────────────────
        case 'simple-interest': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                SI = (P × R × T) / 100
            </div>
            ${calcInput('siPrincipal', 'Principal Amount (₹)', '100000', '₹')}
            ${calcInput('siRate',      'Annual Interest Rate (%)', '8', '', '% p.a.')}
            ${calcInput('siYears',     'Time Period', '5', '', 'Years')}
            ${calcBtn('calcSI()', 'siResult')}
            <div id="siResult"></div>`;

        // ── Compound Interest ─────────────────────────
        case 'compound-interest': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                CI = P × (1 + r/n)^(n×t) – P
            </div>
            ${calcInput('ciPrincipal', 'Principal Amount (₹)', '100000', '₹')}
            ${calcInput('ciRate',      'Annual Interest Rate (%)', '8', '', '% p.a.')}
            ${calcInput('ciYears',     'Time Period', '5', '', 'Years')}
            <div class="mb-4">
                <label class="crm-label">Compounding Frequency</label>
                <select id="ciFreq" class="crm-input">
                    <option value="1">Annually</option>
                    <option value="2">Half Yearly</option>
                    <option value="4" selected>Quarterly</option>
                    <option value="12">Monthly</option>
                </select>
            </div>
            ${calcBtn('calcCI()', 'ciResult')}
            <div id="ciResult"></div>`;

        // ── CAGR ──────────────────────────────────────
        case 'cagr': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                CAGR = (EV/BV)^(1/n) – 1
            </div>
            ${calcInput('cagrBegin',  'Beginning Value (₹)', '100000', '₹')}
            ${calcInput('cagrEnd',    'Ending Value (₹)', '200000', '₹')}
            ${calcInput('cagrYears',  'Number of Years', '5', '', 'Years')}
            ${calcBtn('calcCAGR()', 'cagrResult')}
            <div id="cagrResult"></div>`;

        // ── Inflation ─────────────────────────────────
        case 'inflation': return `
            <div class="text-xs text-crm-gray mb-4 p-3 bg-crm-light rounded-input font-mono">
                FV = PV × (1 + r)ⁿ
            </div>
            ${calcInput('inflAmount',  'Current Value (₹)', '100000', '₹')}
            ${calcInput('inflRate',    'Inflation Rate (%)', '6', '', '% p.a.')}
            ${calcInput('inflYears',   'Number of Years', '10', '', 'Years')}
            ${calcBtn('calcInflation()', 'inflResult')}
            <div id="inflResult"></div>`;

        default: return '<p class="text-sm text-crm-gray">Calculator not found.</p>';
    }
}

// ── Calculation Functions ─────────────────────────────

function calcSIP() {
    const P = parseFloat(document.getElementById('sipAmount').value);
    const r = parseFloat(document.getElementById('sipRate').value) / 100 / 12;
    const n = parseFloat(document.getElementById('sipYears').value) * 12;
    if (!P || !r || !n) return showCalcError('sipResult');

    const M           = P * (((Math.pow(1+r, n) - 1) / r) * (1+r));
    const invested    = P * n;
    const returns     = M - invested;

    document.getElementById('sipResult').innerHTML = resultCard([
        { label: 'Monthly Investment',  value: formatINR(P) },
        { label: 'Total Invested',      value: formatINR(invested) },
        { label: 'Estimated Returns',   value: formatINR(returns) },
        { label: 'Total Value',         value: formatINR(M), highlight: true },
    ]);
}

function calcLumpsum() {
    const P = parseFloat(document.getElementById('lsAmount').value);
    const r = parseFloat(document.getElementById('lsRate').value) / 100;
    const n = parseFloat(document.getElementById('lsYears').value);
    if (!P || !r || !n) return showCalcError('lsResult');

    const A       = P * Math.pow(1+r, n);
    const returns = A - P;

    document.getElementById('lsResult').innerHTML = resultCard([
        { label: 'Principal Amount',   value: formatINR(P) },
        { label: 'Estimated Returns',  value: formatINR(returns) },
        { label: 'Total Value',        value: formatINR(A), highlight: true },
    ]);
}

function calcFD() {
    const P = parseFloat(document.getElementById('fdAmount').value);
    const r = parseFloat(document.getElementById('fdRate').value) / 100;
    const t = parseFloat(document.getElementById('fdYears').value);
    const n = parseFloat(document.getElementById('fdFreq').value);
    if (!P || !r || !t) return showCalcError('fdResult');

    const A        = P * Math.pow(1 + r/n, n*t);
    const interest = A - P;

    document.getElementById('fdResult').innerHTML = resultCard([
        { label: 'Principal Amount',  value: formatINR(P) },
        { label: 'Total Interest',    value: formatINR(interest) },
        { label: 'Maturity Amount',   value: formatINR(A), highlight: true },
    ]);
}

function calcSI() {
    const P = parseFloat(document.getElementById('siPrincipal').value);
    const R = parseFloat(document.getElementById('siRate').value);
    const T = parseFloat(document.getElementById('siYears').value);
    if (!P || !R || !T) return showCalcError('siResult');

    const SI = (P * R * T) / 100;
    const A  = P + SI;

    document.getElementById('siResult').innerHTML = resultCard([
        { label: 'Principal Amount',  value: formatINR(P) },
        { label: 'Simple Interest',   value: formatINR(SI) },
        { label: 'Total Amount',      value: formatINR(A), highlight: true },
    ]);
}

function calcCI() {
    const P = parseFloat(document.getElementById('ciPrincipal').value);
    const r = parseFloat(document.getElementById('ciRate').value) / 100;
    const t = parseFloat(document.getElementById('ciYears').value);
    const n = parseFloat(document.getElementById('ciFreq').value);
    if (!P || !r || !t) return showCalcError('ciResult');

    const A  = P * Math.pow(1 + r/n, n*t);
    const CI = A - P;

    document.getElementById('ciResult').innerHTML = resultCard([
        { label: 'Principal Amount',    value: formatINR(P) },
        { label: 'Compound Interest',   value: formatINR(CI) },
        { label: 'Total Amount',        value: formatINR(A), highlight: true },
    ]);
}

function calcCAGR() {
    const BV = parseFloat(document.getElementById('cagrBegin').value);
    const EV = parseFloat(document.getElementById('cagrEnd').value);
    const n  = parseFloat(document.getElementById('cagrYears').value);
    if (!BV || !EV || !n) return showCalcError('cagrResult');

    const CAGR   = (Math.pow(EV/BV, 1/n) - 1) * 100;
    const growth = EV - BV;

    document.getElementById('cagrResult').innerHTML = resultCard([
        { label: 'Beginning Value',  value: formatINR(BV) },
        { label: 'Ending Value',     value: formatINR(EV) },
        { label: 'Total Growth',     value: formatINR(growth) },
        { label: 'CAGR',             value: CAGR.toFixed(2) + '% p.a.', highlight: true },
    ]);
}

function calcInflation() {
    const PV = parseFloat(document.getElementById('inflAmount').value);
    const r  = parseFloat(document.getElementById('inflRate').value) / 100;
    const n  = parseFloat(document.getElementById('inflYears').value);
    if (!PV || !r || !n) return showCalcError('inflResult');

    const FV   = PV * Math.pow(1+r, n);
    const diff = FV - PV;

    document.getElementById('inflResult').innerHTML = resultCard([
        { label: 'Current Value',        value: formatINR(PV) },
        { label: 'Inflation Impact',     value: formatINR(diff) },
        { label: 'Future Value Needed',  value: formatINR(FV), highlight: true },
    ]);
}

function showCalcError(id) {
    document.getElementById(id).innerHTML = `
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-input">
            <p class="text-xs text-red-600 font-semibold">Please fill in all fields correctly.</p>
        </div>`;
}

function resetCalc(resultId) {
    // Clear result
    document.getElementById(resultId).innerHTML = '';
    // Clear all inputs in modal
    document.querySelectorAll('#modalBody input[type="number"]').forEach(input => {
        input.value = '';
    });
    // Reset selects to default
    document.querySelectorAll('#modalBody select').forEach(select => {
        select.selectedIndex = 0;
    });
}

</script>
@endpush