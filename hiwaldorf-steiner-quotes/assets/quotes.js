(function () {
  'use strict';
  const settings = window.HiSteinerQuotes || {};
  const reader = document.querySelector('.hsq-reader');
  let switching = false;

  function setText(card, selector, value) {
    const node = card.querySelector(selector);
    if (node) node.textContent = value || '';
  }
  function updateQuote(card, q) {
    card.dataset.quoteId = String(q.id);
    setText(card, '.hsq-quote-cn', q.cn);
    const original = card.querySelector('.hsq-original');
    const toggle = card.querySelector('.hsq-original-toggle');
    if (original) {
      original.hidden = true;
      original.replaceChildren();
      if (q.original) {
        const p = document.createElement('p');
        p.textContent = q.original;
        p.style.whiteSpace = 'pre-line';
        original.appendChild(p);
      }
    }
    if (toggle) {
      toggle.hidden = !q.original;
      toggle.setAttribute('aria-expanded', 'false');
      toggle.textContent = '查看原文';
    }
    const meta = card.querySelector('.hsq-meta');
    if (meta) {
      meta.replaceChildren();
      if (q.gaid) {
        const ga = document.createElement('span');
        ga.className = 'hsq-gaid';
        ga.textContent = '作品 ' + q.gaid;
        meta.appendChild(ga);
      }
      if (q.note) {
        const note = document.createElement('span');
        note.className = 'hsq-note';
        note.textContent = q.note;
        meta.appendChild(note);
      }
    }
    const share = card.querySelector('.hsq-share');
    if (share) share.dataset.url = q.url;
  }

  async function nextQuote() {
    if (!reader || switching || !settings.randomEndpoint) return;
    switching = true;
    reader.classList.add('hsq-loading');
    try {
      const current = reader.querySelector('.hsq-card');
      const url = new URL(settings.randomEndpoint);
      url.searchParams.set('exclude', current.dataset.quoteId || '0');
      const response = await fetch(url.toString(), { cache: 'no-store' });
      if (!response.ok) throw new Error('Request failed');
      const q = await response.json();
      updateQuote(current, q);
      // A copied URL always opens this exact quote.
      history.replaceState(null, '', q.url);
      setText(current, '.hsq-feedback', '');
    } catch (error) {
      const card = reader.querySelector('.hsq-card');
      setText(card, '.hsq-feedback', '暂时无法切换，请稍后再试。');
    } finally {
      reader.classList.remove('hsq-loading');
      switching = false;
    }
  }

  document.addEventListener('click', async function (event) {
    const toggle = event.target.closest('.hsq-original-toggle');
    if (toggle) {
      const card = toggle.closest('.hsq-card');
      const original = card && card.querySelector('.hsq-original');
      if (original) {
        original.hidden = !original.hidden;
        toggle.setAttribute('aria-expanded', String(!original.hidden));
        toggle.textContent = original.hidden ? '查看原文' : '收起原文';
      }
      return;
    }
    const share = event.target.closest('.hsq-share');
    if (share) {
      const url = share.dataset.url;
      try {
        if (navigator.share) {
          await navigator.share({ title: '施泰纳语录', url: url });
        } else if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(url);
          setText(share.closest('.hsq-card'), '.hsq-feedback', '已复制语录链接');
        } else {
          window.prompt('复制语录链接', url);
        }
      } catch (error) { /* cancel share */ }
      return;
    }
    if (event.target.closest('.hsq-next')) { nextQuote(); return; }
    if (!reader || !event.target.closest('.hsq-reader')) return;
    if (event.target.closest('a, button, input, textarea, select, .hsq-quotation, .hsq-original, .hsq-meta, .hsq-author, .hsq-topline')) return;
    if (window.getSelection && String(window.getSelection()).trim()) return;
    nextQuote();
  });

  document.addEventListener('keydown', function (event) {
    if (!reader || event.code !== 'Space' || event.repeat || event.altKey || event.ctrlKey || event.metaKey) return;
    const active = document.activeElement;
    if (active && active.matches('input, textarea, button, a, select, [contenteditable="true"]')) return;
    if (window.getSelection && String(window.getSelection()).trim()) return;
    event.preventDefault();
    nextQuote();
  });
}());
