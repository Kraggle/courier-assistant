@php
  $date = K::date($date ?? '')->format('Y/m/d');
  $id = $id ?? K::makeId();
@endphp

<input class="input-bro"
  name="{{ $attributes->get('name') }}"
  type="text"
  hidden
  {{ $attributes->get('required') }} />

<input id="{{ $id }}"
  data-dd-opt-default-date="{{ $date }}"
  type="date"
  tabindex="0"
  {!! $attributes->merge(['class' => 'date-input border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm'])->except(['id', 'date', 'name']) !!} />

<style>
  :root {
    --dd-radius: 0.375rem;
    --dd-shadow: 0 0 #0000, 0 0 #0000, 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --dd-overlay: rgba(0, 0, 0, .75);
    --dd-background: #FFFFFF;
    --dd-text1: #333333;
    --dd-text2: #FFFFFF;
    --dd-primary: #5b20b6;
    --dd-gradient: linear-gradient(45deg, #b22291 0%, #5b20b6 100%);
    --dd-range: rgba(0, 0, 0, 0.05);
    --dd-monthBackground: var(--dd-gradient);
    --dd-monthText: var(--dd-text2);
    --dd-monthBorder: transparent;
    --dd-confirmButtonBackground: var(--dd-gradient);
    --dd-confirmButtonText: var(--dd-text2);
    --dd-selectedBackground: var(--dd-gradient);
    --dd-selectedText: var(--dd-text2);
  }

  .dd__dropdown,
  .dd__dropdown * {
    font-family: inherit !important;
  }

  .dd__item.dd-placeholder {
    opacity: 0.5;
  }
</style>

<!-- datedropper init -->
@pushOnce('scripts')
  <script type="module">
    $(() => {

      new DateDropper({
        selector: 'input.date-input',
        format: "dd/mm/y",
        lang: "en",
        showArrowsOnHover: false,
        overlay: false,
        expandable: true,
        expandedOnly: false,
        doubleView: false,
        startFromMonday: false,
        jump: "10",
        range: false,
        loopAll: true,
        onChange: (res) => {
          const $el = $(res.trigger).siblings('.input-bro');
          $el.val(`${res.output.y}-${res.output.mm}-${res.output.dd}`).trigger('change');
        }
      });
    });
  </script>
@endPushOnce
