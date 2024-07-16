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

<notify-box class="pointer-events-none fixed left-0 right-0 top-0">
  <div class="mx-auto grid max-w-full px-8 py-4 sm:max-w-7xl sm:grid-cols-[1fr_2fr] sm:px-16 sm:py-8">
    <div></div>
    <div class="flex flex-col">
      @foreach ($messages as $type => $msg)
        <x-notify.message :type="$type"
          :message="$msg" />
      @endforeach
    </div>
  </div>
</notify-box>
