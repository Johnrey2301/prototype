document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const authMessage = document.getElementById('auth-message');

    // Send login data to server
    const response = await fetch('../php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
    });
    const text = await response.text();
    if (text.includes('error')) {
        authMessage.style.display = 'block';
        authMessage.style.background = 'rgba(239, 68, 68, 0.10)';
        authMessage.style.border = '1px solid rgba(239, 68, 68, 0.35)';
        authMessage.style.color = '#dc2626';
        authMessage.style.marginBottom = '14px';
        authMessage.textContent = 'Invalid username or password.';
    } else {
        // On success, redirect or reload
        window.location.href = text.trim();
    }
});