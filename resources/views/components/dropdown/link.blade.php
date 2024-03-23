@props(['active'])

@php
  $classes = $active ?? false ? 'border-indigo-400' : 'border-transparent';
@endphp

<a {{ $attributes->merge(['class' => "$classes block w-full pl-4 pr-7 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out capitalize whitespace-nowrap border-r-2"]) }}>{{ $slot }}</a>
