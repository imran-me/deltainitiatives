{{-- Request-a-proposal form for the Services pages. Posts to services.proposal. --}}
@php
    $services = \Modules\Publishing\Models\ServiceRequest::SERVICES;
    $budgets = ['' => 'Not sure yet', 'Under ৳5 lakh' => 'Under ৳5 lakh', '৳5–20 lakh' => '৳5–20 lakh', '৳20 lakh–1 crore' => '৳20 lakh–1 crore', 'Above ৳1 crore' => 'Above ৳1 crore'];
@endphp
<form method="POST" action="{{ route('services.proposal') }}" class="form form-card" id="proposal-form" data-guard novalidate>
    @csrf
    <x-honeypot />
    <h3>Request a proposal</h3>
    <x-flash />
    <x-form.errors-summary />
    <div class="row2">
        <x-form.input name="name" label="Your name" required autocomplete="name" maxlength="120" />
        <x-form.input name="organization" label="Organisation" required autocomplete="organization" maxlength="160" />
    </div>
    <div class="row2">
        <x-form.input name="email" type="email" label="Work email" required autocomplete="email" maxlength="255" />
        <x-form.select name="service" label="Service" :options="$services" :value="$service ?? 'tech-support'" required />
    </div>
    <div class="row2">
        <x-form.input name="phone" type="tel" label="Phone" autocomplete="tel" maxlength="40" />
        <x-form.select name="budget" label="Budget range" :options="$budgets" />
    </div>
    <x-form.textarea name="message" label="What do you need?" required rows="5" maxlength="3000" />
    <x-turnstile />
    <button class="btn pri" type="submit" data-loading-text="Sending…">Send request</button>
</form>
