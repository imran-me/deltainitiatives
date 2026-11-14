<?php

/*
| Admin → Settings form definition. Keys are read on the site with setting('key').
| Defaults live in modules/Core/config/config.php (settings_defaults).
*/

return [
    'settings' => [
        'Identity' => [
            'site_name' => ['label' => 'Site name', 'type' => 'text'],
            'site_tagline' => ['label' => 'Tagline', 'type' => 'text'],
            'site_descriptor' => ['label' => 'Descriptor', 'type' => 'text', 'help' => 'Shown beside the logo and in the footer.'],
            'site_description' => ['label' => 'Default meta description', 'type' => 'textarea', 'help' => 'Used for search engines and link previews when a page has no description. ~155 characters.'],
        ],
        'Contact' => [
            'contact_email' => ['label' => 'General e-mail (hello@)', 'type' => 'email'],
            'ideas_email' => ['label' => 'Ideas e-mail (ideas@)', 'type' => 'email'],
            'partners_email' => ['label' => 'Partnerships e-mail (partners@)', 'type' => 'email'],
            'contact_phone' => ['label' => 'Phone', 'type' => 'text', 'help' => 'Leave empty to hide.'],
            'contact_address' => ['label' => 'Address', 'type' => 'textarea', 'help' => 'University or office address.'],
            'office_hours' => ['label' => 'Office hours', 'type' => 'text'],
        ],
        'Social' => [
            'social_linkedin' => ['label' => 'LinkedIn URL', 'type' => 'url'],
            'social_facebook' => ['label' => 'Facebook URL', 'type' => 'url'],
            'social_x' => ['label' => 'X (Twitter) URL', 'type' => 'url'],
            'social_youtube' => ['label' => 'YouTube URL', 'type' => 'url'],
            'social_instagram' => ['label' => 'Instagram URL', 'type' => 'url'],
        ],
        'Idea Portal' => [
            'ideas_open' => ['label' => 'Accepting idea submissions', 'type' => 'boolean', 'help' => 'Turn off to pause the Idea Portal form (a notice is shown instead).'],
            'ideas_notify_emails' => ['label' => 'Extra alert addresses', 'type' => 'text', 'help' => 'Comma-separated. Added to every active account with the "Receive new idea alerts" permission.'],
            'agreement_version' => ['label' => 'Contributor Agreement version', 'type' => 'text', 'help' => 'Recorded with every submission. Bump it whenever the agreement text changes.'],
        ],
        'Partnerships' => [
            'brochure_path' => ['label' => 'Partnership brochure (PDF)', 'type' => 'file', 'help' => 'Offered on Partner with Us. Max 20 MB.'],
        ],
        'Analytics & SEO' => [
            'cf_analytics_token' => ['label' => 'Cloudflare Web Analytics token', 'type' => 'text', 'help' => 'Cookie-free analytics. Find it in Cloudflare → Analytics & Logs → Web Analytics.'],
            'google_site_verification' => ['label' => 'Google Search Console verification code', 'type' => 'text', 'help' => 'Only the content value of the meta tag.'],
        ],
    ],

    'backups' => [
        'keep' => (int) env('BACKUP_KEEP', 14),
        'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH'),
    ],
];
