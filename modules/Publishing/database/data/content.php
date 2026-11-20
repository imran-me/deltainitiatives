<?php

// Initial content (articles, ideas, people) moved from the static v3 pages into the database.
// Generated from deltainitiatives-v3 by port tooling. Used by the 2026_11_21 data migration only.

return array (
  'articles' => 
  array (
    0 => 
    array (
      'slug' => 'adoption-pathway',
      'title' => 'From student project to public service: a fair adoption pathway',
      'title_bn' => 'ছাত্র প্রকল্প থেকে জনসেবা: ন্যায্য গ্রহণের পথ',
      'category' => 'brief',
      'area' => 'governance',
      'status_label' => 'Forthcoming · Q1 2027',
      'reading_minutes' => 6,
      'sdgs' => '16,17',
      'summary' => 'Young innovators lack a route to government.
A transparent intake and credit rule would fix most of it.
We propose a five-step pathway ministries can adopt.',
      'tags' => 'policy,adoption,youth',
      'cover_key' => 'jamuna-sirajganj',
      'body' => '## The problem

Every year, Bangladeshi universities produce thousands of final-year projects, theses and hackathon prototypes. Some of them answer questions that ministries and agencies are actively asking. Almost none reach those ministries. The route from a student’s laptop to a government file does not exist in any formal sense.

Where ideas do travel, they travel through personal networks. That is unfair to the innovator, who has no protection and no credit, and it is risky for government, which has no way to judge the evidence behind what it receives.

> The question is not whether young Bangladeshis have ideas. It is whether the state has a door they can walk through.

## What a fair pathway needs

A working pathway needs three things. First, a public record that shows who had an idea and when, so credit is never in doubt. Second, an independent validation step, so decision-makers receive evidence rather than enthusiasm. Third, a clear adoption rule, so a ministry knows how to pilot an idea and what it owes the person who had it.

## Our recommendation

We propose a five-step pathway, from intake to implementation, that a ministry or a2i could adopt without new legislation. The full brief sets out each step, the roles involved and a simple credit and benefit-sharing rule.',
    ),
    1 => 
    array (
      'slug' => 'bangla-ai-readiness',
      'title' => 'Bangla AI readiness: data, compute and people',
      'title_bn' => 'বাংলা এআই প্রস্তুতি: ডেটা, কম্পিউট ও মানুষ',
      'category' => 'report',
      'area' => 'ai',
      'status_label' => 'Forthcoming · Q1 2027',
      'reading_minutes' => 18,
      'sdgs' => '9,4',
      'summary' => 'Bangla is spoken by hundreds of millions but under-served by AI.
The bottleneck is open data, not talent.
We map what a national effort would need.',
      'tags' => 'ai,bangla,data',
      'cover_key' => 'riverside-solar',
      'body' => '## Why Bangla is under-served

Bangla is one of the most widely spoken languages in the world, yet it is a small presence in the data that today’s AI systems learn from. The result is familiar to anyone who has tried a voice assistant in Bangla: it works for standard written forms and breaks down for regional speech, mixed Bangla and English, and the language of everyday public services.

This is not a question of talent. Bangladeshi engineers and researchers already build language tools. The gap is in the shared layer underneath: open datasets, agreed benchmarks and access to the compute needed to train and test models.

> A country cannot adopt AI responsibly in a language its AI does not understand.

## Three layers of readiness

**Data.** Consented, documented speech and text across dialects, released under licences that let universities and startups use it. **Compute.** Shared access to training and evaluation hardware, so that a student lab does not need its own cluster. **People.** Researchers who can evaluate models for accuracy and fairness in Bangla, not only build them.

## What this report will do

The full report will map what exists today in each layer, where the gaps are, and what a coordinated national effort would need from government, universities and the technology organisations we are built to work with. It will close with a short list of first projects that the AI for Bangla Lab can start with.',
    ),
    2 => 
    array (
      'slug' => 'flood-warnings',
      'title' => 'Who hears the warning? Flood alerts on the chars',
      'title_bn' => 'সতর্কবার্তা কে শোনে? চরের বন্যা সতর্কতা',
      'category' => 'working',
      'area' => 'climate',
      'status_label' => 'Forthcoming · Q2 2027',
      'reading_minutes' => 12,
      'sdgs' => '13,11',
      'summary' => 'Warnings exist but often do not reach char families in time.
Trust and language matter as much as technology.
We test a low-cost sensor and SMS design.',
      'tags' => 'flood,early-warning,sensors',
      'cover_key' => 'modis-flood',
      'body' => '## The last mile of a warning

Flood forecasting in Bangladesh has improved a great deal. But a forecast is only useful if it reaches the person who has to move their family, livestock and seed stock, and if they trust it enough to act. On the chars, the shifting river islands of the Jamuna and Brahmaputra, that last mile is the hardest part.

Char families are often far from official river gauges, may not have reliable internet, and hear warnings that use place names and water levels that do not match what they see on their own island.

> A warning that arrives on time, in the wrong words, is still a warning that arrives too late.

## A low-cost design to test

This working paper sets out a design that young engineers on the Delta pipeline have proposed: small solar-powered water-level sensors placed near the community, a low-power radio mesh between them, and short Bangla SMS alerts that refer to local landmarks. The design aims to work without smartphones or mobile data.

## What we will measure

The field test will ask three questions: how much earlier a family receives a local warning, whether they understand and trust it, and what it costs to install and maintain per village. We will publish the method before the results, and the results whatever they show.',
    ),
    3 => 
    array (
      'slug' => 'idea-ledger',
      'title' => 'How the Idea Ledger protects an idea',
      'title_bn' => 'আইডিয়া লেজার কীভাবে একটি আইডিয়াকে সুরক্ষা দেয়',
      'category' => 'explainer',
      'area' => 'governance',
      'status_label' => 'Published',
      'reading_minutes' => 4,
      'sdgs' => '9,16',
      'summary' => 'Every idea gets an ID, a timestamp and a fingerprint.
Anyone can verify the record later.
It is proof of priority, not a patent.',
      'tags' => 'idea-ledger,sha-256,protection',
      'cover_key' => 'meghna-chars-1879',
      'body' => '## The problem it solves

Young innovators often keep their best ideas to themselves because they fear someone more powerful will take them. That fear is reasonable, and it is costly: ideas that are never shared are never improved, tested or adopted. The Idea Ledger is designed to make sharing safe.

## What happens when you submit

When you send an idea through the portal, three things are recorded at once. The idea gets a permanent **ID**. The server records a **timestamp** in universal time. And the text of your idea is passed through **SHA-256**, a standard cryptographic function that turns any text into a 64-character fingerprint. You receive all three in a receipt.

> Change a single letter of your idea and its fingerprint changes completely. That is what makes it proof.

## What is public, and what is not

The public ledger shows only the ID, the date, the title, the fingerprint and the stage your idea has reached. The full text stays private unless you choose to share it. Later, anyone can take the exact text you hold, compute its fingerprint, and confirm that it matches the ledger entry from that date. You can try it yourself on the Idea Portal page.

## What it is not

The ledger is proof of priority, not a patent. It does not stop someone from using a similar idea, but it gives you a clear, verifiable record that you had it first, which matters for credit, for partnerships and, if you choose to file one, for a patent application.',
    ),
    4 => 
    array (
      'slug' => 'five-stage-method',
      'title' => 'What the five-stage method means in practice',
      'title_bn' => 'পাঁচ ধাপের পদ্ধতি বাস্তবে কী বোঝায়',
      'category' => 'explainer',
      'area' => 'governance',
      'status_label' => 'Published',
      'reading_minutes' => 5,
      'sdgs' => '9,17',
      'summary' => 'Every idea moves through Discover, Develop, Validate, Brief and Implement.
Each stage has an exit test.
Stages are published so anyone can follow progress.',
      'tags' => 'method,pipeline',
      'cover_key' => 'padma-fishing-boat',
      'body' => '## Five stages, one direction

Every idea on the ledger moves through the same five stages: Discover, Develop, Validate, Brief and Implement. The stages are not paperwork. Each one asks a different question, and an idea only moves on when that question has a good answer.

## What each stage asks

**Discover** asks whether the problem is real and who has it. **Develop** asks whether a working version can be built. **Validate** asks whether it works in the field, for whom and at what cost. **Brief** asks whether decision-makers can act on the evidence. **Implement** asks whether someone is willing to adopt it.

> A stage is finished when its question is answered, not when its time is up.

## Why we show the stage in public

Every idea page and initiative shows its stage as a rail. Anyone can see where an idea stands and hold us to moving it forward honestly, including when an idea stops because the evidence says it should.',
    ),
    5 => 
    array (
      'slug' => 'targets-before-results',
      'title' => 'Why we publish targets before results',
      'title_bn' => 'ফলাফলের আগে কেন লক্ষ্য প্রকাশ করি',
      'category' => 'explainer',
      'area' => 'governance',
      'status_label' => 'Published',
      'reading_minutes' => 4,
      'sdgs' => '16,17',
      'summary' => 'Targets set in advance can be checked.
Results reported against them build trust.
We will publish misses as well as hits.',
      'tags' => 'transparency,targets',
      'cover_key' => 'sylhet-tea',
      'body' => '## The problem with results-only reporting

Organisations that only publish results can choose which results to show. Readers cannot tell what was planned, what was missed or what was quietly dropped.

## What we do instead

We publish targets before we have results: ideas protected, ideas developed, briefs delivered and pilots running. When results come in, we report them against those targets, including the ones we miss.

> A target you can check is worth more than a result you cannot.

## How to hold us to it

The Impact page lists our year-one targets and roadmap. Each annual report will repeat them next to the actual figures, with an explanation for every gap.',
    ),
    6 => 
    array (
      'slug' => 'strong-idea-submission',
      'title' => 'How to write a strong idea submission',
      'title_bn' => 'কীভাবে একটি শক্তিশালী আইডিয়া জমা দেবেন',
      'category' => 'explainer',
      'area' => 'ventures',
      'status_label' => 'Published',
      'reading_minutes' => 6,
      'sdgs' => '4,9',
      'summary' => 'Start with the problem and who has it.
Show what you have already tried.
Be honest about what you do not know.',
      'tags' => 'idea-portal,how-to',
      'cover_key' => 'paddy-field',
      'body' => '## Start with the problem

Reviewers read hundreds of ideas. The strongest ones begin with a clear problem: who has it, where, and what it costs them today. A solution only makes sense once the problem is clear.

## Show what you have tried

Tell us what you have already built, tested or learned, even if it failed. Early evidence, however small, is more convincing than a big promise.

> We back people who know what they do not know yet.

## Be honest about the gaps

Every idea has unknowns. Naming them shows judgement and helps us match you with the right mentor, lab or partner. Write in Bangla or English, whichever lets you be most precise.',
    ),
  ),
  'ideas' => 
  array (
    0 => 
    array (
      'slug' => 'low-cost-flood-sensor-mesh-for-char-villages',
      'code' => 'DI-2026-0104',
      'title' => 'Low-cost flood sensor mesh for char villages',
      'area' => 'hardware',
      'stage' => 2,
      'protected_at' => '2026-03-04 09:42:17',
      'sdgs' => '13,11',
      'problem' => 'Families on river islands often get flood warnings too late, in words that do not match what they see.',
      'solution' => 'Solar-powered water-level sensors on a low-power radio mesh send short Bangla SMS alerts that name local landmarks.',
      'how' => NULL,
      'fingerprint' => '0d7def8bc297474dec19074a00496e5c9a31a5c0d35888920429ec0c29a5e0b7',
      'cover_key' => 'jamuna-sirajganj',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Nusrat Jahan',
    ),
    1 => 
    array (
      'slug' => 'bangla-voice-assistant-for-union-parishad-services',
      'code' => 'DI-2026-0111',
      'title' => 'Bangla voice assistant for union parishad services',
      'area' => 'ai',
      'stage' => 3,
      'protected_at' => '2026-03-11 09:42:17',
      'sdgs' => '16,9',
      'problem' => 'Union parishad services are hard to reach for people who cannot read forms or navigate websites.',
      'solution' => 'A Bangla voice assistant that answers questions about common services and walks people through the steps.',
      'how' => NULL,
      'fingerprint' => '14ddfef48dfc9982de176948b9825141f71d5eba18f3a0f152f70668370a60aa',
      'cover_key' => NULL,
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Arif Rahman',
    ),
    2 => 
    array (
      'slug' => 'saline-tolerant-seedling-nursery-kit',
      'code' => 'DI-2026-0118',
      'title' => 'Saline-tolerant seedling nursery kit',
      'area' => 'climate',
      'stage' => 1,
      'protected_at' => '2026-04-02 09:42:17',
      'sdgs' => '2,13',
      'problem' => 'Coastal soils are turning saline and seedlings fail before they are planted out.',
      'solution' => 'A nursery kit with tested soil mixes and salt-tolerant varieties, designed with coastal farmers.',
      'how' => NULL,
      'fingerprint' => '15f6f59a09da9828d196476db8f7efda4e84d14d9356a2f0da5bb931e7241121',
      'cover_key' => 'paddy-preparation',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Tanvir Ahmed',
    ),
    3 => 
    array (
      'slug' => 'open-dashboard-for-school-stipend-tracking',
      'code' => 'DI-2026-0125',
      'title' => 'Open dashboard for school stipend tracking',
      'area' => 'governance',
      'stage' => 4,
      'protected_at' => '2026-04-19 09:42:17',
      'sdgs' => '4,16',
      'problem' => 'Families and schools cannot easily see whether stipends were paid, to whom and when.',
      'solution' => 'An open dashboard that shows stipend status by school, with privacy-safe public views.',
      'how' => NULL,
      'fingerprint' => 'eb2b5446862b9642e2364cbb6e86480b59ba966d12824091f8fbee54dcd6abb0',
      'cover_key' => 'paddy-field',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Sadia Islam',
    ),
    4 => 
    array (
      'slug' => 'paper-strip-test-for-arsenic-in-tube-well-water',
      'code' => 'DI-2026-0132',
      'title' => 'Paper-strip test for arsenic in tube-well water',
      'area' => 'life',
      'stage' => 2,
      'protected_at' => '2026-05-07 09:42:17',
      'sdgs' => '6,3',
      'problem' => 'Arsenic testing for tube-wells is too expensive and slow for regular use.',
      'solution' => 'A paper-strip test that changes colour above a safe threshold, validated against laboratory results.',
      'how' => NULL,
      'fingerprint' => 'da1adb6b3a101c3cd02f3b39b2229b8600483df3f26823b7032616ae909d131d',
      'cover_key' => 'paddy-path',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Rafiq Hasan',
    ),
    5 => 
    array (
      'slug' => 'marketplace-linking-rickshaw-van-owners-to-cargo',
      'code' => 'DI-2026-0139',
      'title' => 'Marketplace linking rickshaw-van owners to cargo',
      'area' => 'ventures',
      'stage' => 1,
      'protected_at' => '2026-06-15 09:42:17',
      'sdgs' => '8',
      'problem' => 'Rickshaw-van owners lose income between jobs while small traders struggle to find transport.',
      'solution' => 'A simple marketplace that matches cargo with nearby van owners by phone or SMS.',
      'how' => NULL,
      'fingerprint' => '97d488800eb7b9368c46da1ae890c8a033ca02ad2b0152e929ae250abf76c593',
      'cover_key' => 'solar-cart',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Mehedi Karim',
    ),
    6 => 
    array (
      'slug' => 'offline-first-telehealth-triage-app',
      'code' => 'DI-2026-0146',
      'title' => 'Offline-first telehealth triage app',
      'area' => 'life',
      'stage' => 3,
      'protected_at' => '2026-07-01 09:42:17',
      'sdgs' => '3',
      'problem' => 'Health workers in remote areas must decide who needs a doctor without a doctor or internet.',
      'solution' => 'An offline-first triage app that follows national guidelines and syncs when a connection returns.',
      'how' => NULL,
      'fingerprint' => 'b95304ebe754aaba7f7175acfc03c88c5d2ffdb1ace6ac71e4ef978dca46bd95',
      'cover_key' => NULL,
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Farhana Akter',
    ),
    7 => 
    array (
      'slug' => 'solar-cold-box-for-fish-traders',
      'code' => 'DI-2026-0153',
      'title' => 'Solar cold box for fish traders',
      'area' => 'hardware',
      'stage' => 0,
      'protected_at' => '2026-08-22 09:42:17',
      'sdgs' => '7,14',
      'problem' => 'Fish traders lose stock to heat between landing and market.',
      'solution' => 'A solar-powered cold box sized for a trader\'s daily catch, built for local repair.',
      'how' => NULL,
      'fingerprint' => '8af50e22f4897719f3a7d5bf42a3f7313333e11a2bf1cbe7fb93cfb8f8c327f1',
      'cover_key' => 'padma-fishing-boat-2',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => NULL,
    ),
    8 => 
    array (
      'slug' => 'mangrove-carbon-mapping-with-drones',
      'code' => 'DI-2026-0160',
      'title' => 'Mangrove carbon mapping with drones',
      'area' => 'climate',
      'stage' => 0,
      'protected_at' => '2026-09-30 09:42:17',
      'sdgs' => '13,15',
      'problem' => 'The carbon held by mangroves is poorly measured, so communities cannot benefit from it.',
      'solution' => 'Low-cost drone surveys and open methods to map mangrove carbon over time.',
      'how' => NULL,
      'fingerprint' => '7ab2d6498c8bad3e15c7053f070a4ee4badc7f19323e25888c191ec947d0b737',
      'cover_key' => 'sundarbans-mangrove',
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => 'Lamia Chowdhury',
    ),
    9 => 
    array (
      'slug' => 'plain-bangla-legal-aid-chatbot',
      'code' => 'DI-2026-0167',
      'title' => 'Plain-Bangla legal aid chatbot',
      'area' => 'ai',
      'stage' => 1,
      'protected_at' => '2026-10-12 09:42:17',
      'sdgs' => '16',
      'problem' => 'Legal information is written in language most people cannot use.',
      'solution' => 'A plain-Bangla chatbot that explains common legal situations and points to free legal aid.',
      'how' => NULL,
      'fingerprint' => 'ea8f7d66f9f5acf7b9f5db2d5a1081cab4147f44e64732d3b780f57297c24b77',
      'cover_key' => NULL,
      'is_featured' => false,
      'is_demo' => true,
      'initiator' => NULL,
    ),
    10 => 
    array (
      'slug' => 'eon',
      'code' => 'DI-2026-0174',
      'title' => 'EON: one brain, many decision layers for business',
      'area' => 'ai',
      'stage' => 2,
      'protected_at' => '2026-11-19 09:42:17',
      'sdgs' => '8,9',
      'problem' => 'Owners of growing Bangladeshi businesses run many desks, ledgers and sometimes several companies at once. The data sits in an ERP, but getting a straight answer means asking an accountant, waiting for a report or reading screens that were never designed for decisions.',
      'solution' => 'EON sits on top of a running ERP as one brain with one memory. You ask by voice or text, in Bangla, English or Banglish. It reads the live data, computes the answer, explains the rule it used and recommends an action. Every morning it briefs; approvals queue in one place; nothing is saved until you say yes.',
      'how' => 'Understand: Its own intent model understands Bangla, English and Banglish natively, without translating to English first.
Read: It reads the ERP database through read-only access: accounts, payroll, HR and attendance, expenses, CRM, tasks and projects.
Compute: Totals, ageing and ledgers are computed in the database; forecasting and anomaly detection run in a small analytics layer.
Answer and act: It answers with the numbers behind the answer, drafts reminders and notices, and can open screens or fill forms after your confirmation.
Degrade gracefully: If the server or a language model is unavailable, a rule-based brain keeps answering, so the system never goes silent.',
      'fingerprint' => 'ac8564611fde8e6e9fac03bc5fe5118ac909067fc0c43e23218d65a458a203cc',
      'cover_key' => 'solar-building',
      'is_featured' => true,
      'is_demo' => false,
      'initiator' => NULL,
    ),
  ),
  'people' => 
  array (
    0 => 
    array (
      'slug' => 'nusrat-jahan',
      'name' => 'Nusrat Jahan',
      'kind' => 'contributor',
      'role' => 'Delta Initiator',
      'area' => 'hardware',
      'bio' => 'Working on: Flood sensor mesh.',
      'is_demo' => true,
      'sort_order' => 0,
    ),
    1 => 
    array (
      'slug' => 'arif-rahman',
      'name' => 'Arif Rahman',
      'kind' => 'contributor',
      'role' => 'Policy Initiator',
      'area' => 'ai',
      'bio' => 'Working on: Bangla civic voice assistant.',
      'is_demo' => true,
      'sort_order' => 1,
    ),
    2 => 
    array (
      'slug' => 'sadia-islam',
      'name' => 'Sadia Islam',
      'kind' => 'contributor',
      'role' => 'Impact Initiator',
      'area' => 'governance',
      'bio' => 'Working on: Stipend tracking dashboard.',
      'is_demo' => true,
      'sort_order' => 2,
    ),
    3 => 
    array (
      'slug' => 'rafiq-hasan',
      'name' => 'Rafiq Hasan',
      'kind' => 'contributor',
      'role' => 'Research Initiator',
      'area' => 'life',
      'bio' => 'Working on: Arsenic paper-strip test.',
      'is_demo' => true,
      'sort_order' => 3,
    ),
    4 => 
    array (
      'slug' => 'farhana-akter',
      'name' => 'Farhana Akter',
      'kind' => 'contributor',
      'role' => 'Delta Initiator',
      'area' => 'life',
      'bio' => 'Working on: Offline telehealth triage.',
      'is_demo' => true,
      'sort_order' => 4,
    ),
    5 => 
    array (
      'slug' => 'tanvir-ahmed',
      'name' => 'Tanvir Ahmed',
      'kind' => 'contributor',
      'role' => 'Young Initiator',
      'area' => 'climate',
      'bio' => 'Working on: Saline-tolerant nursery kit.',
      'is_demo' => true,
      'sort_order' => 5,
    ),
    6 => 
    array (
      'slug' => 'mehedi-karim',
      'name' => 'Mehedi Karim',
      'kind' => 'contributor',
      'role' => 'Venture Initiator',
      'area' => 'ventures',
      'bio' => 'Working on: Cargo van marketplace.',
      'is_demo' => true,
      'sort_order' => 6,
    ),
    7 => 
    array (
      'slug' => 'lamia-chowdhury',
      'name' => 'Lamia Chowdhury',
      'kind' => 'contributor',
      'role' => 'Young Initiator',
      'area' => 'climate',
      'bio' => 'Working on: Mangrove carbon mapping.',
      'is_demo' => true,
      'sort_order' => 7,
    ),
    8 => 
    array (
      'slug' => 'executive-director',
      'name' => 'Executive Director',
      'kind' => 'team',
      'role' => 'Executive Director',
      'area' => NULL,
      'bio' => 'Leads the institute. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 0,
    ),
    9 => 
    array (
      'slug' => 'director-of-research',
      'name' => 'Director of Research',
      'kind' => 'team',
      'role' => 'Director of Research',
      'area' => NULL,
      'bio' => 'Methods, peer review. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 1,
    ),
    10 => 
    array (
      'slug' => 'director-of-innovation',
      'name' => 'Director of Innovation',
      'kind' => 'team',
      'role' => 'Director of Innovation',
      'area' => NULL,
      'bio' => 'Labs, Idea Portal. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 2,
    ),
    11 => 
    array (
      'slug' => 'director-of-policy',
      'name' => 'Director of Policy',
      'kind' => 'team',
      'role' => 'Director of Policy',
      'area' => NULL,
      'bio' => 'Briefs, government. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 3,
    ),
    12 => 
    array (
      'slug' => 'head-of-services',
      'name' => 'Head of Services',
      'kind' => 'team',
      'role' => 'Head of Services',
      'area' => NULL,
      'bio' => 'Client delivery. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 4,
    ),
    13 => 
    array (
      'slug' => 'head-of-partnerships',
      'name' => 'Head of Partnerships',
      'kind' => 'team',
      'role' => 'Head of Partnerships',
      'area' => NULL,
      'bio' => 'Industry, UN, academia. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 5,
    ),
    14 => 
    array (
      'slug' => 'board-1',
      'name' => 'Chair, Board of Trustees',
      'kind' => 'board',
      'role' => 'Chair, Board of Trustees',
      'area' => NULL,
      'bio' => 'Independent oversight. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 0,
    ),
    15 => 
    array (
      'slug' => 'board-2',
      'name' => 'Trustee',
      'kind' => 'board',
      'role' => 'Trustee',
      'area' => NULL,
      'bio' => 'Finance and audit. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 1,
    ),
    16 => 
    array (
      'slug' => 'board-3',
      'name' => 'Trustee',
      'kind' => 'board',
      'role' => 'Trustee',
      'area' => NULL,
      'bio' => 'Research ethics. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 2,
    ),
    17 => 
    array (
      'slug' => 'board-4',
      'name' => 'Trustee',
      'kind' => 'board',
      'role' => 'Trustee',
      'area' => NULL,
      'bio' => 'Youth representative. Appointment to be announced.',
      'is_demo' => false,
      'sort_order' => 3,
    ),
  ),
);
