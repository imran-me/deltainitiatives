/**
 * Delta Initiatives — progressive enhancement only.
 * Every page works without JavaScript; this adds navigation, motion and form niceties.
 * No inline handlers / eval, so the strict Content-Security-Policy holds.
 */

const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------- Site header: solid on scroll ---------- */
function initHeader() {
    const header = $('[data-site-header]');
    if (!header) return;
    const alwaysSolid = header.hasAttribute('data-solid');
    const update = () => header.classList.toggle('is-scrolled', alwaysSolid || window.scrollY > 24);
    update();
    window.addEventListener('scroll', update, { passive: true });
}

/* ---------- Mobile navigation ---------- */
function initMobileNav() {
    const toggle = $('[data-nav-toggle]');
    const panel = $('#mobile-nav');
    if (!toggle || !panel) return;

    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open));
        panel.hidden = !open;
        document.body.style.overflow = open ? 'hidden' : '';
        // The icons are SVG elements, which have no .hidden property — toggle the attribute.
        $$('[data-nav-icon]', toggle).forEach((icon) => {
            icon.toggleAttribute('hidden', icon.dataset.navIcon !== (open ? 'close' : 'open'));
        });
        if (open) $('a, button', panel)?.focus();
    };

    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    $$('[data-nav-close]', panel).forEach((el) => el.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
    window.matchMedia('(min-width: 80rem)').addEventListener('change', (e) => e.matches && setOpen(false));
}

/* ---------- Reveal on scroll ---------- */
function initReveal() {
    const items = $$('[data-reveal]');
    if (!items.length) return;
    if (reduceMotion || !('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );
    items.forEach((el) => observer.observe(el));
}

/* ---------- Character counters: <textarea data-counter maxlength="..."> ---------- */
function initCounters() {
    $$('[data-counter]').forEach((field) => {
        const output = document.getElementById(field.dataset.counter);
        const max = Number(field.getAttribute('maxlength'));
        if (!output || !max) return;
        const update = () => {
            const left = max - field.value.length;
            output.textContent = `${field.value.length.toLocaleString()} / ${max.toLocaleString()}`;
            output.classList.toggle('text-red-600', left < max * 0.05);
        };
        field.addEventListener('input', update);
        update();
    });
}

/* ---------- File inputs: list chosen files with sizes ---------- */
function initFileInputs() {
    $$('input[type="file"][data-file-list]').forEach((input) => {
        const list = document.getElementById(input.dataset.fileList);
        const maxFiles = Number(input.dataset.maxFiles || 0);
        const maxBytes = Number(input.dataset.maxKb || 0) * 1024;
        if (!list) return;
        input.addEventListener('change', () => {
            list.innerHTML = '';
            const files = Array.from(input.files || []);
            let problem = '';
            if (maxFiles && files.length > maxFiles) problem = input.dataset.msgTooMany || `Please choose at most ${maxFiles} files.`;
            files.forEach((file) => {
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between gap-3 rounded-lg bg-white px-3 py-2 text-sm ring-1 ring-navy-100';
                const name = document.createElement('span');
                name.className = 'truncate text-navy-800';
                name.textContent = file.name;
                const size = document.createElement('span');
                size.className = 'shrink-0 tabular-nums text-navy-400';
                size.textContent = file.size > 1048576 ? `${(file.size / 1048576).toFixed(1)} MB` : `${Math.max(1, Math.round(file.size / 1024))} KB`;
                if (maxBytes && file.size > maxBytes) {
                    size.className = 'shrink-0 font-medium text-red-600';
                    problem = input.dataset.msgTooBig || 'One of the files is too large.';
                }
                li.append(name, size);
                list.append(li);
            });
            input.setCustomValidity(problem);
            if (problem) input.reportValidity();
        });
    });
}

/* ---------- Prevent double submits; show progress label ---------- */
function initSubmitGuards() {
    $$('form[data-guard]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }
            form.dataset.submitting = '1';
            $$('button[type="submit"]', form).forEach((btn) => {
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                if (btn.dataset.loadingText) {
                    btn.dataset.originalText = btn.innerHTML;
                    btn.textContent = btn.dataset.loadingText;
                }
            });
            // Re-enable after 20s in case of network failure / validation in another tab.
            setTimeout(() => {
                form.dataset.submitting = '0';
                $$('button[type="submit"]', form).forEach((btn) => {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
                });
            }, 20000);
        });
    });
}

