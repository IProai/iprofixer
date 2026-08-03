const root = document.documentElement;
const isArabic = root.dir === 'rtl';
const main = document.querySelector('main#main');

const media = {
  hero: 'https://images.pexels.com/photos/3829548/pexels-photo-3829548.jpeg?auto=compress&cs=tinysrgb&w=1800',
  polished: 'https://images.pexels.com/photos/20849333/pexels-photo-20849333.jpeg?auto=compress&cs=tinysrgb&w=1600',
  inventory: 'https://images.pexels.com/photos/12492018/pexels-photo-12492018.jpeg?auto=compress&cs=tinysrgb&w=1400',
  detail: 'https://images.pexels.com/photos/269257/pexels-photo-269257.jpeg?auto=compress&cs=tinysrgb&w=1400',
};

if (main) {
  const heroPanel = main.querySelector('.hero-art .art-panel-main');
  if (heroPanel instanceof HTMLElement) {
    heroPanel.style.backgroundImage = `linear-gradient(180deg,rgba(8,22,35,.04),rgba(8,22,35,.58)),url('${media.hero}')`;
    heroPanel.style.backgroundPosition = 'center';
    heroPanel.style.backgroundSize = 'cover';
  }

  const signalStrip = main.querySelector('.signal-strip');
  const assessment = main.querySelector('.assessment-section');
  const mount = signalStrip ?? assessment;

  const gallery = document.createElement('section');
  gallery.className = 'experience-gallery';
  gallery.innerHTML = `
    <div class="container">
      <div class="section-topline"><div>
        <p class="eyebrow">${isArabic ? 'عناية متخصصة بأدوات المائدة' : 'Specialist cutlery care'}</p>
        <h2>${isArabic ? 'كل خدمة مرتبطة بحالة المعدن، لا بصورة مطعم عامة.' : 'Every service starts with the metal, not a generic restaurant image.'}</h2>
      </div></div>
      <div class="experience-gallery-grid">
        <article class="experience-card experience-card-large">
          <img src="${media.hero}" alt="${isArabic ? 'تلميع شوكة من الستانلس ستيل في ورشة متخصصة' : 'Stainless-steel fork being professionally polished'}" loading="eager" decoding="async">
          <div class="experience-card-overlay"><span>${isArabic ? 'تلميع احترافي' : 'Professional polishing'}</span><h3>${isArabic ? 'معالجة الخدوش والبهتان وإعادة التجانس البصري للسطح.' : 'Addressing dullness, marks and inconsistent surface appearance.'}</h3></div>
        </article>
        <div class="experience-stack">
          <article class="experience-card"><img src="${media.polished}" alt="${isArabic ? 'أدوات مائدة من الستانلس ستيل بعد العناية' : 'Stainless-steel cutlery with a refreshed finish'}" loading="lazy" decoding="async"><div class="experience-card-overlay"><span>${isArabic ? 'اللمسة النهائية' : 'Finish quality'}</span><h3>${isArabic ? 'مظهر متجانس قبل إعادة القطع إلى التشغيل.' : 'A consistent finish before assets return to service.'}</h3></div></article>
          <article class="experience-card"><img src="${media.inventory}" alt="${isArabic ? 'مجموعة أدوات مائدة من الستانلس ستيل للتقييم' : 'Stainless-steel cutlery inventory ready for review'}" loading="lazy" decoding="async"><div class="experience-card-overlay"><span>${isArabic ? 'تقييم المخزون' : 'Inventory review'}</span><h3>${isArabic ? 'تصنيف الحالة والكميات والأولوية قبل اعتماد النطاق.' : 'Condition, quantity and priority are reviewed before scope approval.'}</h3></div></article>
        </div>
      </div>
    </div>`;

  const comparison = document.createElement('section');
  comparison.className = 'comparison-section';
  comparison.innerHTML = `
    <div class="container">
      <div class="comparison-heading"><div><p class="eyebrow light-eyebrow">${isArabic ? 'مقارنة تفاعلية' : 'Interactive comparison'}</p><h2>${isArabic ? 'حرّك المؤشر لرؤية الفرق بين سطح باهت ولمسة مصقولة.' : 'Move the control to compare a tired surface with a polished finish.'}</h2></div><p>${isArabic ? 'عرض توضيحي بصري؛ يتم تقييم كل قطعة فعلياً حسب نوع الستانلس ستيل وعمق الخدوش وحالة السطح.' : 'Illustrative only. Each real item is assessed by steel type, scratch depth and surface condition.'}</p></div>
      <div class="comparison" data-comparison style="--position:52%">
        <img class="comparison-before" src="${media.detail}" alt="${isArabic ? 'سطح أدوات مائدة بحالة باهتة توضيحية' : 'Illustrative tired cutlery surface'}" draggable="false">
        <div class="comparison-after-wrap"><img src="${media.detail}" alt="${isArabic ? 'سطح أدوات مائدة بلمسة مصقولة توضيحية' : 'Illustrative polished cutlery surface'}" draggable="false"></div>
        <span class="comparison-label comparison-label-before">${isArabic ? 'قبل' : 'Before'}</span>
        <span class="comparison-label comparison-label-after">${isArabic ? 'بعد' : 'After'}</span>
        <span class="comparison-handle" aria-hidden="true"></span>
        <input class="comparison-range" type="range" min="4" max="96" value="52" aria-label="${isArabic ? 'تحريك مقارنة قبل وبعد' : 'Move before and after comparison'}">
      </div>
    </div>`;

  const smart = document.createElement('section');
  smart.className = 'smart-section';
  smart.innerHTML = `<div class="container smart-grid"><div><p class="eyebrow">${isArabic ? 'اختيار الخدمة' : 'Service selector'}</p><h2>${isArabic ? 'ابدأ من حالة القطع الفعلية.' : 'Start from the actual condition of the assets.'}</h2><p>${isArabic ? 'اختر أقرب وصف، وسنوجّهك إلى التقييم أو التلميع أو العناية الدورية.' : 'Choose the closest condition and we will guide you to assessment, polishing or recurring care.'}</p></div><div class="smart-panel"><div class="smart-options">
    <button class="smart-option" type="button" data-route="assessment"><strong>${isArabic ? 'الحالة غير واضحة' : 'Condition is unclear'}</strong><span>${isArabic ? 'ابدأ بتقييم فني للمخزون.' : 'Begin with an asset condition review.'}</span></button>
    <button class="smart-option" type="button" data-route="polishing"><strong>${isArabic ? 'بهتان وخدوش سطحية' : 'Dullness and surface marks'}</strong><span>${isArabic ? 'راجع مسار تلميع أدوات المائدة.' : 'Review the cutlery polishing route.'}</span></button>
    <button class="smart-option" type="button" data-route="hollowware"><strong>${isArabic ? 'قطع تقديم مجوفة' : 'Hollowware and service pieces'}</strong><span>${isArabic ? 'حدّد نوع القطع والمعدن والاستخدام.' : 'Identify piece type, metal and usage.'}</span></button>
    <button class="smart-option" type="button" data-route="recurring"><strong>${isArabic ? 'استخدام كثيف ومتكرر' : 'Heavy recurring use'}</strong><span>${isArabic ? 'استكشف برنامج عناية دوري.' : 'Explore a recurring care plan.'}</span></button>
  </div><div class="smart-result" aria-live="polite"><strong></strong><span></span><a href="#assessment">${isArabic ? 'انتقل إلى طلب التقييم' : 'Continue to assessment'} →</a></div></div></div>`;

  if (mount === signalStrip) {
    signalStrip.after(gallery, comparison, smart);
  } else if (assessment) {
    assessment.before(gallery, comparison, smart);
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

    const positionFromPointer = (clientX) => {
      const rect = comparisonEl.getBoundingClientRect();
      if (rect.width <= 0) return;
      setPosition(((clientX - rect.left) / rect.width) * 100);
    };

    let dragging = false;
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

  const result = smart.querySelector('.smart-result');
  const routes = {
    assessment: [isArabic ? 'التقييم الفني للمخزون' : 'Asset condition review', isArabic ? 'الأفضل عندما تكون الخامة أو الأولوية أو قابلية المعالجة غير واضحة.' : 'Best when material, priority or treatment suitability is unclear.'],
    polishing: [isArabic ? 'تلميع أدوات المائدة' : 'Cutlery polishing', isArabic ? 'مناسب للبهتان وآثار الاستخدام والخدوش السطحية بعد التقييم.' : 'Suitable for dullness, use marks and light surface scratches after assessment.'],
    hollowware: [isArabic ? 'العناية بالقطع المجوفة' : 'Hollowware care', isArabic ? 'مسار مخصص لأطباق التقديم والأوعية والقطع الأكبر حجماً.' : 'A dedicated route for serving dishes, bowls and larger service pieces.'],
    recurring: [isArabic ? 'برنامج عناية دورية' : 'Recurring care plan', isArabic ? 'للمنشآت ذات الدوران العالي والاستخدام المتكرر.' : 'For operations with high turnover and repeated use.'],
  };

  smart.querySelectorAll('.smart-option').forEach((button) => {
    button.addEventListener('click', () => {
      smart.querySelectorAll('.smart-option').forEach((item) => item.classList.remove('is-selected'));
      button.classList.add('is-selected');
      const route = routes[button.dataset.route];
      if (!route || !(result instanceof HTMLElement)) return;
      result.querySelector('strong').textContent = route[0];
      result.querySelector('span').textContent = route[1];
      result.classList.add('is-visible');
    });
  });
}

const releaseScroll = () => {
  if (!document.body.classList.contains('nav-open')) {
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';
  }
};
window.addEventListener('pageshow', releaseScroll);
window.addEventListener('orientationchange', releaseScroll);
window.addEventListener('resize', releaseScroll, { passive: true });
