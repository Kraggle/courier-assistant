@props(['button' => false, 'tabs' => false])

<tab-wrap {!! $attributes !!}>
  <tab-nav class="mb-4 flex h-[48px] items-end justify-between border-b border-gray-400 px-4 text-2xl font-medium text-gray-900 md:mb-5 md:px-6">
    <div {!! $tabs->attributes->merge(['class' => 'flex items-end justify-start gap-1']) !!}>
      {{ $tabs }}
    </div>
    @if ($button)
      <div {!! $button->attributes->merge(['class' => 'flex gap-3 self-start']) !!}>
        {{ $button }}
      </div>
    @endif
  </tab-nav>

  <tab-content class="grid">
    {{ $content }}
  </tab-content>
</tab-wrap>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      $('[tab]').on('click', function() {
        const tab = $(this).attr('tab');
        K.addURLParam('tab', tab);
        setActiveTab($(this).closest('tab-wrap'), tab);

        $(this).trigger('tab-change');
      });

      $('tab-wrap[active]').each(function() {
        setActiveTab($(this), $(this).attr('active'));
      });
    });

    function setActiveTab($wrap, active) {
      const tab = {
          on: 'border-gray-400 border-b-2 text-base sm:text-lg border-b-white bottom-[-1px]',
          off: 'border-b-0 border-gray-300 text-gray-500 text-sm sm:text-base hover:text-gray-700 hover:border-gray-400 focus:text-gray-700 focus:border-gray-400'
        },
        content = {
          on: 'opacity-100 pointer-events-auto',
          off: 'opacity-0 pointer-events-none'
        };

      $('tab-nav [tab]', $wrap).removeClass(tab.on).addClass(tab.off);
      $(`tab-nav [tab=${active}]`, $wrap).removeClass(tab.off).addClass(tab.on);

      $('tab-content [tab]', $wrap).removeClass(content.on).addClass(content.off);
      $(`tab-content [tab=${active}]`, $wrap).removeClass(content.off).addClass(content.on);
    }
  </script>
@endpushOnce
