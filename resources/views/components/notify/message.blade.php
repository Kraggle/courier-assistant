@props(['py' => 'py-2 md:py-3', 'px' => 'px-3 md:px-4', 'type' => 'status', 'message'])

@php
  switch ($type) {
      case 'success':
      case 'message':
          $class = 'bg-green-100 border-green-400 text-gray-800';
          $icon = 'far fa-check-circle text-green-700';
          break;
      case 'error':
      case 'warning':
          $class = 'bg-red-100 border-red-400 text-gray-800';
          $icon = 'far fa-exclamation-triangle text-red-700';
          break;
      case 'status':
      case 'info':
          $class = 'bg-blue-100 border-blue-400 text-gray-800';
          $icon = 'far fa-info-circle text-blue-700';
          break;
  }
@endphp

<notify-wrap class="block w-full">

  <notify {!! $attributes->twMerge(['class' => "mb-4 opacity-100 scale-100 translate-y-0 transition ease-out duration-400 flex gap-4 justify-between overflow-hidden {$class} border shadow-md rounded-full {$py} {$px} pointer-events-auto"]) !!}>

    <div class="flex items-center gap-4">
      <x-icon class="{{ $icon }}" />
      <p>{{ $message }}</p>
    </div>

    <button>
      <x-icon class="fas fa-times text-gray-900" />
    </button>

  </notify>
</notify-wrap>

@pushOnce('scripts')
  <script type="module">
    const Notify = {
      close() {
        $(this)
          .removeClass('opacity-100 scale-100 translate-y-0')
          .addClass('opacity-0 scale-80 -translate-y-24');
        setTimeout(() => {
          $(this).closest('notify-wrap').remove();
        }, 400)
      }
    };

    $(() => {
      setTimeout(() => {
        $('notify').each(Notify.close);
      }, 5000)

      $('notify button').click(function() {
        $(this).closest('notify').each(Notify.close);
      });
    });
  </script>
@endPushOnce
