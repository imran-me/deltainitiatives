<?php

namespace Modules\CMS\Seeders;

use Modules\CMS\Models\LegalPage;

/**
 * Draft legal texts. They must be reviewed by a qualified lawyer in Bangladesh
 * and published (Admin → Content → Legal pages) before launch (Build Plan p.6).
 */
class LegalPages
{
    protected const DRAFT_NOTE = "> **Draft for legal review.** This text was prepared as a starting point and must be reviewed by qualified legal counsel before launch.\n\n";

    public static function seed(): void
    {
        foreach (static::pages() as $slug => $page) {
            LegalPage::query()->firstOrCreate(['slug' => $slug], $page + [
                'version' => '1.0',
                'effective_date' => now()->startOfMonth()->toDateString(),
                'is_published' => true,
            ]);
        }
    }

    /** @return array<string, array<string, string>> */
    public static function pages(): array
    {
        return [
            'privacy' => [
                'title' => 'Privacy Policy',
                'summary' => 'What personal information we collect through this website, why, who can see it, and the choices you have.',
                'body' => self::DRAFT_NOTE.<<<'MD'
## Who we are

Delta Initiatives ("we", "us") is a research, innovation and policy institute based in Dhaka, Bangladesh. This policy explains how we handle personal information collected through **deltainitiatives.com**. Questions can be sent to **deltainitiativesbd@gmail.com**.

## What we collect

We only collect what you choose to give us through our forms, plus the minimum technical information needed to keep the site secure.

- **Idea Portal submissions** — your name, e-mail, phone, institution, team details, your answers and any files you upload.
- **Join Us applications** — your contact details, studies, skills, motivation and, optionally, a CV.
- **Partner and contact enquiries** — your name, organisation, contact details and message.
- **Event registrations** — your name, contact details and institution.
- **Newsletter** — your e-mail address, and the date you confirmed your subscription.
- **Technical information** — your IP address and browser type when you submit a form, used for security and to prevent abuse.

We use **Cloudflare Web Analytics**, which measures page visits without cookies and without tracking you across other websites. We do not use advertising trackers.

## Why we use it

- To review and respond to what you send us, and to run the programmes you apply for.
- To send you the receipt for an idea submission and other messages you asked for.
- To protect the website and our community from spam and abuse.
- To keep accurate records — for example, proof of when an idea was submitted.

We do not sell your information and we do not use it for advertising.

## Who can see it

Only authorised members of the Delta Initiatives team and reviewers who need it for their role. Each person has their **own account**, access depends on their role, and access to submissions is **recorded in an audit log**. Reviewers and mentors are bound by confidentiality.

We use trusted service providers to run the website: **Hostinger** (hosting and database), **Cloudflare** (domain, security and analytics) and **Google Workspace** (e-mail). They process data on our behalf and only as needed to provide their service.

## How long we keep it

- Idea submissions: for as long as needed to evaluate and support the idea, and afterwards a minimal record (Idea ID, timestamp and fingerprint) so that proof of submission remains available.
- Applications and enquiries: up to 24 months, unless an ongoing relationship requires longer.
- Newsletter: until you unsubscribe.

## Your choices and rights

You can ask us to **see, correct or delete** the information we hold about you, or **withdraw your consent**, by e-mailing deltainitiativesbd@gmail.com. Every newsletter e-mail includes a one-click unsubscribe link.

## Security

The website uses HTTPS everywhere. Uploaded files are stored privately and are never publicly accessible. Admin access requires personal accounts with strong passwords, and we keep regular backups.

## Young people

Many of our contributors are students. If you are under 18, please make sure a parent or guardian knows about and agrees to your submission.

## Changes

If we change this policy we will update the version and date on this page.
MD,
            ],

            'terms' => [
                'title' => 'Terms of Use',
                'summary' => 'The rules for using the Delta Initiatives website and its forms.',
                'body' => self::DRAFT_NOTE.<<<'MD'
## Using this website

By using deltainitiatives.com you agree to these terms. If you do not agree, please do not use the website.

## Submitting ideas and other content

When you submit an idea through the Idea Portal, the **Contributor Agreement** applies in addition to these terms. In short: **your idea stays yours**, and we may use it only to review it and support you.

When you send us anything else (an application, enquiry or message), you confirm the information is accurate and that you are allowed to share it.

## Acceptable use

Please do not:

- submit content that is unlawful, misleading, abusive or that infringes someone else's rights;
- try to gain unauthorised access to any part of the website, including the admin panel;
- send spam or use automated tools to submit forms.

We may remove content or block access that breaks these rules.

## Our content

The text, design, logo and other materials on this website belong to Delta Initiatives unless stated otherwise. You may share links to our pages and quote short extracts with credit. Please ask before reusing anything else.

## Links to other websites

We may link to other websites. We are not responsible for their content or privacy practices.

## No guarantees

We work hard to keep the website accurate and available, but we cannot guarantee it will always be error-free or uninterrupted. Submitting an idea or application does not guarantee selection, funding or any other outcome.

## Liability

To the extent permitted by law, Delta Initiatives is not liable for indirect or consequential loss arising from use of this website.

## Governing law

These terms are governed by the laws of Bangladesh, and the courts of Dhaka have jurisdiction.

## Contact

Questions about these terms: **deltainitiativesbd@gmail.com**.
MD,
            ],

            'contributor-agreement' => [
                'title' => 'Contributor Agreement',
                'summary' => 'The promises we make to everyone who submits an idea — and the few we ask in return. Your idea stays yours.',
                'body' => self::DRAFT_NOTE.<<<'MD'
## The short version

- **You keep ownership** of your idea and everything you submit.
- We use it **only to review it and to support you**.
- We keep it **confidential** and never publish details without your written permission.
- We give you **proof**: an Idea ID, a timestamp and a SHA-256 digital fingerprint, sent to you by e-mail.

## 1. Ownership

You (and your team) keep all rights, including any intellectual property, in the idea and materials you submit. Delta Initiatives claims no ownership of them by receiving them.

## 2. What you allow us to do

You give Delta Initiatives a limited, non-exclusive, royalty-free permission to store, read, copy internally and evaluate your submission, and to share it with reviewers and mentors **only for the purpose of assessing and supporting your idea**. We will not use your idea for any other purpose, or give it to anyone else, without your written permission.

## 3. Confidentiality

- Access to submissions is limited to authorised team members and reviewers, each with a personal account. Every view and change is recorded in an audit log.
- Reviewers and mentors are bound by confidentiality.
- We will never publish the details of your idea without your team's written permission. We may publish anonymous, aggregate information (for example, "120 ideas were received in climate resilience").

## 4. Proof of submission

When you submit, the Idea Portal records:

- a unique **Idea ID** (for example DI-2026-0001);
- the exact **date and time** of submission;
- a **SHA-256 fingerprint** — a unique code calculated from exactly what you submitted, including your files. Changing even one character changes the fingerprint completely.

We e-mail you a receipt containing all three, and we keep an unaltered record. If anyone later claims your idea, this record shows what was submitted and when. You can check a receipt at any time on our **Verify** page.

## 5. Your promises

By submitting, you confirm that:

- the idea is your team's own work, or you have the right to share it;
- every team member listed has agreed to the submission;
- you are not sharing anyone else's confidential information without permission.

## 6. No obligation

We review every submission fairly against published criteria, but we are not obliged to select, fund or develop any idea. We may receive ideas that are similar to yours from others, independently. Our records of who submitted what, and when, help us treat everyone fairly.

## 7. Joining Delta Labs

If your idea is selected for Delta Labs, we will agree a separate written agreement with your team before any joint development begins. That agreement will cover ownership of new work, credit and any commercial arrangements. Nothing in this Contributor Agreement transfers your rights to us.

## 8. Withdrawing

You can withdraw your submission at any time by e-mailing **deltainitiativesbd@gmail.com** with your Idea ID. We will delete your answers and files and keep only the minimal record — Idea ID, timestamp and fingerprint — so that proof of your submission remains available to you.

## 9. Personal data

We handle personal information as described in our **Privacy Policy**.

## 10. Governing law

This agreement is governed by the laws of Bangladesh.
MD,
            ],
        ];
    }
}
