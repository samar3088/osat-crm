<x-layouts.auth title="Sign In — Investment CRM">

    {{-- ═══ LEFT PANEL ═══ --}}
    <x-slot:left>
        <div class="w-[45%] min-h-screen flex flex-col justify-center items-center px-12 py-16 relative overflow-hidden"
             style="background: linear-gradient(145deg, #0a4a78 0%, #0e6099 50%, #1a7ec4 100%)">

            {{-- Decorative circles (matching your template) --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -left-16 w-72 h-72 rounded-full bg-white/5"></div>

            {{-- Floating Stats Card --}}
            <div class="relative z-10 w-full max-w-sm animate-float">
                <div class="rounded-card p-8 border border-white/20"
                     style="background:rgba(255,255,255,0.1); backdrop-filter:blur(20px)">

                    {{-- Stat Boxes Row 1 --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="rounded-[12px] p-4 text-center bg-white/15">
                            <div class="text-xl font-extrabold text-white">3,685</div>
                            <div class="text-[10px] text-white/70 mt-1 font-medium">Total Clients</div>
                        </div>
                        <div class="rounded-[12px] p-4 text-center bg-white/15">
                            <div class="text-xl font-extrabold text-white">₹966 Cr</div>
                            <div class="text-[10px] text-white/70 mt-1 font-medium">Total AUM</div>
                        </div>
                    </div>

                    {{-- Stat Boxes Row 2 --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="rounded-[12px] p-4 text-center bg-white/15">
                            <div class="text-xl font-extrabold text-white">2,074</div>
                            <div class="text-[10px] text-white/70 mt-1 font-medium">SIP Clients</div>
                        </div>
                        <div class="rounded-[12px] p-4 text-center bg-white/15">
                            <div class="text-xl font-extrabold text-white">₹3.58 Cr</div>
                            <div class="text-[10px] text-white/70 mt-1 font-medium">Total SIP</div>
                        </div>
                    </div>

                    {{-- Progress Bars --}}
                    <div class="rounded-[12px] p-4 bg-white/10 mb-3">
                        <div class="flex justify-between text-[10px] text-white/75 font-semibold mb-2">
                            <span>Monthly Target (SIP)</span><span>72%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/20 overflow-hidden">
                            <div class="h-full rounded-full w-[72%]"
                                 style="background:linear-gradient(90deg,#4fc3f7,#fff)"></div>
                        </div>
                    </div>
                    <div class="rounded-[12px] p-4 bg-white/10">
                        <div class="flex justify-between text-[10px] text-white/75 font-semibold mb-2">
                            <span>Monthly Target (Lumpsum)</span><span>55%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/20 overflow-hidden">
                            <div class="h-full rounded-full w-[55%]"
                                 style="background:linear-gradient(90deg,#4fc3f7,#fff)"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Left Panel Text --}}
            <div class="relative z-10 text-center mt-10">
                <h2 class="text-2xl font-extrabold text-white leading-snug">
                    Manage Wealth.<br/>Track Growth.
                </h2>
                <p class="text-sm text-white/70 mt-3 leading-relaxed">
                    Your complete platform for managing clients,<br/>
                    targets, and investment performance.
                </p>
            </div>

        </div>
    </x-slot:left>

    {{-- ═══ RIGHT PANEL (Login Form) ═══ --}}
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="flex items-center gap-3 mb-11">
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

        {{-- Heading --}}
        <h1 class="text-3xl font-extrabold text-dark">Welcome back 👋</h1>
        <p class="text-sm text-crm-gray mt-2">Sign in to your account to continue</p>

        {{-- Session Error --}}
        @if(session('error'))
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-input text-sm text-red-600 font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-input">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600 font-medium">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="mt-7">
            @csrf

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="crm-label">Email Address</label>
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
                           autocomplete="email"
                           required/>
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-2">
                <label for="password" class="crm-label">Password</label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password"
                           id="password"
                           name="password"
                           class="crm-input @error('password') border-crm-danger @enderror"
                           placeholder="Enter your password"
                           autocomplete="current-password"
                           required/>
                    {{-- Eye Toggle --}}
                    <button type="button"
                            onclick="togglePass('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-crm-gray hover:text-primary">
                        <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember Me + Forgot --}}
            <div class="flex items-center justify-between mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           name="remember"
                           class="w-4 h-4 accent-primary cursor-pointer"/>
                    <span class="text-sm text-crm-gray font-medium">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}"
                   class="text-sm text-primary font-semibold hover:underline">
                    Forgot Password?
                </a>
            </div>

            {{-- Submit --}}
            <button type="submit" class="crm-btn-primary mt-7">
                Sign In to Dashboard
            </button>

        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 mt-7">
            <div class="flex-1 h-px bg-crm-border"></div>
            <span class="text-xs text-crm-gray font-medium whitespace-nowrap">New to the platform?</span>
            <div class="flex-1 h-px bg-crm-border"></div>
        </div>

        {{-- Register Link --}}
        <p class="text-center mt-6 text-sm text-crm-gray">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Create Account</a>
        </p>

        {{-- Security Badge --}}
        <div class="crm-security-badge">
            <svg class="w-4 h-4 stroke-primary fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span class="text-xs text-primary font-semibold">
                256-bit SSL encrypted — Your data is always safe
            </span>
        </div>

    </div>

    {{-- JS for password toggle --}}
    @push('scripts')
    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
    @endpush

</x-layouts.auth>