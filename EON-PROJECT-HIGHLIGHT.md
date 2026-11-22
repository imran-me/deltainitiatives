# EON — project highlight for the Delta Initiatives website

**Decision:** the Delta Initiatives website will highlight **EON** as a Delta Initiatives project.
Written 22 Nov 2026 from EON's own documents. Quotes are exact doc wording. Anything not found in the docs is marked **(unverified)**.

## 1. What EON is

- Full name: **EON — Efficiency Operation Nexus.** "One brain, many bodies: an adaptive decision intelligence layer" (EON-Concept-Note.pdf).
- "EON is an adaptive intelligence layer — not an application, not a chatbot, not an ERP feature. A portable reasoning core attaches to a system already running, learns it from its own source and database, and answers the people who depend on it" (eon-main\eon\EON-Concept-Note.pdf).
- "An independent AI that reads any system, understands it, and decides." (push-worktree\designs\print\note\EON-concept-note.pdf, summit edition).
- **Runs without an external AI API — verified, with one caveat:**
  - "EON computes its answers; it does not generate them. No language model, no GPU, no per-query cost." (Concept Note)
  - "Not a wrapper on a foreign model. Nothing is sent to an external endpoint." (Concept Note)
  - "With the API switch off, every answer comes from EON's own code and its own trained model, on our own server. It reads the real ERP database. No outside AI is called." (eon-how-it-works.pdf)
  - Caveat: "An outside language model (Google Gemini) is an optional helper you can switch on or off" and it is **off by default**; even when on, "Gemini only reads the question. The figures still come only from the ERP." (eon-how-it-works.pdf; eon-technical-note.pdf: "No language model is required ... its output never supplies numbers.")
  - Website wording to use: *"Runs on its own brain — no external AI API required."* Avoid "never uses any AI API" in absolute terms.

## 2. Key features (from docs)

- Voice or text in **Bangla, English and Banglish**, "understood natively. Nothing is translated into English first." (how-it-works)
- Reads the **live ERP database** and computes the answer; "every figure comes from the ERP, and EON says which rule it applied." (how-it-works)
- **Acts**: "opens ERP screens, fills in the ERP's own forms, prints reports, approves requests and records payments. Nothing is ever saved until you say 'yes'." (how-it-works)
- **Reasons**: explains *why* a figure moved and what to do next ("A dashboard shows a figure. EON explains why the figure moved" — Concept Note).
- **৳0 marginal cost per question** — "no model API, no GPU, no token meter" (Concept Note); "Zero-cost AI: runs on your own server" (eon-flyer.pdf).
- **Private**: "your accounts never leave your system" (flyer); "the data never leaves the server it lives on" (Concept Note).
- Names matched by sound across scripts ("ইমরান" finds "Imran"); remembers the conversation ("and last month?") (how-it-works).
- Automations: "morning brief, reminders, alerts and approvals queue" (flyer).
- Speed: "15–60 ms" round trip (Concept Note).

## 3. How it works (from docs)

1. Voice (browser speech recognition, optional Whisper) or typed text; spoken numbers and script repaired (technical note).
2. Understanding in layers: exact operations → a composition engine (measure × entity × period × operation: total, rank, compare, trend, breakdown, why) → the **trained intent model** → rule-based answerers and the account book (how-it-works).
3. Intent model: "an averaged perceptron, a classic machine-learning classifier", trained "On more than 114,000 real, labelled questions"; answers on its own "only when it is clearly sure" (how-it-works). Learned weights live in `server/kb/intent-model.json`.
4. "EON has no neural network and no GPU. Its 'brain' is source code plus one learned weight file" on the same server as the ERP (eon-inside-the-brain.pdf).
5. Actions go through a **YES-gate** before any ERP form is posted (technical note).
- Stack: PHP 8.3 server brain, JavaScript browser side, MySQL, Hostinger/LiteSpeed, git-push deploy (technical note).
- First host: Epal ERP — Laravel 12 / MySQL, 12 companies (technical note).

> Note: figures differ between document editions (e.g. verified questions 1,191/1,191 in the Concept Note vs 2,382/2,382 in the summit concept note; tables 203 vs 273; code ~38,000 vs ~76,000 lines). Use the summit edition or avoid hard numbers on the site. The summit note's "$3.6B worth of recorded error found" is a doc claim but should be checked before publishing **(unverified as to source data)**.

## 4. Who it's for

- Owners and directors of multi-company businesses running an ERP: "Answering a director's question ... takes several screens and a reconciliation. EON answers in one turn" (technical note).
- Trades named on the flyer: air ticketing, contract flights, visa processing, manpower & consultancy, interior design, IT services, construction.
- Sector per Concept Note: "Applied AI · Enterprise & Government Digital Infrastructure · Bangla Language Technology"; it claims the first tenancy "makes the next fourteen [classes of Bangladeshi system] a configuration problem" — government/other-system use is a stated aim, not yet shown **(unverified in deployment)**.

## 5. Live demo

- **https://eon.gulfrabit.com/** — "Live system eon.gulfrabit.com" (Concept Note); "Ask EON yourself eon.gulfrabit.com" (leaflet). Access to the ERP side requires sign-in (demo sheets say "sign in to the ERP in Edge").

## 6. Bangladesh AI Business Summit 2026

