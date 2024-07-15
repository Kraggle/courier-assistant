@php
  $title = [
      'news' => 'News',
      'tips' => 'Tips & Tricks',
  ][$type];
@endphp

<x-layout.app :title="$title">
  <x-section.one>

  </x-section.one>
</x-layout.app>
