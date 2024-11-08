@props(['title' => '', 'center' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
  $title = ($title ? Str::title($title) . ' - ' : '') . config('app.name', 'Laravel');

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

  <meta http-equiv="Cache-Control"
    content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma"
    content="no-cache" />
  <meta http-equiv="Expires"
    content="0" />

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

  <style>
    .bg-pattern {
      background-image: url("{{ Vite::asset('resources/images/bg-1098.png') }}");
      background-repeat: repeat;
      background-size: 250px 250px;
    }

    .bg-pattern-light {
      background-image: url("{{ Vite::asset('resources/images/bg-white-1098.png') }}");
      background-repeat: repeat;
      background-size: 250px 250px;
    }
  </style>
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

      {{ $slot }}
    </main>

    <x-layout.footer />
  </div>

  <x-notify.section></x-notify.section>

  <modal-wrap>
    {{-- @include('modal.keep-alive') --}}
    {{-- @include('modal.loading') --}}
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