- EON was exhibited at the **Bangladesh AI Business Summit 2026, Dhaka** — "Stall U-13 · University Zone" (EON-leaflet-A4.pdf; summit concept note "Stall U-13"). Stall U-13 is also visible in the summit photos.
- Exhibited under **Daffodil International University** (leaflet, concept note). Demo sheets were checked live on 4 Nov 2026; summit photos are dated 4–5 Nov 2026 (DJI file names).
- Presented as a Delta Initiatives project on Delta's Facebook summit posts (Project 1\v2). Delta Initiatives' formal role in EON is not stated in the EON docs **(unverified — confirm with owner)**.

## 7. Suggested website section

- **Eyebrow:** Project · Applied AI
- **Headline:** EON — an AI that runs on its own brain
- **Short copy:** EON is an independent reasoning layer that sits on top of a live business system and answers in Bangla, English or Banglish. It computes every figure from the real data instead of generating it, explains why numbers moved, and acts only after you say "yes" — with no external AI API required and ৳0 cost per question. Showcased at the Bangladesh AI Business Summit 2026, Stall U-13.
- **Three tiles:** Understands (Bangla · English · Banglish, voice or text) · Reasons (computed, auditable answers) · Acts (opens screens, fills forms, behind a yes-gate)
- **CTA:** "Try EON live" → https://eon.gulfrabit.com/ · secondary "Read the concept note" (link the summit concept note PDF).
- Visuals: mascot (eon-wave-full.png) + one summit photo + app screenshot.

## 8. LOCATIONS — every EON asset

| Type | Full path |
|---|---|
| Code repo (main) | I:\Sites\EON\eon-main |
| Code repo (push worktree, incl. ERP modules + designs) | I:\Sites\EON\push-worktree |
| Code copy | I:\Sites\eon-main\eon-main |
| ERP modules | I:\Sites\EON\push-worktree\erp\Modules\EON ; I:\Sites\EON\push-worktree\erp\Modules\EonAi |
| Docs PDFs | I:\Sites\EON\EON-docs\eon-how-it-works.pdf ; eon-technical-note.pdf ; eon-inside-the-brain.pdf ; eon-demo-sheet.pdf ; eon-flyer.pdf (+ .html sources, eon-flyer.png) in the same folder |
| Demo question sheet | I:\Sites\EON\EON-docs$f.pdf |
| Concept note (full) | I:\Sites\EON\eon-main\eon\EON-Concept-Note.pdf ; I:\Sites\EON\eon-main\eon\eon-ict-concept.html |
| Concept note (summit edition) | I:\Sites\EON\push-worktree\designs\print\note\EON-concept-note.pdf (+ preview-1..8.png, sheet.png) |
| Leaflet | I:\Sites\EON\push-worktree\designs\print\EON-leaflet-A4.pdf ; EON-leaflet-A4.svg |
| Markdown docs | I:\Sites\EON\eon-main\docs\ (demo-script, deploy, erp-domain-map, erp-host, lineage-and-architecture, summit-demo, summit-ten-questions .md) ; same set in I:\Sites\EON\push-worktree\docs\ |
| Logo files | I:\Sites\EON\push-worktree\designs\print\logo\EON-logo-square.png ; EON-logo-square-512.png ; EON-logo-square-transparent.png ; EON-logo-square-transparent-512.png |
| Mascot / avatar | I:\Sites\EON\push-worktree\designs\assets\eon-wave-full.png ; eon-curious-full.png ; eon-celebrate-full.png |
| App screenshots | I:\Sites\EON\push-worktree\designs\assets\screens\eon-ask-conversation.png ; eon-brief.png |
| Other design assets | I:\Sites\EON\push-worktree\designs\assets\qr.png ; ...\assets\team\ ; diu-logo.png |
| Booth print: back wall | I:\Sites\EON\push-worktree\designs\print\wall\EON-backwall-5.9x8.33ft.pdf (+ -300dpi.png/.tif) ; ...\print\EON-backwall-TV-GUIDE.pdf |
| Booth print: side panel | I:\Sites\EON\push-worktree\designs\print\side-panel\EON-side-panel-2x8.3ft.pdf (+ -300dpi.png/.tif) |
| Booth print: fascia | I:\Sites\EON\push-worktree\designs\print\fascia\EON-fascia-6ftx8in.pdf (+ png/tif) |
| Booth preview | I:\Sites\EON\push-worktree\designs\print\stall-preview.png ; print set copy in ...\print\Print Item Team EON\ |
| Reel video | I:\Machine001V1.002\Personal\DCCI\Edit\Reels\55 EON Needs No Internet - Reel.mp4 ; also ...\Reels\56 Main Character At The AI Summit - Reel.mp4 |
| Summit films | I:\Machine001V1.002\Personal\DCCI\Edit\DCCI Summit - Metro to Summit Day (Trailer) v2.mp4 ; ...\DCCI Summit - The Night Before (Cinematic Vlog) v6.mp4 |
| Summit photos (edited) | I:\Machine001V1.002\Personal\DCCI\photo\Edited\ (incl. DJI_20261105125035_0311_D (edited).jpg, DJI_20261105132410_0327_D (edited).jpg — 1280x720 only) |
| Summit raw media | I:\Machine001V1.002\Personal\DCCI\ (DJI_202611*.MP4/.JPG) ; previews of missing clips in ...\DCCI\4 Previews of missing clips\ |
| Delta FB summit posts (v2) | I:\Machine001V1.002\Delta Initiatives\Project 1\v2\DI_EON_summit_v2_01_independent.png ; _02_understand ; _03_reason ; _04_act (+ @2x) |
| Delta FB summit posts (v1) | I:\Machine001V1.002\Delta Initiatives\Project 1\DI_EON_summit_01_team_at_booth.png … _04_presenter.png |
