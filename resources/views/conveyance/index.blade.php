@extends('layouts.app')

@section('page-title', 'Conveyance')
@section('page-subtitle', auth()->user()->isSuperAdmin() ? 'Manage all conveyance claims' : 'Your conveyance claims')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">

    {{-- Stats (Super Admin only) --}}
    @if(auth()->user()->isSuperAdmin())
    <div class="flex items-center gap-4" id="conveyanceStats">
        <div class="bg-white rounded-card px-5 py-3 shadow-card flex items-center gap-3">
            <div class="w-8 h-8 rounded-[8px] bg-orange-50 flex items-center justify-center">
                <svg class="w-4 h-4 stroke-orange-500 fill-none stroke-2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="text-lg font-extrabold text-dark" id="statPending">—</div>
                <div class="text-xs text-crm-gray">Pending</div>
            </div>
        </div>
        <div class="bg-white rounded-card px-5 py-3 shadow-card flex items-center gap-3">
            <div class="w-8 h-8 rounded-[8px] bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 stroke-green-500 fill-none stroke-2" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="text-lg font-extrabold text-dark" id="statApproved">—</div>
                <div class="text-xs text-crm-gray">Approved</div>
            </div>
        </div>
        <div class="bg-white rounded-card px-5 py-3 shadow-card flex items-center gap-3">
            <div class="w-8 h-8 rounded-[8px] bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 stroke-red-500 fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            <div>
                <div class="text-lg font-extrabold text-dark" id="statRejected">—</div>
                <div class="text-xs text-crm-gray">Rejected</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Submit Button (Team Member only) --}}
    @if(!auth()->user()->isSuperAdmin())
    <div></div>
    @endif

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
                <th>Amount (₹)</th>
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

            {{-- Row 1: Type + Date --}}
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

            {{-- Amount --}}
            <div class="mb-4">
                <label class="crm-label">Amount (₹) <span class="text-crm-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-crm-gray">₹</span>
                    <input type="number" id="convAmount" class="crm-input pl-8"
                           placeholder="0.00" min="1" step="0.01"/>
                </div>
            </div>

            {{-- Remarks --}}
            <div class="mb-4">
                <label class="crm-label">Remarks</label>
                <textarea id="convRemarks" class="crm-input h-20 resize-none"
                          placeholder="Describe the expense..."></textarea>
            </div>

            {{-- Bill Upload --}}
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

            {{-- Error --}}
            <div id="claimError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="claimErrorText"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="submitClaim()" class="crm-btn-primary flex-1">
                    Submit Claim
                </button>
                <button onclick="closeModal('claimModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold
                               hover:bg-crm-border transition-all">
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

            {{-- Claim Summary --}}
            <div class="bg-crm-light rounded-input p-4 mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-crm-gray font-medium">Team Member</span>
                    <span class="font-bold text-dark" id="actionMemberName"></span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-crm-gray font-medium">Type</span>
                    <span class="font-bold text-dark" id="actionType2"></span>
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
                <button onclick="confirmAction()" id="actionBtn" class="crm-btn-primary flex-1">
                    Confirm
                </button>
                <button onclick="closeModal('actionModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold
                               hover:bg-crm-border transition-all">
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
        <p class="text-sm text-crm-gray mb-6">
            This will delete your pending claim. This action cannot be undone.
        </p>
        <input type="hidden" id="deleteConveyanceId"/>
        <div class="flex gap-3">
            <button onclick="confirmDelete()"
                    class="flex-1 py-3 bg-red-500 text-white rounded-input
                           text-sm font-bold hover:bg-red-600 transition-all">
                Yes, Delete
            </button>
            <button onclick="closeModal('deleteModal')"
                    class="flex-1 py-3 bg-crm-light text-crm-gray rounded-input
                           border border-crm-border text-sm font-bold
                           hover:bg-crm-border transition-all">
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

