<?php
require __DIR__ . '/includes/config.php';

// ── CSRF token ──────────────────────────────────────────────────────────────
if (empty($_SESSION['ai_contact_token'])) {
    $_SESSION['ai_contact_token'] = bin2hex(random_bytes(16));
}
$ai_contact_token = $_SESSION['ai_contact_token'];

// ── Form handler ────────────────────────────────────────────────────────────
$status   = isset($_GET['ai_status']) ? preg_replace('/[^a-z]/', '', $_GET['ai_status']) : '';
$old      = ['n' => '', 'e' => '', 'p' => '', 'o' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ai_contact_submit'])) {
    $token_ok = isset($_POST['ai_contact_token']) && hash_equals($_SESSION['ai_contact_token'] ?? '', $_POST['ai_contact_token']);

    $name    = trim($_POST['ai_name']    ?? '');
    $email   = trim($_POST['ai_email']   ?? '');
    $org     = trim($_POST['ai_org']     ?? '');
    $phone   = trim($_POST['ai_phone']   ?? '');
    $service = trim($_POST['ai_service'] ?? '');
    $message = trim($_POST['ai_message'] ?? '');

    if (!$token_ok || $name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $qs = http_build_query(['ai_status' => 'error', 'n' => $name, 'e' => $email, 'p' => $phone, 'o' => $org]);
        header('Location: ' . url('contact.php') . '?' . $qs);
        exit;
    }

    // Best-effort: record the lead in the admin CRM. The contact form must keep
    // working over plain email even if the database is unavailable/unconfigured.
    try {
        require_once __DIR__ . '/lib/db.php';
        require_once __DIR__ . '/lib/crypto.php';
        db()->prepare(
            'INSERT INTO leads (id, source, name_enc, email_bi, email_enc, company_enc, phone_enc, service, message_enc, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        )->execute([
            db_new_id(), 'contact_form',
            encrypt_value($name), blind_index($email), encrypt_value($email),
            encrypt_value($org), encrypt_value($phone), $service, encrypt_value($message),
        ]);
    } catch (Throwable $e) {
        error_log('lead insert failed: ' . $e->getMessage());
    }

    $subject  = "New Enquiry from {$name} — Access Infra Website";
    $textBody = "Name:             {$name}\n";
    $textBody .= "Email:            {$email}\n";
    $textBody .= "Phone/WhatsApp:   {$phone}\n";
    $textBody .= "Organisation:     {$org}\n";
    $textBody .= "Service Interest: {$service}\n\n";
    $textBody .= "Message:\n{$message}\n";
    $htmlBody = '<p><strong>Name:</strong> ' . e($name) . '</p>'
        . '<p><strong>Email:</strong> ' . e($email) . '</p>'
        . '<p><strong>Phone/WhatsApp:</strong> ' . e($phone) . '</p>'
        . '<p><strong>Organisation:</strong> ' . e($org) . '</p>'
        . '<p><strong>Service Interest:</strong> ' . e($service) . '</p>'
        . '<p><strong>Message:</strong><br>' . nl2br(e($message)) . '</p>';

    require_once __DIR__ . '/lib/sendpulse.php';
    $sent = sendpulse_send_email(ADMIN_EMAIL, 'Access Infra', $subject, $htmlBody, $textBody, $email);

    if (!$sent) {
        // Fall back to PHP mail() if SendPulse isn't configured or the request failed.
        $headers = "Content-Type: text/plain; charset=UTF-8\r\nReply-To: {$name} <{$email}>";
        $sent = @mail(ADMIN_EMAIL, $subject, $textBody, $headers);
    }

    if ($sent) {
        $reply_subject = 'Thank you for reaching out — Access Infra Consulting';
        $reply_text = "Dear {$name},\n\nThank you for contacting Access Infra. We have received your message and will get back to you as soon as possible.\n\nBest regards,\nAccess Infra Consulting\n" . ADMIN_EMAIL . "\n" . SITE_URL;
        $reply_html = '<p>Dear ' . e($name) . ',</p>'
            . '<p>Thank you for contacting Access Infra. We have received your message and will get back to you as soon as possible.</p>'
            . '<p>Best regards,<br>Access Infra Consulting<br>' . e(ADMIN_EMAIL) . '<br><a href="' . e(SITE_URL) . '">' . e(SITE_URL) . '</a></p>';

        $replySent = sendpulse_send_email($email, $name, $reply_subject, $reply_html, $reply_text);
        if (!$replySent) {
            @mail($email, $reply_subject, $reply_text, "Content-Type: text/plain; charset=UTF-8");
        }
    }

    unset($_SESSION['ai_contact_token']);
    header('Location: ' . url('contact.php') . '?ai_status=' . ($sent ? 'sent' : 'mailfail'));
    exit;
}

