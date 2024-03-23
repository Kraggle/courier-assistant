@props(['name', 'show' => false, 'maxWidth' => '2xl', 'overflow' => 'overflow-y-auto'])

@php
  $maxWidth = [
      'sm' => 'sm:max-w-sm',
      'md' => 'sm:max-w-md',
      'lg' => 'sm:max-w-lg',
      'xl' => 'sm:max-w-xl',
      '2xl' => 'sm:max-w-2xl',
  ][$maxWidth];
@endphp

<div x-data="modal"
  x-on:open-modal.window="$event.detail == '{{ $name }}' ? updateValues($event) : null"
  x-on:close-modal.window="$event.detail == '{{ $name }}' ? closeModal() : null"
  x-on:close.stop="closeModal()"
  x-on:keydown.escape.window="closeModal()"
  x-show="show"
  class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
  style="display: {{ $show ? 'block' : 'none' }};">

  <div x-show="show"
    x-on:click="closeModal()"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 transform transition-all">
    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
  </div>

  <div x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    {!! $attributes->merge(['class' => $maxWidth . ' transform rounded-lg bg-white shadow-xl transition-all mx-auto w-full max-h-[calc(100%-2rem)] ' . $overflow]) !!}>
    {{ $slot }}
  </div>

</div>

{{-- script used to refresh a page if a set time has elapsed and to open the modal in the same way as it was opened before the reload to keep the input values, this prevents the user filling in a form then it failing because their session has ended --}}
@pushOnce('scripts')
  <script type="module">
    $(() => {
      const modal = K.urlParam('modal'),
        success = {{ session()->has('success') ? 0 : 1 }};
      if (success && modal)
        $(`#${modal}`).trigger('click');
      else K.removeURLParam('modal');
    });

    document.addEventListener('alpine:init', () => {
      Alpine.data('modal', () => ({
        show: @js($show),

        updateValues(e) {
          this.show = true;

          $('body').addClass('overflow-y-hidden');

          const btnId = $(e.srcElement).attr('id');
          btnId && K.addURLParam('modal', btnId);

          const el = $(e.target);
          if (!el.data('modal'))
            return;

          K.each(el.data('modal'), (key, value) => {
            const $root = $(this.$root),
              element = key.split('.')[0],
              field = key.split('.')[1],
              $el = $root.find('[x-ref=' + element + ']');

            if (!$el) return;

            if (field == 'changes') { // specific to the changes modal

              $el.html('');
              const $tr = $('<tr></tr>', {
                  class: 'odd:bg-white even:bg-gray-50 border-b last-of-type:border-0'
                }),
                $td = $('<td></td>', {
                  class: 'px-2 py-2 first-of-type:pl-4 md:first-of-type:pl-6 last-of-type:pr-4 md:last-of-type:pr-6'
                }),
                $div = $('<div></div>', {
                  class: 'whitespace-nowrap'
                }),
                dateReplace = 'T00:00:00.000000Z';

              K.each(value, (i, v) => {
                const $row = $tr.clone(),
                  $a = $('<div></div>'),
                  $o = $a.clone(),
                  $n = $a.clone();

                K.each(v.properties.attributes, (attr, val) => {
                  $a.append($div.clone().text(attr));
                  $o.append($div.clone().text(v.properties.old[attr].replace(dateReplace, '')));
                  $n.append($div.clone().text(val.replace(dateReplace, '')));
                });

                $row.append($td.clone().text(v.date));
                $row.append($td.clone().text(v.user));
                $row.append($td.clone().html($a));
                $row.append($td.clone().html($o));
                $row.append($td.clone().html($n));

                $el.append($row);
              });
            } else if (element == 'week') { // specific to the week modal

              const $body = $root.find('[x-ref=body]');
              let $row = $root.find('[x-ref=row]').first().detach();
              $root.find('[x-ref=row]').remove();

              K.each(value.routes, (i, r) => {
                $row = $row.clone();
                $body.append($row);
                $('[x-ref=id]', $row).val(r.id);
                $('[x-ref=invoice_mileage]', $row).val(r.invoice_mileage);
                $('[x-ref=bonus]', $row).val(r.bonus);
                $('[x-ref=vat]', $row).prop('checked', r.vat);
                $('[x-ref=date]', $row).text(dayjs(r.date).format('ddd, MMM D'));
              });

            } else if (field == 'data') {
              K.each(value, (k, v) => {
                $el.data(k, v);
              });
            } else if (field == 'value') {
              $el.val(value);

              if ($el.hasClass('date-input')) {
                $el.val(dayjs(value).format('DD/MM/YYYY'));
                $el[0].datedropper('set', {
                  defaultDate: value
                });
                $el.siblings('.input-bro').val(dayjs(value).format('YYYY-MM-DD'));
              }
              if ($el.hasClass('select-input')) {
                $el.trigger('change');
              }

            } else if (field == 'text' || !field) {
              $el.text(value);

            } else if (field == 'checked') {
              $el.prop('checked', value);

            } else if (field == 'removeclass') {
              $el.removeClass(value);

            } else if (field == 'addclass') {
              $el.addClass(value);

            } else if (field == 'set-inputs') {
              $('input', $el).val(value);

            } else if (field == 'set-img') {
              $('img', $el).attr('src', value);

            } else {
              $el.attr(field, value);
            }

            {{-- console.log($el, field, value); --}}
          });
        },

        closeModal() {
          this.show = false;
          $('body').removeClass('overflow-y-hidden');
          K.removeURLParam('modal');
        }
      }));
    });
  </script>
@endPushOnce
