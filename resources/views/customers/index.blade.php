@extends('layouts.app')

@section('page-title', 'Customers')
@section('page-subtitle', 'Manage your clients')

@section('content')

{{-- ═══ HEADER ROW ═══ --}}
<div class="flex items-center justify-between mb-6 flex-wrap gap-4">
    <div class="flex items-center gap-3">
        {{-- Export Excel --}}
        <a href="{{ route('customers.export-excel') }}"
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

    {{-- Add Button --}}
    @can('create customers')
    <button onclick="openCreateModal()"
            class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white
                   rounded-input text-sm font-bold hover:bg-primary-dark
                   transition-all hover:shadow-btn">
        <svg class="w-4 h-4 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Client
    </button>
    @endcan
</div>

{{-- ═══ FILTERS ═══ --}}
<div class="bg-white rounded-card shadow-card p-4 mb-5">
    <div class="flex items-end gap-3 flex-wrap">

        {{-- Client Type --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Client Type</label>
            <select id="filterType" class="crm-input">
                <option value="">All Types</option>
                <option value="New Client">New Client</option>
                <option value="Existing Client">Existing Client</option>
                <option value="Prospect Client">Prospect Client</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Status</label>
            <select id="filterStatus" class="crm-input">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        {{-- Assigned RM — Super Admin only --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="flex-1 min-w-[150px]">
            <label class="crm-label">Assigned RM</label>
            <input type="text" id="filterAssigned" class="crm-input"
                   placeholder="Search RM name..."/>
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
    <table id="customersTable" class="w-full" style="width:100%">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Client Name</th>
                <th>PAN</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Type</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ═══ CREATE / EDIT MODAL ═══ --}}
<div id="customerModal"
     class="hidden fixed inset-0 bg-black/50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white rounded-card shadow-card w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between p-6 border-b border-crm-border">
            <h2 id="modalTitle" class="text-base font-extrabold text-dark">Add Client</h2>
            <button onclick="closeModal('customerModal')"
                    class="w-8 h-8 rounded-full bg-crm-light flex items-center justify-center
                           hover:bg-red-50 hover:text-red-500 transition-all">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <input type="hidden" id="customerId"/>

            {{-- Row 1: Client Type + Client Name + PAN --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="crm-label">Client Type</label>
                    <select id="clientType" class="crm-input">
                        <option value="">— Select —</option>
                        <option value="New Client">New Client</option>
                        <option value="Existing Client">Existing Client</option>
                        <option value="Prospect Client">Prospect Client</option>
                    </select>
                </div>
                <div>
                    <label class="crm-label">Client Name <span class="text-crm-danger">*</span></label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" id="clientName" class="crm-input" placeholder="Full Name"/>
                    </div>
                </div>
                <div>
                    <label class="crm-label">Client PAN</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <input type="text" id="clientPan" class="crm-input"
                               placeholder="ABCDE1234F"
                               oninput="this.value = this.value.toUpperCase()"/>
                    </div>
                </div>
            </div>

            {{-- Row 2: Mobile + Email + Source --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="crm-label">Mobile</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.84a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/>
                        </svg>
                        <input type="text" id="clientMobile" class="crm-input" placeholder="+91 9876543210"/>
                    </div>
                </div>
                <div>
                    <label class="crm-label">Email</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input type="email" id="clientEmail" class="crm-input" placeholder="client@email.com"/>
                    </div>
                </div>
                <div>
                    <label class="crm-label">Source</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <input type="text" id="clientSource" class="crm-input" placeholder="Referral, Walk-in..."/>
                    </div>
                </div>
            </div>

            {{-- Row 3: DOB + Follow Date + Assigned To --}}
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="crm-label">Date of Birth</label>
                    <input type="date" id="clientDob" class="crm-input"/>
                </div>
                <div>
                    <label class="crm-label">Assign To</label>
                    <select id="clientAssignedTo" class="crm-input">
                        <option value="">— Select RM —</option>
                        @foreach($teamMembers as $member)
                        <option value="{{ $member->id }}">
                            {{ $member->name }}
                            {{ $member->employee_code ? '('.$member->employee_code.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 4: Status (edit only) --}}
            <div class="mb-4" id="statusRow" style="display:none">
                <label class="crm-label">Status</label>
                <select id="clientStatus" class="crm-input">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            {{-- Remarks --}}
            <div class="mb-6">
                <label class="crm-label">Remarks</label>
                <textarea id="clientRemarks"
                          class="crm-input h-24 resize-none"
                          placeholder="Add any notes about this client..."></textarea>
            </div>

            {{-- Error --}}
            <div id="modalError"
                 class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-input">
                <p class="text-sm text-red-600 font-semibold" id="modalErrorText"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="saveCustomer()" class="crm-btn-primary flex-1">
                    Save Client
                </button>
                <button onclick="closeModal('customerModal')"
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
    <div class="bg-white rounded-card shadow-card w-full max-w-sm p-6">
        <div class="text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 stroke-red-500 fill-none stroke-2" viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-dark mb-2">Delete Client?</h3>
            <p class="text-sm text-crm-gray mb-6">
                Are you sure you want to delete <strong id="deleteClientName"></strong>?
                This action can be restored by Super Admin.
            </p>
            <input type="hidden" id="deleteClientId"/>
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
</div>

@endsection

@push('scripts')
<script>
let customersTable;
const isAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};

$(document).ready(function() {
    customersTable = $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  '{{ route("customers.list") }}',
            type: 'GET',
            beforeSend: function() { showLoader(); },
            complete:   function() { hideLoader(); },
            error: function() {
                hideLoader();
                showToast('Failed to load customers.', 'error');
            },
            data: function(d) {
                d.filter_type     = $('#filterType').val();
                d.filter_status   = $('#filterStatus').val();
                d.filter_assigned = isAdmin ? $('#filterAssigned').val() : '';
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex',   
                name: 'DT_RowIndex',   
                orderable: false, 
                searchable: false, 
                width: '50px' 
            },
            { 
                data: 'name_email',    
                name: 'name_email',    
                title: 'Client Name'
            },
            { 
                data: 'client_pan',    
                name: 'client_pan',    
                title: 'PAN'
            },
            { 
                data: 'client_mobile', 
                name: 'client_mobile', 
                title: 'Mobile'
            },
            { 
                data: 'client_email',  
                name: 'client_email',  
                title: 'Email'
            },
            { 
                data: 'type_badge',    
                name: 'type_badge',    
                title: 'Type',
                orderable: false 
            },
            { 
                data: 'assigned_name', 
                name: 'assigned_name', 
                title: 'Assigned To',
                orderable: false 
            },
            { 
                data: 'status_badge',  
                name: 'status_badge',  
                title: 'Status',
                orderable: false 
            },
            { 
                data: 'actions',       
                name: 'actions',       
                title: 'Actions',
                orderable: false, 
                searchable: false 
            },
        ],
        //dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"lB><"flex items-center gap-2"f>>rtip',
        dom: '<"flex items-center justify-between mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rtip',
        /* buttons: [
            {
                extend:    'excelHtml5',
                text:      'Excel',
                className: 'px-4 py-2 bg-green-50 text-green-600 rounded-input text-sm font-bold border border-green-200 hover:bg-green-100',
                title:     'Customers List'
            },
            {
                extend:    'pdfHtml5',
                text:      'PDF',
                className: 'px-4 py-2 bg-red-50 text-red-500 rounded-input text-sm font-bold border border-red-200 hover:bg-red-100',
                title:     'Customers Report'
            },
        ], */
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            search:            '',
            searchPlaceholder: 'Search customers...',
            lengthMenu:        'Show _MENU_ entries',
            info:              'Showing _START_ to _END_ of _TOTAL_ clients',
            infoEmpty:         'No clients found',
            emptyTable:        'No customers added yet',
            paginate:          { first: '«', last: '»', next: '›', previous: '‹' },
            processing:        '<div class="flex items-center gap-2 text-primary"><div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div> Loading...</div>'
        },
        order:     [[1, 'asc']],
        responsive: true,
    });
});

// ── Table Actions ─────────────────────────────────────
function refreshTable() { customersTable.ajax.reload(null, false); }

function updateExportLinks() {
    const params = new URLSearchParams();
    const type     = $('#filterType').val();
    const status   = $('#filterStatus').val();
    const assigned = isAdmin ? $('#filterAssigned').val() : '';

    if (type)     params.append('filter_type', type);
    if (status)   params.append('filter_status', status);
    if (assigned) params.append('filter_assigned', assigned);

    const query = params.toString() ? '?' + params.toString() : '';
    $('#exportExcelBtn').attr('href', '{{ route("customers.export-excel") }}' + query);
}

function applyFilters() {
    customersTable.ajax.reload(null, false);
    updateExportLinks();
}

function resetFilters() {
    $('#filterType').val('');
    $('#filterStatus').val('');
    if (isAdmin) $('#filterAssigned').val('');
    customersTable.ajax.reload(null, false);
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

// ── Create Modal ──────────────────────────────────────
function openCreateModal() {
    document.getElementById('modalTitle').textContent  = 'Add Client';
    document.getElementById('customerId').value        = '';
    document.getElementById('clientType').value        = '';
    document.getElementById('clientName').value        = '';
    document.getElementById('clientPan').value         = '';
    document.getElementById('clientMobile').value      = '';
    document.getElementById('clientEmail').value       = '';
    document.getElementById('clientSource').value      = '';
    document.getElementById('clientDob').value         = '';
    document.getElementById('clientAssignedTo').value  = '';
    document.getElementById('clientRemarks').value     = '';
    document.getElementById('statusRow').style.display = 'none';
    document.getElementById('modalError').classList.add('hidden');
    openModal('customerModal');
}

// ── Edit Modal ────────────────────────────────────────
async function openEditModal(id) {
    const res = await ajaxGet(`/customers/${id}`);
    if (!res.success) return showToast('Failed to load client data.', 'error');
    const c = res.data;
    document.getElementById('modalTitle').textContent      = 'Edit Client';
    document.getElementById('customerId').value            = c.id;
    document.getElementById('clientType').value            = c.client_type   ?? '';
    document.getElementById('clientName').value            = c.client_name;
    document.getElementById('clientPan').value             = c.client_pan    ?? '';
    document.getElementById('clientMobile').value          = c.client_mobile ?? '';
    document.getElementById('clientEmail').value           = c.client_email  ?? '';
    document.getElementById('clientSource').value          = c.source_detail ?? '';
    document.getElementById('clientDob').value             = c.date_of_birth ?? '';
    document.getElementById('clientAssignedTo').value      = c.assigned_to   ?? '';
    document.getElementById('clientRemarks').value         = c.full_remarks  ?? '';
    document.getElementById('clientStatus').value          = c.is_active ? '1' : '0';
    document.getElementById('statusRow').style.display     = 'block';
    document.getElementById('modalError').classList.add('hidden');
    openModal('customerModal');
}

// ── Save Customer ─────────────────────────────────────
async function saveCustomer() {
    const id     = document.getElementById('customerId').value;
    const isEdit = !!id;

    const name   = document.getElementById('clientName').value.trim();
    const mobile = document.getElementById('clientMobile').value.trim();
    const email  = document.getElementById('clientEmail').value.trim();

    // Frontend Validation
    if (!name) {
        document.getElementById('modalError').classList.remove('hidden');
        document.getElementById('modalErrorText').textContent = '• Client name is required.';
        return;
    }
    if (mobile && mobile.replace(/\D/g, '').length < 10) {
        document.getElementById('modalError').classList.remove('hidden');
        document.getElementById('modalErrorText').textContent = '• Mobile number must be at least 10 digits.';
        return;
    }
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('modalError').classList.remove('hidden');
        document.getElementById('modalErrorText').textContent = '• Please enter a valid email address.';
        return;
    }
    document.getElementById('modalError').classList.add('hidden');

    const payload = {
        client_type:   document.getElementById('clientType').value,
        client_name:   name,
        client_pan:    document.getElementById('clientPan').value,
        client_mobile: mobile,
        client_email:  email,
        source_detail: document.getElementById('clientSource').value,
        date_of_birth: document.getElementById('clientDob').value      || null,
        assigned_to:   document.getElementById('clientAssignedTo').value || null,
        full_remarks:  document.getElementById('clientRemarks').value,
        is_active:     document.getElementById('clientStatus')?.value  ?? '1',
    };

    showLoader();
    try {
        const response = await fetch(
            isEdit ? `/customers/${id}` : '{{ route("customers.store") }}',
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
            closeModal('customerModal');
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
        const res  = await fetch(`/customers/${id}/status`, {
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
    document.getElementById('deleteClientId').value         = id;
    document.getElementById('deleteClientName').textContent = name;
    openModal('deleteModal');
}

async function confirmDelete() {
    const id = document.getElementById('deleteClientId').value;
    showLoader();
    try {
        const res  = await fetch(`/customers/${id}`, {
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