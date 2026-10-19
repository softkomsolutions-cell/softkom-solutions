(function () {
  'use strict';

  function send(name, params) {
    params = params || {};
    params.page_path = window.location.pathname;
    if (typeof window.gtag === 'function') {
      window.gtag('event', name, params);
      return;
    }
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(Object.assign({ event: name }, params));
  }

  function text(el) {
    return (el && (el.textContent || el.value) || '').trim().replace(/\s+/g, ' ').slice(0, 120);
  }

  if (document.querySelector('[data-assessment-start]')) {
  }

  document.addEventListener('click', function (event) {
    var link = event.target.closest('a,button');
    if (!link) return;

    var href = link.getAttribute('href') || '';
    var label = text(link);

    if (link.matches('[data-assessment-start]')) {
      send('softkom_assessment_start', { cta_label: label });
    }

    if (link.matches('[data-assessment-next]')) {
      var progress = document.querySelector('[data-assessment-progress-percent]');
      send('softkom_assessment_continue', { progress: text(progress) });
    }

    if (/\/projects\/?(?:#|\?|$)/.test(href)) {
      send('softkom_client_work_click', { cta_label: label });
    }

    if (/\/assessment\/?(?:#|\?|$)/.test(href)) {
      send('softkom_assessment_cta_click', { cta_label: label });
    }

    if (/\/contact\/?/.test(href) || href.indexOf('#discovery') !== -1) {
      send('softkom_contact_cta_click', { cta_label: label });
    }

    if (href.indexOf('mailto:') === 0) {
      send('softkom_email_click', { cta_label: label });
    }

    if (href.indexOf('tel:') === 0) {
      send('softkom_phone_click', { cta_label: label });
    }

    if (href.indexOf('wa.me/') !== -1 || href.indexOf('whatsapp') !== -1) {
      send('softkom_whatsapp_click', { cta_label: label });
    }
  });

  var contact = document.querySelector('[data-sk-contact-form]');
  if (contact) {
    var started = false;
    contact.addEventListener('focusin', function () {
      if (started) return;
      started = true;
      send('softkom_contact_form_start');
    });
    contact.addEventListener('submit', function () {
      send('softkom_contact_form_submit');
    });
  }

  var assessmentLead = document.querySelector('[data-assessment-lead-form]');
  if (assessmentLead) {
    assessmentLead.addEventListener('submit', function () {
      send('softkom_assessment_lead_submit');
    });
  }

  var results = document.querySelector('[data-assessment-results]');
  if (results && 'MutationObserver' in window) {
    new MutationObserver(function () {
      if (!results.hidden && !results.dataset.trackedVisible) {
        results.dataset.trackedVisible = '1';
        send('softkom_assessment_results_view');
      }
    }).observe(results, { attributes: true, attributeFilter: ['hidden'] });
  }

  var marks = { 50: false, 75: false, 90: false };
  function onScroll() {
    var doc = document.documentElement;
    var max = Math.max(1, doc.scrollHeight - window.innerHeight);
    var pct = Math.round((window.scrollY / max) * 100);
    [50, 75, 90].forEach(function (mark) {
      if (!marks[mark] && pct >= mark) {
        marks[mark] = true;
        send('softkom_scroll_depth', { percent: mark });
      }
    });
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  window.setTimeout(function () {
    if (document.visibilityState === 'visible') {
      send('softkom_engaged_20_seconds');
    }
  }, 20000);

  if (new URLSearchParams(window.location.search).get('sent') === '1') {
    send('softkom_enquiry_success');
  }
})();