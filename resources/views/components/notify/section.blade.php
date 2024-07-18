@php
  $messages = [];
  if (session()->has('status')) {
      $messages['status'] = session()->get('status');
  }
  if (session()->has('error')) {
      $messages['error'] = session()->get('error');
  }
  if (session()->has('success')) {
      $messages['success'] = session()->get('success');
  }
  if (session()->has('info')) {
      $messages['info'] = session()->get('info');
  }
  if (session()->has('warning')) {
      $messages['warning'] = session()->get('warning');
  }
  if (session()->has('message')) {
      $messages['message'] = session()->get('message');
  }
@endphp

<notify-box class="pointer-events-none fixed left-0 right-0 top-0"
  data-messages="{{ json_encode($messages) }}">
  <blank-notify class="hidden">
    <notify-wrap class="block w-full transition-all">

      <notify class="scale-80 pointer-events-auto mb-4 flex -translate-y-24 justify-between gap-4 overflow-hidden rounded-full border px-3 py-2 opacity-0 shadow-md transition duration-700 ease-out md:px-4 md:py-3">

        <div class="flex items-center gap-4">
          <x-icon class=""
            icon />
          <message></message>
        </div>

        <button>
          <x-icon class="fas fa-times text-gray-900" />
        </button>

      </notify>
    </notify-wrap>
  </blank-notify>

  <div class="mx-auto grid max-w-full px-8 py-4 sm:max-w-7xl sm:grid-cols-[1fr_2fr] sm:px-16 sm:py-8">
    <div></div>
    <notify-slot></notify-slot>
  </div>

</notify-box>
