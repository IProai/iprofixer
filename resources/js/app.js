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

const rfqForm = document.querySelector('form.quick-assessment');

if (rfqForm instanceof HTMLFormElement) {
  rfqForm.enctype = 'multipart/form-data';

  const submitButton = rfqForm.querySelector('button[type="submit"]');
  const field = document.createElement('label');
  const isArabic = document.documentElement.dir === 'rtl';

  field.className = 'full-field';
  field.textContent = isArabic
    ? 'صور الحالة أو قائمة الجرد (اختياري)'
    : 'Condition photos or inventory (optional)';

  const input = document.createElement('input');
  input.type = 'file';
  input.name = 'attachments[]';
  input.multiple = true;
  input.accept = '.jpg,.jpeg,.png,.webp,.pdf';
  input.setAttribute('aria-describedby', 'rfq-attachment-guidance');

  const guidance = document.createElement('small');
  guidance.id = 'rfq-attachment-guidance';
  guidance.textContent = isArabic
    ? 'حتى 5 ملفات، وبحد أقصى 10 ميجابايت لكل ملف. JPG أو PNG أو WEBP أو PDF.'
    : 'Up to 5 files, maximum 10 MB each. JPG, PNG, WEBP or PDF.';

  field.append(input, guidance);

  if (submitButton) {
    rfqForm.insertBefore(field, submitButton);
  } else {
    rfqForm.append(field);
  }
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
