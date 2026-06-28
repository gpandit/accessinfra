<?php require __DIR__ . '/includes/config.php'; $page_title = 'Vendor Consulting — Access Infra'; require __DIR__ . '/includes/header.php'; ?>
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
  --surface: #1c3a6b; --surface2: #132040;
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
@keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
.marquee-track { animation:marquee 28s linear infinite; }
.marquee-track:hover { animation-play-state:paused; }
.content-zoom { transition:zoom 0.2s ease; }
.brand-logo { border-radius:8px; transition:background 0.2s, padding 0.2s; }
[data-theme="dark"] .brand-logo { background:#fff; padding:6px 10px; }
@media (max-width:900px) { .desktop-controls { display:none !important; } .desktop-nav { display:none !important; } .hamburger { display:flex !important; } }
@media (max-width:768px) {
  .two-col { grid-template-columns:1fr !important; }
  .activity-card { grid-template-columns:1fr !important; }
  .activity-num { padding:14px 0 !important; }
  .activity-body { grid-template-columns:1fr !important; padding:22px !important; }
  .activity-body > div:last-child { min-width:0 !important; margin-top:4px; }
}
</style>

<script type="text/babel">
const { useState, useEffect, useRef } = React;
const { DotMorph, AI_SHAPES } = window;

const SITE_URL = '<?php echo esc_js( SITE_URL ); ?>';
const HOME     = SITE_URL + '/';
const ABOUT    = SITE_URL + '/about/';
const GOVT     = SITE_URL + '/government-departments/';
const CONTACT  = SITE_URL + '/contact/';
const LOGO_URL = '<?php echo esc_js( asset_url("assets/img/logo.png") ); ?>';
const PRIVACY  = SITE_URL + '/privacy-policy/';
const COOKIEPOLICY = SITE_URL + '/cookie-policy/';

const LANGS = [
  { code:'en', label:'EN',    full:'English' },
  { code:'kn', label:'ಕನ್ನಡ', full:'ಕನ್ನಡ'   },
  { code:'hi', label:'हि',    full:'हिंदी'   },
  { code:'te', label:'తె',    full:'తెలుగు' },
];
const FS_STEPS = [
  { zoom:1,    label:'A',   title:'Default size'     },
  { zoom:1.12, label:'A+',  title:'Large size'       },
  { zoom:1.26, label:'A++', title:'Extra large size' },
];

const T = {
  en: {
    nav:{ home:'Home', about:'About Us', services:'Services', smartSchool:'Smart School', caseStudies:'Case Studies', vendor:'Vendor Consulting', govt:'Government Departments', contact:'Contact' },
    heroLabels:['Bridges','Schools','Solar Farms','Highways','Power Grids','Community Halls'],
    heroTag:'Vendor Consulting',
    heroH1a:'We Help Vendors Navigate',
    heroH1b:'Government Markets',
    heroDesc:'From identifying the right opportunity to winning the bid and getting paid — Access Infra is your strategic partner at every stage of engaging with government.',
    heroCta1:'Explore Our Services',
    heroCta2:'Get in Touch',
    vendorsBanner:'Vendors we have worked with',
    activitiesTag:'What We Do',
    activitiesH2:'Our Consulting Activities',
    activitiesDesc:'A full-spectrum advisory practice built for vendors who want to grow with government.',
    ctaH2:'Ready to enter government markets?',
    ctaDesc:'Talk to our team and find out how we can help your company identify, pursue and win government contracts.',
    ctaBtn1:'Get Started',
    ctaBtn2:'View Govt. Departments →',
    footerCopy:'© 2026 Access Infra Consulting. All rights reserved.',
    activities:[
      { num:'01', accent:'#1a56db', subtitle:'Fit Assessment & Positioning', title:'Placement Advisory', desc:'We analyse your product and service offerings to identify the government departments, schemes and projects where your solutions are most relevant and will be accepted. We map your strengths to live procurement pipelines and help you position effectively.', points:['Department & scheme fit analysis','Competitive landscape mapping','Stakeholder alignment strategy','Entry-point identification'] },
      { num:'02', accent:'#1e3a8a', subtitle:'Eligibility & Compliance Checks', title:'Tender Qualification Advisory', desc:'Before you commit resources, we assess whether your company qualifies for a specific tender — reviewing eligibility criteria, turnover requirements, technical qualifications, and past-performance clauses — so you only pursue tenders you can win.', points:['Eligibility criteria review','Turnover & net-worth checks','Technical qualification gaps','Pre-bid query support'] },
      { num:'03', accent:'#1a56db', subtitle:'End-to-End Bid Management', title:'Tender Drafting Services', desc:'Our team prepares technically and commercially strong bid documents — from PQ / RFQ responses to full technical proposals — ensuring compliance with government formats while highlighting your unique value proposition.', points:['Pre-qualification documents','Technical & commercial bids','BOQ structuring','Compliance checklist & review'] },
      { num:'04', accent:'#1e3a8a', subtitle:'Ongoing Advisory Post-Award', title:'Project Lifecycle Support', desc:'Winning is only the beginning. We stand by you through delivery — facilitating milestone-based payments, resolving grievances with departments, managing variation orders, and navigating any disputes that arise during execution.', points:['Milestone payment facilitation','Grievance resolution','Variation & VO management','Department liaison support'] },
      { num:'05', accent:'#1a56db', subtitle:'Proactive Proposal Development', title:'New Project Ideation', desc:'We help vendors think ahead. We brainstorm opportunities where your products and services could solve pressing government problems, craft unsolicited proposal concepts, and connect you with the right officers to present your ideas — before a tender is even floated.', points:['Opportunity brainstorming sessions','Unsolicited proposal drafting','Product-to-scheme matchmaking','Officer introductions & meetings'] },
    ],
  },
  kn: {
    nav:{ home:'ಮುಖಪುಟ', about:'ನಮ್ಮ ಬಗ್ಗೆ', services:'ಸೇವೆಗಳು', smartSchool:'ಸ್ಮಾರ್ಟ್ ಶಾಲೆ', caseStudies:'ಪ್ರಕರಣ ಅಧ್ಯಯನಗಳು', vendor:'ವೆಂಡರ್ ಸಲಹೆ', govt:'ಸರ್ಕಾರಿ ಇಲಾಖೆಗಳು', contact:'ಸಂಪರ್ಕ' },
    heroLabels:['ಸೇತುವೆಗಳು','ಶಾಲೆಗಳು','ಸೌರ ಫಾರ್ಮ್‌ಗಳು','ಹೆದ್ದಾರಿಗಳು','ವಿದ್ಯುತ್ ಗ್ರಿಡ್‌ಗಳು','ಸಮುದಾಯ ಭವನಗಳು'],
    heroTag:'ವೆಂಡರ್ ಸಲಹೆ',
    heroH1a:'ಸರ್ಕಾರಿ ಮಾರುಕಟ್ಟೆಯಲ್ಲಿ',
    heroH1b:'ನ್ಯಾವಿಗೇಟ್ ಮಾಡಲು ನೆರವು',
    heroDesc:'ಸರಿಯಾದ ಅವಕಾಶ ಗುರುತಿಸುವಿಕೆಯಿಂದ ಬಿಡ್ ಗೆಲ್ಲುವವರೆಗೆ — ಆಕ್ಸೆಸ್ ಇನ್ಫ್ರಾ ಸರ್ಕಾರದೊಂದಿಗೆ ಪ್ರತಿ ಹಂತದಲ್ಲೂ ನಿಮ್ಮ ಕಾರ್ಯತಂತ್ರ ಪಾಲುದಾರ.',
    heroCta1:'ನಮ್ಮ ಸೇವೆಗಳು ನೋಡಿ',
    heroCta2:'ಸಂಪರ್ಕಿಸಿ',
    vendorsBanner:'ನಾವು ಕೆಲಸ ಮಾಡಿದ ವೆಂಡರ್‌ಗಳು',
    activitiesTag:'ನಾವು ಏನು ಮಾಡುತ್ತೇವೆ',
    activitiesH2:'ನಮ್ಮ ಸಲಹಾ ಚಟುವಟಿಕೆಗಳು',
    activitiesDesc:'ಸರ್ಕಾರದೊಂದಿಗೆ ಬೆಳೆಯಲು ಬಯಸುವ ವೆಂಡರ್‌ಗಳಿಗಾಗಿ ನಿರ್ಮಿಸಲಾದ ಸಂಪೂರ್ಣ ಸಲಹಾ ಸೇವೆ.',
    ctaH2:'ಸರ್ಕಾರಿ ಮಾರುಕಟ್ಟೆ ಪ್ರವೇಶಿಸಲು ಸಿದ್ಧರಿದ್ದೀರಾ?',
    ctaDesc:'ನಿಮ್ಮ ಕಂಪನಿ ಸರ್ಕಾರಿ ಟೆಂಡರ್‌ಗಳನ್ನು ಗುರುತಿಸಲು ಮತ್ತು ಗೆಲ್ಲಲು ನಾವು ಹೇಗೆ ಸಹಾಯ ಮಾಡಬಹುದು ಎಂದು ನಮ್ಮ ತಂಡದೊಂದಿಗೆ ಮಾತನಾಡಿ.',
    ctaBtn1:'ಪ್ರಾರಂಭಿಸಿ',
    ctaBtn2:'ಸರ್ಕಾರಿ ಇಲಾಖೆಗಳು ನೋಡಿ →',
    footerCopy:'© 2026 ಆಕ್ಸೆಸ್ ಇನ್ಫ್ರಾ ಕನ್ಸಲ್ಟಿಂಗ್. ಎಲ್ಲ ಹಕ್ಕುಗಳೂ ಕಾಯ್ದಿರಿಸಲಾಗಿದೆ.',
    activities:[
      { num:'01', accent:'#1a56db', subtitle:'ಸ್ಥಾನ ನಿರ್ಣಯ ಮತ್ತು ಹೊಂದಾಣಿಕೆ', title:'ಪ್ಲೇಸ್‌ಮೆಂಟ್ ಸಲಹೆ', desc:'ನಿಮ್ಮ ಉತ್ಪನ್ನ ಮತ್ತು ಸೇವಾ ಕೊಡುಗೆಗಳನ್ನು ವಿಶ್ಲೇಷಿಸಿ ಸೂಕ್ತ ಸರ್ಕಾರಿ ಇಲಾಖೆಗಳನ್ನು ಗುರುತಿಸುತ್ತೇವೆ.', points:['ಇಲಾಖೆ ಮತ್ತು ಯೋಜನೆ ಹೊಂದಾಣಿಕೆ ವಿಶ್ಲೇಷಣೆ','ಸ್ಪರ್ಧಾತ್ಮಕ ಭೂದೃಶ್ಯ ನಕ್ಷೆ','ಮಧ್ಯಸ್ಥಗಾರ ಜೋಡಣೆ ತಂತ್ರ','ಪ್ರವೇಶ ಬಿಂದು ಗುರುತಿಸುವಿಕೆ'] },
      { num:'02', accent:'#1e3a8a', subtitle:'ಅರ್ಹತೆ ಮತ್ತು ಅನುಸರಣೆ ತಪಾಸಣೆ', title:'ಟೆಂಡರ್ ಅರ್ಹತಾ ಸಲಹೆ', desc:'ಸಂಪನ್ಮೂಲ ತೊಡಗಿಸುವ ಮೊದಲು ನಿಮ್ಮ ಕಂಪನಿ ನಿರ್ದಿಷ್ಟ ಟೆಂಡರ್‌ಗೆ ಅರ್ಹವೇ ಎಂದು ಮೌಲ್ಯಮಾಪನ ಮಾಡುತ್ತೇವೆ.', points:['ಅರ್ಹತಾ ಮಾನದಂಡ ಪರಿಶೀಲನೆ','ವಹಿವಾಟು ಮತ್ತು ನಿವ್ವಳ ಮೌಲ್ಯ ತಪಾಸಣೆ','ತಾಂತ್ರಿಕ ಅರ್ಹತಾ ಅಂತರ','ಬಿಡ್-ಪೂರ್ವ ಪ್ರಶ್ನೆ ಬೆಂಬಲ'] },
      { num:'03', accent:'#1a56db', subtitle:'ಸಂಪೂರ್ಣ ಬಿಡ್ ನಿರ್ವಹಣೆ', title:'ಟೆಂಡರ್ ಡ್ರಾಫ್ಟಿಂಗ್ ಸೇವೆ', desc:'PQ/RFQ ಪ್ರತಿಕ್ರಿಯೆಗಳಿಂದ ಸಂಪೂರ್ಣ ತಾಂತ್ರಿಕ ಪ್ರಸ್ತಾವಗಳವರೆಗೆ ಬಲವಾದ ಬಿಡ್ ದಾಖಲೆಗಳನ್ನು ತಯಾರಿಸುತ್ತೇವೆ.', points:['ಪೂರ್ವ-ಅರ್ಹತಾ ದಾಖಲೆಗಳು','ತಾಂತ್ರಿಕ ಮತ್ತು ವಾಣಿಜ್ಯ ಬಿಡ್‌ಗಳು','BoQ ರಚನೆ','ಅನುಸರಣಾ ಪಟ್ಟಿ ಮತ್ತು ಪರಿಶೀಲನೆ'] },
      { num:'04', accent:'#1e3a8a', subtitle:'ಅಡ್ಜ್ಯೂಡಿಕೇಷನ್ ನಂತರ ನಿರಂತರ ಸಲಹೆ', title:'ಪ್ರಾಜೆಕ್ಟ್ ಜೀವನಚಕ್ರ ಬೆಂಬಲ', desc:'ಗೆಲ್ಲುವುದು ಮಾತ್ರ ಆರಂಭ. ಮೈಲ್‌ಸ್ಟೋನ್ ಪಾವತಿ ಮತ್ತು ವಿವಾದ ಪರಿಹಾರದಲ್ಲಿ ನಿಮ್ಮ ಜೊತೆ ನಿಲ್ಲುತ್ತೇವೆ.', points:['ಮೈಲ್‌ಸ್ಟೋನ್ ಪಾವತಿ ಸೌಲಭ್ಯ','ದೂರು ಪರಿಹಾರ','ವ್ಯತ್ಯಾಸ ಆದೇಶ ನಿರ್ವಹಣೆ','ಇಲಾಖಾ ಸಂಪರ್ಕ ಬೆಂಬಲ'] },
      { num:'05', accent:'#1a56db', subtitle:'ಪ್ರಾಕ್ಟಿವ್ ಪ್ರಸ್ತಾವ ಅಭಿವೃದ್ಧಿ', title:'ಹೊಸ ಪ್ರಾಜೆಕ್ಟ್ ವಿಚಾರ ಮಂಥನ', desc:'ಟೆಂಡರ್ ಜಾರಿಗೆ ಬರುವ ಮೊದಲೇ ಸರಿಯಾದ ಅಧಿಕಾರಿಗಳೊಂದಿಗೆ ಸಂಪರ್ಕ ಕಲ್ಪಿಸುತ್ತೇವೆ.', points:['ಅವಕಾಶ ಮಂಥನ ಸೆಶನ್‌ಗಳು','ಸ್ವಯಂಪ್ರೇರಿತ ಪ್ರಸ್ತಾವ ರಚನೆ','ಉತ್ಪನ್ನ-ಯೋಜನೆ ಹೊಂದಾಣಿಕೆ','ಅಧಿಕಾರಿ ಪರಿಚಯ ಮತ್ತು ಸಭೆಗಳು'] },
    ],
  },
  hi: {
    nav:{ home:'होम', about:'हमारे बारे में', services:'सेवाएं', smartSchool:'स्मार्ट स्कूल', caseStudies:'केस स्टडीज़', vendor:'वेंडर परामर्श', govt:'सरकारी विभाग', contact:'संपर्क' },
    heroLabels:['पुल','स्कूल','सौर फार्म','हाईवे','पावर ग्रिड','सामुदायिक भवन'],
    heroTag:'वेंडर परामर्श',
    heroH1a:'वेंडर्स को सरकारी',
    heroH1b:'बाज़ारों में मार्गदर्शन',
    heroDesc:'सही अवसर की पहचान से लेकर बिड जीतने और भुगतान पाने तक — एक्सेस इन्फ्रा आपका रणनीतिक साझेदार है।',
    heroCta1:'हमारी सेवाएं देखें',
    heroCta2:'संपर्क करें',
    vendorsBanner:'जिन वेंडर्स के साथ हमने काम किया है',
    activitiesTag:'हम क्या करते हैं',
    activitiesH2:'हमारी परामर्श गतिविधियाँ',
    activitiesDesc:'सरकार के साथ विकास करने के इच्छुक वेंडर्स के लिए पूर्ण-स्पेक्ट्रम सलाहकारी सेवा।',
    ctaH2:'सरकारी बाज़ारों में प्रवेश के लिए तैयार हैं?',
    ctaDesc:'हमारी टीम से बात करें।',
    ctaBtn1:'शुरू करें',
    ctaBtn2:'सरकारी विभाग देखें →',
    footerCopy:'© 2026 एक्सेस इन्फ्रा कंसल्टिंग. सर्वाधिकार सुरक्षित.',
    activities:[
      { num:'01', accent:'#1a56db', subtitle:'उपयुक्तता मूल्यांकन', title:'प्लेसमेंट परामर्श', desc:'हम विभागों की पहचान करते हैं जहाँ आपके समाधान सबसे अधिक प्रासंगिक होंगे।', points:['विभाग उपयुक्तता विश्लेषण','प्रतिस्पर्धात्मक मानचित्रण','हितधारक रणनीति','प्रवेश बिंदु पहचान'] },
      { num:'02', accent:'#1e3a8a', subtitle:'पात्रता जाँच', title:'टेंडर योग्यता परामर्श', desc:'संसाधन लगाने से पहले पात्रता का मूल्यांकन।', points:['पात्रता समीक्षा','टर्नओवर जाँच','तकनीकी अंतराल','प्री-बिड सहायता'] },
      { num:'03', accent:'#1a56db', subtitle:'संपूर्ण बिड प्रबंधन', title:'टेंडर ड्राफ्टिंग', desc:'PQ/RFQ से पूर्ण तकनीकी प्रस्तावों तक मजबूत बिड दस्तावेज़।', points:['पूर्व-योग्यता दस्तावेज़','तकनीकी व वाणिज्यिक बिड','BOQ संरचना','अनुपालन समीक्षा'] },
      { num:'04', accent:'#1e3a8a', subtitle:'पुरस्कार के बाद सहायता', title:'प्रोजेक्ट लाइफसाइकिल', desc:'जीतना शुरुआत है — डिलीवरी तक हम साथ हैं।', points:['मील भुगतान','शिकायत समाधान','वेरिएशन प्रबंधन','विभागीय संपर्क'] },
      { num:'05', accent:'#1a56db', subtitle:'सक्रिय प्रस्ताव विकास', title:'नई परियोजना विचार', desc:'टेंडर से पहले अधिकारियों से परिचय।', points:['विचार-मंथन','अनचाहे प्रस्ताव','उत्पाद-योजना मिलान','अधिकारी परिचय'] },
    ],
  },
  te: {
    nav:{ home:'హోమ్', about:'మా గురించి', services:'సేవలు', smartSchool:'స్మార్ట్ స్కూల్', caseStudies:'కేస్ స్టడీస్', vendor:'వెండర్ కన్సల్టింగ్', govt:'ప్రభుత్వ శాఖలు', contact:'సంప్రదించండి' },
    heroLabels:['వంతెనలు','పాఠశాలలు','సోలార్ ఫార్మ్‌లు','హైవేలు','విద్యుత్ గ్రిడ్‌లు','కమ్యూనిటీ హాల్స్'],
    heroTag:'వెండర్ కన్సల్టింగ్',
    heroH1a:'వెండర్లు ప్రభుత్వ',
    heroH1b:'మార్కెట్‌లలో నావిగేట్ చేయడానికి',
    heroDesc:'యాక్సెస్ ఇన్‌ఫ్రా మీ వ్యూహాత్మక భాగస్వామి.',
    heroCta1:'మా సేవలు చూడండి',
    heroCta2:'సంప్రదించండి',
    vendorsBanner:'మేము పని చేసిన వెండర్లు',
    activitiesTag:'మేము ఏమి చేస్తాం',
    activitiesH2:'మా కన్సల్టింగ్ కార్యకలాపాలు',
    activitiesDesc:'ప్రభుత్వంతో వృద్ధి చెందాలనుకునే వెండర్లకు సేవ.',
    ctaH2:'సిద్ధంగా ఉన్నారా?',
    ctaDesc:'మా బృందంతో మాట్లాడండి.',
    ctaBtn1:'ప్రారంభించండి',
    ctaBtn2:'ప్రభుత్వ శాఖలు →',
    footerCopy:'© 2026 యాక్సెస్ ఇన్‌ఫ్రా కన్సల్టింగ్.',
    activities:[
      { num:'01', accent:'#1a56db', subtitle:'అనుకూలత అంచనా', title:'ప్లేస్‌మెంట్ అడ్వైజరీ', desc:'మీ పరిష్కారాలకు అనువైన శాఖలను గుర్తిస్తాం.', points:['శాఖ అనుకూలత','పోటీ మ్యాపింగ్','వాటాదారుల వ్యూహం','ప్రవేశ బిందువు'] },
      { num:'02', accent:'#1e3a8a', subtitle:'అర్హత తనిఖీలు', title:'టెండర్ అర్హతా అడ్వైజరీ', desc:'వనరులు పెట్టుబడి పెట్టే ముందు అర్హత అంచనా.', points:['అర్హతా సమీక్ష','టర్నోవర్ తనిఖీ','సాంకేతిక అంతరాలు','ప్రి-బిడ్ సహాయం'] },
      { num:'03', accent:'#1a56db', subtitle:'బిడ్ నిర్వహణ', title:'టెండర్ డ్రాఫ్టింగ్', desc:'బలమైన బిడ్ పత్రాలు తయారు చేస్తాం.', points:['ప్రి-క్వాలిఫికేషన్','సాంకేతిక బిడ్‌లు','BOQ నిర్మాణం','సమ్మతి సమీక్ష'] },
      { num:'04', accent:'#1e3a8a', subtitle:'అవార్డ్ తర్వాత సహాయం', title:'ప్రాజెక్ట్ లైఫ్‌సైకిల్', desc:'గెలవడం కేవలం ప్రారంభం — డెలివరీ వరకు మీతో ఉంటాం.', points:['మైల్‌స్టోన్ చెల్లింపు','ఫిర్యాదు పరిష్కారం','వేరియేషన్ నిర్వహణ','శాఖా సంప్రదింపు'] },
      { num:'05', accent:'#1a56db', subtitle:'ప్రతిపాదన అభివృద్ధి', title:'కొత్త ప్రాజెక్ట్ ఐడియేషన్', desc:'టెండర్ ముందే అధికారులతో పరిచయం.', points:['బ్రెయిన్‌స్టార్మింగ్','అభ్యర్థించని ప్రతిపాదన','ఉత్పత్తి-పథకం మిలాన్','అధికారి పరిచయాలు'] },
    ],
  },
};

const VENDORS = [
  { name:'TechBridge Solutions', abbr:'TB', color:'#1a56db' },
  { name:'NovaSystems',          abbr:'NS', color:'#1e3a8a' },
  { name:'CivicTech India',      abbr:'CT', color:'#7c3aed' },
  { name:'DataPulse',            abbr:'DP', color:'#dc2626' },
  { name:'Infracore Ltd',        abbr:'IC', color:'#ea580c' },
  { name:'SmartGov Pro',         abbr:'SG', color:'#0891b2' },
  { name:'VisionX',              abbr:'VX', color:'#059669' },
  { name:'BridgePoint',          abbr:'BP', color:'#4f46e5' },
  { name:'Nexalink',             abbr:'NL', color:'#1e3a8a' },
  { name:'GovTech Hub',          abbr:'GT', color:'#b45309' },
  { name:'SafeNet India',        abbr:'SN', color:'#1a56db' },
  { name:'Urbana Systems',       abbr:'US', color:'#7c3aed' },
];

function Tag({ children, color='blue' }) {
  const bg  = color==='teal'?'rgba(30,58,138,0.1)':color==='white'?'rgba(255,255,255,0.18)':'rgba(26,86,219,0.1)';
  const col = color==='teal'?'#1e3a8a':color==='white'?'#ffffff':'#1a56db';
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
            <button key={l.code} onClick={()=>{ setLang(l.code); setOpen(false); }} style={{ display:'block', width:'100%', textAlign:'left', padding:'10px 16px', background:lang===l.code?'var(--bg2)':'transparent', border:'none', cursor:'pointer', color:lang===l.code?'#1a56db':'var(--text2)', fontSize:nfs, fontWeight:lang===l.code?700:400, fontFamily:'Inter,sans-serif', transition:'background 0.15s' }}
              onMouseEnter={e=>{ if(lang!==l.code) e.currentTarget.style.background='var(--bg2)'; }} onMouseLeave={e=>{ if(lang!==l.code) e.currentTarget.style.background='transparent'; }}>
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
  const step = FS_STEPS[fsIdx];
  return (
    <button onClick={cycleFontSize} title={step.title} style={{ display:'flex', alignItems:'center', gap:5, background:'var(--bg2)', border:'1.5px solid var(--border)', borderRadius:8, padding:'5px 10px', cursor:'pointer', color:'var(--text2)', fontFamily:'Sora,sans-serif', fontWeight:700, transition:'border-color 0.2s', lineHeight:1, whiteSpace:'nowrap' }}
      onMouseEnter={e=>e.currentTarget.style.borderColor='#1a56db'} onMouseLeave={e=>e.currentTarget.style.borderColor='var(--border)'}>
      <span style={{ display:'flex', gap:2, alignItems:'center', marginRight:2 }}>
        {FS_STEPS.map((_,i) => <span key={i} style={{ width:4, height:4, borderRadius:'50%', background:i<=fsIdx?'#1a56db':'var(--border)', transition:'background 0.2s' }} />)}
      </span>
      <span style={{ fontSize:nfs+fsIdx*1.5 }}>{step.label}</span>
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
    { label:t.nav.home,        href:HOME,    active:true },
    { label:t.nav.services,    href:ABOUT+'#services' },
    { label:t.nav.smartSchool, href:ABOUT+'#smart-school' },
    { label:t.nav.caseStudies, href:ABOUT+'#case-studies' },
    { label:t.nav.govt,        href:GOVT },
    { label:t.nav.about,       href:ABOUT },
    { label:t.nav.contact,     href:CONTACT },
  ];
  return (
    <nav className="ai-nav" style={{ position:'sticky', top:0, left:0, right:0, zIndex:100, background:'var(--surface)', borderBottom:'1px solid var(--border)', boxShadow:scrolled?'var(--shadow)':'none', backdropFilter:'blur(12px)', transition:'box-shadow 0.3s', padding:'0 clamp(16px,5vw,80px)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto', display:'flex', alignItems:'center', justifyContent:'space-between', height:68 }}>
        <a href={HOME} className="brand-logo" style={{ textDecoration:'none', display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:42, width:'auto', display:'block' }} />
        </a>
        <div className="desktop-nav" style={{ display:'flex', alignItems:'center', gap:2 }}>
          {links.map(l => (
            <a key={l.href} href={l.href} style={{ color:l.active?'#1a56db':'var(--text2)', textDecoration:'none', fontSize:nfs, fontWeight:l.active?700:500, padding:'6px 10px', borderRadius:6, transition:'all 0.15s', background:l.active?'rgba(26,86,219,0.08)':'transparent' }}
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
          <button onClick={toggleDark} style={{ background:'none', border:'1.5px solid var(--border)', borderRadius:999, padding:'6px 10px', cursor:'pointer', color:'var(--text2)', fontSize:nfs, display:'flex', alignItems:'center', gap:5, transition:'all 0.2s' }}>
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
          {links.map(l => <a key={l.href} href={l.href} onClick={()=>setOpen(false)} style={{ display:'block', padding:'10px 0', color:'var(--text2)', textDecoration:'none', fontSize:nfs, fontWeight:500, borderBottom:'1px solid var(--border)' }}>{l.label}</a>)}
          <div style={{ marginTop:16, paddingTop:14, borderTop:'1px solid var(--border)', display:'flex', alignItems:'center', justifyContent:'space-between', flexWrap:'wrap', gap:10 }}>
            <div style={{ display:'flex', gap:4, flexWrap:'wrap' }}>
              {LANGS.map(l => <button key={l.code} onClick={()=>setLang(l.code)} style={{ padding:'5px 10px', borderRadius:6, border:'1.5px solid', borderColor:lang===l.code?'#1a56db':'var(--border)', background:lang===l.code?'rgba(26,86,219,0.1)':'var(--bg2)', color:lang===l.code?'#1a56db':'var(--text2)', fontSize:nfs-1, fontWeight:600, cursor:'pointer' }}>{l.label}</button>)}
            </div>
            <FontSizeBtn fsIdx={fsIdx} cycleFontSize={cycleFontSize} nfs={nfs} />
          </div>
        </div>
      )}
    </nav>
  );
}

function Hero({ t }) {
  return (
    <section id="top" style={{ minHeight:'78vh', display:'flex', flexDirection:'column', justifyContent:'center', padding:'clamp(100px,12vw,140px) clamp(16px,5vw,80px) 60px', background:'linear-gradient(135deg,#0c1f3f 0%,#1e3a8a 55%,#1a56db 100%)', position:'relative', overflow:'hidden' }}>
      <DotMorph shapes={AI_SHAPES.INFRA} labels={t.heroLabels} fullBleed />
      <div style={{ position:'absolute', inset:0, zIndex:1, background:'linear-gradient(180deg,rgba(12,31,63,0.35) 0%,rgba(12,31,63,0.55) 100%)' }}></div>
      <div style={{ maxWidth:1280, margin:'0 auto', width:'100%', position:'relative', zIndex:2 }}>
        <Tag color="white">{t.heroTag}</Tag>
        <h1 style={{ fontSize:'clamp(32px,5vw,62px)', fontWeight:800, marginTop:16, marginBottom:20, lineHeight:1.08, maxWidth:780, textWrap:'pretty', color:'#fff' }}>
          {t.heroH1a}<br />
          <span style={{ color:'#93c5fd' }}>{t.heroH1b}</span>
        </h1>
        <p style={{ fontSize:'clamp(15px,1.6vw,18px)', color:'rgba(255,255,255,0.85)', maxWidth:620, lineHeight:1.85 }}>{t.heroDesc}</p>
        <div style={{ marginTop:36, display:'flex', gap:10, flexWrap:'wrap' }}>
          <a href="#activities" style={{ background:'#fff', color:'#1e3a8a', textDecoration:'none', padding:'12px 24px', borderRadius:10, fontSize:14, fontWeight:700, boxShadow:'0 4px 20px rgba(12,31,63,0.3)', transition:'transform 0.2s,box-shadow 0.2s' }}
            onMouseEnter={e=>{ e.currentTarget.style.transform='translateY(-2px)'; }}
            onMouseLeave={e=>{ e.currentTarget.style.transform=''; }}>
            {t.heroCta1}
          </a>
          <a href={CONTACT} style={{ background:'rgba(255,255,255,0.1)', color:'#fff', textDecoration:'none', padding:'12px 24px', borderRadius:10, fontSize:14, fontWeight:600, border:'1.5px solid rgba(255,255,255,0.4)', transition:'background 0.2s' }}
            onMouseEnter={e=>{ e.currentTarget.style.background='rgba(255,255,255,0.18)'; }}
            onMouseLeave={e=>{ e.currentTarget.style.background='rgba(255,255,255,0.1)'; }}>
            {t.heroCta2}
          </a>
        </div>
      </div>
    </section>
  );
}

function VendorLogo({ vendor }) {
  return (
    <div style={{ display:'inline-flex', alignItems:'center', gap:12, background:'var(--surface)', border:'1px solid var(--border)', borderRadius:12, padding:'14px 24px', margin:'0 12px', minWidth:180, flexShrink:0, transition:'box-shadow 0.2s,border-color 0.2s' }}
      onMouseEnter={e=>{ e.currentTarget.style.boxShadow='var(--shadow-lg)'; e.currentTarget.style.borderColor=vendor.color+'55'; }}
      onMouseLeave={e=>{ e.currentTarget.style.boxShadow='none'; e.currentTarget.style.borderColor='var(--border)'; }}>
      <div style={{ width:38, height:38, borderRadius:9, background:vendor.color, display:'flex', alignItems:'center', justifyContent:'center', flexShrink:0 }}>
        <span style={{ color:'#fff', fontFamily:'Sora,sans-serif', fontWeight:800, fontSize:13 }}>{vendor.abbr}</span>
      </div>
      <span style={{ fontSize:13, fontWeight:600, color:'var(--text2)', whiteSpace:'nowrap' }}>{vendor.name}</span>
    </div>
  );
}

function VendorMarquee() {
  const doubled = [...VENDORS, ...VENDORS];
  return (
    <section style={{ background:'var(--bg2)', borderTop:'1px solid var(--border)', borderBottom:'1px solid var(--border)', overflow:'hidden', position:'relative' }}>
      <div style={{ position:'absolute', left:0, top:0, bottom:0, width:120, background:'linear-gradient(to right,var(--bg2),transparent)', zIndex:2, pointerEvents:'none' }}></div>
      <div style={{ position:'absolute', right:0, top:0, bottom:0, width:120, background:'linear-gradient(to left,var(--bg2),transparent)', zIndex:2, pointerEvents:'none' }}></div>
      <div style={{ padding:'28px 0', display:'flex', overflow:'hidden' }}>
        <div className="marquee-track" style={{ display:'flex', alignItems:'center', willChange:'transform' }}>
          {doubled.map((v,i) => <VendorLogo key={i} vendor={v} />)}
        </div>
      </div>
    </section>
  );
}

function ActivityCard({ activity:a }) {
  const [hovered, setHovered] = useState(false);
  return (
    <div className="activity-card" onMouseEnter={()=>setHovered(true)} onMouseLeave={()=>setHovered(false)}
      style={{ display:'grid', gridTemplateColumns:'80px 1fr', background:'var(--surface2)', border:`1px solid ${hovered?a.accent+'44':'var(--border)'}`, borderRadius:18, overflow:'hidden', boxShadow:hovered?'var(--shadow-lg)':'var(--shadow)', transform:hovered?'translateY(-2px)':'none', transition:'all 0.25s' }}>
      <div className="activity-num" style={{ background:`linear-gradient(180deg,${a.accent},${a.accent}88)`, display:'flex', alignItems:'center', justifyContent:'center', padding:'28px 0' }}>
        <span style={{ fontFamily:'Sora,sans-serif', fontWeight:800, fontSize:22, color:'rgba(255,255,255,0.9)' }}>{a.num}</span>
      </div>
      <div className="activity-body" style={{ padding:'28px 32px', display:'grid', gridTemplateColumns:'1fr auto', gap:24, alignItems:'start' }}>
        <div>
          <div style={{ marginBottom:6 }}>
            <span style={{ fontSize:11, color:a.accent, fontWeight:700, textTransform:'uppercase', letterSpacing:'0.07em' }}>{a.subtitle}</span>
          </div>
          <h3 style={{ fontSize:20, fontWeight:700, marginBottom:12 }}>{a.title}</h3>
          <p style={{ color:'var(--text2)', fontSize:14.5, lineHeight:1.8, maxWidth:620 }}>{a.desc}</p>
        </div>
        <div style={{ minWidth:200 }}>
          <ul style={{ listStyle:'none', display:'flex', flexDirection:'column', gap:8 }}>
            {a.points.map(p => (
              <li key={p} style={{ display:'flex', alignItems:'flex-start', gap:8, fontSize:13, color:'var(--text2)' }}>
                <span style={{ width:18, height:18, borderRadius:5, background:`${a.accent}18`, color:a.accent, display:'flex', alignItems:'center', justifyContent:'center', fontSize:10, fontWeight:800, flexShrink:0, marginTop:1 }}>✓</span>
                {p}
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}

function Activities({ t }) {
  return (
    <section id="activities" style={{ padding:'clamp(60px,8vw,120px) clamp(16px,5vw,80px)', background:'var(--bg)' }}>
      <div style={{ maxWidth:1280, margin:'0 auto' }}>
        <Reveal>
          <div style={{ textAlign:'center', marginBottom:56 }}>
            <Tag color="teal">{t.activitiesTag}</Tag>
            <h2 style={{ fontSize:'clamp(26px,3.5vw,46px)', fontWeight:800, marginTop:14 }}>{t.activitiesH2}</h2>
            <p style={{ color:'var(--text3)', fontSize:16, maxWidth:500, margin:'12px auto 0', lineHeight:1.7 }}>{t.activitiesDesc}</p>
          </div>
        </Reveal>
        <div style={{ display:'flex', flexDirection:'column', gap:22 }}>
          {t.activities.map((a,i) => <Reveal key={i} delay={i*60}><ActivityCard activity={a} /></Reveal>)}
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
          <a href={GOVT} style={{ background:'rgba(255,255,255,0.08)', color:'rgba(255,255,255,0.85)', textDecoration:'none', padding:'13px 26px', borderRadius:10, fontSize:14, fontWeight:600, border:'1px solid rgba(255,255,255,0.15)', whiteSpace:'nowrap', transition:'background 0.2s' }}
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
        <div className="brand-logo" style={{ display:'flex', alignItems:'center' }}>
          <img src={LOGO_URL} alt="Access Infra" style={{ height:32, width:'auto', display:'block' }} />
        </div>
        <div style={{ display:'flex', gap:20, flexWrap:'wrap' }}>
          {[[nav.home,HOME],[nav.services,ABOUT+'#services'],[nav.smartSchool,ABOUT+'#smart-school'],[nav.caseStudies,ABOUT+'#case-studies'],[nav.govt,GOVT],[nav.about,ABOUT],[nav.contact,CONTACT],['Privacy Policy',PRIVACY],['Cookie Policy',COOKIEPOLICY]].map(([l,h]) => (
            <a key={h} href={h} style={{ color:'var(--text3)', textDecoration:'none', fontSize:12.5, transition:'color 0.2s' }}
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
        <div style={{ background:'var(--bg2)' }}>
          <div style={{ maxWidth:1280, margin:'0 auto', padding:'24px clamp(16px,5vw,80px) 10px' }}>
            <p style={{ fontSize:11, color:'var(--text3)', textTransform:'uppercase', letterSpacing:'0.1em', fontWeight:600, textAlign:'center' }}>{t.vendorsBanner}</p>
          </div>
          <VendorMarquee />
        </div>
        <Activities t={t} />
        <CTABanner t={t} />
        <Footer t={t} lang={lang} />
      </div>
    </>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
