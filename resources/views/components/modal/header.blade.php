@props(['title' => '', 'help' => false])

<div class="relative mb-3">
  <div class="text-center font-serif text-xl font-light uppercase tracking-widest text-gray-400"
    @if ($title) ref="title" @endif>
    @if ($title)
      {{ $title }}
    @else
      {{ $slot }}
    @endif
  </div>

  @if ($help)
    <x-icon class="far fa-circle-question absolute right-0 top-0 cursor-pointer"
      data-help-trigger="false"
      :title="__('Toggle help text!')" />
  @endif
</div>
