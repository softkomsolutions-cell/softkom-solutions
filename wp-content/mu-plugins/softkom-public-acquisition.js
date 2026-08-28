(function () {
    'use strict';

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    onReady(function () {
        var config = window.softkomPublicAcquisition || {};

        function setText(root, selector, value) {
            if (!root || !value) return;
            var el = root.querySelector(selector);
            if (el) el.textContent = value;
        }

        function setLink(root, selector, label, href) {
            if (!root) return;
            var el = root.querySelector(selector);
            if (!el) return;
            if (label) el.textContent = label;
            if (href) el.setAttribute('href', href);
        }

        function hideInternalResultMetrics(root) {
            if (!root) return;
            [
                '[data-assessment-commercial-score]',
                '[data-assessment-intent-score]',
                '[data-assessment-overall-score]',
                '[data-assessment-temperature]'
            ].forEach(function (selector) {
                var value = root.querySelector(selector);
                if (!value) return;
                var tile = value.closest('.sk-assessment-metrics > div');
                if (!tile) tile = value.parentElement;
                if (tile) {
                    tile.hidden = true;
                    tile.setAttribute('aria-hidden', 'true');
                    tile.style.display = 'none';
                }
            });
        }

        function currentLeadEmail() {
            var form = document.querySelector('[data-assessment-lead] form');
            if (!form) return '';
            var email = form.querySelector('input[type="email"], input[name="email"]');
            return email ? String(email.value || '').trim() : '';
        }

        function postConversion(action, eventName) {
            var email = currentLeadEmail();
            if (!email || !config.ajaxUrl || !config.nonce) {
                return Promise.reject(new Error('Assessment email unavailable.'));
            }

            var data = new FormData();
            data.append('action', action);
            data.append('nonce', config.nonce);
            if (eventName) data.append('event', eventName);
            data.append('email', email);
            data.append('industry', config.industry || 'softkom');

            return fetch(config.ajaxUrl, {
                method: 'POST',
                body: data,
                credentials: 'same-origin',
                keepalive: true
            }).then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok || !payload || !payload.success) {
                        var message = payload && payload.data && payload.data.message
                            ? payload.data.message
                            : 'We could not submit your request.';
                        throw new Error(message);
                    }
                    return payload.data || {};
                });
            });
        }

        function trackConversion(eventName) {
            postConversion('softkom_public_conversion', eventName).catch(function () {});
        }

        function requestStrategyCall(link) {
            if (!link || link.getAttribute('data-request-busy') === '1') return;

            var originalLabel = link.textContent;
            link.setAttribute('data-request-busy', '1');
            link.setAttribute('aria-busy', 'true');
            link.textContent = 'Sending Request…';

            postConversion('softkom_strategy_request').then(function (data) {
                link.removeAttribute('href');
                link.setAttribute('role', 'status');
                link.setAttribute('aria-busy', 'false');
                link.setAttribute('data-request-complete', '1');
                link.textContent = data.already_requested ? 'Request Already Received' : 'Request Sent ✓';

                var panel = link.closest('.sk-assessment-next-step, [data-assessment-next-step]');
                if (!panel) panel = link.parentElement;
                if (panel) {
                    var body = panel.querySelector('[data-assessment-next-step-body]');
                    if (body) body.textContent = data.message || 'Your strategy call request has been received. Softkom will contact you shortly.';
                }
            }).catch(function () {
                link.setAttribute('aria-busy', 'false');
                link.removeAttribute('data-request-busy');
                link.textContent = originalLabel;
                window.location.href = 'mailto:info@softkomsolutions.com?subject=' + encodeURIComponent('Softkom Strategy Call Request');
            });
        }

        var hero = document.querySelector('.sk-assessment-hero');
        if (hero) {
            setText(hero, '.sk-assessment-eyebrow', 'Free Business Systems & AI Readiness Assessment');
            setText(hero, 'h1', 'How ready is your business to scale with better systems and AI?');
            setText(hero, '.sk-assessment-lead', 'Discover where manual processes, disconnected tools, reporting gaps and missed automation opportunities may be costing your business time, margin and growth.');
            setText(hero, '[data-assessment-start]', 'Start My Free Assessment');
            setText(hero, '.sk-assessment-note', 'Takes about 3 minutes. You’ll receive an instant maturity score and practical recommendations.');
        }

        var leadSection = document.querySelector('[data-assessment-lead]');
        if (leadSection) {
            setText(leadSection, '.sk-assessment-eyebrow', 'Your Assessment Is Complete');
            setText(leadSection, 'h2', 'Get your personalised business systems and AI readiness results.');
            var card = leadSection.querySelector('.sk-assessment-card');
            if (card) {
                var intro = Array.prototype.slice.call(card.children).find(function (element) {
                    return element.tagName === 'P';
                });
                if (intro) intro.textContent = 'Enter your details so we can generate your results, recommendations and next-step guidance.';
            }
        }

        var results = document.querySelector('[data-assessment-results]');
        if (results) {
            setText(results, '.sk-assessment-eyebrow', 'Your Business Systems & AI Readiness Results');
            setText(results, '[data-assessment-next-step-title]', 'Want help turning these findings into a practical improvement plan?');
            setText(results, '[data-assessment-next-step-body]', 'Request a strategy conversation with Softkom to review the highest-value opportunities across systems, automation, AI and growth.');
            setLink(results, '[data-assessment-next-step-link]', 'Request a Strategy Call', '#request-strategy-call');
            hideInternalResultMetrics(results);
        }

        function reapplyResultsCopy() {
            if (!results) return;
            hideInternalResultMetrics(results);
            setText(results, '.sk-assessment-eyebrow', 'Your Business Systems & AI Readiness Results');
            setText(results, '[data-assessment-next-step-title]', 'Want help turning these findings into a practical improvement plan?');

            var link = results.querySelector('[data-assessment-next-step-link]');
            if (link && link.getAttribute('data-request-complete') === '1') return;

            setText(results, '[data-assessment-next-step-body]', 'Request a strategy conversation with Softkom to review the highest-value opportunities across systems, automation, AI and growth.');
            setLink(results, '[data-assessment-next-step-link]', 'Request a Strategy Call', '#request-strategy-call');
        }

        if (results) {
            var observer = new MutationObserver(function () {
                window.requestAnimationFrame(reapplyResultsCopy);
            });
            observer.observe(results, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
                attributeFilter: ['hidden']
            });

            results.addEventListener('click', function (event) {
                var link = event.target.closest('[data-assessment-next-step-link]');
                if (!link) return;
                event.preventDefault();
                if (link.getAttribute('data-request-complete') === '1') return;
                trackConversion('strategy_call_clicked');
                requestStrategyCall(link);
            });
        }

        document.documentElement.setAttribute('data-softkom-acquisition-mode', 'public');
        document.documentElement.setAttribute('data-softkom-industry', config.industry || 'softkom');
    });
}());