{{-- the add modal --}}
<x-modal class="p-4 md:p-6"
  name="add-refuel"
  help-root>
  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('refuel.add', $vehicle->id) ?? '' }}">
    @csrf
    @method('PUT')

    {{-- modal header --}}
    <x-modal.header :title="Msg::add('refuel')"
      :help="true" />

    <div class="hidden"
      help-message>
      <div class="flex gap-2">
        <x-icon class="far fa-info-circle text-base text-blue-400" />
        <div class="flex flex-col">
          <p class="text-xs text-gray-500">
            For this to work you have to completely fill your tank on each refuel, have the first reading from a full tank and complete this on each refuel.
          </p>

          <p class="text-xs text-gray-500">
            Honestly, it is worth it to see the extra money you earn on fuel each day though.
          </p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
      <div class="flex flex-col">
        {{-- date picker --}}
        @define($key = 'date')
        <x-form.wrap class="required"
          value="date"
          :key="$key"
          help="The date of the refuel. This will be used to both sort the refuels and to find the most accurate cost per mile on your routes.">

          <x-form.date class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}" />

        </x-form.wrap>

        {{-- mileage input --}}
        @define($key = 'mileage')
        <x-form.wrap class="required"
          value="odometer reading"
          :key="$key"
          help="The reading from your odometer when you fill the tank. You have to completely fill the tank to get an accurate reading of how many miles since the last refill it cost to fill up.">

          <x-form.text class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            placeholder="Please enter reading..." />

        </x-form.wrap>

        {{-- cost input --}}
        @define($key = 'cost')
        <x-form.wrap class="required"
          value="cost to refuel"
          :key="$key"
          help="The amount the refuel cost. This has help to show the exact cost per mile since the last refuel.">

          <x-form.text-prefix class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            type="number"
            ref="{{ $key }}"
            placeholder="Please enter value..."
            step="0.01">

            <x-icon class="fas fa-sterling-sign text-gray-400" />

          </x-form.text-prefix>

        </x-form.wrap>

        {{-- first input --}}
        <div>
          <x-form.check id="first"
            name="first"
            ref="first"
            label="Have you skipped any refuels to this one OR is this the first?" />

          <x-form.error class="mt-2"
            :messages="$errors->get('cost')" />
        </div>
      </div>

      {{-- image --}}
      @define($key = 'image')
      <x-form.wrap value="receipt"
        ref="image-wrap"
        :key="$key"
        help="Add a photo of your receipt. This will be kept available to you for tax purposes.">

        <x-form.image class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}" />

      </x-form.wrap>
    </div>

    <div class="flex justify-between">
      <x-button.danger class="no-loader"
        open-modal="destroy-refuel"
        ref="destroy">
        delete
      </x-button.danger>

      <span></span>

      {{-- submit --}}
      <div class="flex justify-end">
        <x-button.light close-modal>
          cancel
        </x-button.light>

        <x-button.dark ref="submit">
          add
        </x-button.dark>
      </div>
    </div>

  </form>
</x-modal>
