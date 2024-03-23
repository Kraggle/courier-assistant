@props(['size' => '8', 'color' => 'black'])

<div {!! $attributes->merge(['class' => 'flex items-center justify-center gap-2']) !!}>
  <span class='sr-only'>Loading...</span>
  <div class='h-{{ $size }} w-{{ $size }} bg-{{ $color }} animate-bounce rounded-full [animation-delay:-0.3s]'></div>
  <div class='h-{{ $size }} w-{{ $size }} bg-{{ $color }} animate-bounce rounded-full [animation-delay:-0.15s]'></div>
  <div class='h-{{ $size }} w-{{ $size }} bg-{{ $color }} animate-bounce rounded-full'></div>
</div>
