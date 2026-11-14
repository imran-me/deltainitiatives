# What the team needs to provide

From the Build Plan (p.7, *What we need from the team*), plus items this build depends on.
Everything below can be entered through the admin panel — no code changes needed.

| ✔ | Item | Where it goes |
|---|---|---|
| ☐ | Buy `deltainitiatives.com` in Cloudflare | Cloudflare (see DEPLOYMENT.md) |
| ☐ | Final logo (SVG/PNG). Until then the Δ mark in the code is used | Replace `modules/Core/resources/views/components/delta-mark.blade.php` and `public/favicon.svg`, `public/images/*` |
| ☐ | Team list: name, role, real photo (square, ≥ 600 px), LinkedIn | Admin → Content → Team & advisors |
| ☐ | Confirmed advisors (if any) | Admin → Content → Team & advisors (group: Advisory Board) |
| ☐ | Contact: e-mails, phone, full address | Admin → System → Settings → Contact |
| ☐ | Social media links | Admin → System → Settings → Social |
| ☐ | Partner logos — **only with written permission** (record who approved it) | Admin → Content → Partner logos |
| ☐ | Partnership brochure (PDF) | Admin → System → Settings → Partnerships |
| ☐ | Decision: English only at launch, or English + Bangla. *Built: English site, with Submit an Idea and Join Us also in Bangla, as recommended* | — |
| ☐ | **Programme Plan**: the final 10 questions and the 6 rubric criteria | `modules/IdeaPortal/config/config.php` + `modules/IdeaPortal/lang/{en,bn}/portal.php` |
| ☐ | Confirm or edit the **8 focus areas** (drafted for this build) | Admin → Content → Focus areas |
| ☐ | Review the draft copy: hero, story, vision, mission, values, arms, pipeline | Admin → Content → Page copy / Institutional arms / Pipeline steps / Values |
| ☐ | **Legal review** of Privacy Policy, Terms and Contributor Agreement (drafts provided) | Admin → Content → Legal pages |
| ☐ | Real events (dates, venues, capacity) | Admin → Programmes → Events |
| ☐ | Cloudflare Turnstile keys, Google Workspace app password, Cloudflare Web Analytics token | `.env` / Admin → Settings |
| ☐ | Names and e-mails of every reviewer (each gets a personal account) | Admin → System → Users & roles |

## Before launch

1. Replace sample content, then **Admin → Dashboard → Remove samples**.
2. Headline numbers stay hidden until there is something real to show — live counts (ideas, partners, events) appear automatically; manual figures (e.g. pilots) need *Verified* ticked with a note on how they were checked.
