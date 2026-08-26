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
        const config = window.softkomIndustryFunnel;

        if (!config || !config.copy || !Array.isArray(config.questions)) {
            return;
        }

        document.documentElement.setAttribute(
            'data-softkom-industry-profile',
            config.key || ''
        );

        function setText(root, selector, value) {
            if (!root || !value) {
                return;
            }

            const element = root.querySelector(selector);

            if (element && element.textContent !== value) {
                element.textContent = value;
            }
        }

        function setLink(root, selector, label, url) {
            if (!root) {
                return;
            }

            const link = root.querySelector(selector);

            if (!link) {
                return;
            }

            if (label && link.textContent !== label) {
                link.textContent = label;
            }

            if (url && link.getAttribute('href') !== url) {
                link.setAttribute('href', url);
            }
        }

        const hero = document.querySelector('.sk-assessment-hero');
        const leadSection = document.querySelector(
            '[data-assessment-lead]'
        );
        const resultsSection = document.querySelector(
            '[data-assessment-results]'
        );
        const app = document.querySelector('[data-assessment-app]');
        const leadForm = document.querySelector(
            '[data-assessment-lead-form]'
        );

        if (hero) {
            setText(
                hero,
                '.sk-assessment-eyebrow',
                config.copy.eyebrow
            );
            setText(hero, 'h1', config.copy.title);
            setText(
                hero,
                '.sk-assessment-lead',
                config.copy.lead
            );
            setText(
                hero,
                '[data-assessment-start]',
                config.copy.start_label
            );
            setText(
                hero,
                '.sk-assessment-note',
                config.copy.note
            );
        }

        if (leadSection) {
            setText(
                leadSection,
                '.sk-assessment-eyebrow',
                config.copy.complete_eyebrow
            );
            setText(
                leadSection,
                'h2',
                config.copy.complete_title
            );

            const card = leadSection.querySelector(
                '.sk-assessment-card'
            );
            const intro = card
                ? Array.from(card.children).find(function (element) {
                    return element.tagName === 'P';
                })
                : null;

            if (
                intro &&
                config.copy.complete_body &&
                intro.textContent !== config.copy.complete_body
            ) {
                intro.textContent = config.copy.complete_body;
            }
        }

        if (leadForm) {
            const hiddenFields = {
                industry_key: config.key || '',
                industry_name: config.name || '',
                industry_profile_version: String(
                    config.version || 1
                )
            };

            Object.keys(hiddenFields).forEach(function (name) {
                let input = leadForm.querySelector(
                    'input[name="' + name + '"]'
                );

                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    leadForm.appendChild(input);
                }

                input.value = hiddenFields[name];
            });
        }

        function currentQuestionIndex() {
            const label = document.querySelector(
                '[data-assessment-progress-label]'
            );

            if (!label) {
                return -1;
            }

            const match = label.textContent.match(
                /Question\s+(\d+)/i
            );

            return match ? parseInt(match[1], 10) - 1 : -1;
        }

        function applyCurrentQuestion() {
            if (!app) {
                return;
            }

            const index = currentQuestionIndex();
            const question = config.questions[index];

            if (!question) {
                return;
            }

            setText(
                app,
                '[data-assessment-section]',
                question.section
            );
            setText(
                app,
                '[data-assessment-question]',
                question.question
            );
            setText(
                app,
                '[data-assessment-help]',
                question.help
            );
        }

        function applyResultsCopy() {
            if (!resultsSection) {
                return;
            }

            setText(
                resultsSection,
                '.sk-assessment-eyebrow',
                config.copy.results_eyebrow
            );
            setText(
                resultsSection,
                '[data-assessment-next-step-title]',
                config.copy.next_step_title
            );
            setText(
                resultsSection,
                '[data-assessment-next-step-body]',
                config.copy.next_step_body
            );
            setLink(
                resultsSection,
                '[data-assessment-next-step-link]',
                config.copy.next_step_label,
                config.copy.next_step_url
            );
        }

        const startButton = document.querySelector(
            '[data-assessment-start]'
        );
        const backButton = document.querySelector(
            '[data-assessment-back]'
        );
        const nextButton = document.querySelector(
            '[data-assessment-next]'
        );

        [startButton, backButton, nextButton].forEach(
            function (button) {
                if (!button) {
                    return;
                }

                button.addEventListener('click', function () {
                    window.setTimeout(applyCurrentQuestion, 0);
                });
            }
        );

        if (app) {
            const questionObserver = new MutationObserver(function () {
                window.requestAnimationFrame(applyCurrentQuestion);
            });

            questionObserver.observe(app, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
                attributeFilter: ['hidden']
            });
        }

        if (resultsSection) {
            const resultsObserver = new MutationObserver(function () {
                window.requestAnimationFrame(applyResultsCopy);
            });

            resultsObserver.observe(resultsSection, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
                attributeFilter: ['hidden']
            });
        }

        applyCurrentQuestion();
        applyResultsCopy();
    });
}());