/* ---------- Confirm before destructive actions: <form data-confirm="Are you sure?"> ---------- */
function initConfirm() {
    document.addEventListener(
        'submit',
        (e) => {
            const form = e.target;
            if (form instanceof HTMLFormElement && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        },
        true,
    );
}

/* ---------- Auto-submit filters: <select data-autosubmit> ---------- */
function initAutoSubmit() {
    $$('[data-autosubmit]').forEach((el) => el.addEventListener('change', () => el.form?.requestSubmit()));
}

/* ---------- Copy to clipboard: <button data-copy="text"> ---------- */
function initCopy() {
    $$('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(btn.dataset.copy);
                const label = btn.dataset.copiedLabel || 'Copied';
                const original = btn.getAttribute('aria-label') || '';
                btn.setAttribute('aria-label', label);
                btn.classList.add('text-river-600');
                const live = $('[data-copy-status]', btn.parentElement) || btn;
                const prev = live.textContent;
                if (live !== btn) live.textContent = label;
                setTimeout(() => {
                    btn.setAttribute('aria-label', original);
                    btn.classList.remove('text-river-600');
                    if (live !== btn) live.textContent = prev;
                }, 1800);
            } catch {
                /* clipboard unavailable — no-op */
            }
        });
    });
}

/* ---------- Disclosure toggles: <button data-toggle="#id"> ---------- */
function initToggles() {
    $$('[data-toggle]').forEach((btn) => {
        const target = $(btn.dataset.toggle);
        if (!target) return;
        btn.addEventListener('click', () => {
            const open = target.hidden;
            target.hidden = !open;
            btn.setAttribute('aria-expanded', String(open));
        });
    });
}

/* ---------- Admin sidebar (mobile) ---------- */
function initAdminSidebar() {
    const sidebar = $('[data-admin-sidebar]');
    const backdrop = $('[data-admin-backdrop]');
    if (!sidebar) return;
    const set = (open) => {
        sidebar.classList.toggle('-translate-x-full', !open);
        backdrop?.classList.toggle('hidden', !open);
        $$('[data-admin-sidebar-toggle]').forEach((b) => b.setAttribute('aria-expanded', String(open)));
    };
    $$('[data-admin-sidebar-toggle]').forEach((btn) =>
        btn.addEventListener('click', () => set(sidebar.classList.contains('-translate-x-full'))),
    );
    backdrop?.addEventListener('click', () => set(false));
}

/* ---------- Rubric scoring: live total ---------- */
function initRubric() {
    $$('[data-rubric]').forEach((form) => {
        const output = $('[data-rubric-total]', form);
        const update = () => {
            let total = 0;
            let weightSum = 0;
            $$('input[type="radio"]:checked', form).forEach((radio) => {
                const weight = Number(radio.dataset.weight || 1);
                total += Number(radio.value) * weight;
                weightSum += weight;
            });
            if (output) output.textContent = weightSum ? (total / weightSum).toFixed(2) : '—';
        };
        form.addEventListener('change', update);
        update();
    });
}

/* ---------- Dismiss flash messages ---------- */
function initFlash() {
    $$('[data-dismiss]').forEach((btn) => btn.addEventListener('click', () => btn.closest('[data-flash]')?.remove()));
}

/* ---------- Partner form: preselect type from links like ?type=research#inquiry ---------- */
function initPreselect() {
    $$('[data-preselect]').forEach((link) => {
        link.addEventListener('click', () => {
            const select = document.getElementById(link.dataset.preselectTarget);
            if (select) select.value = link.dataset.preselect;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileNav();
    initReveal();
    initCounters();
    initFileInputs();
    initSubmitGuards();
    initConfirm();
    initAutoSubmit();
    initCopy();
    initToggles();
    initAdminSidebar();
    initRubric();
    initFlash();
    initPreselect();
});
