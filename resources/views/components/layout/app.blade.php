@props(['title' => '', 'center' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

  $title = ($title ? __(':Title - ', ['title' => $title]) : '') . config('app.name', 'Laravel');

  $center = $center ? 'sm:justify-center' : '';
@endphp

<head>
  <meta charset="utf-8">
  <meta name="viewport"
    content="width=device-width, initial-scale=1">
  <meta name="csrf-token"
    content="{{ csrf_token() }}">
  <meta http-equiv="refresh"
    content="{{ config('session.lifetime') * 60 }}">

  <title>{{ $title }}</title>

  <link href="{{ Vite::asset('resources/images/icon.svg') }}"
    rel="icon">

  {{-- font awesome 6.5.1 --}}
  <link href="https://fa.kgl.app/css/all.min.css"
    rel="stylesheet">

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com"
    rel="preconnect">
  <link href="https://fonts.gstatic.com"
    rel="preconnect"
    crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Advent+Pro:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <link href="https://use.typekit.net/syn1jur.css"
    rel="stylesheet">

  {{-- Scripts --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('styles')

</head>

<body class="bg-gray-100 font-sans text-lg antialiased">
  <div class="bg-pattern flex min-h-screen flex-col gap-6 md:gap-8">
    <x-layout.navigation />

    {{-- Page Content --}}
    <main class="{{ $center }} flex w-full flex-1 flex-col gap-6 md:gap-8">

      {{-- Page Heading --}}
      @if (isset($header))
        <header class="bg-white shadow">
          <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $header }}
          </div>
        </header>
      @endif

      @foreach ($messages as $type => $msg)
        <x-notify.message :type="$type"
          :message="$msg" />
      @endforeach

      {{ $slot }}
    </main>

    <x-layout.footer />
  </div>

  <modal-wrap>
    {{-- @include('modal.keep-alive') --}}
    @include('modal.loading')
    @stack('modals')
  </modal-wrap>

  @pushOnce('scripts')
    <script type="module">
      $(() => {
        $('[data-help-trigger]').on('click', function() {
          const $els = $(this).closest('[help-root]').find('[help-message]');
          $els[$els.eq(0).is(':visible') ? 'slideUp' : 'slideDown'](500);
        });
      });
    </script>
  @endPushOnce

  <script-wrap>
    @stack('scripts')
  </script-wrap>

</body>

</html>
