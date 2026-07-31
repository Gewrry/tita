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

            <x-text-input id="password" class="block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

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
</x-guest-layout>

