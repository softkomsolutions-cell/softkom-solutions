(function () {
  'use strict';

  const cfg = window.softkomRevenueLab || {};
  const scanForm = document.getElementById('sk-rl-scan-form');
  const unlockForm = document.getElementById('sk-rl-unlock-form');
  const running = document.getElementById('sk-rl-running');
  const results = document.getElementById('sk-rl-results');
  const errorBox = document.getElementById('sk-rl-error');
  const scoreEl = document.getElementById('sk-rl-score');
  const titleEl = document.getElementById('sk-rl-result-title');
  const summaryEl = document.getElementById('sk-rl-summary');
  const personasEl = document.getElementById('sk-rl-personas');
  const findingsEl = document.getElementById('sk-rl-findings');
  const unlockStatus = document.getElementById('sk-rl-unlock-status');
  let activeToken = '';

  function post(action, data) {
    const body = new URLSearchParams(Object.assign({ action, nonce: cfg.nonce }, data || {}));
    return fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString()
    }).then(async (res) => {
      const json = await res.json().catch(() => ({}));
      if (!res.ok || !json.success) {
        const message = json && json.data && json.data.message ? json.data.message : 'Something went wrong.';
        throw new Error(message);
      }
      return json.data;
    });
  }

  function setError(message) {
    errorBox.hidden = false;
    errorBox.textContent = message;
  }

  function clearError() {
    errorBox.hidden = true;
    errorBox.textContent = '';
  }

  function animateSteps() {
    const steps = Array.from(running.querySelectorAll('[data-step]'));
    steps.forEach((s) => s.classList.remove('is-active', 'is-done'));
    let i = 0;
    const timer = setInterval(() => {
      if (i > 0 && steps[i - 1]) steps[i - 1].classList.replace('is-active', 'is-done');
      if (steps[i]) steps[i].classList.add('is-active');
      i += 1;
      if (i > steps.length) clearInterval(timer);
    }, 700);
    return timer;
  }

  function personaIcon(status) {
    return status === 'converted' ? '✓' : status === 'stuck' ? '!' : '×';
  }

  function renderPersonas(personas) {
    personasEl.innerHTML = '';
    personas.forEach((p) => {
      const card = document.createElement('article');
      card.className = 'sk-rl-persona sk-rl-persona--' + p.status;
      const journey = (p.journey || []).map((step) => '<li>' + escapeHtml(step) + '</li>').join('');
      card.innerHTML =
        '<div class="sk-rl-persona-top">' +
          '<span class="sk-rl-status">' + personaIcon(p.status) + '</span>' +
          '<div><h3>' + escapeHtml(p.name) + '</h3><p>' + escapeHtml(p.goal) + '</p></div>' +
        '</div>' +
        '<ol class="sk-rl-journey">' + journey + '</ol>' +
        '<p class="sk-rl-reason">' + escapeHtml(p.reason) + '</p>';
      personasEl.appendChild(card);
    });
  }

  function renderFindings(findings) {
    findingsEl.innerHTML = '';
    findings.forEach((f) => {
      const row = document.createElement('div');
      row.className = 'sk-rl-finding sk-rl-finding--' + f.severity;
      row.innerHTML =
        '<div class="sk-rl-dot"></div>' +
        '<div><strong>' + escapeHtml(f.title) + '</strong><p>' + escapeHtml(f.recommendation) + '</p></div>';
      findingsEl.appendChild(row);
    });
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
  }

  if (scanForm) {
    scanForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      clearError();
      results.hidden = true;
      running.hidden = false;
      const timer = animateSteps();
      const input = scanForm.querySelector('[name="url"]');
      try {
        const data = await post('softkom_revenue_lab_scan', { url: input.value.trim() });
        clearInterval(timer);
        running.hidden = true;
        activeToken = data.token || '';
        scoreEl.textContent = data.score;
        titleEl.textContent = data.title ? 'Customer test: ' + data.title : 'Your simulated customer test';
        summaryEl.textContent = data.summary || '';
        renderPersonas(data.personas || []);
        renderFindings(data.findings || []);
        results.hidden = false;
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } catch (error) {
        clearInterval(timer);
        running.hidden = true;
        setError(error.message);
      }
    });
  }

  if (unlockForm) {
    unlockForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      clearError();
      if (!activeToken) {
        setError('Run the website test first.');
        return;
      }
      unlockStatus.textContent = 'Unlocking full results...';
      const fd = new FormData(unlockForm);
      try {
        const data = await post('softkom_revenue_lab_unlock', {
          token: activeToken,
          name: fd.get('name'),
          company: fd.get('company'),
          email: fd.get('email')
        });
        renderPersonas(data.personas || []);
        renderFindings(data.findings || []);
        unlockStatus.innerHTML =
          'Full result unlocked. <a href="' + escapeHtml(data.next_step_url || '#') + '">Book a strategy conversation</a>.';
      } catch (error) {
        unlockStatus.textContent = error.message;
      }
    });
  }
})();