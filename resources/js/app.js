import './bootstrap';

const menuToggle = document.querySelector('.menu-toggle');
const siteNav = document.querySelector('#site-nav');

if (menuToggle instanceof HTMLButtonElement && siteNav instanceof HTMLElement) {
  const closeMenu = () => {
    siteNav.classList.remove('is-open');
    menuToggle.setAttribute('aria-expanded', 'false');
  };

  menuToggle.addEventListener('click', () => {
    const willOpen = !siteNav.classList.contains('is-open');
    siteNav.classList.toggle('is-open', willOpen);
    menuToggle.setAttribute('aria-expanded', String(willOpen));
  });

  siteNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeMenu();
      menuToggle.focus();
    }
  });

  document.addEventListener('click', (event) => {
    if (
      siteNav.classList.contains('is-open') &&
      event.target instanceof Node &&
      !siteNav.contains(event.target) &&
      !menuToggle.contains(event.target)
    ) {
      closeMenu();
    }
  });

  window.addEventListener('resize', () => {
    if (window.matchMedia('(min-width: 1051px)').matches) {
      closeMenu();
    }
  });
}

const reducedMotion = window.matchMedia(
  '(prefers-reduced-motion: reduce)',
).matches;
const revealTargets = document.querySelectorAll(
  '.section, .service-row, .industry-grid a, .signal-grid > div, .page-card, .process-row',
);

if (!reducedMotion && 'IntersectionObserver' in window) {
  document.documentElement.classList.add('motion-ready');

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    },
    { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
  );

  revealTargets.forEach((target) => observer.observe(target));
}
