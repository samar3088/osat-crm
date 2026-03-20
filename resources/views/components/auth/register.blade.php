<x-layouts.auth title="Create Account — Investment CRM">

    {{-- ═══ LEFT PANEL ═══ --}}
    <x-slot:left>
        <div class="w-[40%] min-h-screen flex flex-col justify-center items-center px-10 py-16 relative overflow-hidden"
             style="background: linear-gradient(145deg, #0a4a78 0%, #0e6099 60%, #1a7ec4 100%)">

            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/[0.06]"></div>
            <div class="absolute -bottom-16 -left-10 w-60 h-60 rounded-full bg-white/[0.06]"></div>

            {{-- Brand --}}
            <div class="relative z-10 text-center mb-12">
                <div class="w-16 h-16 rounded-[18px] flex items-center justify-center mx-auto mb-4 border border-white/25"
                     style="background:rgba(255,255,255,0.15); backdrop-filter:blur(10px)">
                    <svg class="w-8 h-8 stroke-white fill-none stroke-[1.8]" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="text-2xl font-extrabold text-white">Investment CRM</div>
                <div class="text-xs text-white/65 mt-1 font-medium">Your Ultimate Wealth Partner</div>
            </div>

            {{-- Steps Card --}}
            <div class="relative z-10 w-full max-w-xs rounded-card p-7 border border-white/20"
                 style="background:rgba(255,255,255,0.1); backdrop-filter:blur(20px)">
                <div class="text-[11px] font-bold text-white/60 tracking-widest uppercase mb-5">
                    Setup Process
                </div>

                {{-- Step 1 - Active --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center text-xs font-extrabold flex-shrink-0">
                        1
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Create Account</div>
                        <div class="text-[11px] text-white/60 mt-0.5">Register with your details</div>
                    </div>
                </div>

                {{-- Step 2 - Pending --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold flex-shrink-0 text-white/60"
                         style="background:rgba(255,255,255,0.15)">
                        2
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Admin Approval</div>
                        <div class="text-[11px] text-white/60 mt-0.5">Your account will be reviewed</div>
                    </div>
                </div>

                {{-- Step 3 - Pending --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold flex-shrink-0 text-white/60"
                         style="background:rgba(255,255,255,0.15)">
                        3
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Access Dashboard</div>
                        <div class="text-[11px] text-white/60 mt-0.5">Start managing your clients</div>
                    </div>
                </div>
            </div>

            {{-- Benefits --}}
            <div class="relative z-10 text-center mt-10">
                <p class="text-xs text-white/55 leading-relaxed">
                    <strong class="text-white/85">Manage clients</strong> ·
                    <strong class="text-white/85">Track AUM</strong><br/>
                    <strong class="text-white/85">Conveyance claims</strong> ·
                    <strong class="text-white/85">Financial tools</strong><br/>
                    All in one secure platform
                </p>
            </div>

        </div>
    </x-slot:left>

    {{-- ═══ RIGHT PANEL (Register Form) ═══ --}}
    <div class="w-full max-w-lg">

        <h1 class="text-3xl font-extrabold text-dark">Create your account</h1>
        <p class="text-sm text-crm-gray mt-2 leading-relaxed">
            Fill in the details below to request access to the CRM platform.
        </p>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-input">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600 font-medium">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Success --}}
        @if(session('success'))
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-input text-sm text-green-600 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-6">
            @csrf

            {{-- Row: Full Name + Employee Code --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="name" class="crm-label">
                        Full Name <span class="text-crm-danger">*</span>
                    </label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="crm-input @error('name') border-crm-danger @enderror"
                               placeholder="Rajesh Kumar"
                               required/>
                    </div>
                </div>
                <div>
                    <label for="employee_code" class="crm-label">Employee Code</label>
                    <div class="relative">
                        <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <input type="text"
                               id="employee_code"
                               name="employee_code"
                               value="{{ old('employee_code') }}"
                               class="crm-input"
                               placeholder="EMP-1001"/>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="crm-label">
                    Email Address <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="crm-input @error('email') border-crm-danger @enderror"
                           placeholder="you@company.com"
                           required/>
                </div>
                <p class="crm-hint">Use your official work email address</p>
            </div>

            {{-- Password --}}
            <div class="mb-5">
                <label for="password" class="crm-label">
                    Password <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="password"
                           name="password"
                           class="crm-input @error('password') border-crm-danger @enderror"
                           placeholder="Create a strong password"
                           oninput="checkStrength(this)"
                           required/>
                    <button type="button"
                            onclick="togglePass('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                {{-- Strength Bar --}}
                <div class="flex gap-1 mt-2" id="strengthBar">
                    <div class="flex-1 h-1 rounded bg-crm-border" id="s1"></div>
                    <div class="flex-1 h-1 rounded bg-crm-border" id="s2"></div>
                    <div class="flex-1 h-1 rounded bg-crm-border" id="s3"></div>
                    <div class="flex-1 h-1 rounded bg-crm-border" id="s4"></div>
                </div>
                <p class="crm-hint" id="strengthLabel">
                    Min. 8 characters with uppercase, number & symbol
                </p>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-5">
                <label for="password_confirmation" class="crm-label">
                    Confirm Password <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="crm-input"
                           placeholder="Re-enter password"
                           oninput="matchPass(this)"
                           required/>
                    <button type="button"
                            onclick="togglePass('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <p class="crm-hint" id="matchHint"></p>
            </div>

            {{-- Terms --}}
            <div class="flex items-start gap-3 mt-6">
                <input type="checkbox"
                       id="terms"
                       name="terms"
                       class="w-4 h-4 mt-0.5 accent-primary cursor-pointer flex-shrink-0"
                       required/>
                <label for="terms" class="text-sm text-crm-gray leading-relaxed cursor-pointer">
                    I agree to the
                    <a href="#" class="text-primary font-semibold hover:underline">Terms of Service</a>
                    and
                    <a href="#" class="text-primary font-semibold hover:underline">Privacy Policy</a>.
                    I understand my account requires admin approval.
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="crm-btn-primary mt-6">
                Create Account →
            </button>

        </form>

        <p class="text-center mt-5 text-sm text-crm-gray">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign In</a>
        </p>

    </div>

    @push('scripts')
    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        function checkStrength(el) {
            const v = el.value;
            let score = 0;
            if (v.length >= 8) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;
            const colors = ['#ef4444','#f97316','#eab308','#10b981'];
            const labels = ['Weak','Fair','Good','Strong'];
            for (let i = 1; i <= 4; i++) {
                document.getElementById('s'+i).style.background = i <= score ? colors[score-1] : '#e2e8f0';
            }
            const lbl = document.getElementById('strengthLabel');
            lbl.textContent = score > 0 ? `Password strength: ${labels[score-1]}` : 'Min. 8 characters with uppercase, number & symbol';
        }
        function matchPass(el) {
            const pwd = document.getElementById('password').value;
            const hint = document.getElementById('matchHint');
            if (!el.value) return;
            const match = el.value === pwd;
            hint.textContent = match ? '✓ Passwords match' : 'Passwords do not match';
            hint.className = match ? 'crm-hint text-crm-success' : 'crm-hint-error';
        }
    </script>
    @endpush

</x-layouts.auth>