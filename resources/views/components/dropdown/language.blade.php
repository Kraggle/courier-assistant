@props(['align' => 'left', 'width' => '32', 'contentClasses' => 'py-1 bg-white'])

<div {{ $attributes->merge(['class' => '']) }}>
  <x-dropdown.wrap :align="$align"
    :width="$width"
    :contentClasses="$contentClasses">
    <x-slot:trigger>
      {{ $slot }}
    </x-slot>

    <x-slot:content>
      {{-- english --}}
      <x-dropdown.link class="cursor-pointer"
        href="{{ url()->current() . '?en' }}">
        <div class="flex gap-2">
          <img class="h-5 rounded-sm border border-gray-700"
            src="{{ Vite::asset('resources/images/flags/en.svg') }}" />
          <span>{{ __('English') }}</span>
        </div>
      </x-dropdown.link>

      {{-- bulgarian --}}
      <x-dropdown.link class="cursor-pointer"
        href="{{ url()->current() . '?bg' }}">
        <div class="flex gap-2">
          <img class="h-5 rounded-sm border border-gray-700"
            src="{{ Vite::asset('resources/images/flags/bg.svg') }}" />
          <span>{{ __('Bulgarian') }}</span>
        </div>
      </x-dropdown.link>

      {{-- polish --}}
      <x-dropdown.link class="cursor-pointer"
        href="{{ url()->current() . '?pl' }}">
        <div class="flex gap-2">
          <img class="h-5 rounded-sm border border-gray-700"
            src="{{ Vite::asset('resources/images/flags/pl.svg') }}" />
          <span>{{ __('Polish') }}</span>
        </div>
      </x-dropdown.link>

      {{-- romanian --}}
      <x-dropdown.link class="cursor-pointer"
        href="{{ url()->current() . '?ro' }}">
        <div class="flex gap-2">
          <img class="h-5 rounded-sm border border-gray-700"
            src="{{ Vite::asset('resources/images/flags/ro.svg') }}" />
          <span>{{ __('Romanian') }}</span>
        </div>
      </x-dropdown.link>

    </x-slot>
  </x-dropdown.wrap>
</div>
