@props(['align' => 'right', 'width' => '', 'contentClasses' => 'py-1 bg-white'])

@php
  switch ($align) {
      case 'left':
          $alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
          break;
      case 'top':
          $alignmentClasses = 'origin-top';
          break;
      case 'right':
      default:
          $alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
          break;
  }

  switch ($width) {
      case '48':
          $width = 'w-48';
          break;
      case '44':
          $width = 'w-44';
          break;
      case '40':
          $width = 'w-40';
          break;
      case '36':
          $width = 'w-36';
          break;
      case '32':
          $width = 'w-32';
          break;
      case '28':
          $width = 'w-28';
          break;
      default:
          $width = '';
  }
@endphp

<div x-data="{ open: false }"
  class="relative"
  @click.outside="open = false"
  @close.stop="open = false">
  <div class="inline-flex h-full"
    @click="open = ! open">
    {{ $trigger }}
  </div>

  <div x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="{{ $width }} {{ $alignmentClasses }} absolute z-50 mt-2 rounded-md shadow-lg"
    style="display: none;"
    @click="open = false">
    <div class="{{ $contentClasses }} rounded-md ring-1 ring-black ring-opacity-5">
      {{ $content }}
    </div>
  </div>
</div>
