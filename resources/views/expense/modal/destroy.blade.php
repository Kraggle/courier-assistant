@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- the destroy modal --}}
<x-modal class="p-4 md:p-6"
  name="destroy-expense">
  <form class="{{ $gap }} flex flex-col"
    ref="form"
    method="POST"
    action="">
    @csrf
    @method('delete')

    {{-- modal header --}}
    <x-modal.header :title="Msg::delete('expense')" />

    {{-- modal content --}}
    <p class="text-sm"
      ref="message">
      {{ Msg::sureDelete('expense') }}
    </p>

    {{-- choice --}}
    @define($key = 'choice')
    <x-form.wrap :key="$key"
      ref="choice-wrap"
      :value="''"
      :help="''">

      <x-form.select id="{{ $key }}_destroy"
        name="{{ $key }}"
        ref="{{ $key }}"
        minresultsforsearch=999>

        <x-slot:elements>
          <div value="this">Just this recurrence</div>
          <div value="next">This and all future recurrences</div>
          <div value="all">All recurrences</div>
        </x-slot>

      </x-form.select>

    </x-form.wrap>

    {{-- submit --}}
    <div class="{{ $gap }} flex justify-end">
      <x-button.light x-on:click="$dispatch('close')">
        no
      </x-button.light>

      <x-button.dark class=""
        ref="submit">
        yes
      </x-button.dark>
    </div>

  </form>
</x-modal>
