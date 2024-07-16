@props(['align' => 'right', 'width' => '', 'content-classes' => ''])

@php
  switch ($align) {
      case 'left':
          $alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
          break;
      case 'top':
          $alignmentClasses = 'origin-top';
          break;
      case 'right':
      default:
          $alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
          break;
  }

  switch ($width) {
      case '48':
          $width = 'w-48';
          break;
      case '44':
          $width = 'w-44';
          break;
      case '40':
          $width = 'w-40';
          break;
      case '36':
          $width = 'w-36';
          break;
      case '32':
          $width = 'w-32';
          break;
      case '28':
          $width = 'w-28';
          break;
      default:
          $width = '';
  }
@endphp

<drop-wrap class="relative">
  <drop-btn class="inline-flex h-full">
    {{ $trigger }}
  </drop-btn>

  <drop class="{{ $width }} {{ $alignmentClasses }} pointer-events-none absolute z-50 mt-2 block scale-95 rounded-md opacity-0 shadow-lg transition duration-200 ease-in-out">
    <div {{ $content->attributes->twMerge('py-1 bg-white rounded-md ring-1 ring-black ring-opacity-5') }}>
      {{ $content }}
    </div>
  </drop>
</drop-wrap>

@pushOnce('scripts')
  <script type="module">
    const Dropdown = {
      open() {
        $(this).closest('drop-wrap').addClass('drop-open');
        $(this).siblings('drop')
          .removeClass('opacity-0 scale-95 pointer-events-none')
          .addClass('opacity-100 scale-100 pointer-events-auto');
      },

      close() {
        $('.drop-wrap').removeClass('drop-open');
        $('drop')
          .removeClass('opacity-100 scale-100 pointer-events-auto')
          .addClass('opacity-0 scale-95 pointer-events-none');
      }
    };

    $(() => {
      $('drop-btn').on('click', Dropdown.open);

      $('body').on('click', function(e) {
        if ($('.drop-open').length && !$(e.target).closest('drop-wrap').length)
          Dropdown.close();
      });

      $('drop a, drop button').on('click', Dropdown.close);
    });
  </script>
@endPushOnce
