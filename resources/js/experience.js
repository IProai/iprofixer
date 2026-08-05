const root = document.documentElement;
const isArabic = root.dir === 'rtl';
const main = document.querySelector('main#main');
const compact = window.matchMedia('(max-width: 700px)').matches;

const media = {
  hero: '/media/hero-cutlery.svg',
  cutlery: '/media/service-cutlery.svg',
  hollowware: '/media/service-hollowware.svg',
  assessment: '/media/service-assessment.svg',
  recurring: '/media/service-recurring.svg',
  before: '/media/before-cutlery.svg',
  after: '/media/after-cutlery.svg',
};

const styles = document.createElement('style');
styles.textContent = `
.hero-new{position:relative;overflow:hidden;background:#061827}.hero-new:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(4,17,30,.98),rgba(4,17,30,.9) 38%,rgba(4,17,30,.18) 72%),var(--hero-image) center right/cover no-repeat}.hero-new-grid{position:relative;z-index:1}.hero-art{min-height:420px}.hero-art .art-panel-main{background:transparent!important;border:0!important;box-shadow:none!important}.hero-art .art-panel-main>*:not(.art-caption),.hero-art .art-panel-note{display:none!important}.hero-art .art-caption{backdrop-filter:blur(8px)}
.service-stack{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}.service-row{position:relative;display:flex!important;min-height:330px;padding:0!important;border:1px solid #dce4eb!important;border-radius:12px;overflow:hidden;background:#fff!important;flex-direction:column;align-items:stretch!important;box-shadow:0 12px 30px rgba(7,24,40,.08)}.service-row:before{content:"";display:block;height:150px;background:var(--service-image) center/cover no-repeat}.service-row>span{position:absolute;top:126px;left:18px;width:44px;height:44px;border-radius:50%;display:grid;place-items:center;color:#fff;background:#0b69c7;border:3px solid #fff;font-weight:800}.service-row>div{padding:1.7rem 1.1rem 1rem}.service-row>b{margin:auto 1.1rem 1rem;color:#0b69c7}.service-row:nth-child(1){--service-image:url("${media.cutlery}")}.service-row:nth-child(2){--service-image:url("${media.hollowware}")}.service-row:nth-child(3){--service-image:url("${media.assessment}")}.service-row:nth-child(4){--service-image:url("${media.recurring}")}
.page-hero{position:relative;overflow:hidden;background:#061827!important;min-height:430px}.page-hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(4,17,30,.97),rgba(4,17,30,.72) 48%,rgba(4,17,30,.14)),var(--page-image) center/cover no-repeat}.page-hero-grid{position:relative;z-index:1;min-height:430px;align-items:center}.page-hero h1,.page-hero .page-lead,.page-hero .eyebrow,.page-hero .back-link{color:#fff!important}.page-hero .page-index{color:rgba(255,255,255,.55)!important}.detail-card-grid .detail-card{position:relative;overflow:hidden;padding-top:170px!important}.detail-card-grid .detail-card:before{content:"";position:absolute;inset:0 0 auto;height:150px;background:var(--card-image) center/cover no-repeat}.detail-card-grid .detail-card:nth-child(1){--card-image:url("${media.cutlery}")}.detail-card-grid .detail-card:nth-child(2){--card-image:url("${media.hollowware}")}.detail-card-grid .detail-card:nth-child(3){--card-image:url("${media.assessment}")}.detail-card-grid .detail-card:nth-child(4){--card-image:url("${media.recurring}")}
.comparison-section{background:#061827;color:#fff;padding:clamp(3rem,7vw,6rem) 0}.comparison-heading{display:grid;grid-template-columns:1.25fr .75fr;gap:2rem;align-items:end;margin-bottom:1.5rem}.comparison-heading h2,.comparison-heading p{color:#fff}.comparison{position:relative;height:clamp(250px,40vw,470px);overflow:hidden;border-radius:14px;background:#101820;touch-action:pan-y}.comparison img{width:100%;height:100%;object-fit:cover;display:block}.comparison-before{position:absolute;inset:0}.comparison-after-wrap{position:absolute;inset:0;width:var(--position);overflow:hidden}.comparison-after-wrap img{width:100%;height:100%;object-fit:cover;max-width:none}.comparison-handle{position:absolute;top:0;bottom:0;left:var(--position);width:3px;background:#fff;transform:translateX(-50%);pointer-events:none}.comparison-handle:after{content:"↔";position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:46px;height:46px;border-radius:50%;background:#fff;color:#0b69c7;display:grid;place-items:center;font-weight:900}.comparison-label{position:absolute;top:1rem;z-index:2;padding:.35rem .65rem;border-radius:5px;background:rgba(4,17,30,.82);color:#fff;font-size:.75rem;font-weight:800;text-transform:uppercase}.comparison-label-before{left:1rem}.comparison-label-after{right:1rem;background:#0b69c7}.comparison-range{position:absolute;inset:0;z-index:3;width:100%;height:100%;opacity:0;cursor:ew-resize}
[dir="rtl"] .service-row>span{left:auto;right:18px}
@media(max-width:980px){.service-stack{grid-template-columns:repeat(2,minmax(0,1fr))}.hero-new:before{background:linear-gradient(180deg,rgba(4,17,30,.4),rgba(4,17,30,.96) 66%),var(--hero-image) center top/cover no-repeat}.hero-art{min-height:260px}}
@media(max-width:640px){.service-stack{grid-template-columns:1fr}.service-row{min-height:0}.hero-new-grid{display:block!important;padding-top:210px!important}.hero-new:before{background:linear-gradient(180deg,rgba(4,17,30,.08) 0 155px,rgba(4,17,30,.98) 285px),var(--hero-image) 62% top/auto 300px no-repeat}.hero-art{display:none!important}.comparison-heading{grid-template-columns:1fr}.comparison{height:260px}.page-hero,.page-hero-grid{min-height:380px}.page-hero:before{background:linear-gradient(180deg,rgba(4,17,30,.22),rgba(4,17,30,.94)),var(--page-image) center/cover no-repeat}}
`;
document.head.append(styles);
root.style.setProperty('--hero-image', `url("${media.hero}")`);

