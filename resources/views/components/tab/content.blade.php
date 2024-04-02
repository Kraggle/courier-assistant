@props(['tab' => 0])

@php
  $active = 'opacity-100 pointer-events-auto';
  $inactive = 'opacity-0 pointer-events-none';
@endphp

<div {!! $attributes->merge(['class' => "col-start-1 row-start-1 transition-all $inactive"]) !!}
  :class="{
      '{{ $active }}': activeTab === {{ $tab }},
      '{{ $inactive }}': activeTab !== {{ $tab }}
  }">
  {{ $slot }}
</div>
