@php
  $years = $user->taxYears();

  $gap = 'gap-4 md:gap-6';
@endphp

<x-layout.app title="tax view">
  <x-section.one px="">
    <x-tab.link-wrap class="mb-4 mt-2 px-4 text-2xl font-medium text-gray-900 md:mb-5 md:px-6">
      <x-slot name="tabs">
        @foreach ($years as $y)
          <x-tab.link :href="route('tax.show', $y->year)"
            :active="$y->year == $year">
            {{ $y->tab }}
          </x-tab.link>
        @endforeach
      </x-slot>

      <x-slot name="button">

        <form id="claimForm"
          action="{{ route('tax.edit', $tax->id) }}"
          method="POST">
          @csrf
          @method('put')

          @define($key = 'claim_miles')
          {{-- vat --}}
          <x-form.wrap class="relative -top-4 text-base"
            value="Claim Mileage?"
            :key="$key">

            <x-form.toggle class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              ref="{{ $key }}"
              :checked="$tax->claim_miles" />

          </x-form.wrap>
        </form>
        <script type="module">
          $(() => {
            $('#claimForm [type=checkbox]').on('change', function(e) {
              loading();
              setTimeout(() => {
                $('#claimForm').trigger('submit');
              }, 50);
            });
          });
        </script>
      </x-slot>

    </x-tab.link-wrap>

    <div class="{{ $gap }} flex flex-col">
      <div class="{{ $gap }} grid grid-cols-1 px-4 sm:grid-cols-2 md:px-6 lg:grid-cols-3">
        {{-- miles --}}
        <x-form.section label="miles">
          {{-- worked --}}
          <x-form.wrap value="Driven for Work">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ $tax->properties->miles->driven }}"
              readonly />
          </x-form.wrap>

          {{-- reimbursed --}}
          <x-form.wrap value="Reimbursed by Amazon">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ $tax->properties->miles->reimbursed }}"
              readonly />
          </x-form.wrap>

          {{-- claim value --}}
          <x-form.wrap value="Claimable as Expense">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::formatCurrency($tax->properties->miles->claimable) }}"
              readonly />
          </x-form.wrap>

        </x-form.section>

        {{-- fuel --}}
        <x-form.section label="fuel">
          {{-- reimbursed --}}
          <x-form.wrap value="Paid for Fuel">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::formatCurrency($tax->properties->fuel->paid) }}"
              readonly />
          </x-form.wrap>

          {{-- spend --}}
          <x-form.wrap value="Spent on Fuel">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::formatCurrency($tax->properties->fuel->spent) }}"
              readonly />
          </x-form.wrap>

          {{-- earned --}}
          <x-form.wrap value="Earned on Fuel">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::formatCurrency($tax->properties->fuel->earned) }}"
              readonly />
          </x-form.wrap>
        </x-form.section>

        {{-- workload --}}
        <x-form.section class="col-span-1 sm:col-span-2 lg:col-span-1"
          label="workload">
          <div class="{{ $gap }} grid grid-cols-2">
            {{-- work days --}}
            <x-form.wrap value="Days of Work">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ $tax->properties->work->days }}"
                readonly />
            </x-form.wrap>

            {{-- avg per week --}}
            <x-form.wrap value="Days a Week">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ $tax->properties->work->week }}"
                readonly />
            </x-form.wrap>
          </div>

          {{-- avg hrs a day --}}
          <x-form.wrap value="Hours a Day">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::secondsToHuman($tax->properties->work->hours) }}"
              readonly />
          </x-form.wrap>

          {{-- work hours --}}
          <x-form.wrap value="Time Spent Driving">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::secondsToHuman($tax->properties->work->total) }}"
              readonly />
          </x-form.wrap>

        </x-form.section>

      </div>

      <div class="{{ $gap }} grid grid-cols-1 px-4 md:px-6 lg:grid-cols-2">

        {{-- income --}}
        <x-form.section label="income">
          <div class="{{ $gap }} grid grid-cols-3">
            {{-- total --}}
            <x-form.wrap value="Gross Pay">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->total->all) }}"
                readonly />
            </x-form.wrap>

            {{-- daily --}}
            <x-form.wrap value="Gross Per Day">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->total->day) }}"
                readonly />
            </x-form.wrap>

            {{-- hourly --}}
            <x-form.wrap value="Gross Per Hour">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->total->hour) }}"
                readonly />
            </x-form.wrap>
          </div>

          <div class="{{ $gap }} grid grid-cols-3">
            {{-- actual --}}
            <x-form.wrap value="Net Pay">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->actual->all) }}"
                readonly />
            </x-form.wrap>

            {{-- daily --}}
            <x-form.wrap value="Net Per Day">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->actual->day) }}"
                readonly />
            </x-form.wrap>

            {{-- hourly --}}
            <x-form.wrap value="Net Per Hour">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->income->actual->hour) }}"
                readonly />
            </x-form.wrap>
          </div>

          {{-- bonus --}}
          <x-form.wrap value="Total in Bonuses">
            <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
              value="{{ K::formatCurrency($tax->properties->income->bonus) }}"
              readonly />
          </x-form.wrap>
        </x-form.section>

        {{-- expenses --}}
        <x-form.section label="expenses">
          <div class="{{ $gap }} grid grid-cols-3">
            {{-- work --}}
            <x-form.wrap value="work">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->work) }}"
                readonly />
            </x-form.wrap>

            {{-- vehicle --}}
            <x-form.wrap value="vehicle">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->vehicle) }}"
                readonly />
            </x-form.wrap>

            {{-- maintenance --}}
            <x-form.wrap value="maintenance">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->maintenance) }}"
                readonly />
            </x-form.wrap>
          </div>

          <div class="{{ $gap }} grid grid-cols-3">
            {{-- office --}}
            <x-form.wrap value="office">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->office) }}"
                readonly />
            </x-form.wrap>

            {{-- interest --}}
            <x-form.wrap value="interest">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->interest) }}"
                readonly />
            </x-form.wrap>

            {{-- charges --}}
            <x-form.wrap value="charges">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->charges) }}"
                readonly />
            </x-form.wrap>

            {{-- professional --}}
            <x-form.wrap value="professional">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->professional) }}"
                readonly />
            </x-form.wrap>

            {{-- total --}}
            <x-form.wrap class="col-span-2"
              value="Total Expenses">
              <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                value="{{ K::formatCurrency($tax->properties->expense->total) }}"
                readonly />
            </x-form.wrap>
          </div>
        </x-form.section>

      </div>
    </div>

  </x-section.one>
</x-layout.app>
