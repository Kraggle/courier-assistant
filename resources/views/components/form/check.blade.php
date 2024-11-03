@props(['checked', 'label' => '', 'name' => 'check'])

<label class="inline-flex items-center gap-1">
  <input name="{{ $name }}"
    type="hidden"
    value="0">
  <input type="checkbox"
    tabindex="0"
    {!! $attributes->twMerge(['class' => 'h-6 w-6 rounded-md border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50 cursor-pointer']) !!}
    @checked($checked ?? false)>
  <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
</label>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('body').on('change', 'label input[type=checkbox]', function() {
        $(this).closest('label').find('input[type=hidden]').val($(this).is(':checked') ? '1' : '0');
      });
    });
  </script>
@endpushOnce
