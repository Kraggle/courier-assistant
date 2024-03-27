@php
  $label = $attributes->has('label') ? $attributes->get('label') : '';
  $padding = $attributes->has('padding') ? $attributes->get('padding') : 'p-4 md:p-6';
  $gap = $attributes->has('gap') ? $attributes->get('gap') : 'gap-4 md:gap-6';
@endphp

<div {!! $attributes->merge(['class' => 'relative']) !!}>
  @if ($label)
    <div class="relative h-[0.75rem]">
      <x-form.label class="absolute left-2 px-1 before:absolute before:left-0 before:right-0 before:top-1/2 before:z-[-1] before:h-[5px] before:-translate-y-1/2 before:bg-white before:content-['']"
        text="text-base"
        weight="font-semibold"
        :value="$label" />
    </div>
  @endif
  <div class="{{ "$gap $padding" }} flex flex-col rounded-md border border-gray-300">
    {{ $slot }}
  </div>
</div>
