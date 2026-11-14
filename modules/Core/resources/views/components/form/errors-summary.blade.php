{{-- Accessible summary shown at the top of a form after a failed submit. --}}
@if($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-red-50 p-4 ring-1 ring-red-200']) }} role="alert" tabindex="-1" id="form-errors">
        <div class="flex items-start gap-3">
            <x-icon name="circle-alert" class="mt-0.5 size-5 text-red-600" />
            <div>
                <p class="text-sm font-semibold text-red-900">{{ trans_choice('core::ui.forms.errors_title', $errors->count(), ['count' => $errors->count()]) }}</p>
                <ul class="mt-2 list-disc space-y-1 pl-4 text-sm text-red-800">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
