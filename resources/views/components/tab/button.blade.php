@props(['tab' => 0, 'href' => false])

<a {!! $attributes->twMerge(['class' => 'cursor-pointer relative border px-2 py-1 sm:px-4 sm:py-2 rounded-t-md transition duration-150 ease-in-out capitalize']) !!}
  {{ !$href ? "tab=$tab" : '' }}>
  {{ $slot }}
</a>
