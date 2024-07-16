{{-- add dsp modal --}}
<x-modal class="flex flex-col p-4 md:p-6"
  name="add-dsp"
  help-root>

  {{-- modal header --}}
  <x-modal.header title="Select your DSP"
    :help="true" />

  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('dsp.attach') }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-[1fr_auto]"
      ref="dsp_wrap">
      {{-- dsp_id --}}
      @define($key = 'dsp_id')
      <x-form.wrap value="delivery service provider"
        :key="$key"
        help="Search here for the name of the Delivery Service Provider you work for. It's important you select the correct one, as they differ in their pay rates. The one you select, if already added, will most likely already have rates set to date.">

        <x-form.select id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="Select your Delivery Service Provider">

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

      <x-button.dark x-on:click.prevent="$dispatch('open-modal', 'create-dsp')"
        class="no-loader mt-2.5"
        id="createDSP"
        data-modal="{{ json_encode([
            'name.value' => old('name', ''),
            'identifier.value' => old('identifier', ''),
            'in_hand.value' => old('in_hand', 2),
            'pay_day.value' => old('pay_day', 4),
        ]) }}"
        color="bg-violet-700 hover:bg-violet-600 focus:bg-violet-600 active:bg-violet-800">
        create dsp
      </x-button.dark>
    </div>

    {{-- date --}}
    @define($key = 'date')
    <x-form.wrap value="start date"
      :key="$key"
      help="The date that you started working for this DSP. As you can add many, you will only be able to change anything on the latest one you have selected.">

      <x-form.date class="block w-full"
        id="{{ $key }}"
        name="{{ $key }}"
        ref="{{ $key }}" />

    </x-form.wrap>

    <div class="flex justify-between">
      <x-button.danger class="no-loader"
        open-modal="destroy-dsp"
        ref="destroy">
        delete
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="flex justify-end">
        <x-button.light close-modal
          ref="close">
          cancel
        </x-button.light>

        <x-button.dark ref="submit">
          select
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
