<?php

/*
|--------------------------------------------------------------------------
| Idea Portal
|--------------------------------------------------------------------------
| NOTE: the ten questions and the six rubric criteria below are placeholders
| written from the Website Build Plan. Align their wording with the official
| Programme Plan document before launch. Question labels and hints live in
| modules/IdeaPortal/lang/{en,bn}/portal.php under "questions".
*/

return [

    /* Idea IDs look like DI-2026-0001 (sequence restarts every year, Bangladesh time). */
    'id_prefix' => 'DI',

    /* Review workflow, in order. "not_progressed" is the off-ramp at any stage. */
    'statuses' => [
        'received' => ['label' => 'Received', 'tone' => 'blue'],
        'screening' => ['label' => 'Screening', 'tone' => 'amber'],
        'shortlisted' => ['label' => 'Shortlisted', 'tone' => 'teal'],
        'in_delta_labs' => ['label' => 'In Delta Labs', 'tone' => 'green'],
        'not_progressed' => ['label' => 'Not progressed', 'tone' => 'slate'],
    ],

    'workflow' => ['received', 'screening', 'shortlisted', 'in_delta_labs'],

    /* Supporting files: stored privately (storage/app/private/ideas/{code}), never public. */
    'files' => [
        'max' => 3,
        'max_kb' => 10240,
        'mimes' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'],
    ],

    /* The ten Programme Plan questions, in the order they appear on the form. */
    'questions' => [
        'title' => ['type' => 'text', 'max' => 120],
        'summary' => ['type' => 'text', 'max' => 280],
        'problem' => ['type' => 'textarea', 'max' => 2000],
        'evidence' => ['type' => 'textarea', 'max' => 2000],
        'solution' => ['type' => 'textarea', 'max' => 2000],
        'novelty' => ['type' => 'textarea', 'max' => 2000],
        'impact' => ['type' => 'textarea', 'max' => 2000],
        'stage' => ['type' => 'select', 'options' => ['idea', 'research', 'prototype', 'pilot', 'scaling']],
        'support' => ['type' => 'textarea', 'max' => 2000],
        'next_90_days' => ['type' => 'textarea', 'max' => 2000],
    ],

    /* Six-criterion review rubric, each scored 1–5. */
    'rubric' => [
        'problem_significance' => [
            'label' => 'Problem significance',
            'description' => 'How important and well-evidenced is the problem for people in Bangladesh?',
            'weight' => 1,
        ],
        'originality' => [
            'label' => 'Originality',
            'description' => 'Is the approach new, or meaningfully better than what already exists?',
            'weight' => 1,
        ],
        'feasibility' => [
            'label' => 'Feasibility',
            'description' => 'Can this team realistically build and test it with the resources available?',
            'weight' => 1,
        ],
        'impact_potential' => [
            'label' => 'Impact potential',
            'description' => 'How many people could benefit, how deeply — and can it scale?',
            'weight' => 1,
        ],
        'team_capability' => [
            'label' => 'Team capability',
            'description' => 'Does the team have the skills, commitment and insight to deliver?',
            'weight' => 1,
        ],
        'alignment' => [
            'label' => 'Alignment',
            'description' => 'Does it fit Bangladesh’s needs and the Delta Initiatives focus areas?',
            'weight' => 1,
        ],
    ],

    'score_scale' => [1 => 'Weak', 2 => 'Limited', 3 => 'Adequate', 4 => 'Strong', 5 => 'Exceptional'],

    /* Failed receipts / alerts are retried by `ideas:retry-notifications` after this many minutes. */
    'retry_after_minutes' => 2,
];
