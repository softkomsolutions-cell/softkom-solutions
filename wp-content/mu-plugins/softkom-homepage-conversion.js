(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function normalize(value) {
        return String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();
    }

    ready(function () {
        var config = window.softkomHomepageConversion || {};
        if (!config.ajaxUrl || !config.nonce) return;

        var buttons = Array.prototype.slice.call(document.querySelectorAll('a.elementor-button, a[href]'));

        buttons.forEach(function (button) {
            var label = normalize(button.textContent);

            if (label === 'start a project') {
                button.href = config.assessmentUrl;
                var labelNode = button.querySelector('.elementor-button-text');
                if (labelNode) labelNode.textContent = 'Start Free Assessment';
                else button.textContent = 'Start Free Assessment';
            }

            if (label === 'view case studies') {
                button.href = config.calendlyUrl;
                var labelNodeTwo = button.querySelector('.elementor-button-text');
                if (labelNodeTwo) labelNodeTwo.textContent = 'Book Free 30-Min Review';
                else button.textContent = 'Book Free 30-Min Review';
            }
        });

        var form = Array.prototype.slice.call(document.querySelectorAll('form')).find(function (candidate) {
            return candidate.querySelector('.sk-fields') &&
                candidate.querySelector('.sk-thanks') &&
                candidate.querySelector('input[type="email"]') &&
                candidate.querySelector('textarea');
        });

        if (!form) return;

        form.removeAttribute('onsubmit');
        form.onsubmit = null;

        var fields = form.querySelector('.sk-fields');
        var thanks = form.querySelector('.sk-thanks');
        var submit = form.querySelector('button[type="submit"]');

        var honeypot = document.createElement('input');
        honeypot.type = 'text';
        honeypot.name = 'website';
        honeypot.tabIndex = -1;
        honeypot.autocomplete = 'off';
        honeypot.setAttribute('aria-hidden', 'true');
        honeypot.style.position = 'absolute';
        honeypot.style.left = '-9999px';
        form.appendChild(honeypot);

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            var nameInput = form.querySelector('input[type="text"]:not([name="website"])');
            var emailInput = form.querySelector('input[type="email"]');
            var detailsInput = form.querySelector('textarea');

            var name = nameInput ? nameInput.value.trim() : '';
            var email = emailInput ? emailInput.value.trim() : '';
            var details = detailsInput ? detailsInput.value.trim() : '';

            if (!name || !email || details.length < 10) {
                if (detailsInput && details.length < 10) detailsInput.focus();
                return;
            }

            if (submit) {
                submit.disabled = true;
                submit.textContent = 'SENDING...';
            }

            var data = new FormData();
            data.append('action', 'softkom_homepage_lead');
            data.append('nonce', config.nonce);
            data.append('name', name);
            data.append('email', email);
            data.append('details', details);
            data.append('website', honeypot.value || '');

            fetch(config.ajaxUrl, {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        if (!response.ok || !payload || !payload.success) {
                            var message = payload && payload.data && payload.data.message
                                ? payload.data.message
                                : 'Submission failed.';
                            throw new Error(message);
                        }
                        return payload.data || {};
                    });
                })
                .then(function (result) {
                    if (fields) fields.style.display = 'none';

                    if (thanks) {
                        thanks.style.display = 'block';
                        thanks.innerHTML = '';

                        var title = document.createElement('p');
                        title.textContent = 'Thank you — your enquiry is in.';
                        title.style.color = '#F28D35';
                        title.style.fontSize = '1.2rem';
                        title.style.fontWeight = '700';

                        var body = document.createElement('p');
                        body.textContent = 'We have your details. For immediate recommendations, you can also run the free 2-minute Business Systems & AI Readiness Assessment.';

                        var assessment = document.createElement('a');
                        assessment.href = result.assessmentUrl || config.assessmentUrl;
                        assessment.textContent = 'Start the 2-minute assessment →';
                        assessment.style.display = 'inline-block';
                        assessment.style.marginTop = '12px';
                        assessment.style.fontWeight = '700';

                        thanks.appendChild(title);
                        thanks.appendChild(body);
                        thanks.appendChild(assessment);
                    }

                    if (typeof window.gtag === 'function') {
                        window.gtag('event', 'generate_lead', {
                            lead_source: 'homepage_consultation'
                        });
                    }
                })
                .catch(function () {
                    if (submit) {
                        submit.disabled = false;
                        submit.textContent = 'REQUEST CONSULTATION';
                    }
                    window.location.href = 'mailto:info@softkomsolutions.com?subject=' +
                        encodeURIComponent('Softkom homepage consultation request');
                });
        }, true);
    });
}());
