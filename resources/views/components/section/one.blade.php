@props(['py' => 'py-3 md:py-5', 'px' => 'px-4 md:px-6', 'maxWidth' => '7xl'])

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

<div class="{{ $maxWidth }} mx-auto w-full md:px-8">
  <div {!! $attributes->merge(['class' => "overflow-hidden bg-white shadow-sm md:rounded-md {$py} {$px} w-full"]) !!}>
    {{ $slot }}
  </div>
</div>
