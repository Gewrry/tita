@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-[11px] uppercase tracking-widest text-mint-600 mb-2']) }}>
    {{ $value ?? $slot }}
</label>

