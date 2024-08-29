@props(['checked', 'label' => ''])

<label class="inline-flex items-center gap-1">
  <input type="checkbox"
    tabindex="0"
    {!! $attributes->twMerge(['class' => 'h-6 w-6 rounded-md border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50 cursor-pointer']) !!}
    @checked($checked ?? false)>
  <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
</label>
