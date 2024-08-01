<x-layout.app :title="Str::title($post->title)">
  <x-section.one class="p-0 md:p-0"
    maxWidth="3xl">
    <div class="relative">
      @auth
        @if (K::user()->isAdmin())
          <a class="absolute right-0 top-0 rounded-bl-md bg-white px-2 py-1"
            href="{{ route('post.editor', $post->id) }}">
            <x-icon class="fas fa-pencil text-orange-600"
              title="Edit post"></x-icon>
          </a>
        @endif
      @endauth

      <img class="border-b"
        src="{{ $post->bannerUrl() }}" />

      <h1 class="absolute bottom-2 left-2 rounded-sm bg-white px-2 py-1 font-serif text-xl md:text-2xl">
        {{ Str::title($post->title) }}
      </h1>
    </div>

    <article class="content-article p-4 md:p-8">
      {!! $post->content !!}
    </article>

  </x-section.one>
</x-layout.app>

@include('post.modal.image')
