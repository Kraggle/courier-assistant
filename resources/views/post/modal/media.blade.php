{{-- add media modal --}}
<x-modal class="px-0 py-4 md:py-6"
  name="media-picker"
  maxWidth="sm:max-w-[calc(100vw_-_1.5rem)]">
  {{-- modal content --}}
  <div class="flex flex-col">

    {{-- modal header --}}
    <x-modal.header title="Media Manager" />

    @define($tab = request()->get('tab') ?? 0)
    <x-tab.container :active="$tab">

      <x-slot:tabs>
        <x-tab.button tab="0">
          Library
        </x-tab.button>

        <x-tab.button tab="1">
          Upload
        </x-tab.button>
      </x-slot:tabs>

      <x-slot:content>
        <x-tab.content tab="0">
          <x-loader class="my-24"
            id="loader"
            size="6"
            color="bg-gray-300"></x-loader>

          <div class="flex flex-wrap justify-center gap-6 px-4 md:px-6"
            id="imagePicker">

          </div>
        </x-tab.content>

        <x-tab.content class="px-4 md:px-6"
          tab="1">

          <form class="grid grid-cols-[1fr_1fr_auto_auto]"
            id="uploadForm"
            image-root
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('media.upload') }}">

            <img class="col-span-4 max-h-96 max-w-full justify-self-center rounded-md border border-gray-300"
              id="uploadSrc"
              data-no-image="@noImage"
              src="@noImage"
              image-src>

            @define($key = 'caption')
            <x-form.wrap value="image caption"
              :key="$key">

              <x-form.text class="w-full"
                :id="$key"
                :name="$key"
                placeholder="" />

            </x-form.wrap>

            @define($key = 'tag')
            <x-form.wrap value="Search tags"
              :key="$key">

              <x-form.text class="w-full"
                :id="$key"
                :name="$key"
                placeholder="" />

            </x-form.wrap>

            <x-form.wrap value=" ">

              <x-button.light class="relative h-[41.33px]"
                size="md">
                <input class="absolute inset-0 opacity-0"
                  name="image"
                  type="file"
                  tabindex="0"
                  image-upload
                  accept="image/*" />
                browse
              </x-button.light>

            </x-form.wrap>

            <x-form.wrap value=" ">

              <x-button.dark class="no-loader relative h-[41.33px]"
                size="md">
                upload
              </x-button.dark>

            </x-form.wrap>

          </form>
        </x-tab.content>
      </x-slot>
    </x-tab.container>

    <div class="flex justify-between px-4 md:px-6">
      <span></span>

      {{-- submit --}}
      <div class="flex justify-end">

        <x-button.dark class="bg-red-600 hover:bg-red-500 focus:bg-red-700"
          delete-media
          ref="delete">
          delete
        </x-button.dark>

        <x-button.dark class="bg-green-600 hover:bg-green-500 focus:bg-green-700"
          copy-url
          ref="copy"
          close-modal>
          copy
        </x-button.dark>

        <x-button.dark class="bg-blue-600 hover:bg-blue-500 focus:bg-blue-700"
          select-img
          ref="select"
          close-modal>
          select
        </x-button.dark>

        <x-button.light close-modal>
          close
        </x-button.light>
      </div>
    </div>

  </div>
</x-modal>

@push('scripts')
  <script type="module">
    $(() => {
      $('[image-root]').on('change', 'input[type=file]', function() {
        const file = this.files[0];
        if (!file) return;

        const $ir = $(this).closest('[image-root]'),
          reader = new FileReader();
        if (file.type == "application/pdf") {
          $('[image-src]', $ir).attr('src', "{{ Vite::asset('resources/images/no-pdf.svg') }}");
        } else {
          reader.onload = function(e) {
            $('[image-src]', $ir).attr('src', e.target.result);
          };
          reader.readAsDataURL(file);
        }

        let name = $(this).attr('name');
        if (!name) {
          const other = $(`[image-${$(this).is('[image-upload]') ? 'camera' : 'upload'}]`, $ir);
          name = other.attr('name');
          $(this).attr('name', name);
          other.removeAttr('name');
        }
      });

      const $img = $('#uploadSrc');
      $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('_method', 'put');
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
          url: $(this).attr('action'),
          type: $(this).attr('method'),
          data: formData,
          success: data => {
            if (data.success) {
              $img.attr('src', $img.data('noImage'));
              $('input', this).val('');
              $('[tab=0]').trigger('click').trigger('tab-change');
              Notify.message(data.success, 'success');
            } else if (data.error) {
              Notify.message(data.error, 'error');
            }
          },
          cache: false,
          contentType: false,
          processData: false,
        });
      });

      $('tab-content').on('click', 'media-selector:not(.active)', function() {
        const data = $(this).data();
        $('media-selector').removeClass('active');
        $(this).addClass('active');
        $('[copy-url]').removeClass('hidden').data('src', data.src);
        $('[delete-media]').removeClass('hidden').data('id', data.id);
        $('[select-img][input-name]').removeClass('hidden').data(data);
      });

      $('[copy-url]').on('click', function() {
        const src = $(this).data('src');
        navigator.clipboard.writeText(src).then(() => {
          Notify.message('Copied URL to clipboard', 'info');
        }, () => {
          Notify.message('Failed to copy URL to clipboard', 'error');
        });
      });

      $('[select-img]').on('click', function() {
        const data = $(this).data(),
          $el = $(`input[name=${$(this).attr('input-name')}]`);
        $el.val(data.path);
        $el.siblings('img').attr('src', data.src).addClass('object-cover');
      });

      $('[delete-media]').on('click', function() {
        const data = $(this).data();

        $.ajax({
          url: '{{ route('media.delete') }}',
          type: 'POST',
          data: {
            _method: 'DELETE',
            _token: "{{ csrf_token() }}",
            id: data.id,
          },
          success: data => {
            if (data.success) {
              Notify.message(data.success, 'success');
              refreshImages();
            } else if (data.error) {
              Notify.message(data.error, 'error');
            }
          }
        });
      });

      $('a[tab=0]').on('tab-change', refreshImages);
      refreshImages();
    });

    function refreshImages() {
      $('#imagePicker').html('');
      $('#loader').show();

      $.ajax({
        url: "{{ route('media.get') }}",
        method: "POST",
        data: {
          _token: "{{ csrf_token() }}"
        },
        success: function(data) {
          $('#imagePicker').html(data);
          $('#loader').hide();

          $('#imagePicker img').each(function() {
            const $img = $(this),
              $p = $img.parent();
            $img.on('load', function() {
              $('[place=size]', $p).text(`${this.naturalWidth}x${this.naturalHeight}`);
              addTooltip($p);
            });
          });

          refreshAll();
        }
      });

    }
  </script>
@endpush
