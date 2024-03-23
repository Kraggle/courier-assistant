@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the add modal --}}
<x-modal class="p-4 md:p-6"
  name="add-refuel"
  help-root>
  {{-- modal content --}}
  <form x-ref="form"
    class="{{ $gap }} flex flex-col"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('refuel.add', $vehicle->id) ?? '' }}">
    @csrf

    {{-- modal header --}}
    <div class="flex items-center justify-between">
      <div x-ref="title"
        class="font-extralight uppercase tracking-wider">
        {{ Msg::add(__('refuel')) }}
      </div>

      <x-icon class="far fa-circle-question cursor-pointer"
        data-help-trigger="false"
        :title="__('Toggle help text!')" />

    </div>

    <div class="hidden"
      help-message>
      <div class="flex gap-2">
        <x-icon class="far fa-info-circle text-base text-blue-400" />
        <div class="{{ $gap }} flex flex-col">
          <p class="text-xs text-gray-500">
            {{ __('For this to work you have to completely fill your tank on each refuel, have the first reading from a full tank and complete this on each refuel.') }}
          </p>

          <p class="text-xs text-gray-500">
            {{ __('Honestly, it is worth it to see the extra money you earn on fuel each day though.') }}
          </p>
        </div>
      </div>
    </div>

    <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
      <div class="{{ $gap }} flex flex-col">
        {{-- date picker --}}
        @define($key = 'date')
        <x-form.wrap class="required"
          :key="$key"
          :value="__('date')"
          :help="__('The date of the refuel. This will be used to both sort the refuels and to find the most accurate cost per mile on your routes.')">

          <x-form.date x-ref="{{ $key }}"
            class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- mileage input --}}
        @define($key = 'mileage')
        <x-form.wrap class="required"
          :key="$key"
          :value="__('odometer reading')"
          :help="__('The reading from your odometer when you fill the tank. You have to completely fill the tank to get an accurate reading of how many miles since the last refill it cost to fill up.')">

          <x-form.text x-ref="{{ $key }}"
            class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            :placeholder="__('Please enter reading...')" />

        </x-form.wrap>

        {{-- cost input --}}
        @define($key = 'cost')
        <x-form.wrap class="required"
          :key="$key"
          :value="__('cost to refuel')"
          :help="__('The amount the refuel cost. This has help to show the exact cost per mile since the last refuel.')">

          <x-form.text-prefix x-ref="{{ $key }}"
            class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            :placeholder="__('Please enter value...')"
            step="0.01">

            <x-icon class="fas fa-sterling-sign text-gray-400" />

          </x-form.text-prefix>

        </x-form.wrap>

        {{-- first input --}}
        <div>
          <x-form.check x-ref="first"
            id="first"
            name="first"
            label="{{ __('Have you skipped any refuels to this one OR is this the first?') }}" />

          <x-form.error class="mt-2"
            :messages="$errors->get('cost')" />
        </div>
      </div>

      {{-- image --}}
      @define($key = 'image')
      <x-form.wrap x-ref="image-wrap"
        :key="$key"
        :value="__('receipt')"
        :help="__('Add a photo of your receipt. This will be kept available to you for tax purposes.')">

        <x-form.image class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}" />

      </x-form.wrap>
    </div>

    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-ref="destroy"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-refuel')"
        class="no-loader">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-on:click="$dispatch('close')">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark x-ref="submit">
          {{ __('add') }}
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
