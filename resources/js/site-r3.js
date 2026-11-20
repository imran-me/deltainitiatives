/* Generated from deltainitiatives-v3/assets/js/r3.js by port.py. */
/* Delta Initiatives v3, round 3: lightbox, reading options, reading list, sharing, feed (reactions,
   infinite scroll, updates dot). Works on the static site (mock data, localStorage) and in Laravel
   (data-react-url / data-latest-url / data-next point at real endpoints). No inline handlers. */
(function () {
  'use strict';
  var doc = document, root = doc.documentElement;
  function $(s, c) { return (c || doc).querySelector(s); }
  function $$(s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); }
  function store(k, v) {
    try { if (v === undefined) return JSON.parse(localStorage.getItem(k) || 'null'); localStorage.setItem(k, JSON.stringify(v)); } catch (e) { return null; }
  }
  function csrf() { var m = $('meta[name="csrf-token"]'); return m ? m.content : ''; }

  /* ---------- share links + copy link ---------- */
  var here = location.href.split('#')[0];
  $$('[data-share]').forEach(function (a) {
    var u = encodeURIComponent(here), t = encodeURIComponent(doc.title), k = a.dataset.share;
    if (k === 'x') a.href = 'https://twitter.com/intent/tweet?text=' + t + '&url=' + u;
    if (k === 'linkedin') a.href = 'https://www.linkedin.com/sharing/share-offsite/?url=' + u;
    if (k === 'facebook') a.href = 'https://www.facebook.com/sharer/sharer.php?u=' + u;
    if (k === 'whatsapp') a.href = 'https://wa.me/?text=' + t + '%20' + u;
    if (k === 'email') a.href = 'mailto:?subject=' + t + '&body=' + u;
  });
  doc.addEventListener('click', function (e) {
    var b = e.target.closest('[data-copy-link]');
    if (!b) return;
    var url = b.dataset.url ? new URL(b.dataset.url, location.href).href : here;
    var label = b.textContent, done = function () { b.textContent = 'Link copied'; setTimeout(function () { b.textContent = label; }, 1600); };
    if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, done); else done();
  });

  /* ---------- reading options (idea and article pages) ---------- */
  var bar = $('[data-reader]'), art = $('.article.idea-art') || $('article.article');
  if (bar && art) {
    var scale = store('di-read-scale') || 1;
    var apply = function () { art.style.setProperty('--read-scale', String(scale)); };
    apply();
    var mode = $('[data-read="mode"]', bar);
    if (store('di-reading-mode')) { root.classList.add('reading'); mode.setAttribute('aria-pressed', 'true'); }
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('[data-read]'); if (!b) return;
      var k = b.dataset.read;
      if (k === 'mode') { var on = root.classList.toggle('reading'); b.setAttribute('aria-pressed', on); store('di-reading-mode', on); }
      if (k === 'smaller') { scale = Math.max(0.85, Math.round((scale - 0.1) * 100) / 100); apply(); store('di-read-scale', scale); }
      if (k === 'larger') { scale = Math.min(1.4, Math.round((scale + 0.1) * 100) / 100); apply(); store('di-read-scale', scale); }
    });
  }

  /* ---------- reading list (localStorage) ---------- */
  function list() { return store('di-reading-list') || []; }
  function renderList() {
    $$('[data-reading-list]').forEach(function (box) {
      var ul = $('ul', box), hint = $('.hint', box), items = list();
      ul.innerHTML = '';
      items.forEach(function (it) {
        var li = doc.createElement('li'), a = doc.createElement('a'), x = doc.createElement('button');
        a.href = it.url; a.textContent = it.title;
        x.type = 'button'; x.className = 'rl-x'; x.textContent = '×'; x.setAttribute('aria-label', 'Remove ' + it.title);
        x.addEventListener('click', function () { store('di-reading-list', list().filter(function (o) { return o.url !== it.url; })); renderList(); });
        li.appendChild(a); li.appendChild(doc.createTextNode(' ')); li.appendChild(x); ul.appendChild(li);
      });
      if (hint) hint.hidden = items.length > 0;
    });
  }
  $$('[data-save]').forEach(function (b) {
    var url = location.pathname + location.search;
    var saved = function () { return list().some(function (o) { return o.url === url; }); };
    var sync = function () { var s = saved(); b.setAttribute('aria-pressed', s); b.textContent = s ? 'Saved to reading list' : 'Save to reading list'; };
    sync();
    b.addEventListener('click', function () {
      var items = list().filter(function (o) { return o.url !== url; });
      if (!saved()) items.unshift({ url: url, title: b.dataset.title || doc.title });
      store('di-reading-list', items.slice(0, 50)); sync(); renderList();
    });
  });
  renderList();

  /* ---------- lightbox: keyboard, swipe, captions, credits ---------- */
  var gal = $('[data-lightbox]');
  if (gal) {
    var lb = doc.createElement('div');
    lb.className = 'lb'; lb.hidden = true; lb.setAttribute('role', 'dialog'); lb.setAttribute('aria-modal', 'true'); lb.setAttribute('aria-label', 'Photo viewer');
    lb.innerHTML = '<span class="lb-i" aria-live="polite"></span><button type="button" class="lb-x" aria-label="Close">×</button>' +
      '<button type="button" class="lb-p" aria-label="Previous photo">‹</button><button type="button" class="lb-n" aria-label="Next photo">›</button>' +
      '<img alt=""><p class="lb-cap"></p>';
    doc.body.appendChild(lb);
    var lbImg = $('img', lb), lbCap = $('.lb-cap', lb), lbI = $('.lb-i', lb), items = [], cur = 0, opener = null;
    var visible = function () { return $$('[data-lb]', gal).filter(function (a) { return !a.classList.contains('hidden'); }); };
    var show = function (i) {
      cur = (i + items.length) % items.length;
      var a = items[cur], im = $('img', a);
      lbImg.src = a.getAttribute('href'); lbImg.alt = im ? im.alt : '';
      lbCap.textContent = a.dataset.caption || '';
      if (a.dataset.credit) {
        var sm = doc.createElement('small'), link = doc.createElement('a');
        sm.textContent = a.dataset.credit + ' · ';
        link.href = a.dataset.source || '#'; link.target = '_blank'; link.rel = 'noopener'; link.textContent = 'Source';
        sm.appendChild(link); lbCap.appendChild(sm);
      }
      lbI.textContent = (cur + 1) + ' / ' + items.length;
    };
    var open = function (a) { items = visible(); opener = a; lb.hidden = false; root.classList.add('locked'); show(items.indexOf(a)); $('.lb-x', lb).focus(); };
    var close = function () { lb.hidden = true; root.classList.remove('locked'); lbImg.removeAttribute('src'); if (opener) opener.focus(); };
    gal.addEventListener('click', function (e) { var a = e.target.closest('[data-lb]'); if (!a) return; e.preventDefault(); open(a); });
    $('.lb-x', lb).addEventListener('click', close);
    $('.lb-p', lb).addEventListener('click', function () { show(cur - 1); });
    $('.lb-n', lb).addEventListener('click', function () { show(cur + 1); });
    lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
    doc.addEventListener('keydown', function (e) {
      if (lb.hidden) return;
      if (e.key === 'Escape') close();
      else if (e.key === 'ArrowLeft') show(cur - 1);
      else if (e.key === 'ArrowRight') show(cur + 1);
      else if (e.key === 'Tab') {
        var f = $$('button', lb), i = f.indexOf(doc.activeElement);
        e.preventDefault(); f[(i + (e.shiftKey ? -1 : 1) + f.length) % f.length].focus();
      }
    });
    var x0 = null;
    lb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', function (e) {
      if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0; x0 = null;
      if (Math.abs(dx) > 40) show(cur + (dx < 0 ? 1 : -1));
    });
  }

  /* ---------- feed: reactions ---------- */
  function bindReactions(scope) {
    $$('.post', scope).forEach(function (post) {
      if (post.dataset.bound) return; post.dataset.bound = '1';
      var url = post.dataset.reactUrl, id = post.dataset.post, mine = store('di-reacts') || {};
      $$('[data-react]', post).forEach(function (b) {
        var key = id + ':' + b.dataset.react;
        if (mine[key]) b.setAttribute('aria-pressed', 'true');
        b.addEventListener('click', function () {
          var on = b.getAttribute('aria-pressed') !== 'true', cnt = $('[data-n]', b), n = +cnt.textContent || 0;
          b.setAttribute('aria-pressed', on); cnt.textContent = Math.max(0, n + (on ? 1 : -1));
          var m = store('di-reacts') || {}; if (on) m[key] = 1; else delete m[key]; store('di-reacts', m);
          if (!url) return;
          b.disabled = true;
          fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ type: b.dataset.react }) })
            .then(function (r) { if (!r.ok) throw r; return r.json(); })
            .then(function (d) {
              $$('[data-react]', post).forEach(function (o) { if (d.counts && d.counts[o.dataset.react] !== undefined) $('[data-n]', o).textContent = d.counts[o.dataset.react]; });
              b.setAttribute('aria-pressed', !!d.reacted);
            })
            .catch(function () { b.setAttribute('aria-pressed', !on); cnt.textContent = n; })
            .then(function () { b.disabled = false; });
        });
      });
    });
  }
  bindReactions(doc);

  /* ---------- feed: infinite scroll ---------- */
  var more = $('[data-feed-more]'), feedList = $('#feed-list');
  if (more && feedList) {
    var loading = false;
    var load = function () {
      var next = more.dataset.next;
      if (loading || !next) { if (!next) more.classList.add('done'); return; }
      loading = true;
      fetch(next, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          var tpl = doc.createElement('template'); tpl.innerHTML = html;
          var page = tpl.content.querySelector('[data-next]');
          $$('.post', tpl.content).forEach(function (p) { p.classList.add('in'); feedList.appendChild(p); });
          bindReactions(feedList);
          more.dataset.next = page ? page.dataset.next : '';
          if (!more.dataset.next) more.classList.add('done');
        })
        .catch(function () { more.textContent = 'Could not load more posts.'; })
        .then(function () { loading = false; });
    };
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (es) { if (es[0].isIntersecting) load(); }, { rootMargin: '400px' }).observe(more);
    } else { load(); }
  }

  /* ---------- feed: posts / photos view toggle (?view=grid opens photos) ---------- */
  var views = $('[data-views]');
  if (views) {
    var setView = function (v) {
      $$('[data-view]', views).forEach(function (p) { p.hidden = p.dataset.view !== v; });
      $$('[data-view-btn]').forEach(function (b) { b.setAttribute('aria-pressed', b.dataset.viewBtn === v); });
    };
    $$('[data-view-btn]').forEach(function (b) {
      b.addEventListener('click', function () {
        setView(b.dataset.viewBtn);
        var u = new URL(location.href); if (b.dataset.viewBtn === 'grid') u.searchParams.set('view', 'grid'); else u.searchParams.delete('view');
        history.replaceState(null, '', u.toString());
      });
    });
    setView(new URLSearchParams(location.search).get('view') === 'grid' ? 'grid' : 'list');
  }

  /* ---------- insights library: category, tag, search, pagination from the URL ---------- */
  var libBox = $('[data-lib]');
  if (libBox) {
    var qs = new URLSearchParams(location.search), cat = qs.get('category') || '', tag = qs.get('tag') || '', q = (qs.get('q') || '').toLowerCase().trim();
    var per = +libBox.dataset.perPage || 6, pageNo = Math.max(1, parseInt(qs.get('page') || '1', 10) || 1);
    var input = $('.lib-search input'); if (input) input.value = qs.get('q') || '';
    $$('a.chip').forEach(function (c) {
      var u = new URL(c.href), cc = u.searchParams.get('category') || '', tt = u.searchParams.get('tag') || '';
      if (tt ? tt === tag : (cc === cat && !tag)) c.setAttribute('aria-current', 'page');
    });
    var cards = $$('[data-item]', libBox), hits = cards.filter(function (c) {
      return (!cat || c.dataset.type === cat) && (!tag || (' ' + c.dataset.tags + ' ').indexOf(' ' + tag + ' ') > -1) &&
        (!q || q.split(/\s+/).every(function (w) { return (c.dataset.text + ' ' + c.dataset.tags).indexOf(w) > -1; }));
    });
    var pages = Math.max(1, Math.ceil(hits.length / per)); pageNo = Math.min(pageNo, pages);
    cards.forEach(function (c) { c.classList.add('hidden'); });
    hits.slice((pageNo - 1) * per, pageNo * per).forEach(function (c) { c.classList.remove('hidden'); });
    $('.empty', libBox).classList.toggle('hidden', hits.length > 0);
    $('.lib-status', libBox).textContent = (cat || tag || q) ? hits.length + ' result' + (hits.length === 1 ? '' : 's') + (q ? ' for “' + q + '”' : '') + (tag ? ' tagged #' + tag : '') : '';
    var pager = $('.pager', libBox), mk = function (n, label) {
      var p = new URLSearchParams(location.search); p.set('page', n);
      var cur = n === pageNo && !label, a = doc.createElement(cur ? 'span' : 'a');
      a.className = 'pg' + (cur ? ' cur' : '') + (label ? ' next' : '');
      if (cur) a.setAttribute('aria-current', 'page'); else a.href = '?' + p.toString();
      a.textContent = label || String(n); return a;
    };
    if (pages > 1) {
      if (pageNo > 1) pager.appendChild(mk(pageNo - 1, '← Previous'));
      for (var n = 1; n <= pages; n++) pager.appendChild(mk(n));
      if (pageNo < pages) pager.appendChild(mk(pageNo + 1, 'Next →'));
    }
  }

  /* ---------- feed: "updates" dot in the nav ---------- */
  var fl = $('[data-feed-link]');
  if (fl) {
    var seen = store('di-feed-seen'), page = $('[data-feed-page]');
    var mark = function (latest) {
      if (!latest) return;
      if (page) { store('di-feed-seen', latest); return; }
      var dot = $('.dot', fl); if (dot) dot.hidden = !!(seen && seen >= latest);
    };
    if (fl.dataset.latestUrl) {
      fetch(fl.dataset.latestUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.ok ? r.json() : {}; }).then(function (d) { mark(d.latest); }).catch(function () {});
    } else { mark(fl.dataset.latest); }
  }
})();
