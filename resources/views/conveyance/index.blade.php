@extends('layouts.app')

@section('page-title', 'Conveyance')
@section('page-subtitle', auth()->user()->isSuperAdmin() ? 'Manage all conveyance claims' : 'Your conveyance claims')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">
    <div class="flex items-center gap-3">
        {{-- Export Excel --}}
        <a href="{{ route('conveyance.export-excel') }}"
           id="exportExcelBtn"
           class="flex items-center gap-2 px-5 py-2.5 bg-green-500 text-white
                  rounded-input text-sm font-bold hover:bg-green-600
                  transition-all hover:shadow-btn">
            <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            Export Excel
        </a>

        {{-- Stats (Super Admin only) --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-card px-4 py-2 shadow-card flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                <span class="text-xs text-crm-gray">Pending:</span>
                <span class="text-sm font-extrabold text-dark" id="statPending">—</span>
            </div>
            <div class="bg-white rounded-card px-4 py-2 shadow-card flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-xs text-crm-gray">Approved:</span>
                <span class="text-sm font-extrabold text-dark" id="statApproved">—</span>
            </div>
            <div class="bg-white rounded-card px-4 py-2 shadow-card flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                <span class="text-xs text-crm-gray">Rejected:</span>
                <span class="text-sm font-extrabold text-dark" id="statRejected">—</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Submit Claim Button --}}
    <button onclick="openCreateModal()"
            class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                   rounded-input text-sm font-bold hover:bg-primary-dark
                   transition-all hover:shadow-btn">
        <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Submit Claim
    </button>
</div>

