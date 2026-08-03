const root = document.documentElement;
const isArabic = root.dir === 'rtl';
const main = document.querySelector('main#main');
const compact = window.matchMedia('(max-width: 700px)').matches;

const imageWidth = compact ? 640 : 960;
const media = {
  hero: `https://images.pexels.com/photos/3829548/pexels-photo-3829548.jpeg?auto=compress&cs=tinysrgb&w=${imageWidth}`,
  polished: `https://images.pexels.com/photos/20849333/pexels-photo-20849333.jpeg?auto=compress&cs=tinysrgb&w=${compact ? 560 : 760}`,
  detail: `https://images.pexels.com/photos/269257/pexels-photo-269257.jpeg?auto=compress&cs=tinysrgb&w=${compact ? 640 : 900}`,
};

if (main) {
  const heroPanel = main.querySelector('.hero-art .art-panel-main');
  if (heroPanel instanceof HTMLElement) {
    heroPanel.style.backgroundImage = `linear-gradient(180deg,rgba(8,22,35,.05),rgba(8,22,35,.58)),url('${media.hero}')`;
    heroPanel.style.backgroundPosition = 'center';
    heroPanel.style.backgroundSize = 'cover';
  }

  const signalStrip = main.querySelector('.signal-strip');
  const assessment = main.querySelector('.assessment-section');
  const mount = signalStrip ?? assessment;

  const gallery = document.createElement('section');
  gallery.className = 'experience-gallery deferred-section';
  gallery.innerHTML = `
    <div class="container">
      <div class="section-topline"><div>
        <p class="eyebrow">${isArabic ? 'عناية متخصصة بأدوات المائدة' : 'Specialist cutlery care'}</p>
        <h2>${isArabic ? 'الخدمة تبدأ من حالة المعدن ونوع الستانلس ستيل.' : 'The service starts with the metal and stainless-steel condition.'}</h2>
      </div></div>
      <div class="experience-gallery-grid">
        <article class="experience-card experience-card-large">
          <img src="${media.hero}" width="960" height="720" alt="${isArabic ? 'تلميع شوكة من الستانلس ستيل في ورشة متخصصة' : 'Stainless-steel fork being professionally polished'}" loading="lazy" decoding="async">
          <div class="experience-card-overlay"><span>${isArabic ? 'تلميع احترافي' : 'Professional polishing'}</span><h3>${isArabic ? 'معالجة البهتان وآثار الاستخدام وتحسين تجانس السطح.' : 'Addressing dullness, use marks and inconsistent surface appearance.'}</h3></div>
        </article>
        <article class="experience-card">
          <img src="${media.polished}" width="760" height="560" alt="${isArabic ? 'أدوات مائدة من الستانلس ستيل بعد العناية' : 'Stainless-steel cutlery with a refreshed finish'}" loading="lazy" decoding="async">
          <div class="experience-card-overlay"><span>${isArabic ? 'اللمسة النهائية' : 'Finish quality'}</span><h3>${isArabic ? 'فحص المظهر قبل إعادة القطع إلى التشغيل.' : 'The finish is reviewed before items return to service.'}</h3></div>
        </article>
      </div>
    </div>`;

  const comparison = document.createElement('section');
  comparison.className = 'comparison-section deferred-section';
  comparison.innerHTML = `
    <div class="container">
      <div class="comparison-heading"><div><p class="eyebrow light-eyebrow">${isArabic ? 'مقارنة تفاعلية' : 'Interactive comparison'}</p><h2>${isArabic ? 'حرّك المؤشر لمقارنة سطح باهت بلمسة مصقولة.' : 'Move the control to compare a tired surface with a polished finish.'}</h2></div><p>${isArabic ? 'عرض توضيحي فقط؛ يتم تقييم كل قطعة حسب نوع المعدن وحالة السطح.' : 'Illustrative only; every item is assessed by metal type and surface condition.'}</p></div>
      <div class="comparison" data-comparison style="--position:52%">
        <img class="comparison-before" src="${media.detail}" width="900" height="620" alt="${isArabic ? 'سطح باهت قبل التلميع' : 'Tired surface before polishing'}" loading="lazy" decoding="async" draggable="false">
        <div class="comparison-after-wrap"><img src="${media.detail}" width="900" height="620" alt="${isArabic ? 'سطح مصقول بعد المعالجة' : 'Polished surface after treatment'}" loading="lazy" decoding="async" draggable="false"></div>
        <span class="comparison-label comparison-label-before">${isArabic ? 'قبل' : 'Before'}</span>
        <span class="comparison-label comparison-label-after">${isArabic ? 'بعد' : 'After'}</span>
        <span class="comparison-handle" aria-hidden="true"></span>
        <input class="comparison-range" type="range" min="4" max="96" value="52" aria-label="${isArabic ? 'تحريك مقارنة قبل وبعد' : 'Move before and after comparison'}">
      </div>
    </div>`;

  if (mount === signalStrip) {
    signalStrip.after(gallery, comparison);
  } else if (assessment) {
    assessment.before(gallery, comparison);
  }

  const comparisonEl = comparison.querySelector('[data-comparison]');
  const range = comparison.querySelector('.comparison-range');
  if (comparisonEl instanceof HTMLElement && range instanceof HTMLInputElement) {
    const setPosition = (value) => {
      const bounded = Math.max(4, Math.min(96, Number(value)));
      comparisonEl.style.setProperty('--position', `${bounded}%`);
      range.value = String(bounded);
    };

    range.addEventListener('input', () => setPosition(range.value), { passive: true });

    let dragging = false;
    const positionFromPointer = (clientX) => {
      const rect = comparisonEl.getBoundingClientRect();
      if (rect.width > 0) setPosition(((clientX - rect.left) / rect.width) * 100);
    };

    comparisonEl.addEventListener('pointerdown', (event) => {
      dragging = true;
      comparisonEl.setPointerCapture?.(event.pointerId);
      positionFromPointer(event.clientX);
    });
    comparisonEl.addEventListener('pointermove', (event) => {
      if (dragging) positionFromPointer(event.clientX);
    });
    comparisonEl.addEventListener('pointerup', () => { dragging = false; });
    comparisonEl.addEventListener('pointercancel', () => { dragging = false; });
    setPosition(range.value);
  }
}

const releaseScroll = () => {
  if (!document.body.classList.contains('nav-open')) {
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';
  }
};
window.addEventListener('pageshow', releaseScroll, { passive: true });
window.addEventListener('orientationchange', releaseScroll, { passive: true });
