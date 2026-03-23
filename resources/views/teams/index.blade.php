@extends('layouts.app')

@section('page-title', 'Teams')
@section('page-subtitle', 'Manage your teams')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">
    <div class="flex items-center gap-3">
        {{-- Export Excel --}}
        <a href="#"
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
    </div>

    {{-- Add Team Button --}}
    <button onclick="openCreateModal()"
            class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                   rounded-input text-sm font-bold hover:bg-primary-dark
                   transition-all hover:shadow-btn">
        <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Team
    </button>
</div>

{{-- ═══ FILTERS ═══ --}}
<div class="bg-white rounded-card shadow-card p-4 mb-5">
    <div class="flex items-end gap-3 flex-wrap">

        {{-- Name --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Team Name</label>
            <input type="text" id="filterName" class="crm-input"
                   placeholder="Search team name..."/>
        </div>

        {{-- Status --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Status</label>
            <select id="filterStatus" class="crm-input">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

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
    <table id="teamsTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Team Name</th>
                <th>Code</th>
                <th>Description</th>
                <th>Members</th>
                <th>Clients</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ═══ CREATE / EDIT MODAL ═══ --}}
<div id="teamModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-lg">
        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <h2 id="modalTitle" class="text-base font-extrabold text-dark">Add Team</h2>
            <button onclick="closeModal('teamModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <input type="hidden" id="teamId"/>

            {{-- Team Name + Code --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="crm-label">Team Name <span class="text-crm-danger">*</span></label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <input type="text" id="teamName" class="crm-input"
                               placeholder="e.g. Sales Team Alpha"/>
                    </div>
                </div>
                <div>
                    <label class="crm-label">Team Code <span class="text-crm-danger">*</span></label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <input type="text" id="teamCode" class="crm-input"
                               placeholder="TEAM-001"
                               oninput="this.value = this.value.toUpperCase()"/>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label class="crm-label">Description</label>
                <textarea id="teamDescription" class="crm-input h-24 resize-none"
                          placeholder="Brief description of this team..."></textarea>
            </div>

            {{-- Error --}}
            <div id="modalError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="modalErrorText"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="saveTeam()" class="crm-btn-primary flex-1">
                    Save Team
                </button>
                <button onclick="closeModal('teamModal')"
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
        <h3 class="text-base font-extrabold text-dark mb-2">Delete Team?</h3>
        <p class="text-sm text-crm-gray mb-2">
            Are you sure you want to delete <strong id="deleteTeamName"></strong>?
        </p>
        <div class="bg-orange-50 border border-orange-200 rounded-input p-3 mb-6 text-left">
            <p class="text-xs font-bold text-orange-600 mb-1">⚠️ What happens:</p>
            <ul class="text-xs text-orange-600 space-y-1">
                <li>• All members will be <strong>unassigned</strong> from this team</li>
                <li>• All clients will be <strong>unassigned</strong> from this team</li>
                <li>• Team data is <strong>soft deleted</strong> — restorable</li>
            </ul>
        </div>
        <input type="hidden" id="deleteTeamId"/>
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
let teamsTable;

$(document).ready(function() {
    teamsTable = $('#teamsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  '{{ route("teams.list") }}',
            type: 'GET',
            beforeSend: function() { showLoader(); },
            complete:   function() { hideLoader(); },
            error: function() {
                hideLoader();
                showToast('Failed to load teams.', 'error');
            },
            data: function(d) {
                d.filter_status = $('#filterStatus').val();
                d.filter_name   = $('#filterName').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex',     name: 'DT_RowIndex',     orderable: false, searchable: false, width: '50px' },
            { data: 'name',            name: 'name' },
            { data: 'code',            name: 'code' },
            { data: 'description',     name: 'description',     orderable: false },
            { data: 'members_count',   name: 'members_count',   orderable: false, searchable: false },
            { data: 'clients_count',   name: 'clients_count',   orderable: false, searchable: false },
            { data: 'status_badge',    name: 'status_badge',    orderable: false, searchable: false },
            { data: 'created_by_name', name: 'created_by_name', orderable: false },
            { data: 'created_date',    name: 'created_date',    orderable: false },
            { data: 'actions',         name: 'actions',         orderable: false, searchable: false },
        ],
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rtip',
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search: '', searchPlaceholder: 'Search teams...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ teams',
            emptyTable: 'No teams created yet',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            processing: '<div class="flex items-center gap-2 text-primary"><div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div> Loading...</div>'
        },
        order: [[8, 'desc']],
        responsive: true,
    });
});

function refreshTable() { teamsTable.ajax.reload(null, false); }

function applyFilters() { teamsTable.ajax.reload(null, false); }

function resetFilters() {
    $('#filterStatus').val('');
    $('#filterName').val('');
    teamsTable.ajax.reload(null, false);
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

// ── Create Modal ──────────────────────────────────────
async function openCreateModal() {
    document.getElementById('modalTitle').textContent      = 'Add Team';
    document.getElementById('teamId').value                = '';
    document.getElementById('teamName').value              = '';
    document.getElementById('teamDescription').value       = '';
    document.getElementById('modalError').classList.add('hidden');

    // Auto generate code
    const res = await ajaxGet('{{ route("teams.generate-code") }}');
    document.getElementById('teamCode').value = res.success ? res.code : '';

    openModal('teamModal');
}

// ── Edit Modal ────────────────────────────────────────
async function openEditModal(id) {
    const res = await ajaxGet(`/teams/${id}`);
    if (!res.success) return showToast('Failed to load team.', 'error');
    const t = res.data;
    document.getElementById('modalTitle').textContent      = 'Edit Team';
    document.getElementById('teamId').value                = t.id;
    document.getElementById('teamName').value              = t.name;
    document.getElementById('teamCode').value              = t.code;
    document.getElementById('teamDescription').value       = t.description ?? '';
    document.getElementById('modalError').classList.add('hidden');
    openModal('teamModal');
}

// ── Save Team ─────────────────────────────────────────
async function saveTeam() {
    const id     = document.getElementById('teamId').value;
    const isEdit = !!id;
    const name   = document.getElementById('teamName').value.trim();
    const code   = document.getElementById('teamCode').value.trim();

    // Frontend validation
    if (!name) {
        document.getElementById('modalError').classList.remove('hidden');
        document.getElementById('modalErrorText').textContent = '• Team name is required.';
        return;
    }
    if (!code) {
        document.getElementById('modalError').classList.remove('hidden');
        document.getElementById('modalErrorText').textContent = '• Team code is required.';
        return;
    }
    document.getElementById('modalError').classList.add('hidden');

    const payload = {
        name:        name,
        code:        code,
        description: document.getElementById('teamDescription').value,
    };

    showLoader();
    try {
        const response = await fetch(
            isEdit ? `/teams/${id}` : '{{ route("teams.store") }}',
            {
                method:  isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(payload),
            }
        );
        const data = await response.json();
        hideLoader();

        if (data.success) {
            closeModal('teamModal');
            showToast(data.message, 'success');
            refreshTable();
        } else {
            document.getElementById('modalError').classList.remove('hidden');
            if (data.errors) {
                const allErrors = Object.values(data.errors).flat();
                document.getElementById('modalErrorText').innerHTML =
                    allErrors.map(e => `• ${e}`).join('<br>');
            } else {
                document.getElementById('modalErrorText').textContent =
                    data.message ?? 'Something went wrong.';
            }
        }
    } catch(e) {
        hideLoader();
        showToast('Something went wrong.', 'error');
    }
}

// ── Toggle Status ─────────────────────────────────────
async function toggleStatus(id) {
    showLoader();
    try {
        const res  = await fetch(`/teams/${id}/status`, {
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

// ── Delete ────────────────────────────────────────────
function openDeleteModal(id, name) {
    document.getElementById('deleteTeamId').value         = id;
    document.getElementById('deleteTeamName').textContent = name;
    openModal('deleteModal');
}

async function confirmDelete() {
    const id = document.getElementById('deleteTeamId').value;
    showLoader();
    try {
        const res  = await fetch(`/teams/${id}`, {
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