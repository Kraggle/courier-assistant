<div {{ $attributes->merge(['class' => '[&>svg]:h-full']) }}>
  {!! K::getContents(Vite::asset('resources/images/logo.svg')) !!}
</div>
