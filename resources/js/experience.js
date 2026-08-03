const root = document.documentElement;
const isArabic = root.dir === 'rtl';
const main = document.querySelector('main#main');

if (main) {
  const signalStrip = main.querySelector('.signal-strip');
  const assessment = main.querySelector('.assessment-section');

  const gallery = document.createElement('section');
  gallery.className = 'experience-gallery';
  gallery.innerHTML = `
    <div class="container">
      <div class="section-topline">
        <div>
          <p class="eyebrow">${isArabic ? 'الضيافة تُرى قبل أن تُشرح' : 'Hospitality is seen before it is explained'}</p>
          <h2>${isArabic ? 'تفاصيل الخدمة تصنع الانطباع.' : 'Service quality lives in the details.'}</h2>
        </div>
      </div>
      <div class="experience-gallery-grid">
        <article class="experience-card experience-card-large">
          <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&q=88" alt="${isArabic ? 'طاولة ضيافة راقية' : 'Refined hospitality table setting'}" loading="eager">
          <div class="experience-card-overlay"><span>${isArabic ? 'تجربة الضيف' : 'Guest experience'}</span><h3>${isArabic ? 'كل قطعة ظاهرة جزء من صورة المنشأة.' : 'Every visible asset contributes to the property’s reputation.'}</h3></div>
        </article>
        <div class="experience-stack">
          <article class="experience-card"><img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c5?auto=format&fit=crop&w=1200&q=86" alt="${isArabic ? 'مطعم حديث' : 'Modern restaurant interior'}" loading="lazy"><div class="experience-card-overlay"><span>${isArabic ? 'التشغيل' : 'Operations'}</span><h3>${isArabic ? 'المظهر الجيد يبدأ من نظام عناية جيد.' : 'Consistent presentation starts with a consistent care system.'}</h3></div></article>
          <article class="experience-card"><img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=86" alt="${isArabic ? 'ردهة فندق' : 'Hotel environment'}" loading="lazy"><div class="experience-card-overlay"><span>${isArabic ? 'الضيافة' : 'Hospitality'}</span><h3>${isArabic ? 'مصمم للفنادق والمطاعم والفعاليات.' : 'Designed for hotels, restaurants and event operations.'}</h3></div></article>
        </div>
      </div>
    </div>`;

  const metrics = document.createElement('section');
  metrics.className = 'metric-band';
  metrics.innerHTML = `<div class="container metric-grid">
    <div class="metric"><strong data-count="4">0</strong><span>${isArabic ? 'مراحل واضحة من الاستلام إلى التسليم' : 'Clear stages from intake to return'}</span></div>
    <div class="metric"><strong data-count="1">0</strong><span>${isArabic ? 'مرجع موحد لكل طلب' : 'Single reference for every request'}</span></div>
    <div class="metric"><strong data-count="2">0</strong><span>${isArabic ? 'لغتان من اليوم الأول' : 'Languages from day one'}</span></div>
    <div class="metric"><strong data-count="100">0</strong><span>${isArabic ? '٪ توثيق قبل التنفيذ' : '% documented before work'}</span></div>
  </div>`;

  const comparison = document.createElement('section');
  comparison.className = 'comparison-section';
  comparison.innerHTML = `<div class="container">
    <div class="comparison-heading"><div><p class="eyebrow light-eyebrow">${isArabic ? 'شاهد الفرق' : 'See the difference'}</p><h2>${isArabic ? 'اسحب للمقارنة بين الحالة المتعبة والحالة المحسّنة.' : 'Drag to compare tired presentation with a refreshed finish.'}</h2></div><p>${isArabic ? 'هذا العرض توضيحي لتجربة المقارنة، وليس نتيجة عميل أو ادعاء أداء موثقاً.' : 'This is an illustrative comparison experience, not a client result or an unverified performance claim.'}</p></div>
    <div class="comparison" data-comparison>
      <img class="comparison-before" src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1800&q=88" alt="${isArabic ? 'حالة توضيحية قبل العناية' : 'Illustrative condition before care'}">
      <div class="comparison-after-wrap"><img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1800&q=88" alt="${isArabic ? 'حالة توضيحية بعد العناية' : 'Illustrative condition after care'}"></div>
      <span class="comparison-label comparison-label-before">${isArabic ? 'قبل' : 'Before'}</span><span class="comparison-label comparison-label-after">${isArabic ? 'بعد' : 'After'}</span>
      <span class="comparison-handle"></span>
      <input class="comparison-range" type="range" min="5" max="95" value="52" aria-label="${isArabic ? 'تحريك المقارنة' : 'Move comparison'}">
    </div>
    <p class="comparison-note">${isArabic ? 'عرض بصري توضيحي فقط. يتم تقييم كل نطاق فعلياً حسب حالة القطع.' : 'Visual demonstration only. Every real scope is assessed against the actual condition of the assets.'}</p>
  </div>`;

  const smart = document.createElement('section');
  smart.className = 'smart-section';
  smart.innerHTML = `<div class="container smart-grid">
    <div><p class="eyebrow">${isArabic ? 'مسار ذكي' : 'Smart route'}</p><h2>${isArabic ? 'اعثر على نقطة البداية في أقل من دقيقة.' : 'Find the right starting point in under a minute.'}</h2><p>${isArabic ? 'اختر واقع التشغيل الحالي لنقترح المسار الأنسب قبل تعبئة الطلب الكامل.' : 'Choose the situation that best matches your operation and we will suggest the most relevant route before the full request.'}</p></div>
    <div class="smart-panel">
      <div class="smart-steps"><span class="smart-step is-active">1</span><span class="smart-step">2</span><span class="smart-step">3</span></div>
      <div class="smart-options">
        <button class="smart-option" type="button" data-route="assessment"><strong>${isArabic ? 'لا أعرف الحالة' : 'I am unsure of the condition'}</strong><span>${isArabic ? 'ابدأ بتقييم فني للمخزون.' : 'Start with an asset condition review.'}</span></button>
        <button class="smart-option" type="button" data-route="cutlery"><strong>${isArabic ? 'المظهر متعب' : 'Presentation looks tired'}</strong><span>${isArabic ? 'راجع مسار ترميم أدوات المائدة.' : 'Review the cutlery restoration route.'}</span></button>
        <button class="smart-option" type="button" data-route="hollowware"><strong>${isArabic ? 'قطع التقديم تحتاج عناية' : 'Service pieces need care'}</strong><span>${isArabic ? 'ابدأ بمسار العناية بالقطع المجوفة.' : 'Start with hollowware care.'}</span></button>
        <button class="smart-option" type="button" data-route="recurring"><strong>${isArabic ? 'المشكلة تتكرر' : 'The issue keeps returning'}</strong><span>${isArabic ? 'استكشف برنامج عناية دورية.' : 'Explore a recurring care plan.'}</span></button>
      </div>
      <div class="smart-result" aria-live="polite"><strong></strong><span></span><a href="#assessment">${isArabic ? 'انتقل إلى طلب التقييم ←' : 'Continue to assessment →'}</a></div>
    </div>
  </div>`;

  const process = document.createElement('section');
  process.className = 'process-visual';
  process.innerHTML = `<div class="container"><div class="section-topline"><div><p class="eyebrow">${isArabic ? 'منهج واضح' : 'A visible operating route'}</p><h2>${isArabic ? 'لا توجد منطقة رمادية بين الاستلام والتسليم.' : 'No grey area between intake and return.'}</h2></div></div><div class="process-rail">
    <article class="process-card"><span>01</span><h3>${isArabic ? 'استلام موثق' : 'Documented intake'}</h3><p>${isArabic ? 'تسجيل النطاق والكميات والحالة الأولية.' : 'Scope, quantities and initial condition are recorded.'}</p><i></i></article>
    <article class="process-card"><span>02</span><h3>${isArabic ? 'تقييم فني' : 'Technical review'}</h3><p>${isArabic ? 'مراجعة القابلية والأولوية قبل اعتماد النطاق.' : 'Suitability and priority are reviewed before scope approval.'}</p><i></i></article>
    <article class="process-card"><span>03</span><h3>${isArabic ? 'عناية مضبوطة' : 'Controlled care'}</h3><p>${isArabic ? 'تنفيذ المسار المعتمد دون تجاوز الحالة الفعلية.' : 'The approved route is executed against the actual condition.'}</p><i></i></article>
    <article class="process-card"><span>04</span><h3>${isArabic ? 'فحص وإطلاق' : 'Check and release'}</h3><p>${isArabic ? 'فحص نهائي قبل إعادة القطع إلى التشغيل.' : 'A final check takes place before assets return to operation.'}</p><i></i></article>
  </div></div>`;

  const trust = document.createElement('section');
  trust.className = 'trust-strip';
  trust.innerHTML = `<div class="container trust-grid">
    <article class="trust-card"><b>${isArabic ? 'حوكمة' : 'Governance'}</b><h3>${isArabic ? 'لا ادعاءات غير موثقة' : 'No unsupported claims'}</h3><p>${isArabic ? 'يُبنى القرار على حالة الأصل الفعلية ونطاق واضح.' : 'Decisions are based on the actual asset condition and a defined scope.'}</p></article>
    <article class="trust-card"><b>${isArabic ? 'وضوح' : 'Clarity'}</b><h3>${isArabic ? 'مرجع واحد لكل طلب' : 'One reference per request'}</h3><p>${isArabic ? 'يمكن تتبع الطلب منذ لحظة الإرسال وحتى المعالجة.' : 'Every request can be followed from submission through handling.'}</p></article>
    <article class="trust-card"><b>${isArabic ? 'تشغيل' : 'Operations'}</b><h3>${isArabic ? 'مصمم لفرق العمل' : 'Built for operating teams'}</h3><p>${isArabic ? 'المحتوى والمسار موجهان للمشتريات والتشغيل والضيافة.' : 'The content and journey are designed for procurement, operations and hospitality teams.'}</p></article>
  </div>`;

  if (signalStrip) {
    signalStrip.after(gallery, metrics, comparison, smart, process, trust);
  } else if (assessment) {
    assessment.before(gallery, metrics, comparison, smart, process, trust);
  }

  const range = comparison.querySelector('.comparison-range');
  const afterWrap = comparison.querySelector('.comparison-after-wrap');
  const handle = comparison.querySelector('.comparison-handle');
  if (range && afterWrap && handle) {
    const syncComparison = () => {
      const value = `${range.value}%`;
      afterWrap.style.width = value;
      handle.style.left = value;
    };
    range.addEventListener('input', syncComparison);
    syncComparison();
  }

  const smartResult = smart.querySelector('.smart-result');
  const routes = {
    assessment: [isArabic ? 'ابدأ بالتقييم الفني' : 'Start with a technical assessment', isArabic ? 'هذا المسار مناسب عندما تكون الحالة أو الأولوية غير واضحة.' : 'This route fits when condition or priority is not yet clear.'],
    cutlery: [isArabic ? 'مسار ترميم أدوات المائدة' : 'Cutlery restoration route', isArabic ? 'ابدأ بصور واضحة وكميات تقريبية لتسريع المراجعة.' : 'Add clear photos and estimated quantities to speed up the review.'],
    hollowware: [isArabic ? 'مسار العناية بالقطع المجوفة' : 'Hollowware care route', isArabic ? 'حدد أنواع قطع التقديم ومستوى الاستخدام الحالي.' : 'Identify the service-piece types and current usage level.'],
    recurring: [isArabic ? 'برنامج عناية دورية' : 'Recurring care plan', isArabic ? 'هذا المسار مناسب للمنشآت ذات الاستخدام المتكرر.' : 'This route fits operations with repeated high-frequency use.'],
  };
  smart.querySelectorAll('.smart-option').forEach((button) => {
    button.addEventListener('click', () => {
      smart.querySelectorAll('.smart-option').forEach((item) => item.classList.remove('is-selected'));
      button.classList.add('is-selected');
      const route = routes[button.dataset.route];
      if (!route || !smartResult) return;
      smartResult.querySelector('strong').textContent = route[0];
      smartResult.querySelector('span').textContent = route[1];
      smartResult.classList.add('is-visible');
      smart.querySelectorAll('.smart-step').forEach((step, index) => step.classList.toggle('is-active', index < 3));
    });
  });

  const counterObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const node = entry.target;
      const target = Number(node.dataset.count || 0);
      const duration = 900;
      const start = performance.now();
      const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        node.textContent = Math.round(target * progress).toString();
        if (progress < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
      observer.unobserve(node);
    });
  }, { threshold: .5 });
  document.querySelectorAll('[data-count]').forEach((counter) => counterObserver.observe(counter));

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { threshold: .08, rootMargin: '0px 0px -6% 0px' });
    document.querySelectorAll('.experience-card,.smart-panel,.comparison,.process-card,.trust-card').forEach((node) => revealObserver.observe(node));
  }
}
