<?php

return [

    /*
    | Locales the site can render. English is the main site; Bangla (/bn) is
    | available for Submit an Idea and Join Us at launch (Build Plan, p.7).
    */
    'locales' => ['en', 'bn'],

    /* Minimum seconds between rendering a public form and submitting it (time-trap). */
    'min_form_seconds' => (int) env('FORM_MIN_SECONDS', 3),

    /* Show a small "Sample" badge on seeded demo content on the public site. */
    'demo_badges' => (bool) env('SHOW_DEMO_BADGES', true),

    'csp' => [
        'enabled' => (bool) env('CSP_ENABLED', true),
    ],

    /*
    | Fallback values for Admin → Settings. Anything saved in the admin panel
    | overrides these. Values marked TEAM are to be supplied by the team.
    */
    'settings_defaults' => [
        'site_name' => 'Delta Initiatives',
        'site_tagline' => 'Engineering Change.',
        'site_descriptor' => 'A Research, Innovation & Policy Institute',
        'site_description' => 'Delta Initiatives is a research, innovation and policy institute that helps students and young researchers in Bangladesh turn bold ideas into tested solutions and evidence-based policy.',
        'contact_email' => 'hello@deltainitiatives.com',
        'ideas_email' => 'ideas@deltainitiatives.com',
        'partners_email' => 'partners@deltainitiatives.com',
        'contact_phone' => null,                 // TEAM
        'contact_address' => 'Dhaka, Bangladesh', // TEAM: full university / office address
        'office_hours' => 'Sunday – Thursday, 10:00 – 17:00 (BST)',
        'social_linkedin' => null,               // TEAM
        'social_facebook' => null,               // TEAM
        'social_x' => null,                      // TEAM
        'social_youtube' => null,                // TEAM
        'social_instagram' => null,              // TEAM
        'ideas_open' => '1',
        'ideas_notify_emails' => 'ideas@deltainitiatives.com',
        'agreement_version' => '1.0',
        'brochure_path' => null,                 // uploaded from Admin → Settings
        'cf_analytics_token' => null,            // Cloudflare Web Analytics beacon token
        'google_site_verification' => null,      // Google Search Console
    ],
];