// ── Init DataTable ───────────────────────────────────
$(document).ready(function() {

    const columns = [
        {
            data: null, orderable: false, width: '50px',
            render: (data, type, row, meta) => String(meta.row + 1).padStart(2, '0')
        },
    ];

    // Super Admin sees Team Member column
    if (isSuperAdmin) {
        columns.push({
            data: 'team_member',
            render: (data) => `<span class="font-semibold text-dark">${data}</span>`
        });
    }

    columns.push(
        {
            data: 'conveyance_type',
            render: (data) => {
                const colors = {
                    'Travel':        'bg-blue-50 text-blue-600',
                    'Food':          'bg-green-50 text-green-600',
                    'Accommodation': 'bg-purple-50 text-purple-600',
                    'Fuel':          'bg-orange-50 text-orange-500',
                    'Other':         'bg-crm-light text-crm-gray',
                };
                const cls = colors[data] || 'bg-crm-light text-crm-gray';
                return `<span class="px-2 py-1 rounded-full text-xs font-bold ${cls}">${data}</span>`;
            }
        },
        { data: 'conveyance_date' },
        {
            data: 'amount',
            render: (data) => `<span class="font-bold text-dark">₹${data}</span>`
        },
        { data: 'remarks' },
        {
            data: 'bill_path',
            orderable: false,
            render: (data) => data
                ? `<a href="${data}" target="_blank"
                      class="flex items-center gap-1 text-xs text-primary font-bold hover:underline">
                       <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                           <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                           <circle cx="12" cy="12" r="3"/>
                       </svg>
                       View
                   </a>`
                : '<span class="text-xs text-crm-gray">No bill</span>'
        },
        {
            data: 'status',
            render: (data) => {
                const config = {
                    pending:  { cls: 'bg-orange-50 text-orange-500', label: 'Pending' },
                    approved: { cls: 'bg-green-50 text-green-600',   label: 'Approved' },
                    rejected: { cls: 'bg-red-50 text-red-500',       label: 'Rejected' },
                };
                const c = config[data] || config.pending;
                return `<span class="px-3 py-1 rounded-full text-xs font-bold ${c.cls}">${c.label}</span>`;
            }
        },
        { data: 'action_remarks' },
        {
            data: null, orderable: false,
            render: (data, type, row) => {
                let actions = '';

                // Super Admin — Approve/Reject pending claims
                if (isSuperAdmin && row.status === 'pending') {
                    actions += `
                        <button onclick="openActionModal(${row.id}, 'approve', '${row.team_member}', '${row.conveyance_type}', '${row.amount}')"
                                title="Approve"
                                class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center
                                       hover:bg-green-500 hover:text-white text-green-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </button>
                        <button onclick="openActionModal(${row.id}, 'reject', '${row.team_member}', '${row.conveyance_type}', '${row.amount}')"
                                title="Reject"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center
                                       hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>`;
                }

                // Team Member — Delete own pending claims
                if (!isSuperAdmin && row.status === 'pending') {
                    actions += `
                        <button onclick="openDeleteModal(${row.id})"
                                title="Delete"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center
                                       hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>`;
                }

                if (!actions) actions = '<span class="text-xs text-crm-gray">—</span>';
                return `<div class="flex items-center gap-2">${actions}</div>`;
            }
        }
    );

    conveyanceTable = $('#conveyanceTable').DataTable({
        ajax: {
            url:        '{{ route("conveyance.list") }}',
            type:       'GET',
            dataSrc:    'data',
            beforeSend: function() { showLoader(); },
            complete:   function(res) {
                hideLoader();
                // Update stats
                if (isSuperAdmin && res.responseJSON?.data) {
                    const data     = res.responseJSON.data;
                    const pending  = data.filter(r => r.status === 'pending').length;
                    const approved = data.filter(r => r.status === 'approved').length;
                    const rejected = data.filter(r => r.status === 'rejected').length;
                    document.getElementById('statPending').textContent  = pending;
                    document.getElementById('statApproved').textContent = approved;
                    document.getElementById('statRejected').textContent = rejected;
                }
            },
            error: function() {
                hideLoader();
                showToast('Failed to load conveyances.', 'error');
            }
        },
        processing: false,
        serverSide: false,
        columns:    columns,
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"lB><"flex items-center gap-2"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5', text: 'Excel',
                className: 'px-4 py-2 bg-green-50 text-green-600 rounded-input text-sm font-bold border border-green-200 hover:bg-green-100',
                title: 'Conveyance Claims'
            },
            {
                extend: 'pdfHtml5', text: 'PDF',
                className: 'px-4 py-2 bg-red-50 text-red-500 rounded-input text-sm font-bold border border-red-200 hover:bg-red-100',
                title: 'Conveyance Claims Report'
            },
        ],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search: '', searchPlaceholder: 'Search claims...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ claims',
            emptyTable: 'No conveyance claims found',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' }
        },
        order: [[isSuperAdmin ? 3 : 2, 'desc']],
        responsive: true,
    });
});

