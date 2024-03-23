<footer class="w-full text-base">
  <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-2 sm:flex-row sm:justify-between sm:px-6 sm:py-3 lg:px-8">

    <div class="text-gray-=400 flex items-center justify-center gap-3">
      <a href="{{ route('terms-and-conditions') }}">{{ __('Terms & Conditions') }}</a>
      <span>|</span>
      <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
    </div>

    <span class="flex items-center justify-center gap-1 font-bold text-gray-700">
      © {{ __('Copyright') . ' ' . now()->year . ' ' . __('by') }}
      <span class="text-violet-700">{{ config('app.name') }}</span>
    </span>

  </div>
</footer>
