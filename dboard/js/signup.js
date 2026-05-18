(() => {
  function getMessageEl() {
    return document.getElementById('auth-message');
  }

  function showMessage(type, message) {
    const el = getMessageEl();
    if (!el) return;

    el.textContent = message || '';
    el.style.display = 'block';

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
    const locationMatch = text.match(/Location:\s*([^\"\']+)/i);
    if (locationMatch?.[1]) return locationMatch[1].trim();

    const jsRedirectMatch = text.match(
      /window\.location\.(href|replace)\s*=?\s*['\"]([^'\"]+)['\"]/i
    );
    if (jsRedirectMatch?.[2]) return jsRedirectMatch[2].trim();

    return null;
  }

  function getAlertMessageFromText(text) {
    const alertMatch = text.match(/alert\((['\"])(.*?)\1\)/i);
    return alertMatch?.[2] ?? null;
  }

  async function submitSignup(form, endpointUrl) {
    showMessage('info', 'Please wait...');

    const formData = new FormData(form);
    const payload = new URLSearchParams();

    const allowedFields = [
      'firstname',
      'lastname',
      'email',
      'phonenumber',
      'password',
      'password2'
    ];

    for (const key of allowedFields) {
      if (formData.has(key)) payload.append(key, formData.get(key));
    }


    const res = await fetch(endpointUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: payload.toString(),
      credentials: 'include'
    });

    const text = await res.text();

    const redirectUrl = guessRedirectFromText(text);
    if (redirectUrl) {
      window.location.href = redirectUrl;
      return;
    }

    if (!res.ok) {
      showMessage('error', 'Request failed. Please try again.');
      return;
    }

    const alertMessage = getAlertMessageFromText(text);
    if (alertMessage) {
      showMessage('error', alertMessage);
      return;
    }

    showMessage('success', 'Account created. Redirecting...');
    setTimeout(() => {
      window.location.href = '../dboard/login.html';
    }, 700);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action$="signup.php"], form[action="../php/signup.php"], form[action="signup.php"]');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await submitSignup(form, '../php/signup.php');
      } catch {
        showMessage('error', 'Network error. Please try again.');
      }
    });
  });
})();

