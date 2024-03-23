<tr {{ $attributes->merge(['class' => 'odd:bg-white even:bg-gray-50 border-b last-of-type:border-0']) }}>
  {{ $slot }}
</tr>
