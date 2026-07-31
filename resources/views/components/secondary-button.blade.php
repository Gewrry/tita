<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-beige-200 rounded-xl font-bold text-sm text-mint-800 hover:bg-beige-50 focus:outline-none focus:ring-4 focus:ring-mint-500/10 disabled:opacity-25 transition-all duration-300 shadow-sm']) }}>
    {{ $slot }}
</button>

