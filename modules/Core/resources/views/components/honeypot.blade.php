{{-- Honeypot + time-trap. Humans never see or fill these fields. --}}
<div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
    <label for="hp_company_website_{{ $id = \Illuminate\Support\Str::random(6) }}">Leave this field empty</label>
    <input type="text" name="hp_company_website" id="hp_company_website_{{ $id }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="hp_rendered" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) time()) }}">
@error('hp_rendered')
    <p class="text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
@enderror
@error('hp_company_website')
    <p class="text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
@enderror
