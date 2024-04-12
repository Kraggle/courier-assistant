@props(['active' => false, 'title' => '', 'value' => '', 'none' => '', 'icon' => 'fat fa-user'])

<div class="flex place-items-center gap-3 text-gray-400"
  data-tooltip-position="left"
  title="{{ $title }}">
  <x-icon class="{{ $icon }} relative w-9 text-center text-2xl md:text-3xl" />
  @if ($active)
    <span class="text-bold text-xl text-black md:text-2xl">
      {{ $value }}
    </span>
  @else
    <span class="">
      {{ $none }}
    </span>
  @endif
</div>
