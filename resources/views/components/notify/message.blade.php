@props(['py' => 'py-2 md:py-3', 'px' => 'px-3 md:px-4', 'type' => 'status', 'message'])

@php
  switch ($type) {
      case 'success':
      case 'message':
          $class = 'bg-green-100 border-green-400 text-gray-800';
          $icon = 'far fa-check-circle text-green-700';
          break;
      case 'error':
      case 'warning':
          $class = 'bg-red-100 border-red-400 text-gray-800';
          $icon = 'far fa-exclamation-triangle text-red-700';
          break;
      case 'status':
      case 'info':
          $class = 'bg-blue-100 border-blue-400 text-gray-800';
          $icon = 'far fa-info-circle text-blue-700';
          break;
  }
@endphp

<div x-data="{ show: true }"
  x-show="show"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100 scale-100"
  x-transition:leave-end="opacity-0 scale-50"
  x-init="setTimeout(() => show = false, 5000)"
  class="mx-auto w-full max-w-7xl md:px-8">

  <div {!! $attributes->merge(['class' => "flex gap-4 justify-between overflow-hidden {$class} border shadow-md md:rounded-md {$py} {$px}"]) !!}>

    <div class="flex items-center gap-4">
      <x-icon class="{{ $icon }}" />
      <p>{{ $message }}</p>
    </div>

    <button @click="show = !show">
      <x-icon class="fas fa-times text-gray-900" />
    </button>

  </div>
</div>
