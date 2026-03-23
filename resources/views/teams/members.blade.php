@extends('layouts.app')

@section('page-title', $team->name)
@section('page-subtitle', 'Team Code: ' . $team->code . ' · Manage team members')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">

    {{-- Back Button --}}
    <a href="{{ route('teams.index') }}"
       class="flex items-center gap-2 px-4 py-2.5 bg-crm-light text-crm-gray
              rounded-input text-sm font-bold hover:bg-crm-border transition-all border border-crm-border">
        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to Teams
    </a>

    {{-- Team Status Badge --}}
    <div class="flex items-center gap-3">
        <span class="px-3 py-1.5 rounded-full text-xs font-bold
                     {{ $team->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
            {{ $team->is_active ? 'Active Team' : 'Inactive Team' }}
        </span>

        {{-- Assign Member Button --}}
        <button onclick="openAssignModal()"
                class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                       rounded-input text-sm font-bold hover:bg-primary-dark
                       transition-all hover:shadow-btn">
            <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Assign Member
        </button>
    </div>
</div>

{{-- ═══ TEAM STATS ═══ --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-card shadow-card p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-[10px] bg-primary-light flex items-center justify-center">
            <svg class="w-5 h-5 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="text-xl font-extrabold text-dark" id="statTotal">—</div>
            <div class="text-xs text-crm-gray">Total Members</div>
        </div>
    </div>
    <div class="bg-white rounded-card shadow-card p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-[10px] bg-blue-50 flex items-center justify-center">
            <svg class="w-5 h-5 stroke-blue-500 fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <div class="text-xl font-extrabold text-dark" id="statSubAdmin">—</div>
            <div class="text-xs text-crm-gray">Sub Admin</div>
        </div>
    </div>
    <div class="bg-white rounded-card shadow-card p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-[10px] bg-purple-50 flex items-center justify-center">
            <svg class="w-5 h-5 stroke-purple-500 fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <div class="text-xl font-extrabold text-dark" id="statOpsAdmin">—</div>
            <div class="text-xs text-crm-gray">Ops Admin</div>
        </div>
    </div>
    <div class="bg-white rounded-card shadow-card p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-[10px] bg-green-50 flex items-center justify-center">
            <svg class="w-5 h-5 stroke-green-500 fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <div class="text-xl font-extrabold text-dark" id="statRM">—</div>
            <div class="text-xs text-crm-gray">Team Members (RMs)</div>
        </div>
    </div>
</div>

{{-- ═══ MEMBERS TABLE ═══ --}}
<div class="bg-white rounded-card shadow-card overflow-hidden p-5">
    <table id="membersTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Employee Code</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="membersTableBody">
        </tbody>
    </table>
</div>

{{-- ═══ ASSIGN MEMBER MODAL ═══ --}}
<div id="assignModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <div>
                <h2 class="text-base font-extrabold text-dark">Assign Member to Team</h2>
                <p class="text-xs text-crm-gray mt-0.5">{{ $team->name }} ({{ $team->code }})</p>
            </div>
            <button onclick="closeModal('assignModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <label class="crm-label">Select User <span class="text-crm-danger">*</span></label>
                <select id="assignUserId" class="crm-input">
                    <option value="">— Select User —</option>
                    @foreach($unassignedUsers as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                        {{ $user->employee_code ? '(' . $user->employee_code . ')' : '' }}
                        — {{ $user->email }}
                    </option>
                    @endforeach
                </select>
                @if($unassignedUsers->isEmpty())
                <p class="text-xs text-crm-gray mt-2">
                    No unassigned users available. All users are already assigned to a team.
                </p>
                @endif
            </div>

            <div id="assignError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="assignErrorText"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="assignMember()" class="crm-btn-primary flex-1">
                    Assign to Team
                </button>
                <button onclick="closeModal('assignModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold
                               hover:bg-crm-border transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ REMOVE MEMBER MODAL ═══ --}}
<div id="removeModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 stroke-orange-500 fill-none stroke-2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="8.5" cy="7" r="4"/>
                <line x1="18" y1="8" x2="23" y2="13"/>
                <line x1="23" y1="8" x2="18" y2="13"/>
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-dark mb-2">Remove from Team?</h3>
        <p class="text-sm text-crm-gray mb-6">
            Are you sure you want to remove <strong id="removeMemberName"></strong> from this team?
            Their account will remain active.
        </p>
        <input type="hidden" id="removeMemberId"/>
        <div class="flex gap-3">
            <button onclick="confirmRemove()"
                    class="flex-1 py-3 bg-orange-500 text-white rounded-input
                           text-sm font-bold hover:bg-orange-600 transition-all">
                Yes, Remove
            </button>
            <button onclick="closeModal('removeModal')"
                    class="flex-1 py-3 bg-crm-light text-crm-gray rounded-input
                           border border-crm-border text-sm font-bold
                           hover:bg-crm-border transition-all">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- ═══ TRANSFER CLIENTS MODAL ═══ --}}
<div id="transferModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <div>
                <h2 class="text-base font-extrabold text-dark">Transfer Clients</h2>
                <p class="text-xs text-crm-gray mt-0.5">Move all clients from {{ $team->name }} to another team</p>
            </div>
            <button onclick="closeModal('transferModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="bg-orange-50 border border-orange-200 rounded-input p-3 mb-4">
                <p class="text-xs text-orange-600 font-bold">
                    ⚠️ This will transfer ALL clients from {{ $team->name }} to the selected team.
                </p>
            </div>
            <div class="mb-6">
                <label class="crm-label">Transfer to Team <span class="text-crm-danger">*</span></label>
                <select id="targetTeamId" class="crm-input">
                    <option value="">— Select Target Team —</option>
                    @foreach($activeTeams as $t)
                        @if($t->id !== $team->id)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div id="transferError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="transferErrorText"></p>
            </div>
            <div class="flex gap-3">
                <button onclick="transferClients()" class="crm-btn-primary flex-1 !bg-orange-500 hover:!bg-orange-600">
                    Transfer Clients
                </button>
                <button onclick="closeModal('transferModal')"
                        class="flex-1 py-4 px-6 bg-crm-light text-crm-gray rounded-input
                               border border-crm-border text-sm font-bold
                               hover:bg-crm-border transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const teamId = {{ $team->id }};
let membersTable;

$(document).ready(function() {
    membersTable = $('#membersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:        `/teams/${teamId}/members/list`,
            type:       'GET',
            beforeSend: function() { showLoader(); },
            complete:   function(res) {
                hideLoader();
                // Update stats
                if (res.responseJSON?.data) {
                    updateStats(res.responseJSON);
                }
            },
            error: function() {
                hideLoader();
                showToast('Failed to load members.', 'error');
            }
        },
        columns: [
            { data: 'DT_RowIndex',  name: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
            { data: 'name_email',   name: 'name',         title: 'Name' },
            { data: 'employee_code',name: 'employee_code',title: 'Employee Code' },
            { data: 'role_badge',   name: 'role_badge',   title: 'Role',    orderable: false },
            { data: 'status_badge', name: 'status_badge', title: 'Status',  orderable: false },
            { data: 'created_date', name: 'created_at',   title: 'Joined' },
            { data: 'actions',      name: 'actions',      title: 'Actions', orderable: false, searchable: false },
        ],
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rtip',
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search: '', searchPlaceholder: 'Search members...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ members',
            emptyTable: 'No members assigned to this team yet.',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            processing: '<div class="flex items-center gap-2 text-primary"><div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div> Loading...</div>'
        },
        order: [[5, 'desc']],
        responsive: true,
        drawCallback: function() {
            updateStatsFromTable();
        }
    });
});

function refreshMembers() { membersTable.ajax.reload(null, false); }

// ── Update Stats ──────────────────────────────────────
function updateStatsFromTable() {
    // Count from current table data
    let total = 0, subAdmin = 0, opsAdmin = 0, rm = 0;
    membersTable.rows().data().each(function(row) {
        total++;
        const roleHtml = row.role_badge;
        if (roleHtml.includes('Sub Admin'))  subAdmin++;
        if (roleHtml.includes('Ops Admin'))  opsAdmin++;
        if (roleHtml.includes('Team Member')) rm++;
    });
    document.getElementById('statTotal').textContent    = membersTable.page.info().recordsTotal;
    document.getElementById('statSubAdmin').textContent = subAdmin;
    document.getElementById('statOpsAdmin').textContent = opsAdmin;
    document.getElementById('statRM').textContent       = rm;
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

// ── Assign Member ─────────────────────────────────────
function openAssignModal() {
    document.getElementById('assignUserId').value = '';
    document.getElementById('assignError').classList.add('hidden');
    openModal('assignModal');
}

async function assignMember() {
    const userId = document.getElementById('assignUserId').value;
    if (!userId) {
        document.getElementById('assignError').classList.remove('hidden');
        document.getElementById('assignErrorText').textContent = 'Please select a user to assign.';
        return;
    }
    document.getElementById('assignError').classList.add('hidden');

    showLoader();
    try {
        const res  = await fetch(`/teams/${teamId}/members/assign`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ user_id: userId }),
        });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('assignModal');
            showToast(data.message, 'success');
            refreshMembers();
        } else {
            document.getElementById('assignError').classList.remove('hidden');
            document.getElementById('assignErrorText').textContent = data.message;
        }
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Remove Member ─────────────────────────────────────
function openRemoveModal(id, name) {
    document.getElementById('removeMemberId').value         = id;
    document.getElementById('removeMemberName').textContent = name;
    openModal('removeModal');
}

