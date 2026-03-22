@extends('layouts.app')

@section('page-title', 'Team Members')
@section('page-subtitle', 'Manage your team')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">

    <div class="flex items-center gap-3 flex-wrap">
        {{-- Download Sample Target --}}
        <a href="{{ route('team-members.sample-target') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-primary-light text-primary
                  rounded-input text-sm font-bold hover:bg-primary hover:text-white
                  transition-all border border-primary/20">
            <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Sample Target
        </a>

        {{-- Upload Target --}}
        <button onclick="document.getElementById('targetFileInput').click()"
                class="flex items-center gap-2 px-4 py-2.5 bg-orange-50 text-orange-500
                       rounded-input text-sm font-bold hover:bg-orange-100
                       transition-all border border-orange-200">
            <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Upload Target
        </button>
        <input type="file" id="targetFileInput" class="hidden"
               accept=".xlsx,.csv" onchange="uploadTarget(this)"/>
    </div>

    {{-- Add Button --}}
    <button onclick="openCreateModal()"
            class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                   rounded-input text-sm font-bold hover:bg-primary-dark
                   transition-all hover:shadow-btn">
        <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Team Member
    </button>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="bg-white rounded-card shadow-card overflow-hidden p-5">
    <table id="membersTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Employee Code</th>
                <th>Work Type</th>
                <th>Assigned To</th>
                <th>Clients</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ═══ CREATE / EDIT MODAL ═══ --}}
<div id="memberModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <h2 id="modalTitle" class="text-base font-extrabold text-dark">Add Team Member</h2>
            <button onclick="closeModal('memberModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="memberId"/>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Full Name <span class="text-crm-danger">*</span></label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" id="memberName" class="crm-input" placeholder="Full Name"/>
                    </div>
                </div>
                <div>
                    <label class="crm-label">Employee Code</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <input type="text" id="memberEmpCode" class="crm-input" placeholder="EMP-001"/>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="crm-label">Email Address <span class="text-crm-danger">*</span></label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input type="email" id="memberEmail" class="crm-input" placeholder="email@company.com"/>
                </div>
            </div>
            <div class="mb-4">
                <label class="crm-label">
                    Password <span class="text-crm-danger" id="passwordRequired">*</span>
                    <span class="text-crm-gray font-normal text-xs" id="passwordOptional" style="display:none">(leave blank to keep current)</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password" id="memberPassword" class="crm-input" placeholder="Min 8 characters"/>
                    <button type="button" onclick="togglePass('memberPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Work Type</label>
                    <select id="memberWorkType" class="crm-input">
                        <option value="Sales">Sales</option>
                        <option value="Operations">Operations</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
                <div>
                    <label class="crm-label">Assigned To</label>
                    <select id="memberAssignedTo" class="crm-input">
                        <option value="">— Select Admin —</option>
                        @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-6" id="statusRow" style="display:none">
                <label class="crm-label">Status</label>
                <select id="memberStatus" class="crm-input">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div id="modalError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="modalErrorText"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="saveMember()" id="saveBtn" class="crm-btn-primary flex-1">Save Team Member</button>
                <button onclick="closeModal('memberModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold hover:bg-crm-border transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ TARGET MODAL ═══ --}}
