{{-- <x-ideaportal::status-badge :status="$idea->status" /> --}}
@props(['status'])
<x-admin::badge :tone="config('ideaportal.statuses.'.$status.'.tone', 'slate')" :class="$attributes->get('class')">{{ config('ideaportal.statuses.'.$status.'.label', $status) }}</x-admin::badge>
