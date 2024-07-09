@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

{{-- add route modal --}}
<x-modal class="p-4 md:p-6"
  name="add-expense"
  help-root>
  {{-- modal content --}}
  <form class="{{ $gap }} cols-2 flex flex-col"
    mode="add"
    ref="form"
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('expense.add') }}">
    @csrf
    @method('put')

    <input id="is-repeat"
      type="hidden"
      ref="is-repeat" />

    {{-- modal header --}}
    <x-modal.header :title="Msg::add(__('expense'))"
      :help="true" />

    <div class="{{ $gap }} grid grid-cols-1 [.cols-2_&]:md:grid-cols-2">
      <div class="{{ $gap }} flex flex-col">
        {{-- choice --}}
        @define($key = 'choice')
        <x-form.wrap class="choice-option"
          :key="$key"
          ref="choice-wrap"
          :value="__('Edit recurrence')"
          :help="__('')">

          <x-form.select id="{{ $key }}"
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

        <div class="{{ $gap }} flex">
          {{-- date --}}
          @define($key = 'date')
          <x-form.wrap class="flex-grow"
            :key="$key"
            :value="__('date')"
            :help="__('The date of the expense.')">

            <x-form.date class="block w-full"
              id="{{ $key }}_expense"
              name="{{ $key }}"
              ref="{{ $key }}" />

          </x-form.wrap>

          {{-- date_to --}}
          @define($key = 'date_to')
          <x-form.wrap class="to-date-option flex-grow"
            :key="$key"
            :value="__('to date')"
            :help="__('The date to end the recurring expense.')">

            <x-form.date class="block w-full"
              id="{{ $key }}"
              name="{{ $key }}"
              ref="{{ $key }}" />

          </x-form.wrap>
        </div>

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

        {{-- cost --}}
        @define($key = 'cost')
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

        <div class="{{ $gap }} repeat-options flex flex-col">
          {{-- repeat --}}
          @define($key = 'repeat')
          <x-form.wrap :key="$key"
            :value="__('Repeat?')"
            :help="__('')">

            <x-form.select id="{{ $key }}"
              name="{{ $key }}"
              ref="{{ $key }}"
              minresultsforsearch=999>

              <x-slot:elements>
                <div value="never">Doesn't repeat</div>
                <div value="day">Daily</div>
                <div value="week">Weekly</div>
                <div value="month">Monthly</div>
                <div class="hidden"
                  value="last">Monthly</div>
                <div value="year">Annually</div>
                <div value="custom">Custom...</div>
              </x-slot>

            </x-form.select>

          </x-form.wrap>

          <div class="{{ $gap }} every-options grid grid-cols-2">
            {{-- every x --}}
            @define($key = 'every_x')
            <x-form.wrap :key="$key"
              :value="__('Repeat every')"
              :help="__('')">

              <x-form.text class="block w-full"
                id="{{ $key }}"
                name="{{ $key }}"
                type="number"
                ref="{{ $key }}"
                step="1" />

            </x-form.wrap>

            {{-- every --}}
            @define($key = 'every')
            <x-form.wrap :key="$key"
              :value="__(' ')"
              :help="__('')">

              <x-form.select id="{{ $key }}"
                name="{{ $key }}"
                ref="{{ $key }}"
                minresultsforsearch=999>

                <x-slot:elements>
                  <div value="day">Day</div>
                  <div value="week">Week</div>
                  <div value="month">Month</div>
                  <div value="year">Year</div>
                </x-slot>

              </x-form.select>

            </x-form.wrap>
          </div>

          {{-- month --}}
          @define($key = 'month')
          <x-form.wrap class="month-option"
            :key="$key"
            :value="__('Repeat on')"
            :help="__('')">

            <x-form.select id="{{ $key }}"
              name="{{ $key }}"
              ref="{{ $key }}"
              minresultsforsearch=999>

              <x-slot:elements>
                <div value="day">on date</div>
                <div value="nth">nth week day</div>
                <div value="last">last week day</div>
              </x-slot>

            </x-form.select>

          </x-form.wrap>

        </div>

      </div>

      <div class="{{ $gap }} flex flex-col">

        {{-- image --}}
        @define($key = 'image')
        <x-form.wrap class="image-option"
          ref="image-wrap"
          :key="$key"
          :value="__('receipt')"
          :help="__('Add a photo of your receipt. This will be kept available to you for tax purposes.')">

          <x-form.image class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}" />

        </x-form.wrap>

      </div>
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

