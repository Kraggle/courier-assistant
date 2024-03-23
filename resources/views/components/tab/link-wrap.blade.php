@props(['button' => false, 'tabs' => false])

<div {!! $attributes->merge(['class' => 'h-[48px] flex items-end justify-between border-b border-gray-400']) !!}>
  <div {!! $tabs->attributes->merge(['class' => 'flex items-end justify-start gap-1']) !!}>
    {{ $tabs }}
  </div>
  @if ($button)
    <div {!! $button->attributes->merge(['class' => 'flex gap-3 self-start']) !!}>
      {{ $button }}
    </div>
  @endif
</div>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('[tab]').on('click', function() {
        K.addURLParam('tab', $(this).attr('tab'));
      });
    });
  </script>
@endpushOnce