const path = window.location.pathname.replace(/\/$/, '');
const pageHero = document.querySelector('.page-hero');

if (pageHero instanceof HTMLElement) {
  const source = path.endsWith('/cutlery-restoration')
    ? media.cutlery
    : path.endsWith('/hollowware-care')
      ? media.hollowware
      : path.endsWith('/asset-condition-review')
        ? media.assessment
        : path.endsWith('/recurring-care-plans')
          ? media.recurring
          : media.hero;

  pageHero.style.setProperty('--page-image', `url("${source}")`);
}

if (main && document.querySelector('.hero-new')) {
  const signalStrip = main.querySelector('.signal-strip');
  const comparison = document.createElement('section');

  comparison.className = 'comparison-section';
  comparison.innerHTML = `<div class="container"><div class="comparison-heading"><div><p class="eyebrow light-eyebrow">${isArabic ? 'قبل وبعد' : 'Before & after'}</p><h2>${isArabic ? 'شاهد الفرق بين السطح المتعب واللمسة المصقولة.' : 'See the difference between a tired surface and a polished finish.'}</h2></div><p>${isArabic ? 'عرض توضيحي؛ تُراجع كل قطعة بحسب نوع الستانلس ستيل وحالة السطح.' : 'Illustrative comparison; every item is reviewed by stainless-steel type and surface condition.'}</p></div><div class="comparison" data-comparison style="--position:50%"><img class="comparison-before" src="${media.before}" width="1000" height="480" alt="${isArabic ? 'أدوات مائدة قبل الترميم' : 'Cutlery before restoration'}" decoding="async"><div class="comparison-after-wrap"><img src="${media.after}" width="1000" height="480" alt="${isArabic ? 'أدوات مائدة بعد الترميم' : 'Cutlery after restoration'}" decoding="async"></div><span class="comparison-label comparison-label-before">${isArabic ? 'قبل' : 'Before'}</span><span class="comparison-label comparison-label-after">${isArabic ? 'بعد' : 'After'}</span><span class="comparison-handle" aria-hidden="true"></span><input class="comparison-range" type="range" min="4" max="96" value="50" aria-label="${isArabic ? 'تحريك مقارنة قبل وبعد' : 'Move before and after comparison'}"></div></div>`;

  signalStrip?.after(comparison);

  const comparisonElement = comparison.querySelector('[data-comparison]');
  const range = comparison.querySelector('.comparison-range');

  if (
    comparisonElement instanceof HTMLElement &&
    range instanceof HTMLInputElement
  ) {
    const setPosition = (value) => {
      const position = Math.max(4, Math.min(96, Number(value)));
      comparisonElement.style.setProperty('--position', `${position}%`);
    };

    range.addEventListener('input', () => setPosition(range.value), {
      passive: true,
    });
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
