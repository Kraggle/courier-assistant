@props(['disabled' => false])

<input tabindex="0"
  {{ $disabled ? 'disabled' : '' }}
  {!! $attributes->twMerge(['class' => 'w-full block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