<div id="targetModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <div>
                <h2 class="text-base font-extrabold text-dark">Set Target</h2>
                <p class="text-xs text-crm-gray mt-0.5" id="targetMemberName"></p>
            </div>
            <button onclick="closeModal('targetModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="targetMemberId"/>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Year <span class="text-crm-danger">*</span></label>
                    <select id="targetYear" class="crm-input">
                        @foreach(range(now()->year, now()->year + 2) as $yr)
                        <option value="{{ $yr }}" {{ $yr == now()->year ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="crm-label">Month <span class="text-crm-danger">*</span></label>
                    <select id="targetMonth" class="crm-input">
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $month)
                        <option value="{{ $i+1 }}" {{ ($i+1) == now()->month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Target Type <span class="text-crm-danger">*</span></label>
                    <select id="targetType" class="crm-input">
                        <option value="SIP">SIP</option>
                        <option value="Lumpsum">Lumpsum</option>
                    </select>
                </div>
                <div>
                    <label class="crm-label">Plan</label>
                    <select id="targetPlan" class="crm-input">
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Half Yearly">Half Yearly</option>
                        <option value="Annual">Annual</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="crm-label">Target Amount (₹) <span class="text-crm-danger">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-crm-gray">₹</span>
                    <input type="number" id="targetAmount" class="crm-input pl-8" placeholder="500000"/>
                </div>
            </div>
            <div class="mb-6">
                <label class="crm-label">Target No. of Clients</label>
                <input type="number" id="targetInvestors" class="crm-input" placeholder="10"/>
            </div>
            <div id="targetError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="targetErrorText"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="saveTarget()" class="crm-btn-primary flex-1">Save Target</button>
                <button onclick="closeModal('targetModal')"
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
    <div class="bg-white rounded-card shadow-card w-full max-w-sm p-6">
        <div class="text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 stroke-red-500 fill-none stroke-2" viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-dark mb-2">Delete Team Member?</h3>
            <p class="text-sm text-crm-gray mb-3">
                Are you sure you want to delete <strong id="deleteMemberName"></strong>?
            </p>
            <div class="bg-orange-50 border border-orange-200 rounded-input p-3 mb-6 text-left">
                <p class="text-xs font-bold text-orange-600 mb-1">⚠️ What happens:</p>
                <ul class="text-xs text-orange-600 space-y-1">
                    <li>• Assigned clients will be <strong>unassigned</strong></li>
                    <li>• Their targets will be <strong>removed</strong></li>
                    <li>• Activities & conveyances are <strong>preserved</strong></li>
                    <li>• Action can be <strong>restored</strong> by Super Admin</li>
                </ul>
            </div>
            <input type="hidden" id="deleteMemberId"/>
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
</div>

@endsection

@push('scripts')
<script>
let membersTable;

// ── Init DataTable ───────────────────────────────────
$(document).ready(function() {
    membersTable = $('#membersTable').DataTable({
        ajax: {
            url:  '{{ route("team-members.list") }}',
            type: 'GET',
            dataSrc: 'data',
            error: function() {
                showToast('Failed to load team members.', 'error');
            }
        },
        columns: [
            {
                data: null,
                render: (data, type, row, meta) => String(meta.row + 1).padStart(2, '0'),
                orderable: false,
                width: '50px'
            },
            {
                data: 'name',
                render: (data, type, row) => `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-primary">${data.charAt(0).toUpperCase()}</span>
                        </div>
                        <div>
                            <div class="font-bold text-dark text-sm">${data}</div>
                            <div class="text-xs text-gray-400">${row.email}</div>
                        </div>
                    </div>`
            },
            { data: 'employee_code' },
            { data: 'work_type' },
            { data: 'assigned_to' },
            { data: 'clients_count', title: 'Clients' },
            {
                data: 'is_active',
                render: (data, type, row) => `
                    <button onclick="toggleStatus(${row.id}, this)"
                            class="px-3 py-1 rounded-full text-xs font-bold transition-all
                                   ${data ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-red-50 text-red-500 hover:bg-red-100'}">
                        ${data ? 'Active' : 'Inactive'}
                    </button>`
            },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                render: (data, type, row) => `
                    <div class="flex items-center gap-2">
                        <button onclick="openEditModal(${row.id})" title="Edit"
                                class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center
                                       hover:bg-primary hover:text-white text-primary transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button onclick="openTargetModal(${row.id}, '${row.name}')" title="Set Target"
                                class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center
                                       hover:bg-green-500 hover:text-white text-green-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="6"/>
                                <circle cx="12" cy="12" r="2"/>
                            </svg>
                        </button>
                        <button onclick="openDeleteModal(${row.id}, '${row.name}')" title="Delete"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center
                                       hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>
                    </div>`
            }
        ],
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"lB><"flex items-center gap-2"f>>rtip',
        buttons: [
            {
                extend:    'excelHtml5',
                text:      '<svg class="w-4 h-4 stroke-current fill-none stroke-2 inline mr-1" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Excel',
                className: 'px-4 py-2 bg-green-50 text-green-600 rounded-input text-sm font-bold border border-green-200 hover:bg-green-100',
                title:     'Team Members'
            },
            {
                extend:    'pdfHtml5',
                text:      '<svg class="w-4 h-4 stroke-current fill-none stroke-2 inline mr-1" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF',
                className: 'px-4 py-2 bg-red-50 text-red-500 rounded-input text-sm font-bold border border-red-200 hover:bg-red-100',
                title:     'Team Members Report'
            },
        ],
        pageLength:  15,
        lengthMenu:  [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search:         '',
            searchPlaceholder: 'Search team members...',
            lengthMenu:     'Show _MENU_ entries',
            info:           'Showing _START_ to _END_ of _TOTAL_ members',
            infoEmpty:      'No members found',
            emptyTable:     'No team members added yet',
            paginate: {
                first:    '«',
                last:     '»',
                next:     '›',
                previous: '‹'
            }
        },
        order: [[7, 'desc']],
        responsive: true,
    });
});

// ── Refresh table after any action ───────────────────
function refreshTable() {
    membersTable.ajax.reload(null, false);
}

// ── Modal Helpers ────────────────────────────────────
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// ── Create Modal ─────────────────────────────────────
function openCreateModal() {
    document.getElementById('modalTitle').textContent     = 'Add Team Member';
    document.getElementById('memberId').value             = '';
    document.getElementById('memberName').value           = '';
    document.getElementById('memberEmail').value          = '';
    document.getElementById('memberEmpCode').value        = '';
    document.getElementById('memberPassword').value       = '';
    document.getElementById('memberWorkType').value       = 'Sales';
    document.getElementById('memberAssignedTo').value     = '';
    document.getElementById('passwordRequired').style.display = '';
    document.getElementById('passwordOptional').style.display = 'none';
    document.getElementById('statusRow').style.display    = 'none';
    document.getElementById('modalError').classList.add('hidden');
    openModal('memberModal');
}

// ── Edit Modal ───────────────────────────────────────
async function openEditModal(id) {
    const res = await ajaxGet(`/team-members/${id}`);
    if (!res.success) return showToast('Failed to load member data.', 'error');
    const m = res.data;
    document.getElementById('modalTitle').textContent     = 'Edit Team Member';
    document.getElementById('memberId').value             = m.id;
    document.getElementById('memberName').value           = m.name;
    document.getElementById('memberEmail').value          = m.email;
    document.getElementById('memberEmpCode').value        = m.employee_code ?? '';
    document.getElementById('memberPassword').value       = '';
    document.getElementById('memberWorkType').value       = m.work_type ?? 'Sales';
    document.getElementById('memberAssignedTo').value     = m.assigned_to ?? '';
    document.getElementById('memberStatus').value         = m.is_active ? '1' : '0';
    document.getElementById('passwordRequired').style.display = 'none';
    document.getElementById('passwordOptional').style.display = '';
    document.getElementById('statusRow').style.display    = 'block';
    document.getElementById('modalError').classList.add('hidden');
    openModal('memberModal');
}

// ── Save Member ──────────────────────────────────────
async function saveMember() {
    const id     = document.getElementById('memberId').value;
    const isEdit = !!id;
    const payload = {
        name:          document.getElementById('memberName').value,
        email:         document.getElementById('memberEmail').value,
        employee_code: document.getElementById('memberEmpCode').value,
        password:      document.getElementById('memberPassword').value,
        work_type:     document.getElementById('memberWorkType').value,
        assigned_to:   document.getElementById('memberAssignedTo').value || null,
        is_active:     document.getElementById('memberStatus')?.value ?? '1',
        role:          'team_member',
    };

    showLoader();
    try {
        const res = await fetch(isEdit ? `/team-members/${id}` : '{{ route("team-members.store") }}', {
            method:  isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('memberModal');
            showToast(data.message, 'success');
            refreshTable();
        } else {
            document.getElementById('modalError').classList.remove('hidden');
            document.getElementById('modalErrorText').textContent = data.message ?? 'Something went wrong.';
        }
    } catch (e) {
        hideLoader();
        showToast('Something went wrong.', 'error');
    }
}

// ── Toggle Status ────────────────────────────────────
async function toggleStatus(id) {
    showLoader();
    try {
        const res = await fetch(`/team-members/${id}/status`, {
            method:  'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
        });
        const data = await res.json();
        hideLoader();
        if (data.success) { showToast(data.message, 'success'); refreshTable(); }
        else showToast(data.message, 'error');
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Target Modal ─────────────────────────────────────
function openTargetModal(id, name) {
    document.getElementById('targetMemberId').value         = id;
    document.getElementById('targetMemberName').textContent = `Setting target for ${name}`;
    document.getElementById('targetAmount').value           = '';
    document.getElementById('targetInvestors').value        = '';
    document.getElementById('targetError').classList.add('hidden');
    openModal('targetModal');
}

async function saveTarget() {
    const id      = document.getElementById('targetMemberId').value;
    const payload = {
        year:             document.getElementById('targetYear').value,
        month:            document.getElementById('targetMonth').value,
        type:             document.getElementById('targetType').value,
        plan:             document.getElementById('targetPlan').value,
        target_amount:    document.getElementById('targetAmount').value,
        target_investors: document.getElementById('targetInvestors').value || 0,
    };
    showLoader();
    try {
        const res  = await fetch(`/team-members/${id}/target`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        hideLoader();
        if (data.success) { closeModal('targetModal'); showToast(data.message, 'success'); }
        else {
            document.getElementById('targetError').classList.remove('hidden');
            document.getElementById('targetErrorText').textContent = data.message;
        }
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Delete ───────────────────────────────────────────
function openDeleteModal(id, name) {
    document.getElementById('deleteMemberId').value         = id;
    document.getElementById('deleteMemberName').textContent = name;
    openModal('deleteModal');
}

async function confirmDelete() {
    const id = document.getElementById('deleteMemberId').value;
    showLoader();
    try {
        const res  = await fetch(`/team-members/${id}`, {
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

// ── Upload Target ────────────────────────────────────
async function uploadTarget(input) {
    const file = input.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
    showLoader();
    try {
        const res  = await fetch('{{ route("team-members.upload-target") }}', {
            method: 'POST',
            body:   formData,
        });
        const data = await res.json();
        hideLoader();
        if (data.success) showToast(data.message, 'success');
        else showToast(data.message, 'error');
    } catch(e) { hideLoader(); showToast('Upload failed.', 'error'); }
    input.value = '';
}
</script>
@endpush