@props(['for', 'id' => null])
@error($for)
    <p @if($id) id="{{ $id }}" @endif {{ $attributes->merge(['class' => 'mt-2 flex items-start gap-1.5 text-sm font-medium text-red-600']) }} role="alert">
        <x-icon name="circle-alert" class="mt-0.5 size-4" />
        <span>{{ $message }}</span>
    </p>
@enderror
