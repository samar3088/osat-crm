<x-layouts.auth title="Reset Password — Investment CRM">

    {{-- ═══ LEFT PANEL ═══ --}}
    <x-slot:left>
        <div class="w-[45%] min-h-screen flex-shrink-0 flex flex-col justify-center items-center px-12 py-16 relative overflow-hidden"
             style="background: linear-gradient(145deg, #0a4a78 0%, #0e6099 50%, #1a7ec4 100%)">

            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -left-16 w-72 h-72 rounded-full bg-white/5"></div>

            <div class="relative z-10 text-center">
                <div class="w-24 h-24 rounded-[24px] flex items-center justify-center mx-auto mb-8 border border-white/25"
                     style="background:rgba(255,255,255,0.15); backdrop-filter:blur(10px)">
                    <svg class="w-12 h-12 stroke-white fill-none stroke-[1.5]" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-white">Create New<br/>Password</h2>
                <p class="text-sm text-white/70 mt-4 leading-relaxed max-w-xs mx-auto">
                    Choose a strong password to keep your account secure.
                </p>

                {{-- Password Tips --}}
                <div class="mt-10 text-left max-w-xs mx-auto rounded-card p-5 border border-white/20"
                     style="background:rgba(255,255,255,0.1); backdrop-filter:blur(20px)">
                    <div class="text-xs font-bold text-white/60 uppercase tracking-widest mb-3">
                        Password Requirements
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 stroke-white/60 fill-none stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="text-xs text-white/70">At least 8 characters</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 stroke-white/60 fill-none stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="text-xs text-white/70">One uppercase letter</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 stroke-white/60 fill-none stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="text-xs text-white/70">One number</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:left>

    {{-- ═══ RIGHT PANEL ═══ --}}
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 bg-primary rounded-[12px] flex items-center justify-center">
                <svg class="w-6 h-6 stroke-white fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div>
                <div class="text-lg font-extrabold text-dark">Investment CRM</div>
                <div class="text-[11px] text-crm-gray font-medium">Your Ultimate Wealth Partner</div>
            </div>
        </div>

        <h1 class="text-3xl font-extrabold text-dark">Set New Password</h1>
        <p class="text-sm text-crm-gray mt-2">
            Enter your new password below.
        </p>

        {{-- Success --}}
        <div id="successMsg"
             class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-input">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 stroke-green-500 fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <p class="text-sm text-green-700 font-semibold" id="successText"></p>
            </div>
        </div>

        {{-- Error --}}
        <div id="errorMsg"
             class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-input">
            <p class="text-sm text-red-600 font-semibold" id="errorText"></p>
        </div>

        {{-- Form --}}
        <div class="mt-8" id="resetForm">

            <div class="mb-5">
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
                    <button type="button" onclick="togglePass('newPassword')"
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
                           oninput="matchPass(this)"/>
                    <button type="button" onclick="togglePass('confirmPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p class="crm-hint" id="matchHint"></p>
            </div>

            <button onclick="resetPassword()"
                    id="resetBtn"
                    class="crm-btn-primary">
                Reset Password
            </button>
        </div>

        <p class="text-center mt-6 text-sm text-crm-gray">
            Remember your password?
            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign In</a>
        </p>

    </div>

    @push('scripts')
    <script>
    function togglePass(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function matchPass(el) {
        const pwd  = document.getElementById('newPassword').value;
        const hint = document.getElementById('matchHint');
        if (!el.value) return;
        const match = el.value === pwd;
        hint.textContent = match ? '✓ Passwords match' : 'Passwords do not match';
        hint.className   = match ? 'crm-hint text-crm-success' : 'crm-hint-error';
    }

    async function resetPassword() {
        const newPass     = document.getElementById('newPassword').value;
        const confirmPass = document.getElementById('confirmPassword').value;
        const btn         = document.getElementById('resetBtn');

        if (!newPass || !confirmPass) {
            showError('Please fill in all fields.');
            return;
        }

        if (newPass !== confirmPass) {
            showError('Passwords do not match.');
            return;
        }

        btn.textContent   = 'Resetting...';
        btn.disabled      = true;
        btn.style.opacity = '0.8';

        try {
            const res = await fetch('{{ route("password.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({
                    token:                 '{{ $token }}',
                    email:                 '{{ $email }}',
                    password:              newPass,
                    password_confirmation: confirmPass,
                }),
            });

            const data = await res.json();

            if (data.success) {
                document.getElementById('resetForm').classList.add('hidden');
                document.getElementById('successMsg').classList.remove('hidden');
                document.getElementById('successText').textContent = data.message;
                // Redirect to login after 2 seconds
                setTimeout(() => window.location.href = '{{ route("login") }}', 2000);
            } else {
                showError(data.message);
                btn.textContent   = 'Reset Password';
                btn.disabled      = false;
                btn.style.opacity = '1';
            }
        } catch (e) {
            showError('Something went wrong. Please try again.');
            btn.textContent   = 'Reset Password';
            btn.disabled      = false;
            btn.style.opacity = '1';
        }
    }

    function showError(msg) {
        document.getElementById('errorMsg').classList.remove('hidden');
        document.getElementById('errorText').textContent = msg;
        setTimeout(() => document.getElementById('errorMsg').classList.add('hidden'), 4000);
    }
    </script>
    @endpush

</x-layouts.auth>