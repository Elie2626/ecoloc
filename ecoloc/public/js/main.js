/* ECO'LOC — main.js */

// ── Navbar scroll effect ───────────────────────────
const nav = document.getElementById('mainNav');
if (nav) {
  const onScroll = () => {
    nav.classList.toggle('scrolled', window.scrollY > 40);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Force scrolled state on pages with dark headers (auth, catalogue, detail)
  const hasFullHeader = document.querySelector(
    '.auth-page, .catalogue-header, .mat-detail-header'
  );
  if (hasFullHeader) nav.classList.add('scrolled');
}

// ── Fade-in on scroll (Intersection Observer) ──────
const fadeEls = document.querySelectorAll(
  '.stat-card, .step-card, .mat-card, .mat-info-card, .mat-borrow-card, .card-hover, .detail-info, .detail-proprio'
);

if ('IntersectionObserver' in window && fadeEls.length) {
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
        }, i * 60);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  fadeEls.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
    obs.observe(el);
  });
}

// ── Counter animation for stats ────────────────────
const counters = document.querySelectorAll('.stat-card h3');
const countObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el = e.target;
    const target = parseInt(el.textContent.replace(/\D/g, ''));
    const prefix = el.textContent.includes('+') ? '+' : '';
    let current = 0;
    const step = Math.ceil(target / 40);
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = prefix + current;
      if (current >= target) clearInterval(timer);
    }, 30);
    countObs.unobserve(el);
  });
}, { threshold: 0.5 });

counters.forEach(c => countObs.observe(c));