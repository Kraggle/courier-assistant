@props(['maxWidth' => '7xl'])

<div {!! $attributes->twMerge(['class' => K::maxWidth($maxWidth) . ' mx-auto grid w-full grid-cols-1 gap-6 md:grid-cols-2 md:gap-8 md:px-8']) !!}>
  <div {!! $one->attributes->twMerge(['class' => 'overflow-hidden bg-white shadow-sm md:rounded-md py-3 md:py-5 px-4 md:px-6 w-full']) !!}>
    {{ $one }}
  </div>
  <div {!! $two->attributes->twMerge(['class' => 'overflow-hidden bg-white shadow-sm md:rounded-md py-3 md:py-5 px-4 md:px-6 w-full']) !!}>
    {{ $two }}
  </div>
</div>
