@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-ttds']) }}>
    {{ $value ?? $slot }}
</label>