{{-- ═══ FILTERS ═══ --}}
<div class="bg-white rounded-card shadow-card p-4 mb-5">
    <div class="flex items-end gap-3 flex-wrap">

        {{-- Status --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Status</label>
            <select id="filterStatus" class="crm-input">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        {{-- Type --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Type</label>
            <select id="filterType" class="crm-input">
                <option value="">All Types</option>
                <option value="Travel">Travel</option>
                <option value="Food">Food</option>
                <option value="Accommodation">Accommodation</option>
                <option value="Fuel">Fuel</option>
                <option value="Other">Other</option>
            </select>
        </div>

        {{-- Team Member (Super Admin only) --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Team Member</label>
            <input type="text" id="filterMember" class="crm-input"
                   placeholder="Search member..."/>
        </div>
        @endif

        {{-- Buttons --}}
        <div class="flex items-center gap-2 pb-0.5">
            <button onclick="applyFilters()"
                    class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                           rounded-input text-sm font-bold hover:bg-primary-dark transition-all">
                <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                Filter
            </button>
            <button onclick="resetFilters()"
                    class="flex items-center gap-2 px-5 py-2.5 bg-crm-light text-crm-gray
                           rounded-input text-sm font-bold hover:bg-crm-border transition-all
                           border border-crm-border">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                </svg>
                Reset
            </button>
        </div>
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="bg-white rounded-card shadow-card overflow-hidden p-5">
    <table id="conveyanceTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>S.No</th>
                @if(auth()->user()->isSuperAdmin())
                <th>Team Member</th>
                @endif
                <th>Type</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th>Bill</th>
                <th>Status</th>
                <th>Action Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ═══ SUBMIT CLAIM MODAL ═══ --}}
<div id="claimModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <h2 class="text-base font-extrabold text-dark">Submit Conveyance Claim</h2>
            <button onclick="closeModal('claimModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Conveyance Type <span class="text-crm-danger">*</span></label>
                    <select id="convType" class="crm-input">
                        <option value="">— Select Type —</option>
                        <option value="Travel">Travel</option>
                        <option value="Food">Food</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Fuel">Fuel</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="crm-label">Expense Date <span class="text-crm-danger">*</span></label>
                    <input type="date" id="convDate" class="crm-input"
                           max="{{ now()->toDateString() }}"/>
                </div>
            </div>
            <div class="mb-4">
                <label class="crm-label">Amount (₹) <span class="text-crm-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-crm-gray">₹</span>
                    <input type="number" id="convAmount" class="crm-input pl-8"
                           placeholder="0.00" min="1" step="0.01"/>
                </div>
            </div>
            <div class="mb-4">
                <label class="crm-label">Remarks</label>
                <textarea id="convRemarks" class="crm-input h-20 resize-none"
                          placeholder="Describe the expense..."></textarea>
            </div>
            <div class="mb-6">
                <label class="crm-label">Upload Bill / Receipt</label>
                <div class="border-2 border-dashed border-crm-border rounded-input p-4 text-center
                            hover:border-primary transition-all cursor-pointer"
                     onclick="document.getElementById('convBill').click()">
                    <input type="file" id="convBill" class="hidden"
                           accept=".jpg,.jpeg,.png,.pdf"
                           onchange="showFileName(this)"/>
                    <svg class="w-8 h-8 stroke-crm-gray fill-none stroke-2 mx-auto mb-2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p class="text-xs text-crm-gray" id="fileNameText">
                        Click to upload JPG, PNG or PDF (max 2MB)
                    </p>
                </div>
            </div>
            <div id="claimError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="claimErrorText"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="submitClaim()" class="crm-btn-primary flex-1">Submit Claim</button>
                <button onclick="closeModal('claimModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold hover:bg-crm-border transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ APPROVE / REJECT MODAL ═══ --}}
<div id="actionModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <h2 id="actionModalTitle" class="text-base font-extrabold text-dark">Approve Claim</h2>
            <button onclick="closeModal('actionModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="actionConveyanceId"/>
            <input type="hidden" id="actionType"/>
            <div class="bg-crm-light rounded-input p-4 mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-crm-gray font-medium">Team Member</span>
                    <span class="font-bold text-dark" id="actionMemberName"></span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-crm-gray font-medium">Type</span>
                    <span class="font-bold text-dark" id="actionTypeDisplay"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-crm-gray font-medium">Amount</span>
                    <span class="font-bold text-primary text-base" id="actionAmount"></span>
                </div>
            </div>
            <div class="mb-6">
                <label class="crm-label">
                    Remarks
                    <span class="text-crm-danger" id="remarksRequired" style="display:none">*</span>
                    <span class="text-crm-gray font-normal text-xs" id="remarksOptional">(optional)</span>
                </label>
                <textarea id="actionRemarks" class="crm-input h-20 resize-none"
                          placeholder="Add remarks..."></textarea>
            </div>
            <div id="actionError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="actionErrorText"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="confirmAction()" id="actionBtn" class="crm-btn-primary flex-1">Confirm</button>
                <button onclick="closeModal('actionModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold hover:bg-crm-border transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ DELETE MODAL ═══ --}}
<div id="deleteModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 stroke-red-500 fill-none stroke-2" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                <path d="M10 11v6"/><path d="M14 11v6"/>
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-dark mb-2">Delete Claim?</h3>
        <p class="text-sm text-crm-gray mb-6">This will delete your pending claim.</p>
        <input type="hidden" id="deleteConveyanceId"/>
        <div class="flex gap-3">
            <button onclick="confirmDelete()"
                    class="flex-1 py-3 bg-red-500 text-white rounded-input
                           text-sm font-bold hover:bg-red-600 transition-all">
                Yes, Delete
            </button>
            <button onclick="closeModal('deleteModal')"
                    class="flex-1 py-3 bg-crm-light text-crm-gray rounded-input
                           border border-crm-border text-sm font-bold hover:bg-crm-border transition-all">
                Cancel
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const isSuperAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};
let conveyanceTable;

$(document).ready(function() {
    const columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
    ];

    if (isSuperAdmin) {
        columns.push({ data: 'team_member', name: 'team_member', title: 'Team Member' });
    }

    columns.push(
        { data: 'type_badge',      name: 'type_badge',      title: 'Type',           orderable: false },
        { data: 'conveyance_date', name: 'conveyance_date', title: 'Date' },
        { data: 'amount_fmt',      name: 'amount',          title: 'Amount',          orderable: true },
        { data: 'remarks',         name: 'remarks',         title: 'Remarks' },
        { data: 'bill_link',       name: 'bill_link',       title: 'Bill',            orderable: false, searchable: false },
        { data: 'status_badge',    name: 'status_badge',    title: 'Status',          orderable: false },
        { data: 'action_remarks',  name: 'action_remarks',  title: 'Action Remarks' },
        { data: 'actions',         name: 'actions',         title: 'Actions',         orderable: false, searchable: false },
    );

    conveyanceTable = $('#conveyanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  '{{ route("conveyance.list") }}',
            type: 'GET',
            beforeSend: function() { showLoader(); },
            complete: function(res) {
                hideLoader();
                // Update stats
                if (isSuperAdmin) {
                    const data = res.responseJSON?.data ?? [];
                    // Stats come from separate endpoint or count from response
                    updateStats();
                }
            },
            error: function() {
                hideLoader();
                showToast('Failed to load conveyances.', 'error');
            },
            data: function(d) {
                d.filter_status = $('#filterStatus').val();
                d.filter_type   = $('#filterType').val();
                d.filter_member = isSuperAdmin ? $('#filterMember').val() : '';
            }
        },
        columns: columns,
        //dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"lB><"flex items-center gap-2"f>>rtip',
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rtip',
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search: '', searchPlaceholder: 'Search claims...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ claims',
            emptyTable: 'No conveyance claims found',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            processing: '<div class="flex items-center gap-2 text-primary"><div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div> Loading...</div>'
        },
        order: [[isSuperAdmin ? 3 : 2, 'desc']],
        responsive: true,
    });

    // Load stats separately
    if (isSuperAdmin) updateStats();
});

