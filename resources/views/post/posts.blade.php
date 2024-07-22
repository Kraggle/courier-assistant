@php
  $skip = [];
@endphp

<page-data class="hidden"
  data-count="{{ $count }}"></page-data>

@foreach ($posts as $post)
  @php
    $bannerUrl = $post->bannerUrl($skip);
    if (preg_match('/banner-0{0,1}(\d+).jpg/', $bannerUrl, $matches)) {
        $skip[] = (int) $matches[1];
    }
  @endphp
  <a class="relative overflow-hidden rounded-md border shadow-md"
    href="{{ route('post', $post->slug) }}">

    <img class="h-auto w-full"
      src="{{ $bannerUrl }}"
      alt="banner">

    <span class="absolute left-2 top-2 block max-w-[calc(100%_-_1rem)] overflow-hidden text-ellipsis whitespace-nowrap rounded-sm bg-gray-50 px-2 py-1 text-lg font-normal sm:text-xl">
      {{ Str::title($post->title) }}
    </span>

    <div class="absolute bottom-2 right-2 flex gap-1 text-sm font-bold uppercase">
      @foreach ($post->categories as $category)
        <span class="rounded-sm bg-gray-50 px-1">
          {{ $category->name }}
        </span>
      @endforeach
    </div>

  </a>
@endforeach
