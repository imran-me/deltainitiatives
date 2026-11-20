{{-- Delta Expert Network application form. Posts to services.expert. --}}
@php
    $fields = ['Agriculture', 'Design', 'Energy', 'Finance & ERP', 'Policy', 'Public health', 'Software', 'Water engineering', 'Other'];
@endphp
<form method="POST" action="{{ route('services.expert') }}" class="form form-card" id="expert-form" data-guard novalidate>
    @csrf
    <x-honeypot />
    <x-form.errors-summary />
    <div class="row2">
        <x-form.input name="name" label="Full name" required autocomplete="name" maxlength="120" />
        <x-form.input name="email" type="email" label="Email" required autocomplete="email" maxlength="255" />
    </div>
    <div class="row2">
        <x-form.select name="field" label="Field" :options="array_combine($fields, $fields)" placeholder="Choose…" required />
        <x-form.input name="institution" label="Institution or company" autocomplete="organization" maxlength="160" />
    </div>
    <x-form.textarea name="expertise" label="Your expertise in two or three lines" required rows="4" maxlength="2000" />
    <x-form.input name="profile_url" type="url" label="LinkedIn or profile link" maxlength="500" />
    <x-turnstile />
    <button class="btn pri" type="submit" data-loading-text="Sending…">Apply to join</button>
</form>
