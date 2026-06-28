<?php require __DIR__ . '/includes/config.php'; $page_title = 'About Us — Access Infra'; require __DIR__ . '/includes/header.php'; ?>
<div id="root"></div>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --navy: #315C2B; --blue: #FFC100; --teal: #F5D000;
  --bg: #ffffff; --bg2: #f1f5f9; --bg3: #e2e8f0;
  --surface: #ffffff; --surface2: #f8fafc;
  --text: #0f172a; --text2: #334155; --text3: #64748b;
  --border: #e2e8f0;
  --shadow: 0 4px 24px rgba(49,92,43,0.08);
  --shadow-lg: 0 12px 48px rgba(49,92,43,0.14);
}
[data-theme="dark"] {
  --bg: #0c150a; --bg2: #142410; --bg3: #1f3417;
  --surface: #16280f; --surface2: #1f3417;
  --text: #f1f5f9; --text2: #cbd5e1; --text3: #9bb08f;
  --border: #2f4a22;
  --shadow: 0 4px 24px rgba(0,0,0,0.4);
  --shadow-lg: 0 12px 48px rgba(0,0,0,0.5);
}
html { scroll-behavior: smooth; }
body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); transition:background 0.3s,color 0.3s; line-height:1.6; overflow-x:hidden; }
body[data-lang="kn"] { font-family:'Noto Sans Kannada',sans-serif; }
body[data-lang="hi"] { font-family:'Noto Sans Devanagari',sans-serif; }
h1, h2, h3, h4 { font-family:'Sora',sans-serif; line-height:1.2; }
body[data-lang="kn"] h1, body[data-lang="kn"] h2, body[data-lang="kn"] h3, body[data-lang="kn"] h4 { font-family:'Noto Sans Kannada',sans-serif; }
body[data-lang="hi"] h1, body[data-lang="hi"] h2, body[data-lang="hi"] h3, body[data-lang="hi"] h4 { font-family:'Noto Sans Devanagari',sans-serif; }
::selection { background:#FFC10033; }
::-webkit-scrollbar { width:6px; }
::-webkit-scrollbar-track { background:var(--bg2); }
::-webkit-scrollbar-thumb { background:var(--blue); border-radius:3px; }
.grid-dots { background-image:radial-gradient(circle,var(--border) 1px,transparent 1px); background-size:28px 28px; }
@media (max-width:900px) { .desktop-nav { display:none !important; } .hamburger { display:flex !important; } }
@media (min-width:901px) { .nav-cta { display:inline-flex !important; align-items:center; } }
@media (max-width:768px) { .two-col { grid-template-columns:1fr !important; } .stats-card { display:none !important; } }
@media (max-width:900px) { .desktop-controls { display:none !important; } }
.content-zoom { transition:zoom 0.2s ease; }
</style>

<script type="text/babel">
const { useState, useEffect, useRef } = React;
const { DotMorph, AI_SHAPES } = window;
const T = window.TRANSLATIONS;

const SITE_URL = '<?php echo esc_js( SITE_URL ); ?>';
const HOME     = SITE_URL + '/';
const ABOUT    = SITE_URL + '/about/';
const GOVT     = SITE_URL + '/government-departments/';
const CONTACT  = SITE_URL + '/contact/';
const LOGO_URL = SITE_URL + '/assets/img/logo.png';

const PDF_URL  = '<?php echo esc_js( url('assets') ); ?>/assets/uploads/KOPPAL-SMART CLASS.pdf';

const GALLERY_COLORS = ['#636940','#F5D000','#315C2B','#FFC100','#A9FDAC','#243d20'];
const GALLERY_ICONS  = ['🖥️','🏫','📹','📷','👩‍🏫','🤝'];

const FS_STEPS = [
  { label:'A',   zoom:1,    title:'Default size' },
  { label:'A+',  zoom:1.12, title:'Large size' },
  { label:'A++', zoom:1.26, title:'Extra large size' },
];
const LANGS = [
  { code:'en', label:'EN', full:'English' },
  { code:'kn', label:'ಕನ್ನಡ', full:'ಕನ್ನಡ' },
  { code:'hi', label:'हि', full:'हिंदी' },
];

function Tag({ children, color='blue', light=false }) {
  const bg  = color==='teal'?(light?'rgba(245,208,0,0.2)':'rgba(245,208,0,0.1)'):(light?'rgba(255,193,0,0.2)':'rgba(255,193,0,0.1)');
  const col = color==='teal'?'#F5D000':(light?'#A9FDAC':'#FFC100');
  return <span style={{ display:'inline-block', background:bg, color:col, fontSize:12, fontWeight:700, letterSpacing:'0.06em', textTransform:'uppercase', padding:'4px 12px', borderRadius:999 }}>{children}</span>;
}

const REDUCE_MOTION = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function Reveal({ children, delay=0, style={} }) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(REDUCE_MOTION);
  useEffect(() => {
    if (REDUCE_MOTION || !ref.current) return;
    const obs = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) { setVisible(true); obs.disconnect(); }
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    obs.observe(ref.current);
    return () => obs.disconnect();
  }, []);
  return (
    <div ref={ref} style={{ ...style, opacity:visible?1:0, transform:visible?'translateY(0)':'translateY(24px)', transition:`opacity 0.6s ease-out ${delay}ms, transform 0.6s ease-out ${delay}ms`, willChange:'opacity, transform' }}>
      {children}
    </div>
  );
}

