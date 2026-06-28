<?php require __DIR__ . '/includes/config.php'; $page_title = 'Government Departments — Access Infra'; require __DIR__ . '/includes/header.php'; ?>
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
body[data-lang="te"] { font-family:'Noto Sans Telugu',sans-serif; }
h1, h2, h3, h4 { font-family:'Sora',sans-serif; line-height:1.15; letter-spacing:0.04em; font-weight:800; }
body[data-lang="kn"] h1, body[data-lang="kn"] h2, body[data-lang="kn"] h3, body[data-lang="kn"] h4 { font-family:'Noto Sans Kannada',sans-serif; letter-spacing:0; }
body[data-lang="hi"] h1, body[data-lang="hi"] h2, body[data-lang="hi"] h3, body[data-lang="hi"] h4 { font-family:'Noto Sans Devanagari',sans-serif; letter-spacing:0; }
body[data-lang="te"] h1, body[data-lang="te"] h2, body[data-lang="te"] h3, body[data-lang="te"] h4 { font-family:'Noto Sans Telugu',sans-serif; letter-spacing:0; }
::selection { background:#1a56db33; }
::-webkit-scrollbar { width:6px; }
::-webkit-scrollbar-track { background:var(--bg2); }
::-webkit-scrollbar-thumb { background:var(--blue); border-radius:3px; }
.grid-dots { background-image:radial-gradient(circle,var(--border) 1px,transparent 1px); background-size:28px 28px; }
.content-zoom { transition:zoom 0.2s ease; }
@media (max-width:900px) { .desktop-controls { display:none !important; } .desktop-nav { display:none !important; } .hamburger { display:flex !important; } }
@media (max-width:768px) { .two-col { grid-template-columns:1fr !important; } }
</style>

<script type="text/babel">
const { useState, useEffect, useRef } = React;
const { DotMorph, AI_SHAPES } = window;

const SITE_URL = '<?php echo esc_js( SITE_URL ); ?>';
const HOME    = SITE_URL + '/';
const ABOUT   = SITE_URL + '/about/';
const GOVT    = SITE_URL + '/government-departments/';
const CONTACT = SITE_URL + '/contact/';
const LOGO_URL = '<?php echo esc_js( asset_url("assets/img/logo.png") ); ?>';
const PRIVACY  = SITE_URL + '/privacy-policy/';
const COOKIEPOLICY = SITE_URL + '/cookie-policy/';

const LANGS = [
  { code:'en', label:'EN',    full:'English'  },
  { code:'kn', label:'ಕನ್ನಡ', full:'ಕನ್ನಡ'    },
  { code:'hi', label:'हि',    full:'हिंदी'    },
  { code:'te', label:'తె',    full:'తెలుగు'  },
];
const FS_STEPS = [
  { zoom:1,    label:'A',   title:'Default size'     },
  { zoom:1.12, label:'A+',  title:'Large size'       },
  { zoom:1.26, label:'A++', title:'Extra large size' },
];

const DEPT_ICONS = { labour:'⚒️', education:'🏫', police:'🚔', fire:'🚒', health:'🏥', minority:'🤝', irrigation:'💧' };

const T = {
  en: {
    nav:{ home:'Home', about:'About Us', services:'Services', smartSchool:'Smart School', caseStudies:'Case Studies', vendor:'Vendor Consulting', govt:'Government Departments', contact:'Contact' },
    back:'← Back to Home',
    heroTag:'Government Lobbying',
    heroH1a:'Departments Where',
    heroH1b:'We Are Lobbyists',
    heroDesc:'Access Infra is an authorised lobbyist to the governments of Karnataka and Telangana — representing vendor interests and facilitating dialogue across 7 key departments.',
    capital:'Capital:',
    activeLobbyist:'Active Lobbyist',
    deptsTag:'7 Departments',
    deptsH2:'Where We Represent Your Interests',
    deptsDesc:'Each department below represents an active lobbying mandate held by Access Infra across Karnataka and Telangana.',
    deptLabel:'Department',
    focusLabel:'Key Focus Areas',
    approachTag:'Our Approach',
    approachH2:'How We Lobby for You',
    ctaH2:'Want access to these departments?',
    ctaDesc:'Reach out to us to discuss how our lobbying relationships can open doors for your business across Karnataka and Telangana.',
    ctaBtn1:'Talk to Us',
    ctaBtn2:'Vendor Consulting →',
    footerCopy:'© 2026 Access Infra Consulting. All rights reserved.',
    states:[
      { name:'Karnataka', capital:'Bengaluru' },
      { name:'Telangana', capital:'Hyderabad' },
    ],
    depts:[
      { id:'labour',    name:'Labour & Welfare Board',  desc:'The Labour & Welfare Board protects the rights of workers — particularly in the unorganised sector — by administering welfare funds, managing registration of workers, overseeing labour dispute resolution, and enforcing minimum wage and safety regulations.', focus:['Worker registration systems','Welfare fund management','Labour inspection tools','Grievance redressal portals','Digital wage monitoring'] },
      { id:'education', name:'Education',               desc:'The Education Department oversees government schools, mid-day meal programmes, teacher recruitment, curriculum development, and scholarship disbursement. It also drives the National Education Policy rollout and Smart School initiatives.', focus:['Smart classroom technology','Learning management systems','School ERP platforms','Student data management','Teacher training tools'] },
      { id:'police',    name:'Police',                  desc:'The Police Department is responsible for maintaining law and order, public safety, crime prevention, and enforcement of laws across the state. It manages emergency response, traffic management, cyber crime, and anti-narcotics operations.', focus:['Surveillance & CCTV infrastructure','Command & control centres','Cybercrime investigation tools','Traffic management systems','Communication technology'] },
      { id:'fire',      name:'Fire & Safety',           desc:'The Fire & Safety Department operates fire stations across urban and rural areas, responding to emergencies and overseeing safety audits of commercial establishments and public awareness programmes on disaster preparedness.', focus:['Fire detection & suppression systems','Emergency response vehicles','Personal protective equipment','Building safety audit tools','Training simulators'] },
      { id:'health',    name:'Health & Wellbeing',      desc:'The Health & Wellbeing Department manages government hospitals, primary health centres, and community health programmes. It drives public health initiatives, disease surveillance, medical supply chains, and digital health records.', focus:['Hospital management systems','Telemedicine infrastructure','Medical equipment procurement','Health data analytics','Ambulance & emergency fleet'] },
      { id:'minority',  name:'Minority Affairs',        desc:'The Minority Affairs Department implements welfare schemes for religious and linguistic minorities, ensuring socio-economic development, educational access, and protection of civil rights. It disburses scholarships, subsidies, and skill development grants.', focus:['Scholarship management portals','Welfare scheme disbursement','Community centre facilities','Skill development programmes','Documentation & identity services'] },
      { id:'irrigation',name:'Irrigation',              desc:'The Irrigation Department manages dams, canals, reservoirs, and water distribution networks critical to agriculture. It oversees construction and maintenance of irrigation infrastructure, water allocation for farmers, and flood control systems.', focus:['Canal & dam monitoring systems','Water level sensors & IoT','Agricultural water management','Flood early warning systems','GIS-based asset mapping'] },
    ],
    steps:[
      { num:'01', title:'Access & Structured Introductions',  desc:'We facilitate formal and transparent introductions with relevant departments and authorized stakeholders, ensuring your products and services are presented through appropriate institutional channels for fair and merit-based evaluation.' },
      { num:'02', title:'Stakeholder Engagement & Relationship Understanding', desc:'We support clients in understanding institutional frameworks and engaging constructively with department officials, administrative leadership, and public representatives—helping build long-term, professional alignment with sector priorities.' },
      { num:'03', title:'Tender & Scheme Insights', desc:'We provide timely insights on upcoming tenders, budgetary directions, and government schemes based on publicly available information, sectoral trends, and policy developments—helping clients prepare proactively and responsibly.' },
      { num:'04', title:'Policy & Technical Advisory Support', desc:'We offer guidance by closely tracking policy developments, participating in industry consultations where appropriate, and helping clients align their solutions with evolving technical, financial, and regulatory expectations of government departments.' },
    ],
  },
  kn: {
    nav:{ home:'ಮುಖಪುಟ', about:'ನಮ್ಮ ಬಗ್ಗೆ', services:'ಸೇವೆಗಳು', smartSchool:'ಸ್ಮಾರ್ಟ್ ಶಾಲೆ', caseStudies:'ಪ್ರಕರಣ ಅಧ್ಯಯನಗಳು', vendor:'ವೆಂಡರ್ ಸಲಹೆ', govt:'ಸರ್ಕಾರಿ ಇಲಾಖೆಗಳು', contact:'ಸಂಪರ್ಕ' },
    back:'← ಮುಖಪುಟಕ್ಕೆ ಹಿಂತಿರುಗಿ',
    heroTag:'ಸರ್ಕಾರಿ ಲಾಬಿಯಿಂಗ್',
    heroH1a:'ನಾವು ಲಾಬಿಸ್ಟ್‌ಗಳಾಗಿರುವ',
    heroH1b:'ಇಲಾಖೆಗಳು',
    heroDesc:'ಆಕ್ಸೆಸ್ ಇನ್ಫ್ರಾ ಕರ್ನಾಟಕ ಮತ್ತು ತೆಲಂಗಾಣ ಸರ್ಕಾರಗಳಿಗೆ ಅಧಿಕೃತ ಲಾಬಿಸ್ಟ್.',
    capital:'ರಾಜಧಾನಿ:',
    activeLobbyist:'ಸಕ್ರಿಯ ಲಾಬಿಸ್ಟ್',
    deptsTag:'7 ಇಲಾಖೆಗಳು',
    deptsH2:'ನಾವು ನಿಮ್ಮ ಹಿತಾಸಕ್ತಿ ಪ್ರತಿನಿಧಿಸುವ ಸ್ಥಳ',
    deptsDesc:'ಕರ್ನಾಟಕ ಮತ್ತು ತೆಲಂಗಾಣದಾದ್ಯಂತ ಆಕ್ಸೆಸ್ ಇನ್ಫ್ರಾ ಹೊಂದಿರುವ ಸಕ್ರಿಯ ಲಾಬಿ ಆದೇಶ.',
    deptLabel:'ಇಲಾಖೆ', focusLabel:'ಪ್ರಮುಖ ಕಾರ್ಯಕ್ಷೇತ್ರಗಳು',
    approachTag:'ನಮ್ಮ ವಿಧಾನ', approachH2:'ನಾವು ನಿಮಗಾಗಿ ಹೇಗೆ ಲಾಬಿ ಮಾಡುತ್ತೇವೆ',
    ctaH2:'ಈ ಇಲಾಖೆಗಳಿಗೆ ಪ್ರವೇಶ ಬೇಕೇ?',
    ctaDesc:'ನಮ್ಮ ಲಾಬಿ ಸಂಬಂಧಗಳು ನಿಮ್ಮ ವ್ಯವಹಾರಕ್ಕೆ ಹೇಗೆ ಅವಕಾಶ ತೆರೆಯಬಲ್ಲವು ಎಂದು ಚರ್ಚಿಸಲು ಸಂಪರ್ಕಿಸಿ.',
    ctaBtn1:'ನಮ್ಮೊಂದಿಗೆ ಮಾತಾಡಿ', ctaBtn2:'ವೆಂಡರ್ ಸಲಹೆ →',
    footerCopy:'© 2026 ಆಕ್ಸೆಸ್ ಇನ್ಫ್ರಾ ಕನ್ಸಲ್ಟಿಂಗ್.',
    states:[ { name:'ಕರ್ನಾಟಕ', capital:'ಬೆಂಗಳೂರು' }, { name:'ತೆಲಂಗಾಣ', capital:'ಹೈದರಾಬಾದ್' } ],
    depts:[
      { id:'labour',    name:'ಕಾರ್ಮಿಕ ಮತ್ತು ಕಲ್ಯಾಣ ಮಂಡಳಿ', desc:'ಅಸಂಘಟಿತ ವಲಯದ ಕಾರ್ಮಿಕರ ಹಕ್ಕುಗಳ ರಕ್ಷಣೆ, ಕಲ್ಯಾಣ ನಿಧಿ ನಿರ್ವಹಣೆ ಮತ್ತು ಕನಿಷ್ಠ ವೇತನ ಜಾರಿ.', focus:['ಕಾರ್ಮಿಕ ನೋಂದಣಿ','ಕಲ್ಯಾಣ ನಿಧಿ','ಕಾರ್ಮಿಕ ತಪಾಸಣೆ','ದೂರು ಪೋರ್ಟಲ್','ಡಿಜಿಟಲ್ ವೇತನ ಮೇಲ್ವಿಚಾರಣೆ'] },
      { id:'education', name:'ಶಿಕ್ಷಣ', desc:'ಸರ್ಕಾರಿ ಶಾಲೆಗಳು, ಮಧ್ಯಾಹ್ನ ಊಟ, ಶಿಕ್ಷಕ ನೇಮಕ, NEP ಮತ್ತು ಸ್ಮಾರ್ಟ್ ಶಾಲಾ ಉಪಕ್ರಮ.', focus:['ಸ್ಮಾರ್ಟ್ ತರಗತಿ','ಕಲಿಕಾ ನಿರ್ವಹಣಾ ವ್ಯವಸ್ಥೆ','ಶಾಲಾ ERP','ವಿದ್ಯಾರ್ಥಿ ಡೇಟಾ','ಶಿಕ್ಷಕ ತರಬೇತಿ'] },
      { id:'police',    name:'ಪೊಲೀಸ್', desc:'ಕಾನೂನು ಮತ್ತು ಸುವ್ಯವಸ್ಥೆ, ಸಾರ್ವಜನಿಕ ಸುರಕ್ಷತೆ, ಸೈಬರ್ ಅಪರಾಧ ಮತ್ತು ತುರ್ತು ಪ್ರತಿಕ್ರಿಯೆ.', focus:['CCTV ಮೂಲಸೌಕರ್ಯ','ಆದೇಶ ಕೇಂದ್ರ','ಸೈಬರ್ ಅಪರಾಧ','ಸಂಚಾರ ನಿರ್ವಹಣೆ','ಸಂವಹನ'] },
      { id:'fire',      name:'ಅಗ್ನಿಶಾಮಕ ಮತ್ತು ಸುರಕ್ಷತೆ', desc:'ಅಗ್ನಿ ತುರ್ತು ಸ್ಥಿತಿ ಮತ್ತು ಕಟ್ಟಡ ಸುರಕ್ಷತಾ ಲೆಕ್ಕಪರಿಶೋಧನೆ.', focus:['ಅಗ್ನಿ ಪತ್ತೆ','ತುರ್ತು ವಾಹನ','ರಕ್ಷಣಾ ಸಲಕರಣೆ','ಸುರಕ್ಷತಾ ಲೆಕ್ಕಪರಿಶೋಧನೆ','ತರಬೇತಿ'] },
      { id:'health',    name:'ಆರೋಗ್ಯ ಮತ್ತು ಯೋಗಕ್ಷೇಮ', desc:'ಸರ್ಕಾರಿ ಆಸ್ಪತ್ರೆ, PHC ಮತ್ತು ಡಿಜಿಟಲ್ ಆರೋಗ್ಯ ದಾಖಲೆ.', focus:['ಆಸ್ಪತ್ರೆ ನಿರ್ವಹಣಾ','ಟೆಲಿಮೆಡಿಸಿನ್','ವೈದ್ಯಕೀಯ ಖರೀದಿ','ಆರೋಗ್ಯ ಡೇಟಾ','ಆಂಬ್ಯುಲೆನ್ಸ್'] },
      { id:'minority',  name:'ಅಲ್ಪಸಂಖ್ಯಾತ ವ್ಯವಹಾರಗಳು', desc:'ಧಾರ್ಮಿಕ ಮತ್ತು ಭಾಷಿಕ ಅಲ್ಪಸಂಖ್ಯಾತರ ಕಲ್ಯಾಣ ಯೋಜನೆ.', focus:['ವಿದ್ಯಾರ್ಥಿವೇತನ ಪೋರ್ಟಲ್','ಕಲ್ಯಾಣ ವಿತರಣೆ','ಸಮುದಾಯ ಕೇಂದ್ರ','ಕೌಶಲ್ಯ ಅಭಿವೃದ್ಧಿ','ಗುರುತಿನ ಸೇವೆ'] },
      { id:'irrigation',name:'ನೀರಾವರಿ', desc:'ಅಣೆಕಟ್ಟೆ, ಕಾಲುವೆ ಮತ್ತು ನೀರು ವಿತರಣಾ ನಿರ್ವಹಣೆ.', focus:['ಕಾಲುವೆ ಮೇಲ್ವಿಚಾರಣೆ','ನೀರಿನ ಮಟ್ಟ IoT','ಕೃಷಿ ನೀರು','ಪ್ರವಾಹ ಎಚ್ಚರಿಕೆ','GIS ನಕ್ಷೆ'] },
    ],
    steps:[
      { num:'01', title:'ಪ್ರವೇಶ ಮತ್ತು ರಚನಾತ್ಮಕ ಪರಿಚಯಗಳು', desc:'ನಿಮ್ಮ ಉತ್ಪನ್ನಗಳು ಮತ್ತು ಸೇವೆಗಳು ಸೂಕ್ತ ಸಾಂಸ್ಥಿಕ ಮಾರ್ಗಗಳ ಮೂಲಕ ನ್ಯಾಯಯುತ ಮತ್ತು ಮೆರಿಟ್-ಆಧಾರಿತ ಮೌಲ್ಯಮಾಪನಕ್ಕೆ ಒಳಪಡುವಂತೆ, ಸಂಬಂಧಿತ ಇಲಾಖೆಗಳು ಮತ್ತು ಅಧಿಕೃತ ಮಧ್ಯಸ್ಥಗಾರರೊಂದಿಗೆ ಔಪಚಾರಿಕ ಮತ್ತು ಪಾರದರ್ಶಕ ಪರಿಚಯಗಳನ್ನು ನಾವು ಸುಗಮಗೊಳಿಸುತ್ತೇವೆ.' },
      { num:'02', title:'ಮಧ್ಯಸ್ಥಗಾರರ ಸಂಪರ್ಕ ಮತ್ತು ಸಂಬಂಧ ತಿಳುವಳಿಕೆ', desc:'ಸಾಂಸ್ಥಿಕ ಚೌಕಟ್ಟುಗಳನ್ನು ಅರ್ಥಮಾಡಿಕೊಳ್ಳಲು ಮತ್ತು ಇಲಾಖಾ ಅಧಿಕಾರಿಗಳು, ಆಡಳಿತಾತ್ಮಕ ನಾಯಕತ್ವ ಹಾಗೂ ಸಾರ್ವಜನಿಕ ಪ್ರತಿನಿಧಿಗಳೊಂದಿಗೆ ರಚನಾತ್ಮಕವಾಗಿ ತೊಡಗಿಸಿಕೊಳ್ಳಲು ನಾವು ಗ್ರಾಹಕರಿಗೆ ಸಹಾಯ ಮಾಡುತ್ತೇವೆ — ವಲಯದ ಆದ್ಯತೆಗಳೊಂದಿಗೆ ದೀರ್ಘಕಾಲೀನ, ವೃತ್ತಿಪರ ಹೊಂದಾಣಿಕೆಯನ್ನು ನಿರ್ಮಿಸಲು ಇದು ಸಹಕಾರಿಯಾಗುತ್ತದೆ.' },
      { num:'03', title:'ಟೆಂಡರ್ ಮತ್ತು ಯೋಜನಾ ಒಳನೋಟಗಳು', desc:'ಸಾರ್ವಜನಿಕವಾಗಿ ಲಭ್ಯವಿರುವ ಮಾಹಿತಿ, ವಲಯದ ಪ್ರವೃತ್ತಿಗಳು ಮತ್ತು ನೀತಿ ಬೆಳವಣಿಗೆಗಳ ಆಧಾರದ ಮೇಲೆ ಮುಂಬರುವ ಟೆಂಡರ್‌ಗಳು, ಬಜೆಟ್ ನಿರ್ದೇಶನಗಳು ಮತ್ತು ಸರ್ಕಾರಿ ಯೋಜನೆಗಳ ಬಗ್ಗೆ ಸಕಾಲಿಕ ಒಳನೋಟಗಳನ್ನು ನಾವು ನೀಡುತ್ತೇವೆ — ಇದು ಗ್ರಾಹಕರಿಗೆ ಪೂರ್ವಭಾವಿಯಾಗಿ ಮತ್ತು ಜವಾಬ್ದಾರಿಯುತವಾಗಿ ತಯಾರಾಗಲು ಸಹಾಯ ಮಾಡುತ್ತದೆ.' },
      { num:'04', title:'ನೀತಿ ಮತ್ತು ತಾಂತ್ರಿಕ ಸಲಹಾ ಬೆಂಬಲ', desc:'ನೀತಿ ಬೆಳವಣಿಗೆಗಳನ್ನು ನಿಕಟವಾಗಿ ಅನುಸರಿಸುವ ಮೂಲಕ, ಸೂಕ್ತವಾದಲ್ಲಿ ಉದ್ಯಮ ಸಮಾಲೋಚನೆಗಳಲ್ಲಿ ಭಾಗವಹಿಸುವ ಮೂಲಕ, ಮತ್ತು ಸರ್ಕಾರಿ ಇಲಾಖೆಗಳ ವಿಕಸನಗೊಳ್ಳುತ್ತಿರುವ ತಾಂತ್ರಿಕ, ಆರ್ಥಿಕ ಮತ್ತು ನಿಯಂತ್ರಕ ನಿರೀಕ್ಷೆಗಳೊಂದಿಗೆ ಗ್ರಾಹಕರ ಪರಿಹಾರಗಳನ್ನು ಹೊಂದಿಸಲು ಸಹಾಯ ಮಾಡುವ ಮೂಲಕ ನಾವು ಮಾರ್ಗದರ್ಶನ ನೀಡುತ್ತೇವೆ.' },
    ],
  },
  hi: {
    nav:{ home:'होम', about:'हमारे बारे में', services:'सेवाएं', smartSchool:'स्मार्ट स्कूल', caseStudies:'केस स्टडीज़', vendor:'वेंडर परामर्श', govt:'सरकारी विभाग', contact:'संपर्क' },
    back:'← होम पर वापस',
    heroTag:'सरकारी लॉबिंग',
    heroH1a:'वे विभाग जहाँ',
    heroH1b:'हम लॉबिस्ट हैं',
    heroDesc:'एक्सेस इन्फ्रा कर्नाटक और तेलंगाना का अधिकृत लॉबिस्ट है।',
    capital:'राजधानी:', activeLobbyist:'सक्रिय लॉबिस्ट',
    deptsTag:'7 विभाग', deptsH2:'जहाँ हम आपके हितों का प्रतिनिधित्व करते हैं',
    deptsDesc:'नीचे प्रत्येक विभाग में सक्रिय लॉबिंग जिम्मेदारी है।',
    deptLabel:'विभाग', focusLabel:'मुख्य फोकस क्षेत्र',
    approachTag:'हमारा दृष्टिकोण', approachH2:'हम आपके लिए कैसे लॉबी करते हैं',
    ctaH2:'इन विभागों तक पहुँच चाहिए?', ctaDesc:'हमसे संपर्क करें।',
    ctaBtn1:'हमसे बात करें', ctaBtn2:'वेंडर परामर्श →',
    footerCopy:'© 2026 एक्सेस इन्फ्रा कंसल्टिंग.',
    states:[ { name:'कर्नाटक', capital:'बेंगलुरु' }, { name:'तेलंगाना', capital:'हैदराबाद' } ],
    depts:[
      { id:'labour',    name:'श्रम एवं कल्याण बोर्ड', desc:'असंगठित क्षेत्र के श्रमिकों के अधिकारों की रक्षा।', focus:['श्रमिक पंजीकरण','कल्याण निधि','श्रम निरीक्षण','शिकायत पोर्टल','डिजिटल वेतन'] },
      { id:'education', name:'शिक्षा', desc:'सरकारी स्कूल, NEP और स्मार्ट स्कूल पहल।', focus:['स्मार्ट क्लासरूम','LMS','स्कूल ERP','छात्र डेटा','शिक्षक प्रशिक्षण'] },
      { id:'police',    name:'पुलिस', desc:'कानून व्यवस्था, साइबर अपराध और आपातकालीन प्रतिक्रिया।', focus:['CCTV','कमांड सेंटर','साइबर क्राइम','यातायात','संचार'] },
      { id:'fire',      name:'अग्नि और सुरक्षा', desc:'अग्नि आपात और भवन सुरक्षा ऑडिट।', focus:['अग्नि पहचान','आपातकालीन वाहन','PPE','सुरक्षा ऑडिट','प्रशिक्षण'] },
      { id:'health',    name:'स्वास्थ्य एवं कल्याण', desc:'सरकारी अस्पताल और डिजिटल स्वास्थ्य रिकॉर्ड।', focus:['HMS','टेलीमेडिसिन','चिकित्सा खरीद','स्वास्थ्य डेटा','एम्बुलेंस'] },
      { id:'minority',  name:'अल्पसंख्यक मामले', desc:'अल्पसंख्यकों के लिए कल्याण योजनाएं।', focus:['छात्रवृत्ति पोर्टल','योजना वितरण','सामुदायिक केंद्र','कौशल विकास','पहचान सेवाएं'] },
      { id:'irrigation',name:'सिंचाई', desc:'बांध, नहर और जल वितरण प्रबंधन।', focus:['नहर निगरानी','IoT सेंसर','कृषि जल','बाढ़ चेतावनी','GIS'] },
    ],
    steps:[
      { num:'01', title:'पहुँच एवं संरचित परिचय', desc:'हम संबंधित विभागों और अधिकृत हितधारकों के साथ औपचारिक एवं पारदर्शी परिचय की सुविधा प्रदान करते हैं, जिससे आपके उत्पाद और सेवाएं उचित संस्थागत माध्यमों से प्रस्तुत होकर निष्पक्ष एवं योग्यता-आधारित मूल्यांकन प्राप्त करें।' },
      { num:'02', title:'हितधारक संपर्क एवं संबंध समझ', desc:'हम ग्राहकों को संस्थागत ढाँचे को समझने और विभागीय अधिकारियों, प्रशासनिक नेतृत्व तथा जनप्रतिनिधियों के साथ सार्थक रूप से जुड़ने में सहायता करते हैं — जिससे क्षेत्रीय प्राथमिकताओं के साथ दीर्घकालिक, पेशेवर तालमेल बनता है।' },
      { num:'03', title:'टेंडर एवं योजना संबंधी जानकारी', desc:'हम सार्वजनिक रूप से उपलब्ध जानकारी, क्षेत्रीय रुझानों और नीतिगत विकास के आधार पर आने वाले टेंडरों, बजटीय दिशाओं और सरकारी योजनाओं पर समय पर जानकारी प्रदान करते हैं — जिससे ग्राहक सक्रिय और जिम्मेदारी से तैयारी कर सकें।' },
      { num:'04', title:'नीति एवं तकनीकी सलाहकार सहायता', desc:'हम नीतिगत विकास पर बारीकी से नज़र रखते हुए, जहाँ उपयुक्त हो उद्योग सलाह-मशविरों में भाग लेकर, और सरकारी विभागों की तकनीकी, वित्तीय एवं नियामक अपेक्षाओं के साथ ग्राहकों के समाधानों को संतुलित करने में सहायता करके मार्गदर्शन प्रदान करते हैं।' },
    ],
  },
  te: {
    nav:{ home:'హోమ్', about:'మా గురించి', services:'సేవలు', smartSchool:'స్మార్ట్ స్కూల్', caseStudies:'కేస్ స్టడీస్', vendor:'వెండర్ కన్సల్టింగ్', govt:'ప్రభుత్వ శాఖలు', contact:'సంప్రదించండి' },
    back:'← హోమ్‌కి తిరిగి',
    heroTag:'ప్రభుత్వ లాబీయింగ్',
    heroH1a:'మేము లాబీయిస్టులుగా',
    heroH1b:'ఉన్న శాఖలు',
    heroDesc:'యాక్సెస్ ఇన్‌ఫ్రా కర్ణాటక మరియు తెలంగాణ అధికారిక లాబీయిస్ట్.',
    capital:'రాజధాని:', activeLobbyist:'యాక్టివ్ లాబీయిస్ట్',
    deptsTag:'7 శాఖలు', deptsH2:'మేము మీ ప్రయోజనాలను ప్రతినిధిత్వం చేసే చోట',
    deptsDesc:'ప్రతి శాఖలో యాక్సెస్ ఇన్‌ఫ్రా సక్రియ లాబీయింగ్ మాండేట్ ఉంది.',
    deptLabel:'శాఖ', focusLabel:'కీలక దృష్టి కేంద్రాలు',
    approachTag:'మా విధానం', approachH2:'మేము మీ కోసం ఎలా లాబీ చేస్తాం',
    ctaH2:'ఈ శాఖలకు ప్రాప్యత కావాలా?', ctaDesc:'మా బృందంతో మాట్లాడండి.',
    ctaBtn1:'మాతో మాట్లాడండి', ctaBtn2:'వెండర్ కన్సల్టింగ్ →',
    footerCopy:'© 2026 యాక్సెస్ ఇన్‌ఫ్రా కన్సల్టింగ్.',
    states:[ { name:'కర్ణాటక', capital:'బెంగళూరు' }, { name:'తెలంగాణ', capital:'హైదరాబాద్' } ],
    depts:[
      { id:'labour',    name:'లేబర్ & వెల్ఫేర్ బోర్డ్', desc:'అసంఘటిత కార్మికుల హక్కుల రక్షణ.', focus:['కార్మిక నమోదు','సంక్షేమ నిధి','లేబర్ తనిఖీ','ఫిర్యాదు పోర్టల్','డిజిటల్ వేతనం'] },
      { id:'education', name:'విద్య', desc:'ప్రభుత్వ పాఠశాలలు, NEP మరియు స్మార్ట్ స్కూల్.', focus:['స్మార్ట్ క్లాస్‌రూమ్','LMS','స్కూల్ ERP','విద్యార్థి డేటా','టీచర్ ట్రైనింగ్'] },
      { id:'police',    name:'పోలీసు', desc:'శాంతి భద్రతలు మరియు సైబర్ నేరాలు.', focus:['CCTV','కమాండ్ సెంటర్','సైబర్ క్రైమ్','ట్రాఫిక్','కమ్యూనికేషన్'] },
      { id:'fire',      name:'అగ్నిమాపక & భద్రత', desc:'అగ్నిప్రమాదాలు మరియు భవన భద్రత ఆడిట్.', focus:['అగ్నిమాపకం','అత్యవసర వాహనాలు','PPE','భద్రత ఆడిట్','శిక్షణ'] },
      { id:'health',    name:'ఆరోగ్యం & సంక్షేమం', desc:'ప్రభుత్వ ఆసుపత్రులు మరియు డిజిటల్ ఆరోగ్య రికార్డులు.', focus:['HMS','టెలిమెడిసిన్','వైద్య సేకరణ','ఆరోగ్య డేటా','అంబులెన్స్'] },
      { id:'minority',  name:'మైనారిటీ వ్యవహారాలు', desc:'మైనారిటీలకు సంక్షేమ పథకాలు.', focus:['స్కాలర్‌షిప్ పోర్టల్','పథకాల పంపిణీ','కమ్యూనిటీ సెంటర్','నైపుణ్యం','గుర్తింపు'] },
      { id:'irrigation',name:'నీటిపారుదల', desc:'ఆనకట్టలు, కాలువలు మరియు జల నిర్వహణ.', focus:['కాలువ పర్యవేక్షణ','IoT సెన్సర్లు','వ్యవసాయ జలం','వరద హెచ్చరిక','GIS'] },
    ],
    steps:[
      { num:'01', title:'యాక్సెస్ & స్ట్రక్చర్డ్ పరిచయాలు', desc:'మీ ఉత్పత్తులు మరియు సేవలు సముచితమైన సంస్థాగత మార్గాల ద్వారా న్యాయమైన మరియు మెరిట్-ఆధారిత మూల్యాంకనానికి ప్రस్తుతం చేయబడేలా, సంబంధిత శాఖలు మరియు అధికారిక భాగస్వాములతో అధికారిక మరియు పారదర్శక పరిచయాలను మేము సులభతరం చేస్తాం.' },
      { num:'02', title:'భాగస్వాముల సంబంధం & అవగాహన', desc:'సంస్థాగత చట్రాలను అర్థం చేసుకోవడంలో మరియు శాఖా అధికారులు, పరిపాలనా నాయకత్వం, ప్రజా ప్రతినిధులతో నిర్మాణాత్మకంగా సంబంధం పెట్టుకోవడంలో మేము క్లయింట్లకు తోడ్పడతాం — ఇది రంగ ప్రాధాన్యతలతో దీర్ఘకాలిక, వృత్తిపరమైన అనుసంధానాన్ని నిర్మించడంలో సహాయపడుతుంది.' },
      { num:'03', title:'టెండర్ & పథకాల అంతర్దృష్టులు', desc:'ప్రజలకు అందుబాటులో ఉన్న సమాచారం, రంగ ధోరణులు మరియు పాలసీ పరిణామాల ఆధారంగా రాబోయే టెండర్లు, బడ్జెట్ దిక్సూచనలు మరియు ప్రభుత్వ పథకాలపై మేము సకాలంలో అంతర్దృష్టులను అందిస్తాం — ఇది క్లయింట్లు ముందస్తుగా మరియు బాధ్యతాయుతంగా సన్నద్ధం కావడానికి సహాయపడుతుంది.' },
      { num:'04', title:'పాలసీ & సాంకేతిక సలహా మద్దతు', desc:'పాలసీ పరిణామాలను నిశితంగా పరిశీలించడం, తగినప్పుడు పరిశ్రమ సంప్రదింపులలో పాల్గొనడం, మరియు ప్రభుత్వ శాఖల అభివృద్ధి చెందుతున్న సాంకేతిక, ఆర్థిక మరియు నియంత్రణ అంచనాలకు అనుగుణంగా క్లయింట్ల పరిష్కారాలను సరిచేయడంలో సహాయపడడం ద్వారా మేము మార్గదర్శకత్వాన్ని అందిస్తాం.' },
    ],
  },
};

function Tag({ children, color='blue' }) {
  const bg  = color==='teal'?'rgba(30,58,138,0.1)':'rgba(26,86,219,0.1)';
  const col = color==='teal'?'#1e3a8a':'#1a56db';
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

function LangMenu({ lang, setLang, nfs }) {
  const [open, setOpen] = useState(false);
  const ref = useRef(null);
  useEffect(() => {
    const fn = e => { if(ref.current && !ref.current.contains(e.target)) setOpen(false); };
    document.addEventListener('mousedown', fn);
    return () => document.removeEventListener('mousedown', fn);
  }, []);
  const cur = LANGS.find(l => l.code===lang);
  return (
    <div ref={ref} style={{ position:'relative' }}>
      <button onClick={()=>setOpen(o=>!o)} style={{ display:'flex', alignItems:'center', gap:6, background:'var(--bg2)', border:'1.5px solid var(--border)', borderRadius:8, padding:'6px 10px', cursor:'pointer', color:'var(--text2)', fontSize:nfs, fontWeight:600, fontFamily:'Inter,sans-serif', transition:'border-color 0.2s' }}
        onMouseEnter={e=>e.currentTarget.style.borderColor='#1a56db'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
        🌐 {cur.label}
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" strokeWidth="2"><polyline points={open?'2,8 6,4 10,8':'2,4 6,8 10,4'}/></svg>
      </button>
      {open && (
        <div style={{ position:'absolute', top:'calc(100% + 6px)', right:0, zIndex:200, background:'var(--surface)', border:'1px solid var(--border)', borderRadius:10, boxShadow:'var(--shadow-lg)', overflow:'hidden', minWidth:140 }}>
          {LANGS.map(l => (
            <button key={l.code} onClick={()=>{ setLang(l.code); setOpen(false); }} style={{ display:'block', width:'100%', textAlign:'left', padding:'10px 16px', background:lang===l.code?'var(--bg2)':'transparent', border:'none', cursor:'pointer', color:lang===l.code?'#1a56db':'var(--text2)', fontSize:nfs, fontWeight:lang===l.code?700:400, fontFamily:'Inter,sans-serif' }}>
              <span style={{ marginRight:8 }}>{l.label}</span>
              <span style={{ color:'var(--text3)', fontSize:nfs-1 }}>{l.full}</span>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

function FontSizeBtn({ fsIdx, cycleFontSize, nfs }) {
  return (
    <button onClick={cycleFontSize} title={FS_STEPS[fsIdx].title} style={{ display:'flex', alignItems:'center', gap:5, background:'var(--bg2)', border:'1.5px solid var(--border)', borderRadius:8, padding:'5px 10px', cursor:'pointer', color:'var(--text2)', fontFamily:'Sora,sans-serif', fontWeight:700, lineHeight:1, whiteSpace:'nowrap' }}
      onMouseEnter={e=>e.currentTarget.style.borderColor='#1a56db'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
      <span style={{ display:'flex', gap:2, alignItems:'center', marginRight:2 }}>
        {FS_STEPS.map((_,i) => <span key={i} style={{ width:4, height:4, borderRadius:'50%', background:i<=fsIdx?'#1a56db':'var(--border)' }} />)}
      </span>
      <span style={{ fontSize:nfs+fsIdx*1.5 }}>{FS_STEPS[fsIdx].label}</span>
    </button>
  );
}

function Nav({ dark, toggleDark, lang, setLang, fsIdx, cycleFontSize }) {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);
  useEffect(() => {
    const fn = () => setScrolled(window.scrollY>30);
    window.addEventListener('scroll', fn);
    return () => window.removeEventListener('scroll', fn);
  }, []);
  const nfs = 13 + fsIdx*1.5;
  const t = T[lang];
  const links = [
    { label:t.nav.home,        href:HOME },
    { label:t.nav.services,    href:ABOUT+'#services' },
    { label:t.nav.smartSchool, href:ABOUT+'#smart-school' },
    { label:t.nav.caseStudies, href:ABOUT+'#case-studies' },
    { label:t.nav.govt,        href:GOVT, active:true },
    { label:t.nav.about,       href:ABOUT },
    { label:t.nav.contact,     href:CONTACT },
  ];
  return (
    <nav className="ai-nav" style={{ position:'sticky', top:0, left:0, right:0, zIndex:100, background:'var(--surface)', borderBottom:'1px solid var(--border)', boxShadow:scrolled?'var(--shadow)':'none', backdropFilter:'blur(12px)', transition:'box-shadow 0.3s', padding:'0 clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', alignItems:'center', justifyContent:'space-between', height:68 }}>
        <a href={HOME} style={{ textDecoration:'none', display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:42, width:'auto', display:'block' }} />
        </a>
        <div className="desktop-nav" style={{ display:'flex', alignItems:'center', gap:2 }}>
          {links.map(l => (
            <a key={l.href+l.label} href={l.href} style={{ color:l.active?'#1a56db':'var(--text2)', textDecoration:'none', fontSize:nfs, fontWeight:l.active?700:500, padding:'6px 10px', borderRadius:6, background:l.active?'rgba(26,86,219,0.08)':'transparent', transition:'all 0.15s' }}
              onMouseEnter={e=>{ e.target.style.color='#1a56db'; e.target.style.background='var(--bg2)'; }}
              onMouseLeave={e=>{ e.target.style.color=l.active?'#1a56db':'var(--text2)'; e.target.style.background=l.active?'rgba(26,86,219,0.08)':'transparent'; }}>
              {l.label}
            </a>
          ))}
        </div>
        <div style={{ display:'flex', alignItems:'center', gap:8 }}>
          <div className="desktop-controls" style={{ display:'flex', alignItems:'center', gap:8 }}>
            <LangMenu lang={lang} setLang={setLang} nfs={nfs} />
            <FontSizeBtn fsIdx={fsIdx} cycleFontSize={cycleFontSize} nfs={nfs} />
          </div>
          <button onClick={toggleDark} style={{ background:'none', border:'1.5px solid var(--border)', borderRadius:999, padding:'6px 10px', cursor:'pointer', color:'var(--text2)', fontSize:nfs, display:'flex', alignItems:'center', gap:5 }}>
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
          {links.map(l => <a key={l.label} href={l.href} onClick={()=>setOpen(false)} style={{ display:'block', padding:'10px 0', color:'var(--text2)', textDecoration:'none', fontSize:nfs, fontWeight:500, borderBottom:'1px solid var(--border)' }}>{l.label}</a>)}
        </div>
      )}
    </nav>
  );
}

function Hero({ t }) {
  return (
    <section id="top" style={{ minHeight:'78vh', display:'flex', flexDirection:'column', justifyContent:'center', padding:'clamp(100px,12vw,140px) clamp(16px,5vw,80px) 60px', background:'linear-gradient(135deg,#0c1f3f 0%,#1e3a8a 55%,#1a56db 100%)', position:'relative', overflow:'hidden' }}>
      <DotMorph shapes={AI_SHAPES.INDIA_STATES} polygons={AI_SHAPES.INDIA_STATE_POLYGONS} fillRatio={0.4} labels={AI_SHAPES.INDIA_STATE_NAMES} intervalMs={2200} fullBleed />
      <div style={{ position:'absolute', inset:0, zIndex:1, background:'linear-gradient(180deg,rgba(12,31,63,0.35) 0%,rgba(12,31,63,0.55) 100%)' }}></div>
      <div style={{ maxWidth:1280, margin:'0 auto', width:'100%', position:'relative', zIndex:2 }}>
        <div style={{ marginBottom:18 }}>
          <a href={HOME} style={{ display:'inline-flex', alignItems:'center', gap:6, color:'rgba(255,255,255,0.75)', fontSize:13, textDecoration:'none', fontWeight:500 }}
            onMouseEnter={e=>e.currentTarget.style.color='#fff'} onMouseLeave={e=>e.currentTarget.style.color='rgba(255,255,255,0.75)'}>{t.back}</a>
        </div>
        <div style={{ display:'flex', gap:12, flexWrap:'wrap', marginBottom:20 }}>
          <Tag>{t.heroTag}</Tag>
          {t.states.map(s => (
            <div key={s.name} style={{ display:'inline-flex', alignItems:'center', gap:8, background:'rgba(255,255,255,0.1)', border:'1px solid rgba(255,255,255,0.25)', borderRadius:999, padding:'5px 14px' }}>
              <span style={{ width:6, height:6, borderRadius:'50%', background:'#93c5fd', display:'inline-block' }}></span>
              <span style={{ fontSize:12.5, color:'rgba(255,255,255,0.85)', fontWeight:500 }}>{t.activeLobbyist} — {s.name}</span>
            </div>
          ))}
        </div>
        <h1 style={{ fontSize:'clamp(32px,5vw,62px)', fontWeight:800, marginBottom:20, lineHeight:1.08, maxWidth:780, color:'#fff' }}>
          {t.heroH1a}<br />
          <span style={{ color:'#93c5fd' }}>{t.heroH1b}</span>
        </h1>
        <p style={{ fontSize:'clamp(15px,1.6vw,18px)', color:'rgba(255,255,255,0.85)', maxWidth:620, lineHeight:1.85 }}>{t.heroDesc}</p>
        <div style={{ marginTop:32, display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(200px,1fr))', gap:14, maxWidth:760 }}>
          {t.states.map(s => (
            <div key={s.name} style={{ background:'rgba(255,255,255,0.1)', border:'1px solid rgba(255,255,255,0.25)', borderRadius:12, padding:'16px 20px', backdropFilter:'blur(6px)' }}>
              <div style={{ fontSize:22, fontWeight:800, fontFamily:'Sora,sans-serif', color:'#fff' }}>{s.name}</div>
              <div style={{ fontSize:12, color:'rgba(255,255,255,0.7)', marginTop:4 }}>{t.capital} {s.capital}</div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function DeptCards({ t }) {
  const [active, setActive] = useState(null);
  return (
    <section id="departments" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ textAlign:'center', marginBottom:52 }}>
            <Tag color="teal">{t.deptsTag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14 }}>{t.deptsH2}</h2>
            <p style={{ color:'var(--text3)', fontSize:16, maxWidth:520, margin:'12px auto 0', lineHeight:1.7 }}>{t.deptsDesc}</p>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(340px,1fr))', gap:18 }}>
          {t.depts.map((dept,i) => (
            <Reveal key={dept.id} delay={(i%4)*60}>
              <div onClick={()=>setActive(active===dept.id?null:dept.id)}
                style={{ background:'var(--surface2)', border:`1px solid ${active===dept.id?'#1a56db44':'var(--border)'}`, borderRadius:18, overflow:'hidden', boxShadow:active===dept.id?'var(--shadow-lg)':'var(--shadow)', transition:'all 0.25s', cursor:'pointer' }}>
                <div style={{ padding:'22px 26px', display:'flex', alignItems:'center', gap:16 }}>
                  <div style={{ width:48, height:48, borderRadius:12, background:'linear-gradient(135deg,#1a56db,#1e3a8a)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:22, flexShrink:0 }}>{DEPT_ICONS[dept.id]||'🏛️'}</div>
                  <div style={{ flex:1 }}>
                    <div style={{ fontSize:11, color:'#1a56db', fontWeight:700, textTransform:'uppercase', letterSpacing:'0.06em', marginBottom:3 }}>{t.deptLabel}</div>
                    <h3 style={{ fontSize:17, fontWeight:700 }}>{dept.name}</h3>
                  </div>
                  <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" style={{ flexShrink:0, transform:active===dept.id?'rotate(180deg)':'none', transition:'transform 0.25s', color:'var(--text3)' }}><polyline points="5,8 10,13 15,8"/></svg>
                </div>
                {active===dept.id && (
                  <div style={{ padding:'0 26px 22px', borderTop:'1px solid var(--border)' }}>
                    <p style={{ color:'var(--text2)', fontSize:14, lineHeight:1.8, marginBottom:18, paddingTop:18 }}>{dept.desc}</p>
                    <div style={{ fontSize:12, color:'var(--text3)', fontWeight:600, textTransform:'uppercase', letterSpacing:'0.06em', marginBottom:10 }}>{t.focusLabel}</div>
                    <div style={{ display:'flex', flexWrap:'wrap', gap:7 }}>
                      {dept.focus.map(f => (
                        <span key={f} style={{ background:'rgba(26,86,219,0.07)', color:'#1a56db', fontSize:12, fontWeight:600, padding:'4px 10px', borderRadius:6, border:'1px solid rgba(26,86,219,0.15)' }}>{f}</span>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function ApproachSection({ t }) {
  return (
    <section style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg2)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ textAlign:'center', marginBottom:52 }}>
            <Tag color="teal">{t.approachTag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14 }}>{t.approachH2}</h2>
          </div>
        </Reveal>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(auto-fill,minmax(250px,1fr))', gap:20 }}>
          {t.steps.map((step, i) => (
            <Reveal key={i} delay={i*70}>
              <div style={{ background:'var(--surface)', border:'1px solid var(--border)', borderRadius:16, padding:26, boxShadow:'var(--shadow)' }}>
                <div style={{ width:40, height:40, borderRadius:10, background:'linear-gradient(135deg,#1a56db,#1e3a8a)', color:'#fff', display:'flex', alignItems:'center', justifyContent:'center', fontWeight:800, fontFamily:'Sora,sans-serif', fontSize:14, marginBottom:16 }}>{step.num}</div>
                <h3 style={{ fontSize:16, fontWeight:700, marginBottom:10 }}>{step.title}</h3>
                <p style={{ fontSize:13.5, color:'var(--text3)', lineHeight:1.7 }}>{step.desc}</p>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function CTABanner({ t }) {
  return (
    <section style={{ padding:'clamp(50px,7vw,100px) clamp(16px,5vw,80px)', background:'var(--navy)', position:'relative', overflow:'hidden' }}>
      <div style={{ position:'absolute', top:'-30%', right:'-10%', width:500, height:500, borderRadius:'50%', background:'radial-gradient(circle,rgba(26,86,219,0.2),transparent 70%)' }}></div>
      <Reveal style={{ maxWidth:1280, margin:'0 auto', position:'relative', zIndex:1, display:'flex', justifyContent:'space-between', alignItems:'center', flexWrap:'wrap', gap:28 }}>
        <div>
          <h2 style={{ fontSize:'clamp(22px,3vw,38px)', fontWeight:800, color:'#fff', marginBottom:10 }}>{t.ctaH2}</h2>
          <p style={{ color:'rgba(255,255,255,0.6)', fontSize:15, maxWidth:480 }}>{t.ctaDesc}</p>
        </div>
        <div style={{ display:'flex', gap:12, flexWrap:'wrap' }}>
          <a href={CONTACT} style={{ background:'linear-gradient(135deg,#1a56db,#1e3a8a)', color:'#fff', textDecoration:'none', padding:'13px 26px', borderRadius:10, fontSize:14, fontWeight:600, boxShadow:'0 4px 20px rgba(26,86,219,0.4)', whiteSpace:'nowrap' }}>{t.ctaBtn1}</a>
          <a href={HOME} style={{ background:'rgba(255,255,255,0.08)', color:'rgba(255,255,255,0.85)', textDecoration:'none', padding:'13px 26px', borderRadius:10, fontSize:14, fontWeight:600, border:'1px solid rgba(255,255,255,0.15)', whiteSpace:'nowrap', transition:'background 0.2s' }}
            onMouseEnter={e=>e.currentTarget.style.background='rgba(255,255,255,0.14)'} onMouseLeave={e=>e.currentTarget.style.background='rgba(255,255,255,0.08)'}>{t.ctaBtn2}</a>
        </div>
      </Reveal>
    </section>
  );
}

function Footer({ t, lang }) {
  const nav = T[lang].nav;
  return (
    <footer style={{ background:'var(--bg2)', borderTop:'1px solid var(--border)', padding:'clamp(20px,3vw,40px) clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', justifyContent:'space-between', alignItems:'center', flexWrap:'wrap', gap:16 }}>
        <div style={{ display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:32, width:'auto', display:'block' }} />
        </div>
        <div style={{ display:'flex', gap:20, flexWrap:'wrap' }}>
          {[[nav.home,HOME],[nav.services,ABOUT+'#services'],[nav.smartSchool,ABOUT+'#smart-school'],[nav.caseStudies,ABOUT+'#case-studies'],[nav.govt,GOVT],[nav.about,ABOUT],[nav.contact,CONTACT],['Privacy Policy',PRIVACY],['Cookie Policy',COOKIEPOLICY]].map(([l,h]) => (
            <a key={l+h} href={h} style={{ color:'var(--text3)', textDecoration:'none', fontSize:12.5, transition:'color 0.2s' }}
              onMouseEnter={e=>e.target.style.color='#1a56db'} onMouseLeave={e=>e.target.style.color='var(--text3)'}>{l}</a>
          ))}
        </div>
        <p style={{ color:'var(--text3)', fontSize:12 }}>{t.footerCopy}</p>
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
  const t = T[lang];

  return (
    <>
      <Nav dark={dark} toggleDark={()=>setDark(d=>!d)} lang={lang} setLang={setLang} fsIdx={fsIdx} cycleFontSize={cycleFontSize} />
      <div className="content-zoom" style={{ zoom, transformOrigin:'top center' }}>
        <Hero t={t} />
        <DeptCards t={t} />
        <ApproachSection t={t} />
        <CTABanner t={t} />
        <Footer t={t} lang={lang} />
      </div>
    </>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
