import './bootstrap';
import '../css/experience.css';
import './experience';

const documentRoot = document.documentElement;
const siteHeader = document.querySelector('.site-header');
const menuToggle = document.querySelector('.menu-toggle');
const siteNav = document.querySelector('#site-nav');

const isArabic = documentRoot.dir === 'rtl';

if (siteHeader instanceof HTMLElement) {
  const syncHeaderState = () => {
    siteHeader.classList.toggle('is-scrolled', window.scrollY > 18);
  };

  syncHeaderState();
  window.addEventListener('scroll', syncHeaderState, { passive: true });
}

if (menuToggle instanceof HTMLButtonElement && siteNav instanceof HTMLElement) {
  menuToggle.classList.add('menu-toggle-enhanced');

  const setMenuState = (isOpen) => {
    siteNav.classList.toggle('is-open', isOpen);
    document.body.classList.toggle('nav-open', isOpen);
    menuToggle.classList.toggle('is-open', isOpen);
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.setAttribute(
      'aria-label',
      isOpen
        ? isArabic
          ? 'إغلاق القائمة الرئيسية'
          : 'Close main menu'
        : isArabic
          ? 'فتح القائمة الرئيسية'
          : 'Open main menu',
    );
  };

  const closeMenu = () => setMenuState(false);

  menuToggle.addEventListener('click', () => {
    setMenuState(!siteNav.classList.contains('is-open'));
  });

  siteNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && siteNav.classList.contains('is-open')) {
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
  rfqForm.classList.add('consultation-form-enhanced');

  const submitButton = rfqForm.querySelector('button[type="submit"]');
  const field = document.createElement('label');

  field.className = 'full-field upload-field';

  const uploadTitle = document.createElement('span');
  uploadTitle.className = 'upload-title';
  uploadTitle.textContent = isArabic
    ? 'صور الحالة أو قائمة الجرد'
    : 'Condition photos or inventory';

  const uploadOptional = document.createElement('small');
  uploadOptional.className = 'upload-optional';
  uploadOptional.textContent = isArabic ? 'اختياري' : 'Optional';

  const uploadHeading = document.createElement('span');
  uploadHeading.className = 'upload-heading';
  uploadHeading.append(uploadTitle, uploadOptional);

  const input = document.createElement('input');
  input.type = 'file';
  input.name = 'attachments[]';
  input.multiple = true;
  input.accept = '.jpg,.jpeg,.png,.webp,.pdf';
  input.setAttribute('aria-describedby', 'rfq-attachment-guidance');

  const uploadSurface = document.createElement('span');
  uploadSurface.className = 'upload-surface';
  uploadSurface.innerHTML = `<strong>${
    isArabic ? 'اختر الملفات' : 'Choose files'
  }</strong><span>${
    isArabic
      ? 'أضف صوراً واضحة أو ملف جرد لتسريع المراجعة.'
      : 'Add clear photos or an inventory file to speed up the review.'
  }</span>`;

  const guidance = document.createElement('small');
  guidance.id = 'rfq-attachment-guidance';
  guidance.className = 'upload-guidance';
  guidance.textContent = isArabic
    ? 'حتى 5 ملفات، وبحد أقصى 10 ميجابايت لكل ملف. JPG أو PNG أو WEBP أو PDF.'
    : 'Up to 5 files, maximum 10 MB each. JPG, PNG, WEBP or PDF.';

  const fileSummary = document.createElement('span');
  fileSummary.className = 'upload-summary';
  fileSummary.setAttribute('aria-live', 'polite');

  input.addEventListener('change', () => {
    const files = Array.from(input.files ?? []);

    if (files.length === 0) {
      field.classList.remove('has-files');
      fileSummary.textContent = '';
      return;
    }

    field.classList.add('has-files');
    fileSummary.textContent = files
      .map((file) => file.name)
      .slice(0, 5)
      .join(' · ');
  });

  field.append(uploadHeading, input, uploadSurface, guidance, fileSummary);

  if (submitButton) {
    rfqForm.insertBefore(field, submitButton);

    const defaultLabel = submitButton.textContent?.trim() ?? '';
    submitButton.addEventListener('click', () => {
      if (!rfqForm.checkValidity()) {
        return;
      }

      submitButton.classList.add('is-submitting');
      submitButton.setAttribute('aria-busy', 'true');
      submitButton.textContent = isArabic
        ? 'جارٍ إرسال الطلب…'
        : 'Sending request…';

      window.setTimeout(() => {
        submitButton.classList.remove('is-submitting');
        submitButton.removeAttribute('aria-busy');
        submitButton.textContent = defaultLabel;
      }, 12000);
    });
  } else {
    rfqForm.append(field);
  }

  rfqForm.addEventListener(
    'invalid',
    (event) => {
      if (event.target instanceof HTMLElement) {
        event.target.closest('label')?.classList.add('has-error');
      }
    },
    true,
  );

  rfqForm.addEventListener('input', (event) => {
    if (
      event.target instanceof HTMLInputElement ||
      event.target instanceof HTMLSelectElement ||
      event.target instanceof HTMLTextAreaElement
    ) {
      if (event.target.checkValidity()) {
        event.target.closest('label')?.classList.remove('has-error');
      }
    }
  });
}

const reducedMotion = window.matchMedia(
  '(prefers-reduced-motion: reduce)',
).matches;
const revealTargets = document.querySelectorAll(
  '.section, .service-row, .industry-grid a, .signal-grid > div, .detail-card, .process-list article, .scope-list article, .proof-placeholder',
);

if (!reducedMotion && 'IntersectionObserver' in window) {
  documentRoot.classList.add('motion-ready');

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