@push('scripts')
  <script type="module">
    $(() => {
      const $repeat = $('#repeat'),
        $isRepeat = $('#is-repeat'),
        $every = $('#every'),
        $every_x = $('#every_x'),
        $month = $('#month'),
        $choice = $('#choice'),
        $modal = $repeat.closest('[help-root]'),
        $form = $repeat.closest('form');

      function toggleInputs() {
        let repeat = $repeat.val(),
          isRepeat = $isRepeat.val() == 'true',
          every = $every.val(),
          mode = $form.attr('mode'),
          choice = $choice.val();
        const $rO = $('.repeat-options'),
          $eO = $('.every-options'),
          $mO = $('.month-option'),
          $iO = $('.image-option'),
          $dO = $('.to-date-option');

        $rO.add($eO).add($iO).add($mO).add($dO).removeClass('hidden');

        if (mode == 'edit' && isRepeat) {
          if (choice == 'this') {
            $rO.addClass('hidden');
          } else if (choice != 'this')
            $iO.addClass('hidden');

        } else if (mode == 'add') {
          if (repeat != 'never')
            $iO.addClass('hidden');
        }

        switch (repeat) {
          case 'never':
            $eO.add($mO).add($dO).addClass('hidden');
            break;
          case 'custom':
            switch (every) {
              case 'month':
                break;
              default:
                $mO.addClass('hidden');
            }
            break;
          default:
            $eO.add($mO).addClass('hidden');
        }

        const imgOn = $iO.hasClass('hidden');
        $form[imgOn ? 'removeClass' : 'addClass']('cols-2');
        $modal.removeClass('sm:max-w-2xl, sm:max-w-md');
        $modal.addClass(imgOn ? 'sm:max-w-md' : 'sm:max-w-2xl');
      }
      $repeat.add($every).add($choice).on('change', toggleInputs);
      setTimeout(toggleInputs, 200);

      $repeat.on('change', function() {
        // this updates the custom repeat options when selecting single
        const repeat = $repeat.val();
        repeat != 'custom' && $every_x.val(1);
        switch (repeat) {
          case 'day':
          case 'week':
          case 'year':
            $every.val(repeat).trigger('change');
            break;
          case 'month':
          case 'last':
            $every.val('month').trigger('change');
            $month.val(repeat == 'last' ? 'last' : 'nth').trigger('change');
        }
      });

      $('#date_expense').siblings('.input-bro').on('change', function() {
        const date = dayjs($(this).val());
        let $p = $repeat.parent();

        // this updates the repeat options
        $('div[value=week]', $p).text(`Weekly on ${date.format('dddd')}`);
        $('div[value=month]', $p).text(
          `Monthly on the ${K.ordinalWord(date.weekOfMonth())} ${date.format('dddd')}`
        )[date.weekOfMonth() === 5 ? 'addClass' : 'removeClass']('hidden');
        $('div[value=last]', $p).text(
          `Monthly on the last ${date.format('dddd')}`
        )[date.isInLastWeek() ? 'removeClass' : 'addClass']('hidden');
        $('div[value=year]', $p).text(
          `Annually on ${date.format('MMMM')} ${K.ordinalSuffix(date.format('D'))}`
        );
        $repeat.trigger('change');

        // this updates the custom monthly options
        $p = $month.parent();
        $('div[value=day]', $p).text(
          `Monthly on the ${K.ordinalSuffix(date.format('D'))}`
        );
        $('div[value=nth]', $p).text(
          `Monthly on the ${K.ordinalWord(date.weekOfMonth())} ${date.format('dddd')}`
        )[date.weekOfMonth() === 5 ? 'addClass' : 'removeClass']('hidden');
        $('div[value=last]', $p).text(
          `Monthly on the last ${date.format('dddd')}`
        )[date.isInLastWeek() ? 'removeClass' : 'addClass']('hidden');
        $month.trigger('change');

      }).trigger('change');
    });
  </script>
@endpush