function ContextImage({ label, gradient }) {
  return (
    <div role="img" aria-label={label}
      style={{ width:'100%', aspectRatio:'4/3', borderRadius:16, background:gradient, boxShadow:'var(--shadow)' }}>
      {/* placeholder — replace with a real photograph matching the section context */}
    </div>
  );
}

function ContactItem({ icon, label, value, href }) {
  const inner = (
    <div style={{ display:'flex', alignItems:'center', gap:14 }}>
      <div style={{ width:42, height:42, borderRadius:10, background:'var(--bg2)', border:'1px solid var(--border)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:18, flexShrink:0 }}>{icon}</div>
      <div>
        <div style={{ fontSize:12, color:'var(--text3)', fontWeight:500 }}>{label}</div>
        <div style={{ fontSize:15, color:'var(--text)', fontWeight:500 }}>{value}</div>
      </div>
    </div>
  );
  return href ? <a href={href} style={{ textDecoration:'none' }}>{inner}</a> : inner;
}

function LangMenu({ lang, setLang }) {
  const [open, setOpen] = useState(false);
  const ref = useRef(null);
  useEffect(() => {
    const fn = e => { if(ref.current && !ref.current.contains(e.target)) setOpen(false); };
    document.addEventListener('mousedown', fn);
    return () => document.removeEventListener('mousedown', fn);
  }, []);
  const current = LANGS.find(l => l.code===lang);
  return (
    <div ref={ref} style={{ position:'relative' }}>
      <button onClick={()=>setOpen(o=>!o)} style={{ display:'flex', alignItems:'center', gap:6, background:'var(--bg2)', border:'1.5px solid var(--border)', borderRadius:8, padding:'6px 10px', cursor:'pointer', color:'var(--text2)', fontSize:13, fontWeight:600, fontFamily:'Inter,sans-serif', transition:'border-color 0.2s' }}
        onMouseEnter={e=>e.currentTarget.style.borderColor='#FFC100'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
        🌐 {current.label}
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" strokeWidth="2"><polyline points={open?'2,8 6,4 10,8':'2,4 6,8 10,4'}/></svg>
      </button>
      {open && (
        <div style={{ position:'absolute', top:'calc(100% + 6px)', right:0, zIndex:200, background:'var(--surface)', border:'1px solid var(--border)', borderRadius:10, boxShadow:'var(--shadow-lg)', overflow:'hidden', minWidth:130 }}>
          {LANGS.map(l => (
            <button key={l.code} onClick={()=>{ setLang(l.code); setOpen(false); }} style={{ display:'block', width:'100%', textAlign:'left', padding:'10px 16px', background:lang===l.code?'var(--bg2)':'transparent', border:'none', cursor:'pointer', color:lang===l.code?'#FFC100':'var(--text2)', fontSize:14, fontWeight:lang===l.code?700:400, fontFamily:'Inter,sans-serif', transition:'background 0.15s' }}
              onMouseEnter={e=>{ if(lang!==l.code) e.currentTarget.style.background='var(--bg2)'; }} onMouseLeave={e=>{ if(lang!==l.code) e.currentTarget.style.background='transparent'; }}>
              <span style={{ marginRight:8 }}>{l.label}</span>
              <span style={{ color:'var(--text3)', fontSize:12 }}>{l.full}</span>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

function FontSizeBtn({ fsIdx, cycleFontSize }) {
  const step = FS_STEPS[fsIdx];
  return (
    <button onClick={cycleFontSize} title={step.title} style={{ display:'flex', alignItems:'center', gap:4, background:'var(--bg2)', border:'1.5px solid var(--border)', borderRadius:8, padding:'5px 10px', cursor:'pointer', color:'var(--text2)', fontFamily:'Sora,sans-serif', fontWeight:700, transition:'border-color 0.2s', lineHeight:1, whiteSpace:'nowrap' }}
      onMouseEnter={e=>e.currentTarget.style.borderColor='#FFC100'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
      <span style={{ display:'flex', gap:2, alignItems:'center', marginRight:2 }}>
        {FS_STEPS.map((_,i) => <span key={i} style={{ width:4, height:4, borderRadius:'50%', background:i<=fsIdx?'#FFC100':'var(--border)', transition:'background 0.2s' }} />)}
      </span>
      <span style={{ fontSize:12+fsIdx*1.5 }}>{step.label}</span>
    </button>
  );
}

function Nav({ dark, toggleDark, lang, setLang, fsIdx, cycleFontSize }) {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);
  const t = T[lang].nav;
  useEffect(() => {
    const fn = () => setScrolled(window.scrollY>30);
    window.addEventListener('scroll', fn);
    return () => window.removeEventListener('scroll', fn);
  }, []);
  const NAV_LINKS = [
    { label:t.home,        href:HOME },
    { label:t.services,    href:'#services' },
    { label:t.smartSchool, href:'#smart-school' },
    { label:t.caseStudies, href:'#case-studies' },
    { label:t.govt,        href:GOVT },
    { label:t.about,       href:'#about' },
    { label:t.contact,     href:CONTACT },
  ];
  return (
    <nav className="ai-nav" style={{ position:'sticky', top:0, left:0, right:0, zIndex:100, background:'var(--surface)', borderBottom:'1px solid var(--border)', boxShadow:scrolled?'var(--shadow)':'none', backdropFilter:'blur(12px)', transition:'box-shadow 0.3s', padding:'0 clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', alignItems:'center', justifyContent:'space-between', height:68 }}>
        <a href={HOME} style={{ textDecoration:'none', display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:42, width:'auto', display:'block' }} />
        </a>
        <div className="desktop-nav" style={{ display:'flex', alignItems:'center', gap:2 }}>
          {NAV_LINKS.map(l => (
            <a key={l.label} href={l.href} style={{ color:'var(--text2)', textDecoration:'none', fontSize:13, fontWeight:500, padding:'6px 10px', borderRadius:6, transition:'all 0.15s' }}
              onMouseEnter={e=>{ e.target.style.color='#FFC100'; e.target.style.background='var(--bg2)'; }}
              onMouseLeave={e=>{ e.target.style.color='var(--text2)'; e.target.style.background='transparent'; }}>{l.label}</a>
          ))}
        </div>
        <div style={{ display:'flex', alignItems:'center', gap:8 }}>
          <div className="desktop-controls" style={{ display:'flex', alignItems:'center', gap:8 }}>
            <LangMenu lang={lang} setLang={setLang} />
            <FontSizeBtn fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
          </div>
          <button onClick={toggleDark} style={{ background:'none', border:'1.5px solid var(--border)', borderRadius:999, padding:'6px 10px', cursor:'pointer', color:'var(--text2)', fontSize:13, display:'flex', alignItems:'center', gap:5, transition:'all 0.2s' }}>
            <span style={{ fontSize:15 }}>{dark?'☀️':'🌙'}</span>
          </button>
          <button onClick={()=>setOpen(!open)} className="hamburger" style={{ background:'none', border:'none', cursor:'pointer', padding:6, color:'var(--text)', display:'none' }}>
            <svg width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2">
              {open?<><line x1="4" y1="4" x2="18" y2="18"/><line x1="18" y1="4" x2="4" y2="18"/></>:<><line x1="3" y1="7" x2="19" y2="7"/><line x1="3" y1="12" x2="19" y2="12"/><line x1="3" y1="17" x2="19" y2="17"/></>}
            </svg>
          </button>
        </div>
      </div>
      {open && (
        <div style={{ background:'var(--surface)', borderTop:'1px solid var(--border)', padding:'12px 20px 20px' }}>
          {NAV_LINKS.map(l => (
            <a key={l.label} href={l.href} onClick={()=>setOpen(false)} style={{ display:'block', padding:'10px 0', color:'var(--text2)', textDecoration:'none', fontSize:15, fontWeight:500, borderBottom:'1px solid var(--border)' }}>{l.label}</a>
          ))}
          <div style={{ marginTop:16, paddingTop:14, borderTop:'1px solid var(--border)', display:'flex', alignItems:'center', justifyContent:'space-between', flexWrap:'wrap', gap:10 }}>
            <div style={{ display:'flex', alignItems:'center', gap:8 }}>
              <span style={{ fontSize:12, color:'var(--text3)', fontWeight:500 }}>Language:</span>
              <div style={{ display:'flex', gap:4 }}>
                {[{code:'en',label:'EN'},{code:'kn',label:'ಕನ್ನಡ'},{code:'hi',label:'हि'}].map(l => (
                  <button key={l.code} onClick={()=>setLang(l.code)} style={{ padding:'5px 10px', borderRadius:6, border:'1.5px solid', borderColor:lang===l.code?'#FFC100':'var(--border)', background:lang===l.code?'rgba(255,193,0,0.1)':'var(--bg2)', color:lang===l.code?'#FFC100':'var(--text2)', fontSize:12.5, fontWeight:600, cursor:'pointer' }}>{l.label}</button>
                ))}
              </div>
            </div>
            <FontSizeBtn fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
          </div>
          <a href={CONTACT} onClick={()=>setOpen(false)} style={{ display:'block', marginTop:14, textAlign:'center', background:'linear-gradient(135deg,#FFC100,#F5D000)', color:'#fff', textDecoration:'none', padding:'10px', borderRadius:8, fontSize:14, fontWeight:600 }}>{t.contactCta}</a>
        </div>
      )}
    </nav>
  );
}

function Hero({ lang }) {
  const t = T[lang].hero;
  return (
    <section id="top" style={{ minHeight:'100vh', display:'flex', flexDirection:'column', justifyContent:'center', padding:'clamp(100px,12vw,140px) clamp(16px,5vw,80px) 80px', background:'linear-gradient(135deg,#315C2B 0%,#F5D000 55%,#FFC100 100%)', position:'relative', overflow:'hidden' }}>
      <DotMorph shapes={AI_SHAPES.INFRA} fullBleed />
      <div style={{ position:'absolute', inset:0, zIndex:1, background:'linear-gradient(180deg,rgba(49,92,43,0.35) 0%,rgba(49,92,43,0.55) 100%)' }}></div>
      <div style={{ maxWidth:1280, margin:'0 auto', width:'100%', position:'relative', zIndex:2 }}>
        <div className="two-col" style={{ display:'grid', gridTemplateColumns:'1fr auto', gap:40, alignItems:'center' }}>
          <div style={{ maxWidth:720 }}>
            <div style={{ display:'inline-flex', alignItems:'center', gap:8, marginBottom:24, background:'rgba(255,255,255,0.1)', border:'1px solid rgba(255,255,255,0.25)', borderRadius:999, padding:'5px 14px 5px 10px' }}>
              <span style={{ width:6, height:6, borderRadius:'50%', background:'#A9FDAC', display:'inline-block', boxShadow:'0 0 0 3px rgba(110,231,183,0.25)' }}></span>
              <span style={{ fontSize:12.5, color:'rgba(255,255,255,0.85)', fontWeight:500, letterSpacing:'0.03em' }}>{t.badge}</span>
            </div>
            <h1 style={{ fontSize:'clamp(32px,5.5vw,68px)', fontWeight:800, color:'#fff', marginBottom:24, letterSpacing:'-1.5px', textWrap:'pretty', lineHeight:1.08 }}>
              {t.heading1}<br />
              <span style={{ color:'#A9FDAC' }}>{t.headingGrad}</span><br />
              {t.heading2}
            </h1>
            <p style={{ fontSize:'clamp(15px,1.8vw,19px)', color:'rgba(255,255,255,0.85)', maxWidth:560, marginBottom:40, lineHeight:1.8 }}>{t.desc}</p>
            <div style={{ display:'flex', gap:14, flexWrap:'wrap' }}>
              <a href="#services" style={{ background:'#fff', color:'#F5D000', textDecoration:'none', padding:'13px 26px', borderRadius:10, fontSize:14, fontWeight:700, boxShadow:'0 4px 20px rgba(49,92,43,0.3)', transition:'transform 0.2s' }}
                onMouseEnter={e=>{ e.currentTarget.style.transform='translateY(-2px)'; }}
                onMouseLeave={e=>{ e.currentTarget.style.transform=''; }}>{t.cta1}</a>
              <a href="#case-studies" style={{ background:'rgba(255,255,255,0.1)', color:'#fff', textDecoration:'none', padding:'13px 26px', borderRadius:10, fontSize:14, fontWeight:600, border:'1.5px solid rgba(255,255,255,0.4)', transition:'background 0.2s' }}
                onMouseEnter={e=>{ e.currentTarget.style.background='rgba(255,255,255,0.18)'; }}
                onMouseLeave={e=>{ e.currentTarget.style.background='rgba(255,255,255,0.1)'; }}>{t.cta2}</a>
            </div>
          </div>
          <div className="stats-card" style={{ display:'grid', gridTemplateRows:'auto auto auto', gap:12, minWidth:170 }}>
            {t.statLabels.map((label, i) => (
              <div key={i} style={{ background:'rgba(255,255,255,0.1)', border:'1px solid rgba(255,255,255,0.25)', borderRadius:14, padding:'18px 22px', textAlign:'center', backdropFilter:'blur(6px)' }}>
                <div style={{ fontSize:26, fontWeight:800, fontFamily:'Sora,sans-serif', color:'#fff' }}>
                  {['20+','∞','NEP'][i]}
                </div>
                <div style={{ fontSize:11, color:'rgba(255,255,255,0.7)', fontWeight:500, marginTop:4 }}>{label}</div>
              </div>
            ))}
          </div>
        </div>
        <div style={{ marginTop:64, paddingTop:28, borderTop:'1px solid rgba(255,255,255,0.25)' }}>
          <p style={{ fontSize:11, color:'rgba(255,255,255,0.7)', textTransform:'uppercase', letterSpacing:'0.1em', marginBottom:16 }}>{t.programsLabel}</p>
          <div style={{ display:'flex', gap:10, flexWrap:'wrap', alignItems:'center' }}>
            {t.programs.map(p => (
              <span key={p} style={{ fontSize:12.5, fontWeight:600, color:'rgba(255,255,255,0.85)', padding:'5px 12px', borderRadius:6, border:'1px solid rgba(255,255,255,0.25)', background:'rgba(255,255,255,0.08)' }}>{p}</span>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function About({ lang }) {
  const t = T[lang].about;
  const icons = ['🎯','📋','🔗','🛡️'];
  return (
    <section id="about" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg2)' }}>
      <div className="two-col" style={{ maxWidth:1280, margin:'0 auto', display:'grid', gridTemplateColumns:'1fr 1fr', gap:'clamp(32px,6vw,80px)', alignItems:'center' }}>
        <Reveal>
          <div>
            <Tag>{t.tag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginBottom:20, marginTop:14, letterSpacing:'-0.8px' }}>
              {t.heading.split('\n').map((line,i) => <span key={i}>{line}{i===0&&<br />}</span>)}
            </h2>
            <p style={{ color:'var(--text2)', fontSize:15, lineHeight:1.85, marginBottom:14 }}>{t.p1}</p>
            <p style={{ color:'var(--text2)', fontSize:15, lineHeight:1.85, marginBottom:32 }}>{t.p2}</p>
            <div style={{ display:'flex', gap:32, flexWrap:'wrap' }}>
              {t.stats.map(([n,l]) => (
                <div key={l}>
                  <div style={{ fontSize:24, fontWeight:800, fontFamily:'Sora,sans-serif', color:'#FFC100' }}>{n}</div>
                  <div style={{ fontSize:12, color:'var(--text3)', marginTop:2 }}>{l}</div>
                </div>
              ))}
            </div>
          </div>
        </Reveal>
        <Reveal delay={120}>
          <ContextImage label="Access Infra team meeting with government department officials" gradient="linear-gradient(135deg,#FFC10022,#F5D00022)" />
          <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:14, marginTop:14 }}>
            {t.cards.map((c,i) => (
              <div key={i} style={{ background:'var(--surface)', border:'1px solid var(--border)', borderRadius:14, padding:22, boxShadow:'var(--shadow)' }}>
                <span style={{ fontSize:28 }}>{icons[i]}</span>
                <h4 style={{ fontSize:14, fontWeight:700, margin:'10px 0 6px' }}>{c.title}</h4>
                <p style={{ fontSize:13, color:'var(--text3)', lineHeight:1.6 }}>{c.desc}</p>
              </div>
            ))}
          </div>
        </Reveal>
      </div>
    </section>
  );
}

function Services({ lang }) {
  const t = T[lang].services;
  return (
    <section id="services" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ textAlign:'center', marginBottom:52 }}>
            <Tag>{t.tag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14, letterSpacing:'-0.8px' }}>{t.heading}</h2>
            <p style={{ color:'var(--text3)', fontSize:16, maxWidth:480, margin:'12px auto 0' }}>{t.desc}</p>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(290px,1fr))', gap:18 }}>
          {t.items.map((s,i) => (
            <Reveal key={i} delay={(i%3)*60}>
              <div style={{ background:'var(--surface2)', border:'1px solid var(--border)', borderRadius:16, padding:26, transition:'all 0.25s', cursor:'default' }}
                onMouseEnter={e=>{ e.currentTarget.style.transform='translateY(-4px)'; e.currentTarget.style.boxShadow='var(--shadow-lg)'; e.currentTarget.style.borderColor='#FFC10044'; }}
                onMouseLeave={e=>{ e.currentTarget.style.transform=''; e.currentTarget.style.boxShadow=''; e.currentTarget.style.borderColor='var(--border)'; }}>
                <div style={{ fontSize:34, marginBottom:14 }}>{s.icon}</div>
                <h3 style={{ fontSize:16, fontWeight:700, marginBottom:10 }}>{s.title}</h3>
                <p style={{ fontSize:13.5, color:'var(--text3)', lineHeight:1.7 }}>{s.desc}</p>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function SmartSchool({ lang }) {
  const [tab, setTab] = useState(0);
  const t = T[lang].smartSchool;

  function Overview() {
    const ov = t.overview;
    return (
      <div className="two-col" style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:40, alignItems:'start' }}>
        <div>
          <h3 style={{ fontSize:20, fontWeight:700, marginBottom:14 }}>{ov.heading}</h3>
          <p style={{ color:'var(--text2)', fontSize:14.5, lineHeight:1.85, marginBottom:14 }}>{ov.p1}</p>
          <p style={{ color:'var(--text2)', fontSize:14.5, lineHeight:1.85, marginBottom:22 }}>{ov.p2}</p>
          <blockquote style={{ borderLeft:'3px solid #FFC100', paddingLeft:16, fontStyle:'italic', color:'var(--text3)', fontSize:13.5, lineHeight:1.7 }}>{ov.quote}</blockquote>
          <div style={{ marginTop:18 }}>
            <a href={PDF_URL} target="_blank" rel="noopener noreferrer" style={{ display:'inline-flex', alignItems:'center', gap:8, color:'#FFC100', textDecoration:'none', fontSize:13, fontWeight:600 }}>📄 Download DEEP Program Report →</a>
          </div>
        </div>
        <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:10 }}>
          {ov.features.map(f => (
            <div key={f} style={{ background:'var(--bg3)', borderRadius:10, padding:'12px 14px', display:'flex', alignItems:'center', gap:8, fontSize:13, fontWeight:500, color:'var(--text2)', border:'1px solid var(--border)' }}>
              <span style={{ color:'#F5D000', flexShrink:0 }}>✓</span> {f}
            </div>
          ))}
        </div>
      </div>
    );
  }

  function Technology() {
    return (
      <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(270px,1fr))', gap:16 }}>
        {t.tech.items.map(c => (
          <div key={c.num} style={{ background:'var(--bg3)', borderRadius:14, padding:20, border:'1px solid var(--border)' }}>
            <div style={{ width:30, height:30, borderRadius:7, background:'linear-gradient(135deg,#FFC100,#F5D000)', color:'#fff', display:'flex', alignItems:'center', justifyContent:'center', fontWeight:700, fontSize:14, marginBottom:10 }}>{c.num}</div>
            <h4 style={{ fontSize:14, fontWeight:700, marginBottom:10 }}>{c.title}</h4>
            <ul style={{ listStyle:'none', display:'flex', flexDirection:'column', gap:5 }}>
              {c.specs.map(s => (
                <li key={s} style={{ fontSize:12.5, color:'var(--text2)', display:'flex', alignItems:'flex-start', gap:6 }}>
                  <span style={{ color:'#F5D000', marginTop:2, flexShrink:0 }}>✓</span>{s}
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    );
  }

  function Products() {
    const p = t.products;
    return (
      <div>
        <p style={{ color:'var(--text2)', fontSize:14.5, lineHeight:1.75, marginBottom:22 }}>{p.intro}</p>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(250px,1fr))', gap:12 }}>
          {p.items.map(item => (
            <div key={item.n} style={{ display:'flex', gap:12, background:'var(--bg3)', borderRadius:12, padding:16, border:'1px solid var(--border)' }}>
              <div style={{ width:26, height:26, borderRadius:6, background:'linear-gradient(135deg,#FFC100,#F5D000)', color:'#fff', display:'flex', alignItems:'center', justifyContent:'center', fontSize:12, fontWeight:700, flexShrink:0 }}>{item.n}</div>
              <div>
                <div style={{ fontWeight:600, fontSize:13.5, marginBottom:3 }}>{item.title}</div>
                <div style={{ fontSize:12, color:'var(--text3)', lineHeight:1.5 }}>{item.detail}</div>
              </div>
            </div>
          ))}
        </div>
        <div style={{ marginTop:20, padding:'14px 18px', background:'var(--bg3)', borderRadius:10, fontSize:12.5, color:'var(--text3)' }}>
          <strong style={{ color:'var(--text2)' }}>{p.partnerLabel}</strong> {p.partnerDetail}
        </div>
      </div>
    );
  }

  function Impact() {
    const imp = t.impact;
    return (
      <div>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(210px,1fr))', gap:14, marginBottom:28 }}>
          {imp.items.map(item => (
            <div key={item.title} style={{ background:'var(--bg3)', border:'1px solid var(--border)', borderRadius:12, padding:18 }}>
              <span style={{ fontSize:26 }}>{item.icon}</span>
              <h4 style={{ fontSize:13.5, fontWeight:700, margin:'8px 0 5px' }}>{item.title}</h4>
              <p style={{ fontSize:12.5, color:'var(--text3)', lineHeight:1.6 }}>{item.desc}</p>
            </div>
          ))}
        </div>
        <div style={{ background:'linear-gradient(135deg,#FFC10011,#F5D00011)', border:'1px solid #FFC10022', borderRadius:14, padding:24 }}>
          <h4 style={{ fontSize:17, fontWeight:700, marginBottom:12 }}>{imp.citizenHeading}</h4>
          <div style={{ display:'flex', flexWrap:'wrap', gap:8 }}>
            {imp.words.map(w => (
              <span key={w} style={{ background:'linear-gradient(135deg,#FFC100,#F5D000)', color:'#fff', borderRadius:999, padding:'4px 12px', fontSize:12.5, fontWeight:600 }}>{w}</span>
            ))}
          </div>
        </div>
      </div>
    );
  }

  const panels = [<Overview key="ov"/>, <Technology key="tech"/>, <Products key="prod"/>, <Impact key="imp"/>];
  return (
    <section id="smart-school" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg2)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ marginBottom:36 }}>
            <Tag color="teal">{t.tag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14, letterSpacing:'-0.8px', maxWidth:600 }}>{t.heading}</h2>
            <p style={{ color:'var(--text3)', fontSize:16, maxWidth:560, marginTop:10 }}>{t.desc}</p>
          </div>
          <div style={{ display:'flex', gap:2, marginBottom:28, borderBottom:'1px solid var(--border)', overflowX:'auto' }}>
            {t.tabs.map((tab_,i) => (
              <button key={i} onClick={()=>setTab(i)} style={{ background:'none', border:'none', cursor:'pointer', padding:'10px 18px', fontSize:13.5, fontWeight:600, color:tab===i?'#FFC100':'var(--text3)', borderBottom:tab===i?'2px solid #FFC100':'2px solid transparent', marginBottom:-1, transition:'color 0.2s', whiteSpace:'nowrap' }}>{tab_}</button>
            ))}
          </div>
          {panels[tab]}
        </Reveal>
      </div>
    </section>
  );
}

function CaseStudies({ lang }) {
  const t = T[lang].caseStudies;
  return (
    <section id="case-studies" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ marginBottom:44 }}>
            <Tag>{t.tag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14, letterSpacing:'-0.8px' }}>{t.heading}</h2>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(380px,1fr))', gap:22 }}>
          {t.items.map((c,i) => (
            <Reveal key={i} delay={i*80}>
              <div style={{ background:'var(--surface2)', border:'1px solid var(--border)', borderRadius:18, overflow:'hidden', boxShadow:'var(--shadow)', transition:'transform 0.25s,box-shadow 0.25s' }}
                onMouseEnter={e=>{ e.currentTarget.style.transform='translateY(-4px)'; e.currentTarget.style.boxShadow='var(--shadow-lg)'; }}
                onMouseLeave={e=>{ e.currentTarget.style.transform=''; e.currentTarget.style.boxShadow='var(--shadow)'; }}>
                <div style={{ height:6, background:i===0?'linear-gradient(90deg,#FFC100,#F5D000)':'linear-gradient(90deg,#F5D000,#FFC100)' }}></div>
                <div style={{ padding:26 }}>
                  <span style={{ fontSize:11.5, fontWeight:600, color:'#FFC100', textTransform:'uppercase', letterSpacing:'0.06em' }}>{c.tag}</span>
                  <h3 style={{ fontSize:19, fontWeight:700, margin:'9px 0 11px' }}>{c.title}</h3>
                  <p style={{ color:'var(--text2)', fontSize:14, lineHeight:1.75, marginBottom:22 }}>{c.desc}</p>
                  <div style={{ display:'flex', gap:20, marginBottom:22, paddingBottom:18, borderBottom:'1px solid var(--border)' }}>
                    {[c.m1,c.m2,c.m3].map(m => (
                      <div key={m.label}>
                        <div style={{ fontFamily:'Sora,sans-serif', fontSize:20, fontWeight:800, color:'#FFC100' }}>{m.val}</div>
                        <div style={{ fontSize:11.5, color:'var(--text3)' }}>{m.label}</div>
                      </div>
                    ))}
                  </div>
                  <a href={c.href==='#contact'?CONTACT:c.href} style={{ color:'#FFC100', textDecoration:'none', fontWeight:600, fontSize:13.5, display:'flex', alignItems:'center', gap:6, transition:'gap 0.2s' }}
                    onMouseEnter={e=>e.currentTarget.style.gap='10px'} onMouseLeave={e=>e.currentTarget.style.gap='6px'}>{c.cta} →</a>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function Gallery({ lang }) {
  const t = T[lang].gallery;
  return (
    <section id="gallery" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg2)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ marginBottom:36, display:'flex', justifyContent:'space-between', alignItems:'flex-end', flexWrap:'wrap', gap:14 }}>
            <div><Tag>{t.tag}</Tag><h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14, letterSpacing:'-0.8px' }}>{t.heading}</h2></div>
            <p style={{ color:'var(--text3)', fontSize:13.5, maxWidth:300, textAlign:'right' }}>{t.subtext}</p>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(270px,1fr))', gap:14 }}>
          {t.items.map((label,i) => (
            <Reveal key={i} delay={(i%3)*60}>
              <div role="img" aria-label={label} style={{ aspectRatio:'4/3', borderRadius:14, overflow:'hidden', background:GALLERY_COLORS[i], position:'relative', cursor:'pointer', transition:'transform 0.25s' }}
                onMouseEnter={e=>e.currentTarget.style.transform='scale(1.02)'} onMouseLeave={e=>e.currentTarget.style.transform=''}>
                <div style={{ position:'absolute', inset:0, display:'flex', flexDirection:'column', alignItems:'center', justifyContent:'center', gap:10, background:'linear-gradient(145deg,rgba(255,255,255,0.08),rgba(0,0,0,0.2))' }}>
                  <span style={{ fontSize:46, filter:'drop-shadow(0 2px 8px rgba(0,0,0,0.3))' }} aria-hidden="true">{GALLERY_ICONS[i]}</span>
                  <span style={{ color:'rgba(255,255,255,0.9)', fontSize:12.5, fontWeight:600, textAlign:'center', padding:'0 16px' }}>{label}</span>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
        <p style={{ marginTop:16, color:'var(--text3)', fontSize:12.5, textAlign:'center' }}>{t.placeholder}</p>
      </div>
    </section>
  );
}

function Testimonials({ lang }) {
  const t = T[lang].testimonials;
  return (
    <section id="testimonials" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--navy)', position:'relative', overflow:'hidden' }}>
      <div style={{ position:'absolute', top:'-30%', right:'-10%', width:500, height:500, borderRadius:'50%', background:'radial-gradient(circle,rgba(255,193,0,0.15),transparent 70%)' }}></div>
      <div style={{ maxWidth:1280, margin:'0 auto', position:'relative', zIndex:1 }}>
        <Reveal>
          <div style={{ textAlign:'center', marginBottom:52 }}>
            <Tag light>{t.tag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14, letterSpacing:'-0.8px', color:'#fff' }}>{t.heading}</h2>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(300px,1fr))', gap:18 }}>
          {t.items.map((item,i) => (
            <Reveal key={i} delay={i*70}>
              <div style={{ background:'rgba(255,255,255,0.06)', backdropFilter:'blur(10px)', border:'1px solid rgba(255,255,255,0.1)', borderRadius:16, padding:26 }}>
                <div style={{ color:'#A9FDAC', fontSize:28, marginBottom:10, lineHeight:1 }}>"</div>
                <p style={{ color:'rgba(255,255,255,0.8)', fontSize:14.5, lineHeight:1.8, marginBottom:18 }}>{item.text}</p>
                <div>
                  <div style={{ fontWeight:600, color:'#fff', fontSize:13.5 }}>{item.name}</div>
                  <div style={{ color:'rgba(255,255,255,0.5)', fontSize:12.5 }}>{item.org}</div>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function Footer({ lang }) {
  const t = T[lang];
  const nav = t.nav;
  const NAV_LINKS = [
    { label:nav.home,        href:HOME },
    { label:nav.services,    href:'#services' },
    { label:nav.smartSchool, href:'#smart-school' },
    { label:nav.caseStudies, href:'#case-studies' },
    { label:nav.govt,        href:GOVT },
    { label:nav.about,       href:'#about' },
    { label:nav.contact,     href:CONTACT },
  ];
  return (
    <footer style={{ background:'var(--bg2)', borderTop:'1px solid var(--border)', padding:'clamp(28px,4vw,52px) clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', justifyContent:'space-between', alignItems:'center', flexWrap:'wrap', gap:18 }}>
        <div style={{ display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:32, width:'auto', display:'block' }} />
        </div>
        <div style={{ display:'flex', gap:20, flexWrap:'wrap' }}>
          {NAV_LINKS.map(l => (
            <a key={l.label} href={l.href} style={{ color:'var(--text3)', textDecoration:'none', fontSize:12.5, transition:'color 0.2s' }}
              onMouseEnter={e=>e.target.style.color='#FFC100'} onMouseLeave={e=>e.target.style.color='var(--text3)'}>{l.label}</a>
          ))}
        </div>
        <p style={{ color:'var(--text3)', fontSize:12.5 }}>{t.footer.copy}</p>
      </div>
    </footer>
  );
}

function App() {
  const [dark, setDark] = useState(() => { const s=localStorage.getItem('ai-theme'); return s?s==='dark':window.matchMedia('(prefers-color-scheme: dark)').matches; });
  const [lang, setLang] = useState(() => localStorage.getItem('ai-lang')||'en');
  const [fsIdx, setFsIdx] = useState(() => Number(localStorage.getItem('ai-fs')||0));
  const cycleFontSize = () => setFsIdx(i => (i+1)%FS_STEPS.length);

  useEffect(() => { document.documentElement.setAttribute('data-theme',dark?'dark':'light'); localStorage.setItem('ai-theme',dark?'dark':'light'); }, [dark]);
  useEffect(() => { document.body.setAttribute('data-lang',lang); localStorage.setItem('ai-lang',lang); document.documentElement.lang=lang; }, [lang]);
  useEffect(() => { localStorage.setItem('ai-fs',fsIdx); }, [fsIdx]);
  useEffect(() => {
    if (window.location.hash) {
      const el = document.querySelector(window.location.hash);
      if (el) setTimeout(() => el.scrollIntoView({ behavior:'smooth', block:'start' }), 50);
    }
  }, []);

  const zoom = FS_STEPS[fsIdx].zoom;
  const props = { lang };

  return (
    <>
      <Nav dark={dark} toggleDark={()=>setDark(d=>!d)} lang={lang} setLang={setLang} fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
      <div className="content-zoom" style={{ zoom, transformOrigin:'top center' }}>
        <Hero {...props} />
        <About {...props} />
        <Services {...props} />
        <SmartSchool {...props} />
        <CaseStudies {...props} />
        <Gallery {...props} />
        <Testimonials {...props} />
        <Footer {...props} />
      </div>
    </>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
