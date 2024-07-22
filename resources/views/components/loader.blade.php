@props(['size' => '8', 'color' => 'bg-black'])

@php
  $classes =
      [
          2 => 'w-2 h-2',
          3 => 'w-3 h-3',
          4 => 'w-4 h-4',
          5 => 'w-5 h-5',
          6 => 'w-6 h-6',
          7 => 'w-7 h-7',
          8 => 'w-8 h-8',
          9 => 'w-9 h-9',
          10 => 'w-10 h-10',
      ][$size] . " $color";
@endphp

<div {!! $attributes->twMerge(['class' => 'flex items-center justify-center gap-2']) !!}>
  <span class='sr-only'>Loading...</span>
  <div class='{{ $classes }} animate-bounce rounded-full [animation-delay:-0.3s]'></div>
  <div class='{{ $classes }} animate-bounce rounded-full [animation-delay:-0.15s]'></div>
  <div class='{{ $classes }} animate-bounce rounded-full'></div>
</div>
