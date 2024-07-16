{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="show-receipt"
  maxWidth="sm">

  {{-- modal content --}}
  <div class="flex flex-col">

    {{-- modal header --}}
    <x-modal.header title="receipt" />

    <img src="@noImage"
      ref="image">

    {{-- submit --}}
    <div class="flex justify-end">
      <form ref="form"
        method="POST"
        action="">
        @csrf

        <input name="path"
          type="hidden"
          ref="path" />

        <x-button.dark color="no-loader bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800">
          download
        </x-button.dark>

      </form>

      <x-button.light close-modal>
        close
      </x-button.light>
    </div>

  </div>
</x-modal>