async function confirmRemove() {
    const userId = document.getElementById('removeMemberId').value;
    showLoader();
    try {
        const res  = await fetch(`/teams/${teamId}/members/${userId}`, {
            method:  'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
        });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('removeModal');
            showToast(data.message, 'success');
            refreshMembers();
        } else showToast(data.message, 'error');
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}

// ── Transfer Clients ──────────────────────────────────
function openTransferModal() {
    document.getElementById('targetTeamId').value = '';
    document.getElementById('transferError').classList.add('hidden');
    openModal('transferModal');
}

async function transferClients() {
    const targetId = document.getElementById('targetTeamId').value;
    if (!targetId) {
        document.getElementById('transferError').classList.remove('hidden');
        document.getElementById('transferErrorText').textContent = 'Please select a target team.';
        return;
    }
    document.getElementById('transferError').classList.add('hidden');

    showLoader();
    try {
        const res  = await fetch(`/teams/${teamId}/transfer-clients`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ target_team_id: targetId }),
        });
        const data = await res.json();
        hideLoader();
        if (data.success) {
            closeModal('transferModal');
            showToast(data.message, 'success');
        } else {
            document.getElementById('transferError').classList.remove('hidden');
            document.getElementById('transferErrorText').textContent = data.message;
        }
    } catch(e) { hideLoader(); showToast('Something went wrong.', 'error'); }
}
</script>
@endpush