@php
  $post = $post ?? (object) [];
  $content = old(
      'content',
      $post->content ??
          '<h1>Post Creator</h1>
<p>Let\'s make a new one shall we!</p>',
  );

  $route = $editmode ? route('post.update', $post->id) : route('post.create');
@endphp

@push('styles')
  @vite(['resources/js/editor.js'])
@endpush

<x-layout.app title="post creator">
  <form class="flex flex-col gap-6 md:gap-8"
    method="POST"
    action="{{ $route }}">
    @csrf
    @method('put')

    <textarea class="hidden"
      id="content"
      name="content">{!! $content !!}</textarea>

    <x-section.one class="grid grid-cols-3 gap-3"
      maxWidth="full">
      {{-- title --}}
      @define($key = 'title')
      <x-form.wrap class="required"
        value="Title"
        :key="$key">

        <x-form.text class="block w-full"
          :id="$key"
          :name="$key"
          :value="old($key, $post->title ?? '')"
          :ref="$key" />

      </x-form.wrap>

      {{-- slug --}}
      @define($key = 'slug')
      <x-form.wrap class="required"
        value="Slug"
        :key="$key">

        <x-form.text class="block w-full"
          :id="$key"
          :name="$key"
          :value="old($key, $post->slug ?? '')"
          :ref="$key" />

      </x-form.wrap>

      {{-- type --}}
      @define($key = 'type')
      <x-form.wrap value="Post type"
        :key="$key">

        <x-form.select class="block w-full"
          id="{{ $key }}"
          name="{{ $key }}"
          value="{{ 'tips' }}"
          ref="{{ $key }}"
          minresultsforsearch=999>

          <x-slot:options>
            @define($type = old($key, $post->type ?? 'news'))
            <option value="news"
              {{ K::selected($type, 'news') }}>News Article</option>
            <option value="tips"
              {{ K::selected($type, 'tips') }}>Tips & Tricks</option>
          </x-slot>

        </x-form.select>

      </x-form.wrap>

      <div class="col-span-3 grid grid-cols-[1fr_1fr_auto] gap-3">

        {{-- categories --}}
        @define($key = 'categories')
        <x-form.wrap value="Categories"
          :key="$key">

          <x-form.multiselect class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}[]"
            value="{{ 'tips' }}"
            ref="{{ $key }}"
            :tags="true"
            minresultsforsearch=999>

            <x-slot:options>
              @php
                $old = old($key, []);
                foreach ($old as $old_name) {
                    if ($categories->where('name', $old_name)->isEmpty()) {
                        $categories->push((object) ['name' => $old_name]);
                    }
                }
              @endphp
              @foreach ($categories as $category)
                @php
                  $name = $category->name;
                  $selected = ($editmode && $post->hasCategory($name)) || in_array($name, $old) ? 'selected' : '';
                @endphp
                <option value="{{ $name }}"
                  {{ $selected }}>{{ $name }}</option>
              @endforeach
            </x-slot>

          </x-form.multiselect>

        </x-form.wrap>

        {{-- tags --}}
        @define($key = 'tags')
        <x-form.wrap value="Tags"
          :key="$key">

          <x-form.multiselect class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}[]"
            value="{{ 'tips' }}"
            ref="{{ $key }}"
            :tags="true"
            minresultsforsearch=999>

            <x-slot:options>
              @php
                $old = old($key, []);
                foreach ($old as $old_name) {
                    if ($tags->where('name', $old_name)->isEmpty()) {
                        $tags->push((object) ['name' => $old_name]);
                    }
                }
              @endphp
              @foreach ($tags as $tag)
                @php
                  $name = $tag->name;
                  $selected = ($editmode && $post->hasTag($name)) || in_array($name, $old) ? 'selected' : '';
                @endphp
                <option value="{{ $name }}"
                  {{ $selected }}>{{ $name }}</option>
              @endforeach
            </x-slot>

          </x-form.multiselect>

        </x-form.wrap>

        {{-- is_live --}}
        @define($key = 'is_live')
        <x-form.wrap value="Publish"
          :key="$key">

          <x-form.toggle class="block w-full"
            id="{{ $key }}"
            name="{{ $key }}"
            ref="{{ $key }}"
            :checked="old($key, $post->is_live ?? 0)" />

        </x-form.wrap>

      </div>

      <div class="col-span-3"
        image-root>
        <x-form.wrap class="cursor-pointer"
          id="mediaPicker"
          value="Banner"
          open-modal="media-picker">
          <input name="banner"
            type="hidden">
          <img class="h-80 w-full rounded-md border border-gray-300"
            src="@noImage" />
        </x-form.wrap>
      </div>

      {{-- submit --}}
      <div class="col-span-3 flex justify-end">
        <x-form.wrap value=" ">
          <x-button.dark size="md">Save</x-button.dark>
        </x-form.wrap>
      </div>
    </x-section.one>

    <x-section.two maxWidth="full">

      <x-slot:one
        class="max-h-[calc(100vh_-_280px)] !p-0">
        <div class="relative"
          id="editor"></div>
      </x-slot>

      <x-slot:two
        class="max-h-[calc(100vh_-_280px)] overflow-y-auto">
        <div class="content-article"
          id="viewer">{!! $content !!}</div>
      </x-slot>

    </x-section.two>
  </form>

  @include('post.modal.media')
</x-layout.app>

@push('scripts')
  <script type="module"></script>
@endpush
