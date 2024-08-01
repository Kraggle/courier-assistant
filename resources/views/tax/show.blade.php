@php
  $years = $user->taxYears();

  $gap = 'gap-4 md:gap-6';
@endphp

<x-layout.app title="tax view">
  <x-section.one class="px-0 md:px-0">
    @define($tab = $year ?? 0)
    <x-tab.container :active="$tab">
      <x-slot:tabs>
        @foreach ($years as $y)
          <x-tab.link :href="route('tax.show', $y->year)"
            :tab="$y->year">
            {{ $y->tab }}
          </x-tab.link>
        @endforeach
      </x-slot>

      <x-slot:button>
        <form id="claimForm"
          action="{{ route('tax.edit', $tax->id) }}"
          method="POST">
          @csrf
          @method('put')

          <x-button.dark class="bg-green-700 hover:bg-green-600 focus:bg-green-600 active:bg-green-800">Regenerate</x-button.dark>
        </form>
      </x-slot>

      <x-slot:content>
        <div class="flex flex-col">
          <div class="grid grid-cols-1 px-4 sm:grid-cols-2 md:px-6 lg:grid-cols-3">
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
              {{-- <x-form.wrap value="Claimable as Expense">
                <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                  value="{{ K::formatCurrency($tax->properties->miles->claimable) }}"
                  readonly />
              </x-form.wrap> --}}

            </x-form.section>

            {{-- fuel --}}
            <x-form.section label="fuel">
              <div class="grid grid-cols-2">
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
              </div>

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
              <div class="grid grid-cols-2">
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
              {{-- <x-form.wrap value="Time Spent Driving">
                <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                  value="{{ K::secondsToHuman($tax->properties->work->total) }}"
                  readonly />
              </x-form.wrap> --}}

            </x-form.section>

          </div>

          <div class="grid grid-cols-1 px-4 md:px-6 lg:grid-cols-2">

            {{-- income --}}
            <x-form.section label="income">
              <div class="grid grid-cols-3">
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

              <div class="grid grid-cols-3">
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
              <div class="grid grid-cols-2">
                {{-- vehicle & travel --}}
                <x-form.wrap value="vehicle & travel">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->vehicle) }}"
                    readonly />
                </x-form.wrap>

                {{-- maintenance --}}
                {{-- <x-form.wrap value="maintenance">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->maintenance) }}"
                    readonly />
                </x-form.wrap> --}}

                {{-- accountancy --}}
                <x-form.wrap value="accountancy">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->professional) }}"
                    readonly />
                </x-form.wrap>
              </div>

              <div class="grid grid-cols-2">
                {{-- interest --}}
                <x-form.wrap value="interest">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->interest) }}"
                    readonly />
                </x-form.wrap>

                {{-- office --}}
                <x-form.wrap value="office">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->office) }}"
                    readonly />
                </x-form.wrap>
              </div>

              <div class="grid grid-cols-3">
                {{-- other --}}
                <x-form.wrap value="other">
                  <x-form.text class="block w-full text-center text-base font-bold md:text-xl"
                    value="{{ K::formatCurrency($tax->properties->expense->work) }}"
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
      </x-slot>

    </x-tab.container>

  </x-section.one>
</x-layout.app>
