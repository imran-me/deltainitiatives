<?php

return [
    'meta' => [
        'title' => 'Contact',
        'description' => 'Contact Delta Initiatives — general questions, the Idea Portal, partnerships and media. We reply within two working days.',
    ],

    'hero' => [
        'eyebrow' => 'Contact',
        'title' => 'Let’s talk.',
        'intro' => 'Questions, ideas, partnership proposals or media requests — a real person reads every message, and we reply within two working days.',
        'write' => 'Send a message',
    ],

    'channels' => [
        'eyebrow' => 'Reach the right team',
        'title' => 'Write to the people who can help',
        'general' => ['title' => 'General enquiries', 'text' => 'Questions about Delta Initiatives, our programmes or anything else.'],
        'ideas' => ['title' => 'Ideas & the Idea Portal', 'text' => 'Help with a submission, your receipt or the review process.', 'link' => 'Submit an idea'],
        'partners' => ['title' => 'Partnerships', 'text' => 'Universities, companies, NGOs and government bodies.', 'link' => 'Partner with us'],
        'phone' => ['title' => 'Phone', 'text' => 'During office hours.'],
        'visit' => ['title' => 'Where we are', 'hours' => 'Office hours'],
        'media' => 'Journalists: choose “Media” as the topic and we will prioritise your request.',
        'follow' => 'Follow our work',
    ],

    'form' => [
        'eyebrow' => 'Message',
        'title' => 'Send us a message',
        'intro' => 'Pick a topic so your message reaches the right person first time.',
        'required_note' => 'Fields marked * are required.',
        'labels' => [
            'topic' => 'Topic',
            'name' => 'Full name',
            'email' => 'E-mail',
            'phone' => 'Phone',
            'organization' => 'Organisation',
            'subject' => 'Subject',
            'message' => 'Message',
        ],
        'placeholders' => [
            'name' => 'Your full name',
            'email' => 'you@example.com',
            'phone' => '+880 1XXX-XXXXXX',
            'organization' => 'University, company or organisation',
            'subject' => 'What is your message about?',
        ],
        'choose' => 'Choose a topic…',
        'privacy' => 'We use your details only to reply to you. See our :link.',
        'privacy_link' => 'Privacy Policy',
        'submit' => 'Send message',
        'sending' => 'Sending…',
        'success' => 'Thank you — your message :reference has been received. A confirmation is on its way to your inbox, and we will reply within two working days.',
        'another' => 'Send another message',
        'phone_invalid' => 'Please enter a valid phone number.',
        'attributes' => [
            'topic' => 'topic',
            'name' => 'full name',
            'email' => 'e-mail',
            'phone' => 'phone',
            'organization' => 'organisation',
            'subject' => 'subject',
            'message' => 'message',
        ],
    ],

    'topics' => [
        'general' => 'General question',
        'ideas' => 'Ideas & the Idea Portal',
        'partnership' => 'Partnership',
        'media' => 'Media & press',
        'events' => 'Events & programmes',
        'other' => 'Something else',
    ],

    'mail' => [
        'subject' => 'We’ve received your message (:reference) — Delta Initiatives',
        'preheader' => 'Thank you for writing to us. We reply within two working days.',
        'heading' => 'Thank you, :name.',
        'intro' => 'Your message has reached the Delta Initiatives team. Here is a copy of the details for your records.',
        'reference' => 'Reference',
        'topic' => 'Topic',
        'subject_label' => 'Subject',
        'next' => 'We reply to every message within two working days. If you need to add anything, simply reply to this e-mail and quote your reference.',
        'idea_button' => 'Go to the Idea Portal',
        'footer' => 'You are receiving this because you used the contact form on our website. We will not add you to any mailing list.',
    ],
];
