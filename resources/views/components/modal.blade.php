@props(['show' => false, 'maxWidth' => '2xl'])

<modal-box class="pointer-events-none fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">

  <blank class="fixed inset-0 transform opacity-0 transition-all">
    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
  </blank>

  <modal {!! $attributes->twMerge(['class' => K::maxWidth($maxWidth) . ' translate-y-48 opacity-0 scale-50 transform rounded-lg bg-white shadow-xl transition-all mx-auto w-full max-h-[calc(100%-2rem)] overflow-y-auto']) !!}>
    {{ $slot }}
  </modal>

</modal-box>

@pushOnce('scripts')
  <script type="module">
    let readErrors = false;

    // modal app
    const Modal = {
      opened: [],

      open($btn) {
        // go to login if opening modal and not authorised
        $.get("{{ route('get-status') }}", data => {
          if (!data.status)
            window.location.href = "{{ route('login') }}";
        });

        const name = $btn.attr('open-modal'),
          id = $btn.attr('id'),
          data = $btn.data('modal');

        // ensure the modal opens with reload
        id && K.addURLParam('modal', id);

        this.opened.push(name);
        this.toggle(name);

        readErrors && $('.text-red-600', this.$root).remove();
        readErrors = true;

        if (!data) return;

        const $root = $(`modal[name=${name}]`)

        K.each(data, (key, value) => {
          const element = key.split('.')[0],
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

      },

      close(name) {
        this.opened = this.opened.filter(e => e != name);
        this.toggle(name);
        K.removeURLParam('modal');
      },

      escape() {
        this.opened.length && this.close(this.opened.pop());
      },

      toggle(name) {
        const $modal = $(`modal[name=${name}]`),
          $box = $modal.closest('modal-box'),
          $blank = $('blank', $box);

        $box.toggleClass('pointer-events-none pointer-event-auto');
        $blank.toggleClass('opacity-0 opacity-100');
        $modal.toggleClass('opacity-0 opacity-100 translate-y-48 scale-50 translate-y-0 scale-100');
      }
    };

    $(() => {
      // open modal button click
      $('body').on('click', '[open-modal]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        Modal.open($(this));
      });

      // close modal button click
      $('[close-modal]').on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        let name = $(this).attr('close-modal');
        if (name == 'close-modal')
          name = $(this).closest('modal').attr('name');
        Modal.close(name);
      });

      // modal blank click to close
      $('blank').on('click', function() {
        Modal.close($(this).siblings('modal').attr('name'));
      });

      // on esc key pressed
      $('body').on('keydown', function(e) {
        if (e.originalEvent.code == 'Escape' && !$(e.target).is('input'))
          Modal.escape();
      });

      (() => {
        const modal = K.urlParam('modal'),
          success = {{ session()->has('success') ? 0 : 1 }};
        if (success && modal)
          $(`#${modal}`).trigger('click');
        else K.removeURLParam('modal');
      })
      ();
    });
  </script>
@endPushOnce
