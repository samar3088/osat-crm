@extends('layouts.app')

@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account details')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- ═══ PROFILE HEADER CARD ═══ --}}
    <div class="bg-white rounded-card shadow-card overflow-hidden mb-6">

        {{-- Cover --}}
        <div class="h-28 w-full"
             style="background: linear-gradient(135deg, #0a4a78 0%, #0e6099 60%, #1a7ec4 100%)">
        </div>

        {{-- Avatar + Info --}}
        <div class="px-6 pb-6">
            
            {{-- Avatar + Info + Stats in one row --}}
            <div class="flex items-end justify-between -mt-10 mb-4 flex-wrap gap-4">

                {{-- Left: Avatar + Name --}}
                <div class="flex items-end gap-5">
                    <div class="w-20 h-20 rounded-full border-4 border-white shadow-card
                                bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-extrabold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="pb-2">
                        <h2 class="text-xl font-extrabold text-dark">{{ $user->name }}</h2>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-primary-light text-primary capitalize">
                                {{ str_replace('_', ' ', $user->getRoleNames()->first() ?? 'User') }}
                            </span>
                            @if($user->employee_code)
                            <span class="text-xs text-crm-gray font-medium">
                                Code: {{ $user->employee_code }}
                            </span>
                            @endif
                            <span class="text-xs px-2 py-1 rounded-full font-semibold
                                        {{ $user->is_active ? 'bg-green-50 text-crm-success' : 'bg-red-50 text-crm-danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right: Stats (horizontal) --}}
                @if($user->isSuperAdmin())
                <div class="flex items-center gap-8 px-6 py-4 bg-crm-light rounded-card">
                    <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">{{ $stats['total_users'] }}</div>
                        <div class="text-xs text-crm-gray font-medium mt-1">Total Users</div>
                    </div>
                    <div class="w-px h-10 bg-crm-border"></div>
                    <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">{{ $stats['total_clients'] }}</div>
                        <div class="text-xs text-crm-gray font-medium mt-1">Total Clients</div>
                    </div>
                    <div class="w-px h-10 bg-crm-border"></div>
                    <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">{{ $stats['total_team'] }}</div>
                        <div class="text-xs text-crm-gray font-medium mt-1">Team Members</div>
                    </div>
                </div>
                @endif

                @if($user->isTeamMember())
                <div class="flex items-center gap-8 px-6 py-4 bg-crm-light rounded-card">
                <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">{{ $stats['total_clients'] }}</div>
                        <div class="text-xs text-crm-gray font-medium mt-1">My Clients</div>
                    </div>
                    <div class="w-px h-10 bg-crm-border"></div>
                    <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">{{ $stats['target_pct'] }}%</div>
                        <div class="text-xs text-crm-gray font-medium mt-1">Target Achieved</div>
                    </div>
                    <div class="w-px h-10 bg-crm-border"></div>
                    <div class="text-center px-4">
                        <div class="text-2xl font-extrabold text-primary">
                            ₹{{ number_format(($stats['target_amount'] ?? 0) / 100000, 1) }}L
                        </div>
                        <div class="text-xs text-crm-gray font-medium mt-1">This Month Target</div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══ TWO COLUMN LAYOUT ═══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Edit Profile Card --}}
        <div class="bg-white rounded-card shadow-card p-6">
            <h3 class="text-sm font-extrabold text-dark mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Personal Information
            </h3>

            {{-- Read-only --}}
            <div class="mb-4">
                <label class="crm-label">Email Address</label>
                <div class="crm-input bg-crm-light text-crm-gray cursor-not-allowed">
                    {{ $user->email }}
                </div>
                <p class="crm-hint">Email cannot be changed. Contact Super Admin.</p>
            </div>

            <div class="mb-4">
                <label class="crm-label">Member Since</label>
                <div class="crm-input bg-crm-light text-crm-gray cursor-not-allowed">
                    {{ $user->created_at->format('d M Y') }}
                </div>
            </div>

            {{-- Editable --}}
            <div class="mb-4">
                <label class="crm-label">
                    Full Name <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input type="text"
                           id="profileName"
                           value="{{ $user->name }}"
                           class="crm-input"
                           placeholder="Full Name"/>
                </div>
            </div>

            <div class="mb-4">
                <label class="crm-label">Employee Code</label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    <input type="text"
                           id="profileEmpCode"
                           value="{{ $user->employee_code }}"
                           class="crm-input"
                           placeholder="EMP-1001"/>
                </div>
            </div>

            <div class="mb-6">
                <label class="crm-label">Work Type</label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    <select id="profileWorkType" class="crm-input">
                        <option value="Sales"      {{ $user->work_type === 'Sales'      ? 'selected' : '' }}>Sales</option>
                        <option value="Operations" {{ $user->work_type === 'Operations' ? 'selected' : '' }}>Operations</option>
                        <option value="Both"       {{ $user->work_type === 'Both'       ? 'selected' : '' }}>Both</option>
                    </select>
                </div>
            </div>

            <button onclick="updateProfile()" class="crm-btn-primary">
                Save Changes
            </button>
        </div>

        {{-- Change Password Card --}}
        <div class="bg-white rounded-card shadow-card p-6" id="change-password">
            <h3 class="text-sm font-extrabold text-dark mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 stroke-primary fill-none stroke-2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Change Password
            </h3>

            <div class="mb-4">
                <label class="crm-label">
                    Current Password <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="currentPassword"
                           class="crm-input"
                           placeholder="Enter current password"/>
                    <button type="button"
                            onclick="togglePass('currentPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="crm-label">
                    New Password <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="newPassword"
                           class="crm-input"
                           placeholder="Min 8 chars, uppercase & number"/>
                    <button type="button"
                            onclick="togglePass('newPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <label class="crm-label">
                    Confirm New Password <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="confirmPassword"
                           class="crm-input"
                           placeholder="Re-enter new password"
                           oninput="matchProfilePass(this)"/>
                    <button type="button"
                            onclick="togglePass('confirmPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p class="crm-hint" id="passMatchHint"></p>
            </div>

            <button onclick="changePassword()" class="crm-btn-primary">
                Update Password
            </button>

            {{-- Account Info --}}
            <div class="mt-6 pt-5 border-t border-crm-border">
                <h4 class="text-xs font-bold text-crm-gray uppercase tracking-wider mb-3">
                    Account Info
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-xs text-crm-gray">Account Status</span>
                        <span class="text-xs font-semibold
                                     {{ $user->is_active ? 'text-crm-success' : 'text-crm-danger' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if($user->assignedTo)
                    <div class="flex justify-between">
                        <span class="text-xs text-crm-gray">Reports To</span>
                        <span class="text-xs font-semibold text-dark">
                            {{ $user->assignedTo->name }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-xs text-crm-gray">Member Since</span>
                        <span class="text-xs font-semibold text-dark">
                            {{ $user->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function matchProfilePass(el) {
    const pwd  = document.getElementById('newPassword').value;
    const hint = document.getElementById('passMatchHint');
    if (!el.value) return;
    const match = el.value === pwd;
    hint.textContent = match ? '✓ Passwords match' : 'Passwords do not match';
    hint.className   = match ? 'crm-hint text-crm-success' : 'crm-hint-error';
}

async function updateProfile() {
    const res = await ajaxPost('{{ route("profile.update") }}', {
        name:          document.getElementById('profileName').value,
        employee_code: document.getElementById('profileEmpCode').value,
        work_type:     document.getElementById('profileWorkType').value,
    });
    if (res.success) showToast(res.message, 'success');
    else showToast(res.message ?? 'Update failed.', 'error');
}

async function changePassword() {
    const newPass     = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;

    if (newPass !== confirmPass) {
        showToast('Passwords do not match.', 'error');
        return;
    }

    const res = await ajaxPost('{{ route("profile.password") }}', {
        current_password:      document.getElementById('currentPassword').value,
        password:              newPass,
        password_confirmation: confirmPass,
    });

    if (res.success) {
        showToast(res.message, 'success');
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value     = '';
        document.getElementById('confirmPassword').value = '';
    } else {
        showToast(res.message ?? 'Password change failed.', 'error');
    }
}
</script>
@endpush