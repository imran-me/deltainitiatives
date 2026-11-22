{{-- Bangla Delta Expert Network application form. Posts to services.expert (same handler as the English form). --}}
@php
    $fields = ['Agriculture' => 'কৃষি', 'Design' => 'ডিজাইন', 'Energy' => 'জ্বালানি', 'Finance & ERP' => 'অর্থ ও ইআরপি', 'Policy' => 'নীতি', 'Public health' => 'জনস্বাস্থ্য', 'Software' => 'সফটওয়্যার', 'Water engineering' => 'পানি প্রকৌশল', 'Other' => 'অন্যান্য'];
@endphp
<form method="POST" action="{{ route('services.expert') }}" class="form form-card" id="expert-form" data-guard novalidate>
    @csrf
    <x-honeypot />
    <x-form.errors-summary />
    <div class="row2">
        <x-form.input name="name" label="পুরো নাম" required autocomplete="name" maxlength="120" />
        <x-form.input name="email" type="email" label="ইমেইল" required autocomplete="email" maxlength="255" />
    </div>
    <div class="row2">
        <x-form.select name="field" label="ক্ষেত্র" :options="$fields" placeholder="বেছে নিন…" required />
        <x-form.input name="institution" label="প্রতিষ্ঠান বা কোম্পানি" autocomplete="organization" maxlength="160" />
    </div>
    <x-form.textarea name="expertise" label="আপনার দক্ষতা দুই-তিন লাইনে" required rows="4" maxlength="2000" />
    <x-form.input name="profile_url" type="url" label="লিংকডইন বা প্রোফাইল লিংক" maxlength="500" />
    <x-turnstile />
    <button class="btn pri" type="submit" data-loading-text="পাঠানো হচ্ছে…">যোগ দিতে আবেদন করুন</button>
</form>
