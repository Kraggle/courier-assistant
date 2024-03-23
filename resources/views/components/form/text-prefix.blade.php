@props(['disabled' => false])

<div class="relative">

  <div class="absolute top-1/2 -translate-y-1/2 pl-2">
    {{ $slot }}
  </div>

  <input tabindex="0"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-7']) !!}>
</div>
