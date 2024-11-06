<x-layout.app title="score import"
  :center="true">

  <x-section.one class="self-center"
    maxWidth="md"
    help-root>

    <x-modal.header title="Data Import"
      :help="true" />

    <form class="flex flex-col">
      {{-- file input --}}
      @define($key = 'file')
      <x-form.wrap value="Spreadsheet"
        :key="$key"
        left="left-[5.75rem]">

        <x-form.file id="{{ $key }}"
          name="{{ $key }}"
          ref="file"
          accept="text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />

      </x-form.wrap>

      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap value="sheet type"
        :key="$key"
        help="test">

        <x-form.select id="{{ $key }}"
          name="{{ $key }}"
          ref="{{ $key }}"
          placeholder="Select the data import type..."
          minresultsforsearch=999>

          <x-slot:elements>
            @php
              $types = [
                  (object) [
                      'label' => 'Scoresheet',
                      'value' => 'score',
                      'help' => ' Driver scores from scoresheet.',
                  ],
                  (object) [
                      'label' => 'Mentor Scores',
                      'value' => 'mentor',
                      'help' => 'Driver report data from VRM.',
                  ],
                  (object) [
                      'label' => 'Mentor Shift',
                      'value' => 'shift',
                      'help' => 'Shift report data from VRM.',
                  ],
                  (object) [
                      'label' => 'Concessions',
                      'value' => 'dnr',
                      'help' => 'DNR data exported from cortex.',
                  ],
                  (object) [
                      'label' => 'Photo on Delivery',
                      'value' => 'pod',
                      'help' => 'POD data exported from cortex.',
                  ],
              ];
            @endphp
            @foreach ($types as $type)
              <div class="flex items-center"
                value="{{ $type->value }}">
                <span class="font-semibold">{{ $type->label }}</span>
                <span class="text-xs">{{ $type->help }}</span>
              </div>
            @endforeach

          </x-slot>
        </x-form.select>

      </x-form.wrap>
    </form>

  </x-section.one>
</x-layout.app>

@vite(['resources/js/stripe.js'])
