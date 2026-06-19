@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-cyan-100']) }}>
    {{ $value ?? $slot }}
</label>
