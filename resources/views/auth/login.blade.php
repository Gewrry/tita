<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Access Identifier')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@tita.finance" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Security Key')" />

            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full pr-12"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                <button type="button" id="toggle-password"
                    onclick="togglePassword('password', 'eye-login')"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-beige-400 hover:text-mint-600 transition-colors focus:outline-none">
                    <svg id="eye-login" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded-lg border-beige-200 bg-white text-mint-600 shadow-sm focus:ring-mint-500/20 focus:ring-offset-white transition-all cursor-pointer" name="remember">
                <span class="ms-3 text-xs text-beige-400 font-black uppercase tracking-widest group-hover:text-mint-600 transition-colors">{{ __('Keep me synchronized') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-6 mt-10">
            <x-primary-button class="w-full text-sm font-black uppercase tracking-[0.2em] py-5 shadow-2xl shadow-mint-900/20">
                {{ __('Authorize Access') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <a class="text-center text-[10px] text-beige-400 hover:text-mint-600 transition-colors uppercase tracking-[0.2em] font-black" href="{{ route('password.request') }}">
                    {{ __('Credential Recovery') }}
                </a>
            @endif
        </div>
    </form>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            icon.innerHTML = isPassword
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    </script>
</x-guest-layout>

