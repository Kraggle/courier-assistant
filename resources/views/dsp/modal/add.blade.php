@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add dsp modal --}}
<x-modal class="{{ $gap }} flex flex-col p-4 md:p-6"
  name="add-dsp"
  help-root>

  {{-- modal header --}}
  <div class="flex items-center justify-between">
    <div x-ref="title"
      class="font-extralight uppercase tracking-wider">
      {{ __('Select your Delivery Service Provider') }}
    </div>

    <x-icon class="far fa-circle-question cursor-pointer"
      data-help-trigger="false"
      :title="__('Toggle help text!')" />

  </div>

  {{-- modal content --}}
  <form x-ref="form"
    class="{{ $gap }} flex flex-col"
    method="POST"
    action="{{ route('dsp.attach') }}">
    @csrf

    {{-- dsp_id --}}
    @define($key = 'dsp_id')
    <x-form.wrap x-ref="dsp_wrap"
      :key="$key"
      :value="__('DSP')"
      :help="__('Search here for the name of the Delivery Service Provider you work for. It\'s important you select the correct one, as they differ in their pay rates. The one you select, if already added, will most likely already have rates set to date.')">

      <x-form.select x-ref="{{ $key }}"
        id="{{ $key }}"
        name="{{ $key }}"
        :placeholder="__('Select your Delivery Service Provider')">

        <x-slot:elements>

          @foreach (\App\Models\DSP::all()->sortBy('name') as $dsp)
            <div class="flex items-center justify-between"
              value="{{ $dsp->id }}">
              <span>{{ $dsp->name }}</span>
              <span class="text-base font-semibold">{{ $dsp->identifier }}</span>
            </div>
          @endforeach

        </x-slot>
      </x-form.select>

    </x-form.wrap>

    {{-- date --}}
    @define($key = 'date')
    <x-form.wrap :key="$key"
      :value="__('start date')"
      :help="__('The date that you started working for this DSP. As you can add many, you will only be able to change anything on the latest one you have selected.')">

      <x-form.date x-ref="{{ $key }}"
        class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}" />

    </x-form.wrap>

    <div class="{{ $gap }} flex justify-between">
      <x-button.danger x-ref="destroy"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'destroy-dsp')"
        class="no-loader">
        {{ __('delete') }}
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-ref="close"
          x-on:click="$dispatch('close')"
          class="hidden">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark x-ref="submit">
          {{ __('select') }}
        </x-button.dark>
      </div>
    </div>

  </form>

  <div x-ref="add-section"
    class="{{ $gap }} flex flex-col">
    {{-- split header --}}
    <div class="flex items-center justify-between">
      <div class="font-extralight uppercase tracking-wider">
        {{ __('Or... add it here') }}
      </div>
    </div>

    <form class="{{ $gap }} flex flex-col"
      method="POST"
      action="{{ route('dsp.create') }}">
      @csrf

      <div class="{{ $gap }} grid grid-cols-1 md:grid-cols-2">
        {{-- name --}}
        @define($key = 'name')
        <x-form.wrap :key="$key"
          :value="__('DSPs Name')"
          :help="__('The name of your Delivery Service Provider. Please be accurate with this as anyone else searching for your DSP will want to find it easily. Also if there is any profanity found the DSP will be removed and you will loose any data added.')">

          <x-form.text x-ref="{{ $key }}"
            class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

        {{-- identifier --}}
        @define($key = 'identifier')
        <x-form.wrap :key="$key"
          :value="__('Amazon identifier')"
          :help="__('Amazons identifier for your Delivery Service Provider, you can ask your OSM for this if you don\'t know it. It\'s another way for other drivers to find the correct DSP.')">

          <x-form.text x-ref="{{ $key }}"
            class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            placeholder="{{ __('e.g. CLBT, LWTS, ROKL, GAMD') }}" />

        </x-form.wrap>
      </div>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <x-button.light x-on:click="$dispatch('close')">
          {{ __('cancel') }}
        </x-button.light>

        <x-button.dark>
          {{ __('create') }}
        </x-button.dark>
      </div>

    </form>
  </div>
</x-modal>
