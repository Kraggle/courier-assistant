{{-- edit week modal --}}
<x-modal class="py-4 md:py-6"
  name="edit-week"
  help-root>

  {{-- modal content --}}
  <form class="flex flex-col"
    ref="form"
    method="POST"
    action="{{ route('route.week') }}">
    @csrf
    @method('PATCH')

    {{-- modal header --}}
    <x-modal.header>
      <span>update week</span>
      <span ref="title"></span>
    </x-modal.header>

    <table class="w-full table-auto whitespace-nowrap text-sm sm:text-base">
      <x-table.thead>

        @foreach (['date', 'invoice miles', 'bonus', 'Claiming VAT?'] as $header)
          <x-table.th class="{{ $loop->last ? 'w-[1%] pl-2' : ($loop->first ? 'pr-2' : 'px-2') }} whitespace-nowrap text-xs sm:text-sm">
            {{ $header }}
          </x-table.th>
        @endforeach

      </x-table.thead>

      <tbody ref="body">

        <x-table.tr ref="row">

          <x-table.td>
            <input name="id[]"
              type="hidden"
              ref="id">
            <div ref="date"></div>
          </x-table.td>

          <x-table.td>
            {{-- invoice mileage --}}
            @define($key = 'invoice_mileage')
            <x-form.wrap :key="$key">

              <x-form.text name="{{ $key }}[]"
                type="number"
                ref="{{ $key }}"
                :value="old($key)" />

            </x-form.wrap>
          </x-table.td>

          <x-table.td>@define($key = 'bonus')
            {{-- bonus --}}
            <x-form.wrap :key="$key">

              <x-form.text-prefix name="{{ $key }}[]"
                type="number"
                ref="{{ $key }}"
                :value="old($key)"
                step="0.01">

                <x-icon class="fas fa-sterling-sign text-gray-400" />

              </x-form.text-prefix>

            </x-form.wrap>
          </x-table.td>

          <x-table.td>
            @define($key = 'vat')
            {{-- vat --}}
            <x-form.wrap :key="$key">

              <x-form.toggle class="block w-20 sm:w-28"
                name="{{ $key }}[]"
                ref="{{ $key }}"
                :value="old($key)" />

            </x-form.wrap>
          </x-table.td>

        </x-table.tr>
      </tbody>

    </table>

    {{-- submit --}}
    <div class="flex justify-end px-4 md:px-6">
      <x-button.light close-modal>
        cancel
      </x-button.light>

      <x-button.loader>
        <x-slot:text
          ref="submit">save</x-slot>
        <x-slot:loader></x-slot>
      </x-button.loader>
    </div>

  </form>
</x-modal>

@pushOnce('scripts')
  <script type="module">
    $(() => {
      const $mile = $('[ref=start_mileage]');
      $('#type_route').on('change', function() {
        $mile.closest('div[key]')[$(this).val() === 'poc' ? 'removeClass' : 'addClass']('required');
      }).trigger('change');
    });
  </script>
@endpushOnce
