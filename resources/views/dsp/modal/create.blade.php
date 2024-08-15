{{-- add dsp modal --}}
<x-modal class="flex flex-col p-4 md:p-6"
  name="create-dsp"
  help-root>

  {{-- modal header --}}
  <x-modal.header title="Create your DSP"
    :help="true" />

  {{-- modal content --}}
  <form class="flex flex-col"
    method="POST"
    action="{{ route('dsp.create') }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-3">
      {{-- name --}}
      @define($key = 'name')
      <x-form.wrap class="col-span-2"
        value="Delivery service providers name"
        :key="$key"
        help="The name of your Delivery Service Provider. Please be accurate with this as anyone else searching for your DSP will want to find it easily. Also if there is any profanity found the DSP will be removed and you will loose any data added.">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}" />

      </x-form.wrap>

      {{-- identifier --}}
      @define($key = 'identifier')
      <x-form.wrap value="Amazon identifier"
        :key="$key"
        help="Amazons identifier for your Delivery Service Provider, you can ask your OSM for this if you don't know it. It's another way for other drivers to find the correct DSP.">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="e.g. CLBT, LWTS, ROKL, GAMD" />

      </x-form.wrap>
    </div>

    <div class="grid grid-cols-2">
      {{-- in hand --}}
      @define($key = 'in_hand')
      <x-form.wrap value="weeks in hand"
        :key="$key"
        help="How many weeks after you have worked before you are paid, this is usually 2 week, but your DSP may be different.">

        <x-form.text id="{{ $key }}"
          name="{{ $key }}"
          type="number"
          ref="{{ $key }}" />

      </x-form.wrap>

      {{-- pay day --}}
      @define($key = 'pay_day')
      <x-form.wrap value="pay day"
        :key="$key"
        help="The day of the week that your DSP pays it's drivers. This can be different for different DSPs.">

        <x-form.select id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          minresultsforsearch=999>

          <x-slot:options>
            @foreach (Lists::weekDays() as $key => $type)
              <option value="{{ $key }}">{{ $type }}</option>
            @endforeach
          </x-slot>
        </x-form.select>

      </x-form.wrap>
    </div>

    {{-- submit --}}
    <div class="flex justify-end">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader>
        <x-slot:text
          ref="submit">create</x-slot>
        <x-slot:loader></x-slot>
      </x-button.loader>
    </div>

  </form>
</x-modal>
