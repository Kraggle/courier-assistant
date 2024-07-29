@foreach (K::media() as $media)
  <media-selector data-id="{{ $media->id }}"
    data-path="{{ $media->path }}"
    data-src="{{ $media->url }}"
    has-tooltip
    ref="selector">
    <img class="h-36 w-auto cursor-pointer rounded-md border border-gray-300 transition-all [.active_&]:ring-2 [.active_&]:ring-indigo-500 [.active_&]:ring-offset-2"
      src="{{ $media->url }}" />

    <tooltip class="text-xs text-white">
      <div class="grid grid-cols-[auto_1fr] gap-x-1 gap-y-0">
        @if ($media->caption)
          <span class="block text-right font-bold text-gray-300">Caption:</span>
          <span>{{ $media->caption }}</span>
        @endif

        <span class="block text-right font-bold text-gray-300">Size:</span>
        <span place="size"></span>

        <span class="block text-right font-bold text-gray-300">Type:</span>
        <span class="uppercase">{{ $media->type }}</span>
      </div>
    </tooltip>

  </media-selector>
@endForeach
