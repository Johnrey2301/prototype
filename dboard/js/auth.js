(() => {
  function getMessageEl() {
    return document.getElementById('auth-message');
  }

  function showMessage(type, message) {
    const el = getMessageEl();
    if (!el) return;

    el.textContent = message || '';
    el.style.display = 'block';

    // Minimal styling (works with your CSS variables but doesn’t depend on them)
    el.style.padding = '10px 12px';
    el.style.borderRadius = '10px';
    el.style.marginTop = '12px';
    el.style.fontWeight = '600';

    if (type === 'success') {
      el.style.background = 'rgba(16, 185, 129, 0.10)';
      el.style.border = '1px solid rgba(16, 185, 129, 0.35)';
      el.style.color = '#059669';
    } else if (type === 'error') {
      el.style.background = 'rgba(239, 68, 68, 0.10)';
      el.style.border = '1px solid rgba(239, 68, 68, 0.35)';
      el.style.color = '#dc2626';
    } else {
      el.style.background = 'rgba(59, 130, 246, 0.10)';
      el.style.border = '1px solid rgba(59, 130, 246, 0.35)';
      el.style.color = '#2563eb';
    }
  }

  function guessRedirectFromText(text) {
    // Detect a Location header echoed into JS or HTML
    const locationMatch = text.match(/Location:\s*([^\"\']+)/i);
    if (locationMatch?.[1]) return locationMatch[1].trim();

    const jsRedirectMatch = text.match(/window\.location\.(href|replace)\s*=?\s*['\"]([^'\"]+)['\"]/i);
    if (jsRedirectMatch?.[2]) return jsRedirectMatch[2].trim();

    const metaRefreshMatch = text.match(/http-equiv=["']refresh["'][^>]*content=["']\d+;\s*url=([^"']+)["']/i);
    if (metaRefreshMatch?.[1]) return metaRefreshMatch[1].trim();

    return null;
  }

  async function submitAjax(form, endpointUrl) {
    const msgEl = getMessageEl();
    if (msgEl) showMessage('info', 'Please wait...');

    const formData = new FormData(form);
    const payload = new URLSearchParams();
    for (const [k, v] of formData.entries()) payload.append(k, v);

    const res = await fetch(endpointUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: payload.toString(),
      credentials: 'include'
    });

    const text = await res.text();

    // If backend is trying to redirect, attempt to extract the target and navigate.
    const redirectUrl = guessRedirectFromText(text);
    if (redirectUrl) {
      window.location.href = redirectUrl;
      return;
    }

    if (!res.ok) {
      showMessage('error', 'Request failed. Please try again.');
      return;
    }

    // signup.php on error echoes an alert('...') then window.location.href...
    const alertMatch = text.match(/alert\((['\"])(.*?)\1\)/i);
    if (alertMatch?.[2]) {
      showMessage('error', alertMatch[2]);
      return;
    }

    // login.php usually just redirects on success. If we got here, show generic message.
    if (/invalid username|invalid|error/i.test(text)) {
      showMessage('error', 'Invalid username or password!');
      return;
    }

    // Fallback: allow normal form navigation to trigger PHP redirects
    showMessage('success', 'Processing...');
    form.submit();
  }

  function attachForm(formSelector, endpointUrl) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      try {
        await submitAjax(form, endpointUrl);
      } catch {
        showMessage('error', 'Network error. Please try again.');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    // dboard/login.html -> POST ../php/login.php
    attachForm('form[action="login.php"], form[action="../php/login.php"], form[action$="/login.php"]', '../php/login.php');

    // dboard/signup.html -> POST ../php/signup.php
    attachForm('form[action$="signup.php"], form[action="../php/signup.php"], form[action="signup.php"]', '../php/signup.php');
  });
})();

