<?php

/*
|--------------------------------------------------------------------------
| Role-based access control
|--------------------------------------------------------------------------
| Every admin account has exactly one role. Each role grants a set of
| permissions; "*" grants everything. Permissions are registered as Gates,
| so they work with @can, $user->can(), ->can() route middleware and policies.
*/

return [
    'permissions' => [
        'dashboard.view' => 'View the admin dashboard',
        'ideas.view' => 'Read idea submissions and download their files',
        'ideas.score' => 'Score ideas against the six-criterion rubric',
        'ideas.status' => 'Move ideas through the review workflow',
        'ideas.export' => 'Export idea submissions (CSV / Excel)',
        'ideas.notify' => 'Receive an e-mail alert for every new idea',
        'content.manage' => 'Edit website content (pages, team, partners, focus areas, stats, legal pages)',
        'programmes.manage' => 'Manage programmes, events and event registrations',
        'inbox.manage' => 'Read and manage contact messages, partner inquiries and Join Us applications',
        'newsletter.manage' => 'Manage newsletter subscribers',
        'users.manage' => 'Create admin accounts, assign roles, deactivate accounts',
        'audit.view' => 'View the audit log',
        'settings.manage' => 'Change site settings (contact details, social links, brochure, analytics)',
        'backups.manage' => 'Create and download database and file backups',
    ],

    'roles' => [
        'super_admin' => [
            'label' => 'Super Admin',
            'description' => 'Full access, including accounts, settings and backups.',
            'permissions' => ['*'],
        ],
        'admin' => [
            'label' => 'Administrator',
            'description' => 'Runs the site day-to-day: ideas, content, programmes and inboxes.',
            'permissions' => [
                'dashboard.view', 'ideas.view', 'ideas.score', 'ideas.status', 'ideas.export', 'ideas.notify',
                'content.manage', 'programmes.manage', 'inbox.manage', 'newsletter.manage', 'audit.view', 'settings.manage',
            ],
        ],
        'reviewer' => [
            'label' => 'Reviewer',
            'description' => 'Reads and scores ideas and moves them through the review workflow.',
            'permissions' => ['dashboard.view', 'ideas.view', 'ideas.score', 'ideas.status'],
        ],
        'editor' => [
            'label' => 'Content Editor',
            'description' => 'Edits website content, programmes and events. No access to ideas.',
            'permissions' => ['dashboard.view', 'content.manage', 'programmes.manage'],
        ],
        'community' => [
            'label' => 'Community & Partnerships',
            'description' => 'Handles messages, partner inquiries, Join Us applications, registrations and the newsletter.',
            'permissions' => ['dashboard.view', 'programmes.manage', 'inbox.manage', 'newsletter.manage'],
        ],
    ],
];
