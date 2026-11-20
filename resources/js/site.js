/* Delta Initiatives v3 public site behaviour. Generated from deltainitiatives-v3 by port.py.
   Module script, no inline handlers (strict CSP). Form helpers (guards, counters, files) come from app.js. */
import SEARCH_INDEX from './site-index.js';
import './site-r3.js';

(function () {
  'use strict';
  var doc = document, root = doc.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  function $(s, c) { return (c || doc).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); }
  function store(k, v) { try { if (v === undefined) return localStorage.getItem(k); localStorage.setItem(k, v); } catch (e) { return null; } }

  /* header: shrink + blur after 40px; insight progress hairline */
  var hdr = $('.hdr'), bar = $('.progress i');
  function onScroll() {
    var y = window.scrollY;
    if (hdr) hdr.classList.toggle('scrolled', y > 40);
    if (bar) { var h = root.scrollHeight - innerHeight; bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(1, y / h) : 0) + ')'; }
    drawRivers(y);
  }

  /* mobile menu + mega menu */
  var menuBtn = $('.menu-btn'), nav = $('#nav');
  if (menuBtn) menuBtn.addEventListener('click', function () {
    var o = nav.classList.toggle('open'); menuBtn.setAttribute('aria-expanded', o);
  });
  var megaBtns = $$('.mega-btn'), megaBtn = megaBtns[0];
  function setMega(o, only) {
    megaBtns.forEach(function (b) {
      var m = doc.getElementById(b.getAttribute('aria-controls')), open = o && (!only || b === only);
      if (m) m.classList.toggle('open', open); b.setAttribute('aria-expanded', open);
    });
  }
  if (megaBtn) {
    megaBtns.forEach(function (b) {
      b.addEventListener('click', function (e) { e.stopPropagation(); setMega(b.getAttribute('aria-expanded') !== 'true', b); });
    });
    doc.addEventListener('click', function (e) { if (!e.target.closest('.has-mega')) setMega(false); });
  }

  /* reveal with 60ms stagger + counters */
  function count(el) {
    var end = +el.dataset.count, t0 = null;
    if (reduce) { el.textContent = end.toLocaleString('en-US'); return; }
    function f(t) { t0 = t0 || t; var p = Math.min((t - t0) / 1400, 1); el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3))).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(f); }
    requestAnimationFrame(f);
  }
  var rv = $$('.rv'), nums = $$('[data-count]');
  if ('IntersectionObserver' in window && !reduce) {
    rv.forEach(function (el) {
      var sib = el.parentNode ? Array.prototype.indexOf.call(el.parentNode.children, el) : 0;
      el.style.transitionDelay = Math.min(sib, 6) * 60 + 'ms';
    });
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.classList.add('in'); if (e.target.dataset.count) count(e.target); io.unobserve(e.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    rv.concat(nums).forEach(function (e) { io.observe(e); });
  } else { rv.forEach(function (e) { e.classList.add('in'); }); nums.forEach(count); }

  /* Living Delta Map: rivers draw with scroll; hotspot <-> legend linking */
  var map = $('.dmap'), rivers = map ? $$('.river', map) : [];
  function drawRivers(y) {
    if (!rivers.length || reduce || !map.classList.contains('drawn')) return;
    var p = Math.min(1, 0.6 + (y || 0) / 500);
    rivers.forEach(function (r, i) { r.style.strokeDashoffset = Math.max(0, 1 - p * (1 + i * 0.05)).toFixed(3); });
  }
  if (map) {
    if (reduce) rivers.forEach(function (r) { r.style.strokeDashoffset = 0; });
    else requestAnimationFrame(function () { requestAnimationFrame(function () { map.classList.add('drawn'); drawRivers(window.scrollY); }); });
    var link = function (area, on) {
      $$('.map-box [data-area="' + area + '"]').forEach(function (el) { el.classList.toggle('on', on); });
    };
    $$('.map-box [data-area]').forEach(function (el) {
      ['mouseenter', 'focus'].forEach(function (ev) { el.addEventListener(ev, function () { link(el.dataset.area, true); }); });
      ['mouseleave', 'blur'].forEach(function (ev) { el.addEventListener(ev, function () { link(el.dataset.area, false); }); });
    });
  }

  /* magnetic buttons (desktop only) */
  if (finePointer && !reduce) $$('.btn').forEach(function (b) {
    b.addEventListener('pointermove', function (e) {
      var r = b.getBoundingClientRect(), x = (e.clientX - r.left - r.width / 2) / r.width, y = (e.clientY - r.top - r.height / 2) / r.height;
      b.style.transform = 'translate(' + (x * 6).toFixed(1) + 'px,' + (y * 4).toFixed(1) + 'px)';
    });
    b.addEventListener('pointerleave', function () { b.style.transform = ''; });
  });

  /* filter chips: groups by data-key; items carry data-<key> (space-separated lists allowed) */
  $$('[data-filter-list]').forEach(function (box) {
    var list = doc.getElementById(box.dataset.filterList), state = {};
    $$('.chip', box).forEach(function (c) {
      c.addEventListener('click', function () {
        var k = c.dataset.key;
        $$('.chip[data-key="' + k + '"]', box).forEach(function (o) { o.setAttribute('aria-pressed', o === c); });
        state[k] = c.dataset.val; var shown = 0;
        $$('[data-item]', list).forEach(function (it) {
          var ok = Object.keys(state).every(function (key) { return state[key] === 'all' || (' ' + (it.dataset[key] || '') + ' ').indexOf(' ' + state[key] + ' ') > -1; });
          it.classList.toggle('hidden', !ok); if (ok) shown++;
        });
        var em = list.parentNode.querySelector('.empty'); if (em) em.classList.toggle('hidden', shown > 0);
      });
    });
  });

  /* SDG lens */
  var work = $('#work');
  $$('.lens-b').forEach(function (b) {
    b.addEventListener('click', function () {
      $$('.lens-b').forEach(function (o) { o.setAttribute('aria-pressed', o === b); });
      var n = b.dataset.sdg;
      work.className = 'work' + (n ? ' lensing lens-' + n : '');
      $$('.wt', work).forEach(function (t) { t.classList.toggle('hit', !!n && (' ' + t.dataset.sdgs + ' ').indexOf(' ' + n + ' ') > -1); });
    });
  });

  /* SHA-256 helper */
  function sha256(text) {
    if (!(window.crypto && crypto.subtle && window.TextEncoder)) return Promise.resolve('(computed on the server)');
    return crypto.subtle.digest('SHA-256', new TextEncoder().encode(text)).then(function (b) {
      return Array.prototype.map.call(new Uint8Array(b), function (x) { return ('0' + x.toString(16)).slice(-2); }).join('');
    });
  }

  /* generic validation */
  function validate(form) {
    var ok = true;
    $$('[data-req]', form).forEach(function (inp) {
      var f = inp.closest('.field') || inp.parentNode, v = inp.type === 'checkbox' ? inp.checked : inp.value.trim(), bad = !v;
      if (!bad && inp.type === 'email') bad = !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v);
      if (!bad && inp.dataset.min) bad = v.length < +inp.dataset.min;
      f.classList.toggle('bad', bad); inp.setAttribute('aria-invalid', bad);
      if (bad && ok) { inp.focus(); ok = false; }
    });
    return ok;
  }
  var vf = $('.verify-form');
  if (vf) vf.addEventListener('submit', function (ev) {
    ev.preventDefault(); var t = $('#v-t').value.trim(), o = $('.v-out', vf);
    if (!t) { o.textContent = 'Paste some text first.'; return; }
    sha256(t).then(function (h) {
      var row = $$('.ledger .hash').filter(function (c) { return c.title === h; })[0];
      o.textContent = h + (row ? '  ✓ matches ' + row.parentNode.firstElementChild.textContent : '  · no match on this page');
    });
  });
  /* EN / বাংলা headline switch */
  var langBtn = $('[data-lang-toggle]');
  function setLang(bn) {
    $$('[data-bn]').forEach(function (el) {
      if (el.dataset.en === undefined) el.dataset.en = el.innerHTML;
      if (bn) { el.textContent = el.dataset.bn; el.lang = 'bn'; } else { el.innerHTML = el.dataset.en; el.removeAttribute('lang'); }
    });
    root.dataset.lang = bn ? 'bn' : 'en';
    if (langBtn) { langBtn.setAttribute('aria-pressed', bn); langBtn.innerHTML = bn ? 'English' : '<span class="bn">বাংলা</span>'; langBtn.title = bn ? 'Switch headlines to English' : 'Switch headlines to Bangla'; }
  }
  if (langBtn) {
    langBtn.addEventListener('click', function () { var bn = root.dataset.lang !== 'bn'; setLang(bn); store('di-lang', bn ? 'bn' : 'en'); });
    if (store('di-lang') === 'bn') setLang(true);
  }

  /* print + copy */
  $$('[data-print]').forEach(function (b) { b.addEventListener('click', function () { window.print(); }); });
  $$('[data-copy-from]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = $(b.dataset.copyFrom).textContent, done = function () { b.textContent = 'Copied'; setTimeout(function () { b.textContent = 'Copy boilerplate'; }, 1800); };
      if (navigator.clipboard) navigator.clipboard.writeText(t).then(done, done); else done();
    });
  });

  /* command-K search */
  var ck = $('#cmdk'), q = $('#cmdk-q'), res = $('#cmdk-r'), fT = $('#cmdk-type'), fA = $('#cmdk-area'), fS = $('#cmdk-sdg'), sel = 0, lastFocus = null;
  var IDX = SEARCH_INDEX;
  function render() {
    var words = q.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
    var hits = IDX.filter(function (it) {
      if (fT.value && it.k !== fT.value) return false;
      if (fA.value && it.a !== fA.value) return false;
      if (fS.value && it.s.indexOf(+fS.value) < 0) return false;
      var hay = (it.t + ' ' + it.d + ' ' + it.k).toLowerCase();
      return words.every(function (w) { return hay.indexOf(w) > -1; });
    }).slice(0, 12);
    sel = 0; res.innerHTML = '';
    if (!hits.length) { var li = doc.createElement('li'); li.className = 'none'; li.textContent = 'No results. Try another word or clear a filter.'; res.appendChild(li); return; }
    hits.forEach(function (it, i) {
      var li = doc.createElement('li'), a = doc.createElement('a'), b = doc.createElement('b'), s = doc.createElement('small'), e = doc.createElement('em');
      a.href = it.u; a.setAttribute('role', 'option'); a.setAttribute('aria-selected', i === 0);
      b.textContent = it.t; s.textContent = it.m ? it.m + ' · ' + it.d : it.d; e.textContent = it.k;
      a.appendChild(b); a.appendChild(s); a.appendChild(e); li.appendChild(a); res.appendChild(li);
    });
  }
  function openS() { if (!ck) return; lastFocus = doc.activeElement; ck.hidden = false; root.classList.add('locked'); render(); q.focus(); }
  function closeS() { if (!ck || ck.hidden) return; ck.hidden = true; root.classList.remove('locked'); if (lastFocus) lastFocus.focus(); }
  function move(d) {
    var as = $$('a', res); if (!as.length) return;
    sel = (sel + d + as.length) % as.length;
    as.forEach(function (a, i) { a.setAttribute('aria-selected', i === sel); }); as[sel].scrollIntoView({ block: 'nearest' });
  }
  if (ck) {
    $$('[data-open-search]').forEach(function (b) { b.addEventListener('click', function () { if (nav) nav.classList.remove('open'); openS(); }); });
    $$('[data-close-search]').forEach(function (b) { b.addEventListener('click', closeS); });
    ck.addEventListener('click', function (e) { if (e.target === ck) closeS(); });
    [q, fT, fA, fS].forEach(function (el) { el.addEventListener('input', render); });
    doc.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); if (ck.hidden) openS(); else closeS(); return; }
      if (e.key === '/' && ck.hidden && !/input|textarea|select/i.test(doc.activeElement.tagName)) { e.preventDefault(); openS(); return; }
      if (ck.hidden) { if (e.key === 'Escape') setMega(false); return; }
      if (e.key === 'Escape') closeS();
      else if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
      else if (e.key === 'Enter' && doc.activeElement === q) { var a = $$('a', res)[sel]; if (a) location.href = a.href; }
      else if (e.key === 'Tab') {
        var f = $$('input,select,button,a', ck), first = f[0], last = f[f.length - 1];
        if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
  }

  $$('[data-year]').forEach(function (e) { e.textContent = new Date().getFullYear(); });
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
})();
