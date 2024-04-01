@auth
  @php
    $user = K::user();
    $refuel_link = $user->hasVehicle() ? route('refuels', $user->vehicle()->id) : route('vehicle.show');

    $links = [
        [
            'title' => __('subscribe'),
            'route' => 'subscription',
        ],
    ];

    if ($user->subscribed()) {
        $links = [
            [
                'title' => __('home'),
                'route' => 'dashboard',
            ],
            [
                'title' => __('map'),
                'route' => 'map.show',
            ],
            [
                'title' => __('routes'),
                'route' => 'route.show',
            ],
            [
                'title' => __('refuels'),
                'route' => 'refuels',
                'href' => $refuel_link,
            ],
            [
                'title' => __('expenses'),
                'route' => 'expense.show',
            ],
        ];

        if ($user->hasRoutes() && false) {
            $links[] = [
                'title' => __('taxes'),
                'route' => 'tax.show',
                'href' => route('tax.show', $user->taxYears()->first()->year),
            ];
        }

        $links[] = [
            'title' => __('more'),
            'items' => [
                [
                    'title' => __('rates'),
                    'route' => 'rate.show',
                ],
                [
                    'title' => __('DSP'),
                    'route' => 'dsp.show',
                ],
            ],
        ];
    }

  @endphp
@endauth

@guest
  @php
    $links = [
        [
            'title' => __('login'),
            'route' => 'login',
        ],
        [
            'title' => __('register'),
            'route' => 'register',
        ],
    ];

  @endphp
@endguest

<nav x-data="{ open: false }"
  class="border-b border-gray-200 bg-white">

  {{-- Primary Navigation Menu --}}
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 justify-between">
      <div class="flex">
        {{-- Logo --}}
        <div class="flex shrink-0 items-center">
          <a href="{{ route('dashboard') }}">
            <x-svg.logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>

        {{-- Navigation Links --}}
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

          @foreach ($links as $link)
            @if (isset($link['items']))
              <x-dropdown.wrap>
                @define($active = false)

                <x-slot:content>
                  @foreach ($link['items'] as $item)
                    @define($active = request()->routeIs($item['route']) ? true : $active)
                    <x-dropdown.link :href="$item['href'] ?? route($item['route'])"
                      :active="request()->routeIs($item['route'])">
                      {{ $item['title'] }}
                    </x-dropdown.link>
                  @endforeach
                </x-slot>

                <x-slot:trigger>
                  <x-nav.link class="place-center flex cursor-pointer gap-2"
                    :active="$active">
                    {{ $link['title'] }}
                    <x-icon class="far fa-caret-down" />
                  </x-nav.link>
                </x-slot>
              </x-dropdown.wrap>
            @else
              <x-nav.link :href="$link['href'] ?? route($link['route'])"
                :active="request()->routeIs($link['route'])">
                {{ $link['title'] }}
              </x-nav.link>
            @endif
          @endforeach

        </div>
      </div>

      {{-- Settings Dropdown --}}
      <div class="hidden sm:ms-6 sm:flex sm:items-center sm:gap-3">
        <x-dropdown.language align="right"
          width="32">
          <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
            <x-icon class="far fa-language text-base"
              data-tooltip-position="left"
              title="{{ Str::title(__('language selection')) }}" />
          </button>
        </x-dropdown.language>

        @auth
          <x-dropdown.wrap align="right">
            <x-slot:trigger>
              <button class="inline-flex items-center gap-2 rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                <div>{{ Auth::user()->name }}</div>
                <x-icon class="far fa-caret-down" />
              </button>
            </x-slot>

            <x-slot:content>
              <x-dropdown.link :href="route('user.show')"
                :active="request()->routeIs('user.show')">
                {{ __('profile') }}
              </x-dropdown.link>

              {{-- Authentication --}}
              <form method="POST"
                action="{{ route('logout') }}">
                @csrf

                <x-dropdown.link :href="route('logout')"
                  onclick="event.preventDefault(); this.closest('form').submit();">
                  {{ __('Log Out') }}
                </x-dropdown.link>
              </form>
            </x-slot>
          </x-dropdown.wrap>
        @endauth

      </div>

      {{-- Hamburger --}}
      <div class="-me-2 flex items-center sm:hidden">

        <x-dropdown.language align="right"
          width="32">
          <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
            <x-icon class="far fa-language text-base"
              data-tooltip-position="left"
              title="{{ Str::title(__('language selection')) }}" />
          </button>
        </x-dropdown.language>

        <button class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
          @click="open = ! open">
          <svg class="h-6 w-6"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24">
            <path class="inline-flex"
              :class="{ 'hidden': open, 'inline-flex': !open }"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <path class="hidden"
              :class="{ 'hidden': !open, 'inline-flex': open }"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Responsive Navigation Menu --}}
  <div class="hidden sm:hidden"
    :class="{ 'block': open, 'hidden': !open }">
    <div class="space-y-1 pb-3 pt-2">

      @foreach ($links as $link)
        @if (isset($link['items']))
          @foreach ($link['items'] as $item)
            <x-nav.mobile-link :href="$item['href'] ?? route($item['route'])"
              :active="request()->routeIs($item['route'])">
              {{ $item['title'] }}
            </x-nav.mobile-link>
          @endforeach
        @else
          <x-nav.mobile-link :href="$link['href'] ?? route($link['route'])"
            :active="request()->routeIs($link['route'])">
            {{ $link['title'] }}
          </x-nav.mobile-link>
        @endif
      @endforeach

    </div>

    @auth
      {{-- Responsive Settings Options --}}
      <div class="border-t border-gray-200 pb-1 pt-4">

        <div class="px-4">
          <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
          <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
        </div>

        <div class="mt-3 space-y-1">
          <x-nav.mobile-link :href="route('user.show')">
            {{ __('profile') }}
          </x-nav.mobile-link>

          {{-- Authentication --}}
          <form method="POST"
            action="{{ route('logout') }}">
            @csrf

            <x-nav.mobile-link :href="route('logout')"
              onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('Log Out') }}
            </x-nav.mobile-link>
          </form>
        </div>
      </div>
    @endauth
  </div>
</nav>
