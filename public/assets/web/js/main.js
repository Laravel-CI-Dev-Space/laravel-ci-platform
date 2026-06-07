// ================================
// HEADER — sticky + scroll shadow
// ================================
(function () {
  const header = document.querySelector('.site-header');
  if (!header) return;
  const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ================================
// MOBILE MENU — close on link click
// ================================
(function () {
  const collapse = document.getElementById('mainNav');
  if (!collapse) return;
  collapse.querySelectorAll('.nav-link').forEach((link) => {
    link.addEventListener('click', () => {
      if (window.innerWidth < 992 && collapse.classList.contains('show')) {
        const bs = bootstrap.Collapse.getInstance(collapse) || new bootstrap.Collapse(collapse, { toggle: false });
        bs.hide();
      }
    });
  });
})();

// ================================
// COUNTER ANIMATION — stats section
// ================================
(function () {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const animate = (el) => {
    const target = parseInt(el.getAttribute('data-count'), 10);
    const dur = 1600;
    const start = performance.now();
    const step = (now) => {
      const p = Math.min((now - start) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
      el.textContent = Math.floor(eased * target).toLocaleString('en-US');
      if (p < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString('en-US');
    };
    requestAnimationFrame(step);
  };

  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        animate(e.target);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach((c) => obs.observe(c));
})();

// ================================
// GENERIC FILTER TABS (forum / blog / events)
// data-filter-group on container, data-filter on pills, data-category on items
// ================================
(function () {
  const groups = document.querySelectorAll('[data-filter-group]');
  groups.forEach((group) => {
    const pills = group.querySelectorAll('[data-filter]');
    const targetSel = group.getAttribute('data-filter-target');
    const items = targetSel ? document.querySelectorAll(targetSel) : [];

    pills.forEach((pill) => {
      pill.addEventListener('click', () => {
        pills.forEach((p) => p.classList.remove('active'));
        pill.classList.add('active');
        const filter = pill.getAttribute('data-filter');
        items.forEach((item) => {
          const cats = (item.getAttribute('data-category') || '').split(' ');
          const show = filter === 'all' || cats.includes(filter);
          item.style.display = show ? '' : 'none';
        });
      });
    });
  });
})();

// ================================
// JOBS — filter sidebar / off-canvas toggle
// (Bootstrap offcanvas handles most; just sync the save hearts)
// ================================
(function () {
  document.querySelectorAll('.save-heart').forEach((btn) => {
    btn.addEventListener('click', () => {
      btn.classList.toggle('saved');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-regular');
        icon.classList.toggle('fa-solid');
      }
    });
  });
})();

// ================================
// VOTE BUTTONS (forum detail)
// ================================
(function () {
  document.querySelectorAll('[data-vote]').forEach((group) => {
    const scoreEl = group.querySelector('.vote-score');
    let base = parseInt(scoreEl ? scoreEl.textContent : '0', 10) || 0;
    let state = 0; // -1, 0, 1
    group.querySelectorAll('.vote-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const dir = btn.getAttribute('data-dir') === 'up' ? 1 : -1;
        state = state === dir ? 0 : dir;
        group.querySelectorAll('.vote-btn').forEach((b) => b.style.color = '');
        if (state !== 0) btn.style.color = 'var(--orange)';
        if (scoreEl) scoreEl.textContent = base + state;
      });
    });
  });
})();

// ================================
// CODE BLOCKS — copy button
// ================================
(function () {
  document.querySelectorAll('.copy-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const block = btn.closest('.code-block');
      const pre = block ? block.querySelector('pre') : null;
      if (!pre) return;
      const text = pre.innerText;
      try {
        await navigator.clipboard.writeText(text);
      } catch (e) {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (_) {}
        document.body.removeChild(ta);
      }
      const original = btn.innerHTML;
      btn.classList.add('copied');
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
      setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = original; }, 1800);
    });
  });
})();

// ================================
// LOGIN — form interactions
// ================================
(function () {
  const githubBtn = document.getElementById('githubLoginBtn');
  const alertArea = document.getElementById('authAlert');
  if (githubBtn && alertArea) {
    githubBtn.addEventListener('click', (e) => {
      e.preventDefault();
      githubBtn.disabled = true;
      githubBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Connecting to GitHub…';
      setTimeout(() => {
        alertArea.className = 'alert alert-success d-flex align-items-center gap-2';
        alertArea.innerHTML = '<i class="fa-solid fa-circle-check"></i> Demo mode — GitHub OAuth would redirect here.';
        alertArea.classList.remove('d-none');
        githubBtn.disabled = false;
        githubBtn.innerHTML = '<i class="fa-brands fa-github"></i> Continue with GitHub';
      }, 1400);
    });
  }
})();

// ================================
// SCROLL-TRIGGERED REVEAL ANIMATIONS
// ================================
(function () {
  document.documentElement.classList.add('reveal-ready');

  const showAll = () => document.querySelectorAll('.reveal').forEach((r) => r.classList.add('in'));

  if (!('IntersectionObserver' in window)) { showAll(); return; }

  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        const delay = parseFloat(e.target.getAttribute('data-delay') || '0');
        setTimeout(() => e.target.classList.add('in'), delay * 1000);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.01 });

  const revealVisible = () => {
    document.querySelectorAll('.reveal:not(.in)').forEach((r) => {
      const rect = r.getBoundingClientRect();
      if (rect.top < window.innerHeight + 80 && rect.bottom > -80) {
        // Element is in (or just outside) the viewport — show immediately
        r.classList.add('in');
      } else {
        // Below fold — let IntersectionObserver handle it on scroll
        obs.observe(r);
      }
    });
  };

  revealVisible();

  // Re-run after any DOM change (Livewire morphdom, Alpine, etc.)
  if ('MutationObserver' in window) {
    let timer;
    new MutationObserver(() => {
      clearTimeout(timer);
      timer = setTimeout(revealVisible, 80);
    }).observe(document.body, { childList: true, subtree: true });
  }

  window.addEventListener('scroll', revealVisible, { passive: true });
  window.addEventListener('load', () => setTimeout(showAll, 3000));
})();

