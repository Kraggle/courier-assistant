@props(['active' => false, 'icon' => false])

@php
  $classes = $active ? 'border-violet-600 text-violet-800 bg-violet-50 focus:text-violet-900 focus:bg-violet-100 focus:border-violet-800' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300';
@endphp

<a {{ $attributes->merge(['class' => "$classes flex items-center gap-3 w-full ps-3 pe-12 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out capitalize"]) }}>
  @if ($icon)
    <x-icon class="{{ $icon }} w-8 text-center text-2xl opacity-60" />
  @endif
  {{ $slot }}
</a>
