@props(['tab' => 0, 'href' => false])

@php
  $active = 'border-gray-400 border-b-2 text-base sm:text-lg border-b-white bottom-[-1px] ';
  $inactive = 'border-b-0 border-gray-300 text-gray-500 text-sm sm:text-base hover:text-gray-700 hover:border-gray-400 focus:text-gray-700 focus:border-gray-400';
@endphp

<a {!! $attributes->merge(['class' => 'cursor-pointer relative border px-2 py-1 sm:px-4 sm:py-2 rounded-t-md transition duration-150 ease-in-out capitalize']) !!}
  {{ !$href ? "tab=$tab" : '' }}
  :class="{ '{{ $active }}': activeTab === {{ $tab }}, '{{ $inactive }}': activeTab !== {{ $tab }} }"
  @click="activeTab = {{ $tab }}">
  {{ $slot }}
</a>
