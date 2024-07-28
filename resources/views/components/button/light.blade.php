@props(['size' => 'sm', 'href' => false])

<{{ $href ? 'a' : 'button' }} tabindex="0"
  {{ $attributes->twMerge(['type' => 'button', 'class' => 'inline-flex items-center justify-center bg-white border border-gray-300 rounded-md font-semibold text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 ' . K::buttonSize($size)]) }}
  {{ $href ? "href=$href" : '' }}>
  {{ $slot }}
  </{{ $href ? 'a' : 'button' }}>
