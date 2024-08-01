<image-wrap class="pointer-events-none fixed inset-0">

  <blank class="fixed inset-0 transform opacity-0 transition-all">
    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
  </blank>

  <padder class="pointer-events-none absolute p-4">
    <img class="h-full w-full object-contain" />
  </padder>
</image-wrap>

<script type="module">
  $(() => {
    const nudge = (px, rem) => `calc(${px}px + ${rem}rem)`;

    const $wrap = $('image-wrap'),
      $blank = $('blank', $wrap),
      $padder = $('padder', $wrap),
      $img = $('img', $wrap),
      max = 1000;

    $('figure img').on('click', function(e) {
      const sH = $(window).height(),
        sW = $(window).width(),
        w = $(this).width(),
        h = $(this).height(),
        isP = w < h,
        sT = $(window).scrollTop();

      $img.attr('src', $(this).attr('src'));
      $padder.css({
        width: nudge(w, 2),
        height: nudge(h, 2),
        left: nudge(e.pageX - e.offsetX, -1),
        top: nudge(e.pageY - e.offsetY - sT, -1),
        opacity: 1
      }).data('start', {
        width: $padder.outerWidth(),
        height: $padder.outerHeight(),
        left: $padder.position().left,
        top: $padder.position().top
      });

      $wrap.addClass('!pointer-events-auto');
      $blank.addClass('!opacity-100');

      const nW = Math.min(max, sW),
        nH = Math.min(max, sH);

      setTimeout(() => {
        $padder.animate({
          left: (sW / 2) - (nW / 2),
          top: (sH / 2) - (nH / 2),
          width: nW,
          height: nH
        }, 500);
      }, 25);
    });

    $blank.on('click', function() {
      console.log($padder.data('start'))
      $padder.animate($padder.data('start'), 500, () => {
        $wrap.removeClass('!pointer-events-auto');
        $blank.removeClass('!opacity-100');
        $padder.css({
          opacity: 0
        });
      });
    });
  });
</script>
