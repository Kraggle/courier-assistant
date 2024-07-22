@props(['disabled' => false])

<div {!! $attributes->twMerge(['class' => 'w-full border border-gray-300 focus-within:border-indigo-500 focus-within:ring-indigo-500 rounded-md shadow-sm grid grid-cols-[auto_1fr] focus-within:ring-1 bg-white gap-0'])->only('class') !!}>

  <div class="self-center pl-2">
    {{ $slot }}
  </div>

  <input class="w-full border-0 bg-transparent focus:ring-0"
    tabindex="0"
    {!! $attributes->except('class') !!}
    {{ $disabled ? 'disabled' : '' }}>
</div>
