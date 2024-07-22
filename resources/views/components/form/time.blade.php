@php
  $time = K::date($time ?? '')->format('g:i A');
  $id = $id ?? K::makeId();
@endphp

<input id="{{ $id }}"
  type="text"
  tabindex="0"
  {!! $attributes->twMerge(['class' => 'w-full block time-input border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm'])->except(['id', 'time']) !!}>

<style>
  :root {
    --td-textColor: #333333;
    --td-backgroundColor: #FFFFFF;
    --td-primaryColor: #5b20b6;
    --td-displayBackgroundColor: #FFFFFF;
    --td-displayBorderColor: #5b20b6;
    --td-displayBorderStyle: solid;
    --td-displayBorderWidth: 4px;
    --td-handsColor: #5b20b650;
    --td-handleColor: #5b20b6;
    --td-handlePointColor: white;
  }
</style>

<!-- timedropper init -->
<script type="module">
  $('#{{ $id }}').timeDropper({
    format: 'h:mm A',
    meridians: true,
    setCurrentTime: false,
    autoswitch: true,
    mousewheel: true,
    init_animation: 'dropdown'
  });
</script>
