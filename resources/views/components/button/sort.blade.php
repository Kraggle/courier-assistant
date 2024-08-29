@props(['active', 'dir' => 'desc'])

@php
  $active = !$active ? '' : "sort-active sort-$dir";
@endphp

<div {!! $attributes->twMerge(['class' => "$active flex cursor-pointer gap-3 [&.sort-active.sort-asc_.asc]:text-black [&.sort-active.sort-desc_.desc]:text-black sort-button"]) !!}>
  {{ $slot }}
  <div class="relative text-xs text-gray-300">
    <x-icon class="asc fas fa-caret-up absolute left-0 top-0"></x-icon>
    <x-icon class="desc fas fa-caret-down absolute bottom-0 left-0"></x-icon>
  </div>
</div>
