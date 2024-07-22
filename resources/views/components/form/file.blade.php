@props(['disabled' => false])

<input type="file"
  tabindex="0"
  {!! $attributes->twMerge(['class' => 'relative m-0 block w-full min-w-0 flex-auto rounded-md border border-solid border-gray-300 bg-clip-padding px-3 py-2 text-base font-normal shadow-sm outline-none transition duration-300 ease-in-out file:-mx-3 file:-my-2 file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-indigo-900 file:px-3 file:py-2 file:text-gray-100 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-indigo-700 focus:outline-none focus:ring-indigo-500']) !!}
  {{ $disabled ? 'disabled' : '' }}>