function refreshTable() { conveyanceTable.ajax.reload(null, false); }

// ── Modal Helpers ────────────────────────────────────
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// ── Show File Name ───────────────────────────────────
function showFileName(input) {
    const text = document.getElementById('fileNameText');
    text.textContent = input.files[0]
        ? `Selected: ${input.files[0].name}`
        : 'Click to upload JPG, PNG or PDF (max 2MB)';
}

// ── Create Modal ─────────────────────────────────────
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

// ── Submit Claim ─────────────────────────────────────
async function submitClaim() {
    const type    = document.getElementById('convType').value;
    const date    = document.getElementById('convDate').value;
    const amount  = document.getElementById('convAmount').value;

    // Frontend validation
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

    // Use FormData for file upload
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
        const res  = await fetch('{{ route("conveyance.store") }}', {
            method: 'POST',
            body:   formData,
        });
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
                document.getElementById('claimErrorText').innerHTML =
                    allErrors.map(e => `• ${e}`).join('<br>');
            } else {
                document.getElementById('claimErrorText').textContent = data.message;
            }
        }
    } catch(e) {
        hideLoader();
        showToast('Something went wrong.', 'error');
    }
}

// ── Approve / Reject Modal ───────────────────────────
function openActionModal(id, action, member, type, amount) {
    document.getElementById('actionConveyanceId').value  = id;
    document.getElementById('actionType').value          = action;
    document.getElementById('actionRemarks').value       = '';
    document.getElementById('actionMemberName').textContent = member;
    document.getElementById('actionType2').textContent   = type;
    document.getElementById('actionAmount').textContent  = '₹' + amount;
    document.getElementById('actionError').classList.add('hidden');

    if (action === 'approve') {
        document.getElementById('actionModalTitle').textContent = 'Approve Claim';
        document.getElementById('actionBtn').className =
            'crm-btn-primary flex-1 !bg-green-500 hover:!bg-green-600';
        document.getElementById('actionBtn').textContent = 'Approve';
        document.getElementById('remarksRequired').style.display = 'none';
        document.getElementById('remarksOptional').style.display = '';
    } else {
        document.getElementById('actionModalTitle').textContent = 'Reject Claim';
        document.getElementById('actionBtn').className =
            'crm-btn-primary flex-1 !bg-red-500 hover:!bg-red-600';
        document.getElementById('actionBtn').textContent = 'Reject';
        document.getElementById('remarksRequired').style.display = '';
        document.getElementById('remarksOptional').style.display = 'none';
    }

    openModal('actionModal');
}

async function confirmAction() {
    const id      = document.getElementById('actionConveyanceId').value;
    const action  = document.getElementById('actionType').value;
    const remarks = document.getElementById('actionRemarks').value.trim();

    // Remarks required for rejection
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
        hideLoader();

        if (data.success) {
            closeModal('actionModal');
            showToast(data.message, 'success');
            refreshTable();
        } else {
            document.getElementById('actionError').classList.remove('hidden');
            document.getElementById('actionErrorText').textContent = data.message;
        }
    } catch(e) {
        hideLoader();
        showToast('Something went wrong.', 'error');
    }
}

// ── Delete Modal ─────────────────────────────────────
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
    } catch(e) {
        hideLoader();
        showToast('Something went wrong.', 'error');
    }
}
</script>
@endpush