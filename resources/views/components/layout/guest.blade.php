@props(['maxWidth' => 'md', 'padding' => 'p-4 md:p-6', 'title' => ''])

@php
  $maxWidth = [
      'sm' => 'sm:max-w-sm',
      'md' => 'sm:max-w-md',
      'lg' => 'sm:max-w-lg',
      'xl' => 'sm:max-w-xl',
      '2xl' => 'sm:max-w-2xl',
      '3xl' => 'sm:max-w-3xl',
      '4xl' => 'sm:max-w-4xl',
      '5xl' => 'sm:max-w-5xl',
      '6xl' => 'sm:max-w-6xl',
      '7xl' => 'sm:max-w-7xl',
  ][$maxWidth];

  $title = ($title ? Str::title($title) . ' - ' : '') . config('app.name', 'Laravel');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
</head>

<body class="bg-gray-100 font-sans text-lg text-gray-900 antialiased">
  <div class="flex min-h-screen flex-col items-center gap-6 sm:justify-center">
    <header class="mt-6">
      <a href="/"><x-svg.logo class="h-[40px] fill-current sm:h-[70px]" /></a>
    </header>

    <main class="flex w-full flex-1 flex-col items-center sm:justify-center">
      <div {!! $attributes->merge(['class' => "$maxWidth $padding transform rounded-lg bg-white shadow-xl transition-all justify w-[calc(100%_-_2rem)]"]) !!}>
        {{ $slot }}
      </div>
    </main>

    <x-layout.footer />
  </div>

  <x-dropdown.language class="fixed left-2 top-2 z-50">
    <button class="inline-flex items-center rounded-md border border-transparent bg-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-900 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
      <x-icon class="far fa-language text-lg"
        data-tooltip-position="left"
        title="{{ Str::title('language selection') }}" />
    </button>
  </x-dropdown.language>

  @include('modal.loading')
  @stack('modals')
</body>

@stack('scripts')

{{-- @php
  Mail::raw('Hello World!', function ($msg) {
      $msg->to('kraggle27@gmail.com')->subject('Test Email');
  });
@endphp --}}

</html>
