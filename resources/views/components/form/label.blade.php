@props(['value' => null, 'text' => 'text-sm', 'weight' => 'font-medium'])

<label {{ $attributes->merge(['class' => "block text-gray-700 z-10 capitalize {$text} {$weight}"]) }}>
  {{ $value ?? $slot }}
</label>
