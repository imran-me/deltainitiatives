{{-- Request-a-proposal form for the Services pages. Posts to services.proposal. --}}
@php
    $bn = app()->getLocale() === 'bn';
    $t = fn ($en, $b) => $bn ? $b : $en;
    $services = \Modules\Publishing\Models\ServiceRequest::SERVICES;
    $budgets = ['' => $t('Not sure yet', 'এখনও নিশ্চিত নই'), 'Under ৳5 lakh' => 'Under ৳5 lakh', '৳5–20 lakh' => '৳5–20 lakh', '৳20 lakh–1 crore' => '৳20 lakh–1 crore', 'Above ৳1 crore' => 'Above ৳1 crore'];
@endphp
<form method="POST" action="{{ route('services.proposal') }}" class="form form-card" id="proposal-form" data-guard novalidate>
    @csrf
    <x-honeypot />
    <h3>{{ $t("Request a proposal", "প্রস্তাবনা চান") }}</h3>
    <x-flash />
    <x-form.errors-summary />
    <div class="row2">
        <x-form.input name="name" :label="$t('Your name', 'আপনার নাম')" required autocomplete="name" maxlength="120" />
        <x-form.input name="organization" :label="$t('Organisation', 'প্রতিষ্ঠান')" required autocomplete="organization" maxlength="160" />
    </div>
    <div class="row2">
        <x-form.input name="email" type="email" :label="$t('Work email', 'অফিসের ইমেইল')" required autocomplete="email" maxlength="255" />
        <x-form.select name="service" :label="$t('Service', 'সেবা')" :options="$services" :value="$service ?? 'tech-support'" required />
    </div>
    <div class="row2">
        <x-form.input name="phone" type="tel" :label="$t('Phone', 'ফোন')" autocomplete="tel" maxlength="40" />
        <x-form.select name="budget" :label="$t('Budget range', 'বাজেটের পরিসর')" :options="$budgets" />
    </div>
    <x-form.textarea name="message" :label="$t('What do you need?', 'আপনার কী দরকার?')" :value="$message ?? null" required rows="5" maxlength="3000" />
    <x-turnstile />
    <button class="btn pri" type="submit" data-loading-text="{{ $t('Sending…', 'পাঠানো হচ্ছে…') }}">{{ $t("Send request", "অনুরোধ পাঠান") }}</button>
</form>