// ================================
// SCROLL-TO-TOP BUTTON
// ================================
(function () {
  const btn = document.getElementById('scrollTop');
  if (!btn) return;
  const toggle = () => btn.classList.toggle('show', window.scrollY > 500);
  window.addEventListener('scroll', toggle, { passive: true });
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  toggle();
})();

// ================================
// SALARY RANGE — live label (jobs)
// ================================
(function () {
  const range = document.getElementById('salaryRange');
  const label = document.getElementById('salaryValue');
  if (!range || !label) return;
  const update = () => {
    const v = parseInt(range.value, 10);
    label.textContent = (v / 1000).toFixed(0) + 'k FCFA/mo';
  };
  range.addEventListener('input', update);
  update();
})();

// ================================
// TABLE OF CONTENTS — scroll spy (blog detail)
// ================================
(function () {
  // Only real in-page anchors (skip share buttons with href="#")
  const links = [...document.querySelectorAll('.toc-card a[href^="#"]')]
    .filter((l) => (l.getAttribute('href') || '').length > 1);
  if (!links.length) return;
  const targets = links.map((l) => {
    try { return document.querySelector(l.getAttribute('href')); }
    catch (e) { return null; }
  }).filter(Boolean);
  if (!targets.length) return;
  const spy = () => {
    let current = targets[0];
    targets.forEach((t) => { if (t.getBoundingClientRect().top <= 120) current = t; });
    links.forEach((l) => l.classList.toggle('active', l.getAttribute('href') === '#' + (current && current.id)));
  };
  window.addEventListener('scroll', spy, { passive: true });
  spy();
})();

// ================================
// ANSWER EDITOR — preview toggle (forum detail)
// ================================
(function () {
  const toggle = document.getElementById('previewToggle');
  const textarea = document.getElementById('answerEditor');
  const preview = document.getElementById('answerPreview');
  if (!toggle || !textarea || !preview) return;
  toggle.addEventListener('click', () => {
    const showing = !preview.classList.contains('d-none');
    if (showing) {
      preview.classList.add('d-none');
      textarea.classList.remove('d-none');
      toggle.innerHTML = '<i class="fa-solid fa-eye"></i> Preview';
    } else {
      preview.textContent = textarea.value || 'Nothing to preview yet…';
      preview.classList.remove('d-none');
      textarea.classList.add('d-none');
      toggle.innerHTML = '<i class="fa-solid fa-pen"></i> Edit';
    }
  });
})();

// ================================
// CTA COMMAND — copy to clipboard
// ================================
(function () {
  document.querySelectorAll('.cta-cmd-copy').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const cmd = btn.closest('.cta-cmd');
      const text = cmd ? cmd.textContent.replace('$', '').trim() : '';
      try { await navigator.clipboard.writeText(text); } catch (e) {}
      btn.classList.add('copied');
      btn.innerHTML = '<i class="fa-solid fa-check"></i>';
      setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = '<i class="fa-regular fa-copy"></i>'; }, 1600);
    });
  });
})();

// ================================
// CUSTOM CURSOR — dot + trailing ring
// ================================
(function () {
  const fine = window.matchMedia('(pointer: fine)').matches;
  const coarse = window.matchMedia('(pointer: coarse)').matches;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!fine || coarse || reduce) return;

  const dot = document.createElement('div');
  const ring = document.createElement('div');
  dot.className = 'cursor-dot';
  ring.className = 'cursor-ring';
  document.body.appendChild(dot);
  document.body.appendChild(ring);
  document.documentElement.classList.add('cursor-fx');

  let mx = window.innerWidth / 2, my = window.innerHeight / 2;
  let rx = mx, ry = my;
  let shown = false;

  window.addEventListener('mousemove', (e) => {
    mx = e.clientX; my = e.clientY;
    dot.style.left = mx + 'px';
    dot.style.top = my + 'px';
    if (!shown) { shown = true; dot.style.opacity = 1; ring.style.opacity = 1; }
  }, { passive: true });

  document.addEventListener('mouseleave', () => { dot.style.opacity = 0; ring.style.opacity = 0; shown = false; });
  document.addEventListener('mouseenter', () => { if (shown) { dot.style.opacity = 1; ring.style.opacity = 1; } });

  const loop = () => {
    rx += (mx - rx) * 0.18;
    ry += (my - ry) * 0.18;
    ring.style.left = rx + 'px';
    ring.style.top = ry + 'px';
    requestAnimationFrame(loop);
  };
  requestAnimationFrame(loop);

  const interactive = 'a, button, .filter-pill, .vote-btn, .save-heart, .tag, .copy-btn, .cta-cmd-copy, .social-icon, input, textarea, select, [role="button"], .tag-list-item, .contributor-row';
  document.addEventListener('mouseover', (e) => {
    if (e.target.closest && e.target.closest(interactive)) ring.classList.add('is-hover');
  });
  document.addEventListener('mouseout', (e) => {
    if (e.target.closest && e.target.closest(interactive)) ring.classList.remove('is-hover');
  });
  document.addEventListener('mousedown', () => ring.classList.add('is-down'));
  document.addEventListener('mouseup', () => ring.classList.remove('is-down'));
})();

// ================================
// TOOLTIPS & GENERAL BOOTSTRAP INIT
// ================================
(function () {
  if (typeof bootstrap === 'undefined') return;
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
})();
