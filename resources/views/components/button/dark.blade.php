@props(['size' => 'sm', 'href' => false])

@php
  $sized = [
      'sm' => 'px-4 py-2 text-xs',
      'md' => 'px-5 py-3 text-sm',
      'lg' => 'px-6 py-4 text-base',
  ][$size];
@endphp

<{{ $href ? 'a' : 'button' }} tabindex="0"
  {{ $attributes->twMerge(['type' => 'submit', 'class' => "bg-gray-700 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-800 inline-flex items-center justify-center border border-transparent rounded-md font-semibold text-white uppercase tracking-widest focus:outline-none focus:ring-2 shadow-sm focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 $sized"]) }}
  {{ $href ? "href=$href" : '' }}>
  {{ $slot }}
  </{{ $href ? 'a' : 'button' }}>
