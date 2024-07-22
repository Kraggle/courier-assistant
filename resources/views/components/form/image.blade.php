@php

  $btn_class = 'relative px-4 py-2 text-xs inline-flex items-center bg-white border-t border-gray-300 font-semibold text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 justify-center';

@endphp

<div image-root
  {!! $attributes->only('class')->merge(['class' => 'relative w-full rounded-md border border-solid border-gray-300 shadow-sm transition duration-300 ease-in-out focus-within:outline-none focus-within:ring-indigo-500 overflow-hidden']) !!}>

  <img class="border-b-1 aspect-square w-full border-0 object-cover"
    src="@noImage"
    image-src />

  {{-- the input to use --}}

  <div class="grid grid-cols-2 gap-0">
    <div class="{{ $btn_class }}">

      <input class="absolute inset-0 cursor-pointer opacity-0"
        type="file"
        tabindex="0"
        image-camera
        accept="image/*,application/pdf"
        capture="camera" />

      capture
    </div>

    <div class="{{ $btn_class }} border-l">
      <input class="absolute inset-0 cursor-pointer opacity-0"
        type="file"
        tabindex="0"
        image-upload
        {!! $attributes->except('class') !!}
        accept="image/*,application/pdf" />

      browse
    </div>
  </div>

</div>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('[image-root]').on('change', 'input[type=file]', function() {
        const file = this.files[0];
        if (!file) return;

        const $ir = $(this).closest('[image-root]'),
          reader = new FileReader();
        if (file.type == "application/pdf") {
          $('[image-src]', $ir).attr('src', "{{ Vite::asset('resources/images/no-pdf.svg') }}");
        } else {
          reader.onload = function(e) {
            $('[image-src]', $ir).attr('src', e.target.result);
          };
          reader.readAsDataURL(file);
        }

        let name = $(this).attr('name');
        if (!name) {
          const other = $(`[image-${$(this).is('[image-upload]') ? 'camera' : 'upload'}]`, $ir);
          name = other.attr('name');
          $(this).attr('name', name);
          other.removeAttr('name');
        }
      });
    });
  </script>
@endPushOnce
