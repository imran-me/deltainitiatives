{{-- Responsive table wrapper. Put <thead>/<tbody> in the slot. --}}
<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="min-w-full divide-y divide-navy-100 text-sm [&_th]:px-4 [&_th]:py-3 [&_th]:text-left [&_th]:text-xs [&_th]:font-semibold [&_th]:tracking-wide [&_th]:text-navy-500 [&_th]:uppercase [&_th]:whitespace-nowrap [&_td]:px-4 [&_td]:py-3.5 [&_td]:align-middle [&_tbody_tr]:transition [&_tbody_tr:hover]:bg-navy-50/60">
        {{ $slot }}
    </table>
</div>
