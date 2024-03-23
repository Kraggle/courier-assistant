@php
  $value = $attributes->has('value') ? $attributes->get('value') : '';
  $key = $attributes->has('key') ? $attributes->get('key') : '';
  $left = $attributes->has('left') ? $attributes->get('left') : 'left-2';
  $help = $attributes->has('help') ? $attributes->get('help') : '';
@endphp

<div {!! $attributes->merge(['class' => '']) !!}>
  @if ($value)
    <div class="relative h-[0.65rem]">
      <x-form.label class="{{ $left }} absolute px-1 before:absolute before:left-0 before:right-0 before:top-1/2 before:z-[-1] before:h-[calc(100%_-_8px)] before:-translate-y-1/2 before:rounded-sm before:bg-white before:content-['']"
        for="{{ $key }}"
        :value="$value" />
    </div>
  @endif

  {{ $slot }}

  @if ($key)
    <x-form.error class="mt-2"
      :messages="$errors->get($key)" />
  @endif

  @if ($help)
    <div class="hidden"
      help-message>
      <div class="flex gap-2 pl-2 pt-2 text-xs text-gray-500">
        <x-icon class="far fa-info-circle text-base text-blue-400" />
        <p>{!! $help !!}</p>
      </div>
    </div>
  @endif
</div>
