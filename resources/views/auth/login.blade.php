<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form x-data="loginForm()" @submit.prevent="submit" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Access Identifier')" />
            <x-text-input id="email" x-model="form.email" class="block mt-2 w-full transition-colors" type="email" name="email" required autofocus autocomplete="username" placeholder="name@tita.finance" />
            <template x-if="errors.email">
                <p class="text-sm text-red-600 mt-2" x-text="errors.email[0]"></p>
            </template>
            <x-input-error :messages="$errors->get('email')" class="mt-2" x-show="!errors.email" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Security Key')" />

            <x-text-input id="password" x-model="form.password" class="block mt-2 w-full transition-colors"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <template x-if="errors.password">
                <p class="text-sm text-red-600 mt-2" x-text="errors.password[0]"></p>
            </template>
            <x-input-error :messages="$errors->get('password')" class="mt-2" x-show="!errors.password" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" x-model="form.remember" type="checkbox" class="w-5 h-5 rounded-lg border-beige-200 bg-white text-mint-600 shadow-sm focus:ring-mint-500/20 focus:ring-offset-white transition-all cursor-pointer" name="remember">
                <span class="ms-3 text-xs text-beige-400 font-black uppercase tracking-widest group-hover:text-mint-600 transition-colors">{{ __('Keep me synchronized') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-6 mt-10">
            <x-primary-button class="w-full text-sm font-black uppercase tracking-[0.2em] py-5 shadow-2xl shadow-mint-900/20 relative" x-bind:disabled="loading">
                <span x-show="!loading">{{ __('Authorize Access') }}</span>
                <span x-show="loading" class="absolute inset-0 flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </x-primary-button>

            @if (Route::has('password.request'))
                <a class="text-center text-[10px] text-beige-400 hover:text-mint-600 transition-colors uppercase tracking-[0.2em] font-black" href="{{ route('password.request') }}">
                    {{ __('Credential Recovery') }}
                </a>
            @endif
        </div>
    </form>

    @push('scripts')
    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: false,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                errors: {},
                loading: false,
                submit() {
                    this.loading = true;
                    this.errors = {};
                    
                    axios.post('{{ route('login') }}', this.form, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        window.location.href = response.data.redirect || '{{ route('dashboard') }}';
                    })
                    .catch(error => {
                        this.loading = false;
                        if (error.response && error.response.status === 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-guest-layout>

