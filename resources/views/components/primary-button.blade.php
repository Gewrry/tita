<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-mint-500 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-mint-600 focus:outline-none focus:ring-4 focus:ring-mint-500/20 transition-all duration-300 active:scale-95 shadow-lg shadow-mint-900/10']) }}>
    {{ $slot }}
</button>

