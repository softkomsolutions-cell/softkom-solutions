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

        var hero = document.querySelector('.sk-assessment-hero');
        if (hero) {
            setText(hero, '.sk-assessment-eyebrow', 'Free Business Systems & AI Readiness Assessment');
            setText(hero, 'h1', 'How ready is your business to scale with better systems and AI?');
            setText(
                hero,
                '.sk-assessment-lead',
                'Discover where manual processes, disconnected tools, reporting gaps and missed automation opportunities may be costing your business time, margin and growth.'
            );
            setText(hero, '[data-assessment-start]', 'Start My Free Assessment');
            setText(
                hero,
                '.sk-assessment-note',
                'Takes about 3 minutes. You’ll receive an instant maturity score and practical recommendations.'
            );
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
                if (intro) {
                    intro.textContent = 'Enter your details so we can generate your results, recommendations and next-step guidance.';
                }
            }
        }

        var results = document.querySelector('[data-assessment-results]');
        if (results) {
            setText(results, '.sk-assessment-eyebrow', 'Your Business Systems & AI Readiness Results');
            setText(
                results,
                '[data-assessment-next-step-title]',
                'Want help turning these findings into a practical improvement plan?'
            );
            setText(
                results,
                '[data-assessment-next-step-body]',
                'Book a strategy conversation with Softkom to review the highest-value opportunities across systems, automation, AI and growth.'
            );
            setLink(
                results,
                '[data-assessment-next-step-link]',
                'Book a Strategy Call',
                config.contactUrl || '/contact/'
            );
        }

        function reapplyResultsCopy() {
            if (!results) return;
            setText(results, '.sk-assessment-eyebrow', 'Your Business Systems & AI Readiness Results');
            setText(results, '[data-assessment-next-step-title]', 'Want help turning these findings into a practical improvement plan?');
            setText(results, '[data-assessment-next-step-body]', 'Book a strategy conversation with Softkom to review the highest-value opportunities across systems, automation, AI and growth.');
            setLink(results, '[data-assessment-next-step-link]', 'Book a Strategy Call', config.contactUrl || '/contact/');
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
        }

        document.documentElement.setAttribute('data-softkom-acquisition-mode', 'public');
    });
}());