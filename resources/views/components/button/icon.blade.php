@props(['icon' => 'fal fa-star', 'color' => 'bg-gray-800 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900'])

<button tabindex="0"
  {{ $attributes->merge(['type' => 'submit', 'class' => "inline-flex items-center border border-transparent rounded-md font-semibold text-white uppercase tracking-widest focus:outline-none focus:ring-2 shadow-sm focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 px-3 py-2 sm:px-5 sm:py-3 text-[10px] leading-none sm:text-xs {$color}"]) }}>
  <div class="flex flex-col gap-2">
    <x-icon class="{{ $icon }} text-3xl sm:text-5xl" />
    {{ $slot }}
  </div>
</button>
