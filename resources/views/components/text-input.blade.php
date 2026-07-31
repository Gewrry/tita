@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-beige-200 focus:border-mint-500 focus:ring-mint-500/20 text-mint-900 placeholder-beige-300 rounded-xl shadow-sm transition-all duration-300 w-full px-4 py-3 font-semibold']) }}>

