@props(['variant'=>'primary'])
<button {{ $attributes->merge(['class' => $variant==='primary' ? 'btn-primary' : 'btn-secondary']) }}>
  {{ $slot }}
</button>
