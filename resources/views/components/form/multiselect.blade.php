@php
  $noresults = $noresults ?? false;
  $options = $options ?? false;
  $elements = $elements ?? false;
  $id = $id ?? K::makeId();
  $minresultsforsearch = $minresultsforsearch ?? 0;
  $placeholder = $placeholder ?? false;
  $tags = $tags ?? false;
@endphp

<select {!! $attributes->merge(['id' => $id, 'class' => 'select-input']) !!}
  multiple="multiple">
  {{ $options ?? $slot }}
</select>

<elements class="hidden">
  {{ $elements }}
</elements>

<noresults class="hidden">
  {{ $noresults }}
</noresults>

<script type="module">
  const formatOption = function(option, container) {
    if (!option.id)
      return option.text;

    const hidden = $(option.html).hasClass('hidden');
    $(container)[hidden ? 'addClass' : 'removeClass']('hidden');

    return $(option.html).prop("outerHTML");
  };

  const opts = {
    minimumResultsForSearch: {{ $minresultsforsearch }}
  };

  const $p = $('#{{ $id }}').parent(),
    $opts = $('elements > div', $p),
    data = [];
  if ($opts.length > 0) {
    $opts.each((i, el) => {
      data.push({
        id: $(el).attr('value'),
        text: $(el).text(),
        html: $(el)
      })
    });

    opts.data = data;
    opts.templateResult = formatOption;
    opts.templateSelection = formatOption;
    opts.escapeMarkup = function(markup) {
      return markup;
    }
  }

  opts.tags = {{ $tags }};

  @if ($noresults !== false)
    opts.language = {
      noResults: function() {
        return $('noresults', $p).html();
      },
    }
    opts.escapeMarkup = function(markup) {
      return markup;
    }
  @endif

  @if ($placeholder !== false)
    opts.placeholder = '{{ $placeholder }}';
  @endif

  const $el = $('#{{ $id }}');
  $el.select2(opts);
  $el.on('change', refreshAll);
</script>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('.required select').on('change', function() {
        $(this).closest('.required')[$(this).val() ? 'addClass' : 'removeClass']('selected');
      });
    });
  </script>
@endpushOnce
