<div class="bg-pattern-light fixed inset-0 z-[9999] grid -translate-x-full items-center justify-items-center bg-gray-700 transition-all duration-700 ease-in-out"
  id="pageDoor">

  <div class="relative aspect-square w-1/2 max-w-[200px]">

    <x-svg.icon class="absolute inset-0 animate-[spin_3s_linear_infinite] fill-current text-gray-400" />
    <x-svg.icon class="absolute inset-0 animate-[spin_2.8s_linear_infinite] fill-current text-gray-500" />

    <div class="absolute -bottom-20 w-full text-center text-2xl font-bold capitalize tracking-widest text-gray-400 sm:text-3xl">loading...</div>
  </div>

  <script type="module">
    const loading = () => {
      $('#pageDoor').toggleClass('-translate-x-full');
    };
    window.loading = loading;

    $(() => {

      $('form [type="submit"]:not(.no-loader)').on('click', loading);
      $('body').on('click', 'a[href][href!=""]:not(.no-loader)', function(e) {
        e.preventDefault();
        loading();
        setTimeout(() => (window.location.href = $(this).attr('href')), 950);
      });
    });

    /*
    $(window).on('load', function() {
      // $('#pageDoor').removeClass('-translate-x-full');

      loading();
    });
    */
  </script>
</div>
