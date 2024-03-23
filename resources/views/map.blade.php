@php

@endphp

<x-layout.app :title="__('map test')">

  <div class="mx-auto flex w-full flex-1 flex-col sm:max-w-7xl md:px-8">
    <div class="w-full flex-1 overflow-hidden bg-white shadow-sm md:rounded-md"
      id="map">

    </div>
  </div>

  @vite('resources/js/map.js')

  {{-- tailwind classes used in javascript --}}
  <div class="mr-2 hidden h-10 w-10 rounded-sm border-b bg-white text-center text-xl text-gray-600 hover:text-violet-500"></div>
</x-layout.app>
