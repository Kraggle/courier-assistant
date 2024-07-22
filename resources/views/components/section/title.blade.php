@props(['title' => false, 'buttons' => false])

<div {!! $attributes->twMerge(['class' => 'flex justify-between pb-4 md:pb-6']) !!}>
  @if ($title)
    <h2 {!! $title->attributes->merge(['class' => 'font-light text-gray-400 text-xl uppercase font-serif tracking-widest']) !!}>
      {{ $title }}
    </h2>
  @endif
  @if ($buttons)
    <div {!! $buttons->attributes->merge(['class' => 'flex gap-3 self-start']) !!}>
      {{ $buttons }}
    </div>
  @endif
</div>
