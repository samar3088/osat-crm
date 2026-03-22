<x-layouts.auth title="Forgot Password — Investment CRM">

    {{-- ═══ LEFT PANEL ═══ --}}
    <x-slot:left>
        <div class="w-[45%] min-h-screen flex-shrink-0 flex flex-col justify-center items-center px-12 py-16 relative overflow-hidden"
             style="background: linear-gradient(145deg, #0a4a78 0%, #0e6099 50%, #1a7ec4 100%)">

            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -left-16 w-72 h-72 rounded-full bg-white/5"></div>

            {{-- Icon --}}
            <div class="relative z-10 text-center">
                <div class="w-24 h-24 rounded-[24px] flex items-center justify-center mx-auto mb-8 border border-white/25"
                     style="background:rgba(255,255,255,0.15); backdrop-filter:blur(10px)">
                    <svg class="w-12 h-12 stroke-white fill-none stroke-[1.5]" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-white leading-snug">
                    Forgot your<br/>password?
                </h2>
                <p class="text-sm text-white/70 mt-4 leading-relaxed max-w-xs mx-auto">
                    No worries! Enter your registered email and we'll send you a secure reset link instantly.
                </p>

                {{-- Steps --}}
                <div class="mt-10 space-y-4 text-left max-w-xs mx-auto">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white text-primary flex items-center justify-center text-xs font-extrabold flex-shrink-0">1</div>
                        <span class="text-sm text-white/80">Enter your registered email</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold flex-shrink-0 text-white/60" style="background:rgba(255,255,255,0.15)">2</div>
                        <span class="text-sm text-white/60">Check your inbox for reset link</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold flex-shrink-0 text-white/60" style="background:rgba(255,255,255,0.15)">3</div>
                        <span class="text-sm text-white/60">Create your new password</span>
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

        <h1 class="text-3xl font-extrabold text-dark">Reset Password</h1>
        <p class="text-sm text-crm-gray mt-2 leading-relaxed">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        {{-- Success Message --}}
        <div id="successMsg"
             class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-input">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 stroke-green-500 fill-none stroke-2 flex-shrink-0" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <p class="text-sm text-green-700 font-semibold" id="successText"></p>
            </div>
        </div>

        {{-- Error Message --}}
        <div id="errorMsg"
             class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-input">
            <p class="text-sm text-red-600 font-semibold" id="errorText"></p>
        </div>

        {{-- Form --}}
        <div class="mt-8" id="forgotForm">
            <div class="mb-6">
                <label class="crm-label">
                    Email Address <span class="text-crm-danger">*</span>
                </label>
                <div class="relative">
                    <svg class="input-icon fill-none stroke-2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input type="email"
                           id="resetEmail"
                           class="crm-input"
                           placeholder="you@company.com"
                           autocomplete="email"/>
                </div>
            </div>

            <button onclick="sendResetLink()"
                    id="sendBtn"
                    class="crm-btn-primary">
                Send Reset Link
            </button>
        </div>

        <p class="text-center mt-6 text-sm text-crm-gray">
            Remember your password?
            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign In</a>
        </p>

    </div>

    @push('scripts')
    <script>
    async function sendResetLink() {
        const email = document.getElementById('resetEmail').value.trim();
        const btn   = document.getElementById('sendBtn');

        if (!email) {
            showError('Please enter your email address.');
            return;
        }

        btn.textContent  = 'Sending...';
        btn.disabled     = true;
        btn.style.opacity = '0.8';

        try {
            const res = await fetch('{{ route("password.email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                    'Accept':        'application/json',
                },
                body: JSON.stringify({ email }),
            });

            const data = await res.json();

            if (data.success) {
                document.getElementById('forgotForm').classList.add('hidden');
                document.getElementById('successMsg').classList.remove('hidden');
                document.getElementById('successText').textContent = data.message;
            } else {
                showError(data.message);
                btn.textContent  = 'Send Reset Link';
                btn.disabled     = false;
                btn.style.opacity = '1';
            }
        } catch (e) {
            showError('Something went wrong. Please try again.');
            btn.textContent  = 'Send Reset Link';
            btn.disabled     = false;
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