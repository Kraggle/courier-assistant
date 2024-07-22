@auth
  @php
    $user = K::user();
    $refuel_link = $user->hasVehicle() ? route('refuels', $user->vehicle()->id) : route('vehicle.show');

    $links = [
        [
            'title' => 'subscribe',
            'route' => 'subscription',
            'icon' => 'fas fa-receipt',
        ],
    ];

    if ($user->subscribed()) {
        $links = [
            [
                'title' => 'dashboard',
                'route' => 'dashboard',
                'icon' => 'fas fa-house',
            ],
            [
                'title' => 'address finder',
                'route' => 'map.show',
                'icon' => 'fas fa-map',
            ],
            [
                'title' => 'routes',
                'route' => 'route.show',
                'icon' => 'fas fa-compass',
            ],
            [
                'title' => 'refuels',
                'route' => 'refuels',
                'href' => $refuel_link,
                'icon' => 'fas fa-gas-pump',
            ],
            [
                'title' => 'expenses',
                'route' => 'expense.show',
                'icon' => 'fas fa-chart-simple',
            ],
        ];

        if ($user->hasRoutes()) {
            $links[] = [
                'title' => 'tax view',
                'route' => 'tax.show',
                'href' => route('tax.show', $user->taxYears()->first()->year),
                'icon' => 'fas fa-coins',
            ];
        }

        $links[] = [
            'title' => 'pay rates',
            'route' => 'rate.show',
            'icon' => 'fas fa-chart-pie',
        ];
        $links[] = [
            'title' => 'Your DSP',
            'route' => 'dsp.show',
            'icon' => 'fas fa-box',
        ];
    }

  @endphp
@endauth

@guest
  @php
    $links = [
        [
            'title' => 'login',
            'route' => 'login',
            'icon' => 'fas fa-right-to-bracket',
        ],
        [
            'title' => 'register',
            'route' => 'register',
            'icon' => 'fas fa-address-card',
        ],
    ];

  @endphp
@endguest

<nav class="border-b border-gray-200 bg-white">

  {{-- top bar --}}
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    <div class="flex h-16 justify-between">
      {{-- Hamburger --}}
      <div class="-ms-2 flex items-center gap-3">

        <button class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
          id="navBtn">

          <svg class="h-6 w-6"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24">
            <path class="inline-flex"
              :class="{ 'hidden': show, 'inline-flex': !show }"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <path class="hidden"
              :class="{ 'hidden': !show, 'inline-flex': show }"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>

        </button>

        {{-- Logo --}}
        <div class="flex items-center">
          <a href="{{ route('dashboard') }}">
            <x-svg.logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>

      </div>

    </div>
  </div>

  {{-- nav manu --}}
  <div class="absolute bottom-0 top-16 z-50 w-auto -translate-x-full overflow-y-auto bg-white shadow-lg transition-all duration-500 ease-in-out"
    id="navMenu">
    <div class="space-y-1">

      @foreach ($links as $link)
        @if (isset($link['items']))
          @foreach ($link['items'] as $item)
            <x-nav.link :href="$item['href'] ?? route($item['route'])"
              :active="request()->routeIs($item['route'])"
              :icon="$item['icon'] ?? false">
              {{ $item['title'] }}
            </x-nav.link>
          @endforeach
        @else
          <x-nav.link :href="$link['href'] ?? route($link['route'])"
            :active="request()->routeIs($link['route'])"
            :icon="$link['icon'] ?? false">
            {{ $link['title'] }}
          </x-nav.link>
        @endif
      @endforeach

    </div>

    @if (K::user()->isAdmin() || K::hasPosts())
      <div class="my-2 border-t border-gray-200"></div>

      <div class="space-y-1">
        @if (K::hasPosts('news'))
          <x-nav.link :href="route('news')"
            icon="fas fa-newspaper"
            :active="request()->routeIs('news')">
            News Articles
          </x-nav.link>
        @endif

        @if (K::hasPosts('tips'))
          <x-nav.link :href="route('tips')"
            icon="fas fa-messages-question"
            :active="request()->routeIs('tips')">
            Tips & Tricks
          </x-nav.link>
        @endif

        @if (K::user()->isAdmin())
          <x-nav.link :href="route('post.creator')"
            icon="fas fa-comment-plus"
            :active="request()->routeIs('post.creator')">
            Post Creator
          </x-nav.link>
        @endif
      </div>
    @endif

    @auth
      <div class="my-2 border-t border-gray-200"></div>
      {{-- Responsive Settings Options --}}
      <div class="space-y-1">

        <div class="flex gap-3 py-2 pe-12 pl-[calc((100vw_-_80rem)_/_2)]">
          <div class="w-2.5"></div>
          <div>
            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
          </div>
        </div>

        <div class="space-y-1">
          <x-nav.link :href="route('user.show')"
            icon="fas fa-user"
            :active="request()->routeIs('user.show')">
            profile
          </x-nav.link>

          {{-- Authentication --}}
          <form method="POST"
            action="{{ route('logout') }}">
            @csrf

            <x-nav.link :href="route('logout')"
              onclick="event.preventDefault(); this.closest('form').submit();"
              icon="fas fa-right-from-bracket">
              Log Out
            </x-nav.link>
          </form>
        </div>
      </div>
    @endauth
  </div>

  {{-- page blank --}}
  <div class="bg-opacity-15 pointer-events-none fixed inset-0 top-16 z-40 bg-black opacity-0 transition-all"
    id="navClose"></div>
</nav>

@pushOnce('scripts')
  <script type="module">
    $('#navBtn, #navClose').on('click', () => {
      $('#navMenu').toggleClass('translate-x-0 -translate-x-full');
      $('#navClose').toggleClass('opacity-100 pointer-events-auto pointer-events-none opacity-0');
      $('body').toggleClass('overflow-y-hidden');
    });
  </script>
@endPushOnce