// ── Stats ─────────────────────────────────────────────
async function updateStats() {
    try {
        const res = await fetch('{{ route("conveyance.list") }}?stats_only=1&per_page=1', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        });
        // We'll get stats from a dedicated endpoint
    } catch(e) {}

    // Simple count from current visible data
    const res = await ajaxGet('{{ route("conveyance.stats") }}');
    if (res.success) {
        $('#statPending').text(res.pending);
        $('#statApproved').text(res.approved);
        $('#statRejected').text(res.rejected);
    }
}

function refreshTable() {
    conveyanceTable.ajax.reload(null, false);
    if (isSuperAdmin) updateStats();
}

function updateExportLinks() {
    const params = new URLSearchParams();
    const status = $('#filterStatus').val();
    const type   = $('#filterType').val();
    const member = isSuperAdmin ? $('#filterMember').val() : '';

    if (status) params.append('filter_status', status);
    if (type)   params.append('filter_type', type);
    if (member) params.append('filter_member', member);

    const query = params.toString() ? '?' + params.toString() : '';
    $('#exportExcelBtn').attr('href', '{{ route("conveyance.export-excel") }}' + query);
}

function applyFilters() {
    conveyanceTable.ajax.reload(null, false);
    updateExportLinks();
}

function resetFilters() {
    $('#filterStatus').val('');
    $('#filterType').val('');
    if (isSuperAdmin) $('#filterMember').val('');
    conveyanceTable.ajax.reload(null, false);
    updateExportLinks();
}

// ── Modal Helpers ─────────────────────────────────────
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function showFileName(input) {
    document.getElementById('fileNameText').textContent = input.files[0]
        ? 'Selected: ' + input.files[0].name
        : 'Click to upload JPG, PNG or PDF (max 2MB)';
}

// ── Create Modal ──────────────────────────────────────
function openCreateModal() {
    document.getElementById('convType').value    = '';
    document.getElementById('convDate').value    = '';
    document.getElementById('convAmount').value  = '';
    document.getElementById('convRemarks').value = '';
    document.getElementById('convBill').value    = '';
    document.getElementById('fileNameText').textContent = 'Click to upload JPG, PNG or PDF (max 2MB)';
    document.getElementById('claimError').classList.add('hidden');
    openModal('claimModal');
}

