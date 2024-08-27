<div {{ $attributes->twMerge(['class' => 'flex items-center gap-2 whitespace-nowrap']) }}>
  <span {{ $main->attributes->twMerge(['class' => '']) }}>
    {{ $main }}
  </span>

  <span {{ $alt->attributes->twMerge(['class' => 'text-xs font-light text-gray-600 sm:text-sm']) }}>
    {{ $alt }}
  </span>
</div>
