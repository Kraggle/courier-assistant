@props(['maxWidth' => '7xl'])

<div class="{{ K::maxWidth($maxWidth) }} mx-auto w-full md:px-8">
  <div {!! $attributes->twMerge(['class' => 'overflow-hidden bg-white shadow-sm md:rounded-md py-3 md:py-5 px-4 md:px-6 w-full']) !!}>
    {{ $slot }}
  </div>
</div>
