@php
  $title = [
      'news' => 'News Articles',
      'tips' => 'Tips & Tricks',
  ][$type];
@endphp

<x-layout.app :title="$title">
  <x-section.one>
    <x-section.title>
      <x-slot:title>
        {{ $title }}
      </x-slot>
    </x-section.title>

    <div class="mb-6">

      <x-form.text-prefix class="w-full"
        id="searchInput"
        value="{{ $search ?? '' }}"
        placeholder="What are you looking for?">

        <x-icon class="fal fa-search"></x-icon>

      </x-form.text-prefix>

    </div>

    <x-loader class="my-24"
      id="loader"
      size="6"
      color="bg-gray-300"></x-loader>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2"
      id="postWrap"></div>

    <div class="flex justify-between pt-6 text-gray-700"
      id="pagination"
      data-page="{{ $page }}"></div>
  </x-section.one>
</x-layout.app>

<script type="module">
  $(() => {
    const $wrap = $('#postWrap'),
      $search = $('#searchInput'),
      $page = $('#pagination'),
      total = () => parseInt($('[data-count]').data('count') || 0),
      page = () => parseInt($page.data('page') || 1),
      pages = () => Math.ceil(total() / 10),
      itemsLower = () => ((page() - 1) * 10) + 1,
      itemsUpper = () => {
        let x = itemsLower() + 9;
        return total() < x ? total() : x;
      };

    // item grabber
    const populatePage = (resetPage = false) => {
      resetPage && $page.data('page', 1);
      const s = $search.val(),
        p = $page.data('page');

      if (s) K.addURLParam('search', s);
      else K.removeURLParam('search');

      K.addURLParam('page', p);

      $.ajax({
        url: "{{ route('posts') }}",
        method: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          search: s,
          type: "{{ $type }}",
          page: p,
        },
        success: function(data) {
          $wrap.html(data);
          $('#loader').hide();
          buildPagination();
          refreshAll();
        }
      });
    }

    // build the page number links
    const buildPagination = () => {
      const btnClass = 'cursor-pointer border border-gray-300 rounded-md px-2 min-w-7 text-center leading-7 shadow-sm';

      $page.html('').append($('<div />', {
        text: `${itemsLower()} to ${itemsUpper()} of ${total()}`
      }));

      const $p = $('<div />', {
        class: 'flex gap-2'
      }).appendTo($page);

      if (page() > 1) {
        $p.append($('<i />', {
          class: `fal fa-angles-left ${btnClass}`,
          page: 1,
          title: 'First'
        }));
        $p.append($('<i />', {
          class: `fal fa-angle-left ${btnClass}`,
          page: page() - 1,
          title: 'Previous'
        }));
      }

      let start = page() - 3,
        end = page() + 3;
      start = start < 1 ? 1 : start;
      end = end > pages() ? pages() : end;

      if (pages() <= 7) {
        start = 1;
        end = pages();
      } else if (page() <= 3) {
        start = 1;
        end = 7;
      } else if (end >= pages()) {
        start = pages() - 6;
        end = pages();
      }

      for (let i = start; i <= end; i++) {
        $p.append($('<span />', {
          class: `${page() === i? 'border-indigo-300 text-indigo-500 active' : 'border-gray-300'}  cursor-pointer border rounded-md px-2 min-w-7 text-center leading-7 shadow-sm`,
          page: i,
          title: `Page ${i}`,
          text: i
        }));
      }

      if (page() != pages()) {
        $p.append($('<i />', {
          class: `fal fa-angle-right ${btnClass}`,
          page: page() + 1,
          title: 'Next'
        }));
        $p.append($('<i />', {
          class: `fal fa-angles-right ${btnClass}`,
          page: pages(),
          title: 'Last'
        }));
      }
    }

    // update items on search input
    const timer = timed();
    $search.on('input', () => {
      timer.run(() => {
        populatePage(true)
      }, 300);
    });

    // change page on pagination click
    $page.on('click', '[page]:not(.active)', (e) => {
      $page.data('page', parseInt($(e.target).attr('page')));
      populatePage();
    });

    // initial page generation
    {{-- populatePage(); --}}

    const time = timed();
    time.run(() => {
      populatePage();
    }, 100);
  });
</script>
