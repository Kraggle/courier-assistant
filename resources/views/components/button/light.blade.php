@props(['size' => 'sm'])

@php
  $sized = [
      'sm' => 'px-4 py-2 text-xs',
      'md' => 'px-6 py-4 text-sm',
      'lg' => 'px-8 py-6 text-base',
  ][$size];
@endphp

<button tabindex="0"
  {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center bg-white border border-gray-300 rounded-md font-semibold text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 ' . $sized]) }}>
  {{ $slot }}
</button>
