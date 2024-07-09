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

@pushOnce('scripts')
  <script type="module">
    let doneOld = false;

    document.addEventListener('alpine:init', () => {
      Alpine.data('modal', () => ({
        show: @js($show),

        updateValues(e) {
          // go to login if opening modal and not authorised
          $.get("{{ route('get-status') }}", data => {
            if (!data.status)
              window.location.href = "{{ route('login') }}";
          });

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
              $el = $root.find('[ref=' + element + ']');

            if (!$el) return;

            if (field == 'changes') { // specific to the changes modal

              $el.html('');
              const $tr = $('<tr></tr>', {
                  class: 'odd:bg-white even:bg-gray-50 border-b last-of-type:border-0'
                }),
                $td = $('<td></td>', {
                  class: 'px-2 py-2 first-of-type:pl-4 md:first-of-type:pl-6 last-of-type:pr-4 md:last-of-type:pr-6'
                }),
                $div = $('<div />', {
                  class: 'whitespace-nowrap'
                }),
                dateReplace = 'T00:00:00.000000Z';

              K.each(value, (i, v) => {
                const $row = $tr.clone(),
                  $a = $('<div />'),
                  $o = $a.clone(),
                  $n = $a.clone();

                K.each(v.properties.attributes, (attr, val) => {
                  $a.append($div.clone().text(attr));
                  $o.append($div.clone().text((v.properties.old[attr] || '').replace(dateReplace, '')));
                  $n.append($div.clone().text((val || '').replace(dateReplace, '')));
                });

                $row.append(
                  $td.clone().text(v.date),
                  $td.clone().text(v.user),
                  $td.clone().html($a),
                  $td.clone().html($o),
                  $td.clone().html($n)
                );

                $el.append($row);
              });
            } else if (element == 'week') { // specific to the week modal

              const $body = $root.find('[ref=body]');
              let $row = $root.find('[ref=row]').first().detach();
              $root.find('[ref=row]').remove();

              K.each(value.routes, (i, r) => {
                $row = $row.clone();
                $body.append($row);
                $('[ref=id]', $row).val(r.id);
                $('[ref=invoice_mileage]', $row).val(r.invoice_mileage);
                $('[ref=bonus]', $row).val(r.bonus);
                $('[ref=vat]', $row).prop('checked', r.vat);
                $('[ref=date]', $row).text(dayjs(r.date).format('ddd, MMM D'));
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
                $el.siblings('.input-bro').val(dayjs(value).format('YYYY-MM-DD')).trigger('change');
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

          });

          doneOld && $('.text-red-600', this.$root).remove();
          doneOld = true;
        },

        closeModal() {
          this.show = false;
          $('body').removeClass('overflow-y-hidden');
          K.removeURLParam('modal');
        }
      }));
    });

    document.addEventListener('alpine:initialized', () => {
      const modal = K.urlParam('modal'),
        success = {{ session()->has('success') ? 0 : 1 }};
      if (success && modal)
        $(`#${modal}`).trigger('click');
      else K.removeURLParam('modal');
    });
  </script>
@endPushOnce
