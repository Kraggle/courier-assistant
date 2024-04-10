@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="export-modal"
  maxWidth="sm">

  {{-- modal content --}}
  <div class="{{ $gap }} flex flex-col">

    {{-- modal header --}}
    <x-modal.header title="none" />

    <p class="text-sm"
      ref="question"></p>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('close') }}
      </x-button.light>

      <form id="exportForm"
        ref="form"
        method="POST"
        action=""
        filename="">
        @csrf

        <x-button.dark x-on:click="$dispatch('close')"
          id="exportBtn"
          color="no-loader bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800">
          {{ __('export') }}
        </x-button.dark>

      </form>
    </div>

  </div>

  <script type="module">
    {{-- $(() => {
      $('#exportBtn').on('click', e => {
        loading();
        cookieWatch();
      });

      const cookieWatch = () => {
        const cookieInterval = setInterval(() => {
          const di = Cookies.get('download-initiated');
          if (di == $('#exportForm').attr('filename')) {
            clearInterval(cookieInterval);
            loading();
            Cookies.remove('download-initiated');
          }
        }, 500);
      }
    }); --}}
  </script>
</x-modal>
