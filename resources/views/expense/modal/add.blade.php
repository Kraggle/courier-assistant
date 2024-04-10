@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-expense"
  help-root>
  {{-- modal content --}}
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('expense.add') }}">
    @csrf
    @method('put')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add(__('expense'))"
      :help="true" />

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      <div class="{{ $gap }} flex flex-col">
        {{-- date --}}
        @define($key = 'date')
        <x-form.wrap :key="$key"
          :value="__('date')"
          :help="__('The date of the expense.')">

          <x-form.date class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}" />

        </x-form.wrap>

        {{-- type --}}
        @define($key = 'type')
        <x-form.wrap class="required"
          :key="$key"
          :value="__('expense type')"
          :help="__('This is used to categorize the expense for tax purposes.')">

          <x-form.select id="{{ $key }}_expense"
            name="{{ $key }}"
            ref="{{ $key }}"
            :placeholder="__('Select the type...')"
            minresultsforsearch=999>

            <x-slot:elements>
              @foreach (Lists::expenseTypes() as $key => $type)
                <div class="align-center flex items-center gap-1"
                  value="{{ $key }}">
                  <span class="whitespace-nowrap">{{ Str::title($key) }} | </span>
                  <span class="font-gray-500 text-xs leading-none">{{ $type }}</span>
                </div>
              @endforeach
            </x-slot>

          </x-form.select>

        </x-form.wrap>

        {{-- describe --}}
        @define($key = 'describe')
        <x-form.wrap class="required"
          :key="$key"
          :value="__('description')"
          :help="__('What the expense was, only helpful for your records.')">

          <x-form.text class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}"
            :placeholder="__('What was this for?')" />

        </x-form.wrap>

        @define($key = 'cost')
        {{-- cost --}}
        <x-form.wrap class="required"
          :key="$key"
          :value="__('cost')"
          :help="__('The cost of the expense. Will help with expense calculation for taxes.')">

          <x-form.text-prefix class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            :placeholder="__('Please enter value...')"
            step="0.01">

            <x-icon class="fas fa-sterling-sign text-gray-400" />

          </x-form.text-prefix>

        </x-form.wrap>

      </div>

      {{-- image --}}
      @define($key = 'image')
      <x-form.wrap ref="image-wrap"
        :key="$key"
        :value="__('receipt')"
        :help="__('Add a photo of your receipt. This will be kept available to you for tax purposes.')">

        <x-form.image class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}" />

      </x-form.wrap>
    </div>

    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-expense')"
        class="no-loader"
        ref="destroy">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-on:click="$dispatch('close')">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark ref="submit">
          {{ __('add') }}
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
