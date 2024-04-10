@php
  $space = 3;
  $gap = ['gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-5', 'gap-6', 'gap-7'][$space];
@endphp

@auth
  <div x-data=""
    x-on:click="$dispatch('open-modal', 'keep-alive')"
    class="hidden"
    btn="aliveTrigger"></div>

  {{-- the keep alive modal --}}
  <x-modal class="p-4 md:p-6"
    name="keep-alive"
    maxWidth="sm">

    <div class="{{ $gap }} flex flex-col">
      <div class="{{ $gap }} flex">
        <div class="{{ $gap }} flex flex-grow flex-col">

          {{-- modal header --}}
          <x-modal.header :title="__('Session ending')" />

          {{-- modal content --}}
          <p class="text-sm">
            {{ __('Do you want to keep the session alive?') }}
          </p>
        </div>

        <span class="text-5xl font-bold"
          id="aliveCounter">60</span>

      </div>

      {{-- submit --}}
      <div class="{{ $gap }} flex justify-end">
        <form id="logoutForm"
          method="POST"
          action="{{ route('logout') }}">
          @csrf
          <x-button.light type="submit">
            {{ __('no') }}
          </x-button.light>
        </form>

        <x-button.dark x-on:click="$dispatch('close')"
          id="keepAlive">
          {{ __('yes') }}
        </x-button.dark>
      </div>

    </div>

    <div x-show="show && window.aliveTimer()"></div>

  </x-modal>

  @push('scripts')
    <script type="module">
      let aliveInterval;

      $(() => {
        $('#keepAlive').on('click', () => {
          $.getJSON("{{ route('keep.alive') }}", () => {
            clearInterval(aliveInterval);
            aliveTrigger();
          });
        });
      });

      const aliveTrigger = () => {
        $('#aliveCounter').text(60);
        setTimeout(() => {
          $('[btn=aliveTrigger]').trigger('click');
        }, {{ (config('session.lifetime') - 1.5) * 60 * 1000 }}); // session lifetime - 1.5 mins
      };

      window.aliveTimer = () => {
        let c = 60;
        aliveInterval = setInterval(() => {
          $('#aliveCounter').text(c--);
          c <= 0 && $('#logoutForm').trigger('submit');
        }, 1000);
      };

      aliveTrigger();
    </script>
  @endpush
@endauth
