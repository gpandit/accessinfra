(function () {
  var STORAGE_KEY = 'ai-cookie-consent';

  function getConsent() {
    try { return localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
  }

  function setConsent(value) {
    try { localStorage.setItem(STORAGE_KEY, value); } catch (e) {}
    document.cookie = STORAGE_KEY + '=' + value + ';path=/;max-age=' + (60*60*24*180) + ';SameSite=Lax';
  }

  function injectStyles() {
    var style = document.createElement('style');
    style.textContent = [
      '#ai-cookie-banner{position:fixed;left:0;right:0;bottom:0;z-index:9999;',
      'background:#0c150a;background:linear-gradient(135deg,#0c150a 0%,#16280f 100%);',
      'color:#f1f5f9;padding:18px 20px;display:flex;flex-wrap:wrap;align-items:center;',
      'justify-content:center;gap:16px;font-family:Inter,sans-serif;font-size:13.5px;',
      'box-shadow:0 -4px 24px rgba(0,0,0,0.3);border-top:1px solid rgba(255,193,0,0.25);',
      'animation:ai-cookie-slideup 0.4s ease-out;}',
      '@keyframes ai-cookie-slideup{from{transform:translateY(100%);}to{transform:translateY(0);}}',
      '#ai-cookie-banner p{margin:0;max-width:640px;line-height:1.6;color:#e2e8f0;}',
      '#ai-cookie-banner a{color:#FFC100;text-decoration:underline;}',
      '#ai-cookie-banner .ai-cookie-actions{display:flex;gap:10px;flex-wrap:wrap;flex-shrink:0;}',
      '#ai-cookie-banner button{font-family:Inter,sans-serif;font-size:13px;font-weight:600;',
      'padding:9px 18px;border-radius:8px;cursor:pointer;border:1.5px solid transparent;transition:opacity 0.2s;}',
      '#ai-cookie-banner button:hover{opacity:0.85;}',
      '#ai-cookie-accept{background:linear-gradient(135deg,#FFC100,#F5D000);color:#2a2300;}',
      '#ai-cookie-decline{background:transparent;border-color:rgba(255,255,255,0.3);color:#f1f5f9;}',
      '@media (max-width:640px){#ai-cookie-banner{flex-direction:column;text-align:center;}}',
    ].join('');
    document.head.appendChild(style);
  }

  function showBanner() {
    injectStyles();
    var banner = document.createElement('div');
    banner.id = 'ai-cookie-banner';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Cookie consent');
    banner.innerHTML =
      '<p>We use cookies for essential site functionality and to remember your preferences (language, theme). ' +
      'See our <a href="' + (window.AI_COOKIE_POLICY_URL || '/cookie-policy.php') + '">Cookie Policy</a> and ' +
      '<a href="' + (window.AI_PRIVACY_POLICY_URL || '/privacy-policy.php') + '">Privacy Policy</a> to learn more.</p>' +
      '<div class="ai-cookie-actions">' +
      '<button type="button" id="ai-cookie-decline">Necessary Only</button>' +
      '<button type="button" id="ai-cookie-accept">Accept All</button>' +
      '</div>';
    document.body.appendChild(banner);

    document.getElementById('ai-cookie-accept').addEventListener('click', function () {
      setConsent('accepted');
      banner.remove();
    });
    document.getElementById('ai-cookie-decline').addEventListener('click', function () {
      setConsent('declined');
      banner.remove();
    });
  }

  function init() {
    if (!getConsent()) showBanner();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