if ($status === 'error') {
    $old['n'] = $_GET['n'] ?? '';
    $old['e'] = $_GET['e'] ?? '';
    $old['p'] = $_GET['p'] ?? '';
    $old['o'] = $_GET['o'] ?? '';
}

$page_title = 'Contact — Access Infra';
require __DIR__ . '/includes/header.php';
?>
<div id="root"></div>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --navy: #0c1f3f; --blue: #1a56db; --teal: #1e3a8a;
  --bg: #ffffff; --bg2: #f1f5f9; --bg3: #e2e8f0;
  --surface: #ffffff; --surface2: #f8fafc;
  --text: #0f172a; --text2: #334155; --text3: #64748b;
  --border: #e2e8f0;
  --shadow: 0 4px 24px rgba(12,31,63,0.08);
  --shadow-lg: 0 12px 48px rgba(12,31,63,0.14);
}
[data-theme="dark"] {
  --bg: #080f1e; --bg2: #0d1a30; --bg3: #132040;
  --surface: #0f1f3d; --surface2: #132040;
  --text: #f1f5f9; --text2: #cbd5e1; --text3: #94a3b8;
  --border: #1e3254;
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
::selection { background:#1a56db33; }
::-webkit-scrollbar { width:6px; }
::-webkit-scrollbar-track { background:var(--bg2); }
::-webkit-scrollbar-thumb { background:var(--blue); border-radius:3px; }
@media (max-width:900px) { .desktop-nav { display:none !important; } .hamburger { display:flex !important; } .desktop-controls { display:none !important; } }
@media (max-width:700px) { .contact-grid-cols { grid-template-columns:1fr !important; } }
@media (max-width:500px) { .contact-form-row { grid-template-columns:1fr !important; } }
.content-zoom { transition:zoom 0.2s ease; }
</style>

<script src="<?php echo url('assets/js/translations.js'); ?>"></script>
<script type="text/babel">
const { useState, useEffect, useRef } = React;
const T = window.TRANSLATIONS;

const SITE_URL = '<?php echo esc_js( SITE_URL ); ?>';
const HOME     = SITE_URL + '/';
const ABOUT    = SITE_URL + '/about/';
const GOVT     = SITE_URL + '/government-departments/';
const CONTACT  = SITE_URL + '/contact/';
const LOGO_URL = SITE_URL + '/assets/img/logo.png';
const PRIVACY  = SITE_URL + '/privacy-policy/';
const COOKIEPOLICY = SITE_URL + '/cookie-policy/';
const ADMIN_EMAIL  = '<?php echo esc_js( ADMIN_EMAIL ); ?>';
const WHATSAPP_URL = 'https://wa.me/919113915713';

const AI_STATUS    = '<?php echo esc_js( $status ); ?>';
const AI_OLD       = <?php echo json_encode($old, JSON_HEX_TAG | JSON_HEX_APOS); ?>;
const AI_CSRF      = '<?php echo esc_js( $ai_contact_token ); ?>';
const AI_FORM_ACTION = '<?php echo esc_js( url('contact.php') ); ?>';

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
  const bg  = color==='teal'?(light?'rgba(30,58,138,0.2)':'rgba(30,58,138,0.1)'):(light?'rgba(26,86,219,0.2)':'rgba(26,86,219,0.1)');
  const col = color==='teal'?'#1e3a8a':(light?'#93c5fd':'#1a56db');
  return <span style={{ display:'inline-block', background:bg, color:col, fontSize:12, fontWeight:700, letterSpacing:'0.06em', textTransform:'uppercase', padding:'4px 12px', borderRadius:999 }}>{children}</span>;
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
        onMouseEnter={e=>e.currentTarget.style.borderColor='#1a56db'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
        🌐 {current.label}
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" strokeWidth="2"><polyline points={open?'2,8 6,4 10,8':'2,4 6,8 10,4'}/></svg>
      </button>
      {open && (
        <div style={{ position:'absolute', top:'calc(100% + 6px)', right:0, zIndex:200, background:'var(--surface)', border:'1px solid var(--border)', borderRadius:10, boxShadow:'var(--shadow-lg)', overflow:'hidden', minWidth:130 }}>
          {LANGS.map(l => (
            <button key={l.code} onClick={()=>{ setLang(l.code); setOpen(false); }} style={{ display:'block', width:'100%', textAlign:'left', padding:'10px 16px', background:lang===l.code?'var(--bg2)':'transparent', border:'none', cursor:'pointer', color:lang===l.code?'#1a56db':'var(--text2)', fontSize:14, fontWeight:lang===l.code?700:400, fontFamily:'Inter,sans-serif', transition:'background 0.15s' }}
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
      onMouseEnter={e=>e.currentTarget.style.borderColor='#1a56db'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
      <span style={{ display:'flex', gap:2, alignItems:'center', marginRight:2 }}>
        {FS_STEPS.map((_,i) => <span key={i} style={{ width:4, height:4, borderRadius:'50%', background:i<=fsIdx?'#1a56db':'var(--border)', transition:'background 0.2s' }} />)}
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
    { label:t.services,    href:ABOUT+'#services' },
    { label:t.smartSchool, href:ABOUT+'#smart-school' },
    { label:t.caseStudies, href:ABOUT+'#case-studies' },
    { label:t.govt,        href:GOVT },
    { label:t.about,       href:ABOUT },
    { label:t.contact,     href:CONTACT, active:true },
  ];
  return (
    <nav className="ai-nav" style={{ position:'sticky', top:0, left:0, right:0, zIndex:100, background:'var(--surface)', borderBottom:'1px solid var(--border)', boxShadow:scrolled?'var(--shadow)':'none', backdropFilter:'blur(12px)', transition:'box-shadow 0.3s', padding:'0 clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', alignItems:'center', justifyContent:'space-between', height:68 }}>
        <a href={HOME} style={{ textDecoration:'none', display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:42, width:'auto', display:'block' }} />
        </a>
        <div className="desktop-nav" style={{ display:'flex', alignItems:'center', gap:2 }}>
          {NAV_LINKS.map(l => (
            <a key={l.label} href={l.href} style={{ color:l.active?'#1a56db':'var(--text2)', textDecoration:'none', fontSize:13, fontWeight:l.active?700:500, padding:'6px 10px', borderRadius:6, transition:'all 0.15s', background:l.active?'rgba(26,86,219,0.08)':'transparent' }}
              onMouseEnter={e=>{ e.target.style.color='#1a56db'; e.target.style.background='var(--bg2)'; }}
              onMouseLeave={e=>{ e.target.style.color=l.active?'#1a56db':'var(--text2)'; e.target.style.background=l.active?'rgba(26,86,219,0.08)':'transparent'; }}>{l.label}</a>
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
                {LANGS.map(l => (
                  <button key={l.code} onClick={()=>setLang(l.code)} style={{ padding:'5px 10px', borderRadius:6, border:'1.5px solid', borderColor:lang===l.code?'#1a56db':'var(--border)', background:lang===l.code?'rgba(26,86,219,0.1)':'var(--bg2)', color:lang===l.code?'#1a56db':'var(--text2)', fontSize:12.5, fontWeight:600, cursor:'pointer' }}>{l.label}</button>
                ))}
              </div>
            </div>
            <FontSizeBtn fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
          </div>
        </div>
      )}
    </nav>
  );
}

function StatusBanner() {
  if (AI_STATUS === 'sent') {
    return (
      <div style={{ maxWidth:1280, margin:'24px auto 0', padding:'0 clamp(16px,5vw,80px)' }}>
        <div style={{ display:'flex', alignItems:'center', gap:12, background:'rgba(16,185,129,0.12)', border:'1px solid rgba(16,185,129,0.3)', color:'#059669', borderRadius:12, padding:'16px 20px', fontSize:14.5 }}>
          <span style={{ fontSize:20 }}>✅</span>
          <div><strong>Message sent!</strong> Thank you for reaching out. We'll be in touch within one business day. Check your inbox for a confirmation copy.</div>
        </div>
      </div>
    );
  }
  if (AI_STATUS === 'error') {
    return (
      <div style={{ maxWidth:1280, margin:'24px auto 0', padding:'0 clamp(16px,5vw,80px)' }}>
        <div style={{ display:'flex', alignItems:'center', gap:12, background:'rgba(239,68,68,0.12)', border:'1px solid rgba(239,68,68,0.3)', color:'#dc2626', borderRadius:12, padding:'16px 20px', fontSize:14.5 }}>
          <span style={{ fontSize:20 }}>⚠️</span>
          <div><strong>Please fill in all required fields</strong> and try again. If the problem persists, email us directly.</div>
        </div>
      </div>
    );
  }
  if (AI_STATUS === 'mailfail') {
    return (
      <div style={{ maxWidth:1280, margin:'24px auto 0', padding:'0 clamp(16px,5vw,80px)' }}>
        <div style={{ display:'flex', alignItems:'center', gap:12, background:'rgba(239,68,68,0.12)', border:'1px solid rgba(239,68,68,0.3)', color:'#dc2626', borderRadius:12, padding:'16px 20px', fontSize:14.5 }}>
          <span style={{ fontSize:20 }}>❌</span>
          <div><strong>Server error — message not delivered.</strong> Please email us directly at <a href={'mailto:'+ADMIN_EMAIL} style={{ color:'inherit', textDecoration:'underline' }}>{ADMIN_EMAIL}</a>.</div>
        </div>
      </div>
    );
  }
  return null;
}

function ContactPage({ lang }) {
  const t = T[lang].contact;
  return (
    <section style={{ padding:'clamp(60px,8vw,100px) clamp(16px,5vw,80px) clamp(60px,8vw,100px)', background:'var(--bg)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <div style={{ marginBottom:48 }}>
          <Tag>{t.tag}</Tag>
          <h1 style={{ fontSize:'clamp(30px,4.5vw,52px)', fontWeight:800, marginTop:16, letterSpacing:'-1px', lineHeight:1.1 }}>
            {t.heading.split('\n').map((line,i) => <span key={i}>{line}{i===0&&<br />}</span>)}
          </h1>
          <p style={{ color:'var(--text2)', fontSize:16, maxWidth:640, marginTop:18, lineHeight:1.8 }}>{t.desc}</p>
        </div>

        <div className="contact-grid-cols" style={{ display:'grid', gridTemplateColumns:'1fr 1.6fr', gap:40 }}>
          <div>
            <div style={{ display:'flex', alignItems:'center', gap:14, marginBottom:24 }}>
              <div style={{ width:46, height:46, borderRadius:12, background:'rgba(26,86,219,0.12)', border:'1px solid rgba(26,86,219,0.25)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:19, flexShrink:0 }}>📧</div>
              <div>
                <div style={{ fontSize:12, color:'var(--text3)', fontWeight:500 }}>{t.emailLabel}</div>
                <a href={'mailto:'+ADMIN_EMAIL} style={{ color:'var(--text)', fontSize:15, fontWeight:600, textDecoration:'none' }}>{ADMIN_EMAIL}</a>
              </div>
            </div>
            <div style={{ display:'flex', alignItems:'center', gap:14, marginBottom:24 }}>
              <div style={{ width:46, height:46, borderRadius:12, background:'rgba(37,211,102,0.12)', border:'1px solid rgba(37,211,102,0.3)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:19, flexShrink:0 }}>💬</div>
              <div>
                <div style={{ fontSize:12, color:'var(--text3)', fontWeight:500 }}>WhatsApp</div>
                <a href={WHATSAPP_URL} target="_blank" rel="noopener noreferrer" style={{ color:'#25d366', fontSize:15, fontWeight:700, textDecoration:'none' }}>WhatsApp Us →</a>
              </div>
            </div>
            <div style={{ display:'flex', alignItems:'center', gap:14, marginBottom:24 }}>
              <div style={{ width:46, height:46, borderRadius:12, background:'rgba(26,86,219,0.12)', border:'1px solid rgba(26,86,219,0.25)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:19, flexShrink:0 }}>📍</div>
              <div>
                <div style={{ fontSize:12, color:'var(--text3)', fontWeight:500 }}>{t.presenceLabel}</div>
                <span style={{ color:'var(--text)', fontSize:15, fontWeight:600 }}>{t.presenceVal}</span>
              </div>
            </div>
          </div>

          <div style={{ background:'var(--surface2)', border:'1px solid var(--border)', borderRadius:18, padding:'clamp(24px,3vw,36px)', boxShadow:'var(--shadow)' }}>
            <h3 style={{ fontSize:18, fontWeight:700, marginBottom:22 }}>{t.formHeading}</h3>
            <form method="POST" action={AI_FORM_ACTION}>
              <input type="hidden" name="ai_contact_token" value={AI_CSRF} />
              <input type="hidden" name="ai_contact_submit" value="1" />

              <div className="contact-form-row" style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:16, marginBottom:16 }}>
                <div>
                  <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>{t.fields[0].label} <span style={{ color:'#1e3a8a' }}>*</span></label>
                  <input type="text" name="ai_name" placeholder={t.fields[0].placeholder} required defaultValue={AI_OLD.n}
                    style={{ width:'100%', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none' }} />
                </div>
                <div>
                  <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>{t.fields[1].label} <span style={{ color:'#1e3a8a' }}>*</span></label>
                  <input type="email" name="ai_email" placeholder={t.fields[1].placeholder} required defaultValue={AI_OLD.e}
                    style={{ width:'100%', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none' }} />
                </div>
              </div>

              <div className="contact-form-row" style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:16, marginBottom:16 }}>
                <div>
                  <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>Phone / WhatsApp</label>
                  <input type="tel" name="ai_phone" placeholder="+91 98765 43210" defaultValue={AI_OLD.p}
                    style={{ width:'100%', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none' }} />
                </div>
                <div>
                  <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>{t.fields[2].label}</label>
                  <input type="text" name="ai_org" placeholder={t.fields[2].placeholder} defaultValue={AI_OLD.o}
                    style={{ width:'100%', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none' }} />
                </div>
              </div>

              <div style={{ marginBottom:16 }}>
                <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>Service Interest</label>
                <select name="ai_service" style={{ width:'100%', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none', cursor:'pointer' }}>
                  <option value="">— Select a service —</option>
                  <option value="Vendor Scouting &amp; Empanelment">Vendor Scouting &amp; Empanelment</option>
                  <option value="Compliance &amp; Licensing Support">Compliance &amp; Licensing Support</option>
                  <option value="Bid &amp; Tender Support">Bid &amp; Tender Support</option>
                  <option value="Stakeholder Engagement">Stakeholder Engagement</option>
                  <option value="Project Monitoring &amp; Reporting">Project Monitoring &amp; Reporting</option>
                  <option value="General Enquiry">General Enquiry</option>
                </select>
              </div>

              <div style={{ marginBottom:22 }}>
                <label style={{ display:'block', fontSize:12.5, fontWeight:600, color:'var(--text3)', marginBottom:6 }}>{t.messageLabel} <span style={{ color:'#1e3a8a' }}>*</span></label>
                <textarea name="ai_message" placeholder={t.messagePlaceholder} required
                  style={{ width:'100%', minHeight:130, resize:'vertical', background:'var(--bg2)', border:'1px solid var(--border)', borderRadius:8, padding:'10px 14px', color:'var(--text)', fontFamily:'inherit', fontSize:14, outline:'none' }}></textarea>
              </div>

              <button type="submit" style={{ width:'100%', padding:'13px', background:'linear-gradient(135deg,#1a56db,#1e3a8a)', border:'none', borderRadius:10, color:'#fff', fontFamily:'inherit', fontSize:15, fontWeight:700, cursor:'pointer' }}>{t.submit}</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}

function Footer({ lang }) {
  const nav = T[lang].nav;
  const t = T[lang].footer;
  return (
    <footer style={{ background:'var(--bg2)', borderTop:'1px solid var(--border)', padding:'clamp(20px,3vw,40px) clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', justifyContent:'space-between', alignItems:'center', flexWrap:'wrap', gap:16 }}>
        <div style={{ display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:32, width:'auto', display:'block' }} />
        </div>
        <div style={{ display:'flex', gap:20, flexWrap:'wrap' }}>
          {[[nav.home,HOME],[nav.services,ABOUT+'#services'],[nav.smartSchool,ABOUT+'#smart-school'],[nav.caseStudies,ABOUT+'#case-studies'],[nav.govt,GOVT],[nav.about,ABOUT],[nav.contact,CONTACT],['Privacy Policy',PRIVACY],['Cookie Policy',COOKIEPOLICY]].map(([l,h]) => (
            <a key={h} href={h} style={{ color:'var(--text3)', textDecoration:'none', fontSize:12.5, transition:'color 0.2s' }}
              onMouseEnter={e=>e.target.style.color='#1a56db'} onMouseLeave={e=>e.target.style.color='var(--text3)'}>{l}</a>
          ))}
        </div>
        <p style={{ color:'var(--text3)', fontSize:12 }}>{t.copy}</p>
      </div>
      <div style={{ maxWidth:1280, margin:'14px auto 0', paddingTop:14, borderTop:'1px solid var(--border)', textAlign:'center' }}>
        <p style={{ color:'var(--text3)', fontSize:11.5 }}>
          Developed and maintained by <a href="https://aqualeo.co" target="_blank" rel="noopener noreferrer" style={{ color:'#1a56db', textDecoration:'none', fontWeight:600 }}>Aqualeo Digecom</a>
        </p>
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

  const zoom = FS_STEPS[fsIdx].zoom;

  return (
    <>
      <Nav dark={dark} toggleDark={()=>setDark(d=>!d)} lang={lang} setLang={setLang} fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
      <div className="content-zoom" style={{ zoom, transformOrigin:'top center' }}>
        <StatusBanner />
        <ContactPage lang={lang} />
        <Footer lang={lang} />
      </div>
    </>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
