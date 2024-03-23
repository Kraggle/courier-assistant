@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the bulk modal --}}
<x-modal class="p-4 md:p-6"
  name="bulk-expense">
  {{-- modal content --}}
  <form x-ref="form"
    class="{{ $gap }} flex flex-col"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('expense.bulk') }}">
    @csrf

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div x-ref="title"
        class="font-extralight uppercase tracking-wider">
        {{ Msg::bulkTitle(__('expenses')) }}
      </div>
    </div>

    <div>
      <p class="text-sm">
        {{ Msg::bulkHelper(['date', 'cost', 'describe'], ['type']) }}
      </p>
      <p class="text-sm">
        {{ __('If :type is missing, it will just be set as :work.', ['type' => '`type`', 'work' => '`work`']) }}
      </p>
    </div>

    {{-- file input --}}
    @define($key = 'file')
    <x-form.wrap :key="$key"
      :value="__('CSV')"
      left="left-[5.75rem]">

      <x-form.file x-ref="file"
        class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        accept="text/csv" />

    </x-form.wrap>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        {{ __('cancel') }}
      </x-button.light>

      <x-button.dark x-ref="submit">
        {{ __('upload') }}
      </x-button.dark>
    </div>

  </form>
</x-modal>
