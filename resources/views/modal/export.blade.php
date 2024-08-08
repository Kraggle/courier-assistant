{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="export-modal"
  maxWidth="sm">

  {{-- modal content --}}
  <div class="flex flex-col">

    {{-- modal header --}}
    <x-modal.header title="none" />

    <p class="text-sm"
      ref="question"></p>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        close
      </x-button.light>

      <form id="exportForm"
        ref="form"
        method="POST"
        action=""
        filename="">
        @csrf

        <x-button.dark class="no-loader bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800"
          id="exportBtn"
          close-modal>
          export
        </x-button.dark>

      </form>
    </div>

  </div>

  {{-- <script type="module">
    $(() => {
      $('#exportBtn').on('click', e => {
        //loading();
        cookieWatch();
      });

      const cookieWatch = () => {
        const cookieInterval = setInterval(() => {
          const di = Cookies.get('download-initiated');
          if (di == $('#exportForm').attr('filename')) {
            clearInterval(cookieInterval);
            //loading();
            Cookies.remove('download-initiated');
          }
        }, 500);
      }
    });
  </script> --}}
</x-modal>