// ── Submit Claim ──────────────────────────────────────
async function submitClaim() {
    const type   = document.getElementById('convType').value;
    const date   = document.getElementById('convDate').value;
    const amount = document.getElementById('convAmount').value;

    if (!type) {
        document.getElementById('claimError').classList.remove('hidden');
        document.getElementById('claimErrorText').textContent = '• Please select a conveyance type.';
        return;
    }
    if (!date) {
        document.getElementById('claimError').classList.remove('hidden');
        document.getElementById('claimErrorText').textContent = '• Please select the expense date.';
        return;
    }
    if (!amount || parseFloat(amount) < 1) {
        document.getElementById('claimError').classList.remove('hidden');
        document.getElementById('claimErrorText').textContent = '• Please enter a valid amount.';
        return;
    }
    document.getElementById('claimError').classList.add('hidden');

    const formData = new FormData();
    formData.append('conveyance_type', type);
    formData.append('conveyance_date', date);
    formData.append('amount',          amount);
    formData.append('remarks',         document.getElementById('convRemarks').value);
    formData.append('_token',          document.querySelector('meta[name=csrf-token]').content);
    const bill = document.getElementById('convBill').files[0];
    if (bill) formData.append('bill', bill);

    showLoader();
    try {
        const res  = await fetch('{{ route("conveyance.store") }}', { method: 'POST', body: formData });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('claimModal');
            showToast(data.message, 'success');
            refreshTable();
        } else {
            document.getElementById('claimError').classList.remove('hidden');
            if (data.errors) {
                const allErrors = Object.values(data.errors).flat();
                document.getElementById('claimErrorText').innerHTML = allErrors.map(e => `• ${e}`).join('<br>');
            } else {
                document.getElementById('claimErrorText').textContent = data.message;
            }
        }
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Approve / Reject ──────────────────────────────────
function openActionModal(id, action, member, type, amount) {
    document.getElementById('actionConveyanceId').value    = id;
    document.getElementById('actionType').value            = action;
    document.getElementById('actionRemarks').value         = '';
    document.getElementById('actionMemberName').textContent = member;
    document.getElementById('actionTypeDisplay').textContent = type;
    document.getElementById('actionAmount').textContent    = '₹' + amount;
    document.getElementById('actionError').classList.add('hidden');

    if (action === 'approve') {
        document.getElementById('actionModalTitle').textContent  = 'Approve Claim';
        document.getElementById('actionBtn').style.background    = '#10b981';
        document.getElementById('actionBtn').textContent         = 'Approve';
        document.getElementById('remarksRequired').style.display = 'none';
        document.getElementById('remarksOptional').style.display = '';
    } else {
        document.getElementById('actionModalTitle').textContent  = 'Reject Claim';
        document.getElementById('actionBtn').style.background    = '#ef4444';
        document.getElementById('actionBtn').textContent         = 'Reject';
        document.getElementById('remarksRequired').style.display = '';
        document.getElementById('remarksOptional').style.display = 'none';
    }
    openModal('actionModal');
}

async function confirmAction() {
    const id      = document.getElementById('actionConveyanceId').value;
    const action  = document.getElementById('actionType').value;
    const remarks = document.getElementById('actionRemarks').value.trim();

    if (action === 'reject' && !remarks) {
        document.getElementById('actionError').classList.remove('hidden');
        document.getElementById('actionErrorText').textContent = 'Please provide a reason for rejection.';
        return;
    }
    document.getElementById('actionError').classList.add('hidden');

    showLoader();
    try {
        const res  = await fetch(`/conveyance/${id}/${action}`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ action_remarks: remarks }),
        });
        const data = await res.json();
eyLoader();
        if (data.success) {
            closeModal('actionModal');
            showToast(data.message, 'success');
            refreshTable();
        } else {
            document.getElementById('actionError').classList.remove('hidden');
            document.getElementById('actionErrorText').textContent = data.message;
        }
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Delete ────────────────────────────────────────────
function openDeleteModal(id) {
    document.getElementById('deleteConveyanceId').value = id;
    openModal('deleteModal');
}

async function confirmDelete() {
    const id = document.getElementById('deleteConveyanceId').value;
    showLoader();
    try {
        const res  = await fetch(`/conveyance/${id}`, {
            method:  'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
        });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('deleteModal');
            showToast(data.message, 'success');
            refreshTable();
        } else showToast(data.message, 'error');
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}
</script>
@endpush