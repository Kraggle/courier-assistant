@props(['on' => 'Yes', 'off' => 'No', 'checked' => false])

<div {!! $attributes->only('class')->merge(['class' => 'checkwrap border relative rounded-md border-gray-300 shadow-sm']) !!}>
  <input class="pointer-events-none w-full opacity-0"
    type="text"
    value="{{ $checked ? '1' : '0' }}"
    tabindex="-1"
    {!! $attributes->only('name') !!}>
  <label class="absolute inset-0 cursor-pointer">
    <input class="peer sr-only"
      type="checkbox"
      value="1"
      {!! $attributes->except(['class', 'name']) !!}
      {{ $checked ? 'checked' : '' }}>
    <div class="peer relative h-full w-full rounded-md bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-[calc(100%-4px)] after:w-[calc(50%-2px)] after:rounded-md after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-100 peer-checked:after:translate-x-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rtl:peer-checked:after:-translate-x-full"></div>
  </label>
  <div class="pointer-events-none absolute inset-0 grid grid-cols-2 content-center justify-items-center">
    <div class="">{{ $off }}</div>
    <div>{{ $on }}</div>
  </div>

</div>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('body').on('change', '.checkwrap input[type=checkbox]', function() {
        $(this).closest('.checkwrap').find('input[type=text]').val($(this).is(':checked') ? '1' : '0');
      });
    });
  </script>
@endpushOnce
