{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-expense">
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('expense.bulk') }}">
    @csrf
    @method('patch')

    {{-- modal header --}}
    <x-modal.header :title="Msg::bulkTitle('expenses')"
      :help="true" />

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'cost', 'describe'], ['type']) }}
      </p>
      <p class="text-sm">
        'If `type` is missing, it will just be set as `work`.
      </p>
    </div>

    {{-- file input --}}
    @define($key = 'file')
    <x-form.wrap value="CSV"
      :key="$key"
      left="left-[5.75rem]">

      <x-form.file class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="file"
        accept="text/csv" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.dark ref="submit">
        upload
      </x-button.dark>
    </div>

  </form>
</x-modal>
