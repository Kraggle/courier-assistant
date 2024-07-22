@props(['maxWidth' => '7xl'])

@php
  $maxWidth = [
      'sm' => 'sm:max-w-sm',
      'md' => 'sm:max-w-md',
      'lg' => 'sm:max-w-lg',
      'xl' => 'sm:max-w-xl',
      '2xl' => 'sm:max-w-2xl',
      '3xl' => 'sm:max-w-3xl',
      '4xl' => 'sm:max-w-4xl',
      '5xl' => 'sm:max-w-5xl',
      '6xl' => 'sm:max-w-6xl',
      '7xl' => 'sm:max-w-7xl',
      'full' => 'sm:max-w-full',
  ][$maxWidth];
@endphp

<div class="{{ $maxWidth }} mx-auto grid w-full grid-cols-1 gap-6 md:grid-cols-2 md:gap-8 md:px-8">
  <div {!! $one->attributes->twMerge(['class' => 'overflow-hidden bg-white shadow-sm md:rounded-md py-3 md:py-5 px-4 md:px-6 w-full']) !!}>
    {{ $one }}
  </div>
  <div {!! $two->attributes->twMerge(['class' => 'overflow-hidden bg-white shadow-sm md:rounded-md py-3 md:py-5 px-4 md:px-6 w-full']) !!}>
    {{ $two }}
  </div>
</div>
