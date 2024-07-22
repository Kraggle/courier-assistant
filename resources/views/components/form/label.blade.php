@props(['value' => null])

<label {{ $attributes->twMerge(['class' => 'block text-gray-700 z-10 capitalize text-sm font-medium']) }}>
  {{ $value ?? $slot }}
</label>
