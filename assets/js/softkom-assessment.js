(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        const startButton = document.querySelector('[data-assessment-start]');
        const app = document.querySelector('[data-assessment-app]');
        const leadSection = document.querySelector('[data-assessment-lead]');
        const resultsSection = document.querySelector('[data-assessment-results]');
        const leadForm = document.querySelector('[data-assessment-lead-form]');
        const scoreEl = document.querySelector('[data-assessment-score]');
        const maturityEl = document.querySelector('[data-assessment-maturity]');
        const summaryEl = document.querySelector('[data-assessment-summary]');
        const aiScoreEl = document.querySelector('[data-assessment-ai-score]');
        const commercialScoreEl = document.querySelector('[data-assessment-commercial-score]');
        const intentScoreEl = document.querySelector('[data-assessment-intent-score]');
        const overallScoreEl = document.querySelector('[data-assessment-overall-score]');
        const temperatureEl = document.querySelector('[data-assessment-temperature]');
        const prioritiesEl = document.querySelector('[data-assessment-priorities]');
        const recommendationsEl = document.querySelector('[data-assessment-recommendations]');
        const nextStepTitleEl = document.querySelector('[data-assessment-next-step-title]');
        const nextStepBodyEl = document.querySelector('[data-assessment-next-step-body]');
        const nextStepLinkEl = document.querySelector('[data-assessment-next-step-link]');


        /*
         * Traffic Attribution V1 - page-load capture.
         *
         * Capture first-touch campaign information immediately
         * when the assessment page loads rather than waiting
         * until the lead form is submitted.
         */
        const attributionStorageKey =
            'softkomTrafficAttributionV1';

        try {

            let attribution = {};

            const storedAttribution =
                window.sessionStorage.getItem(
                    attributionStorageKey
                );

            if (storedAttribution) {

                try {
                    attribution =
                        JSON.parse(storedAttribution) || {};
                } catch (error) {
                    attribution = {};
                }
            }


            const currentUrl =
                new URL(window.location.href);

            const attributionFields = [
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'utm_term'
            ];


            const hasCurrentCampaign =
                attributionFields.some(function (field) {
                    return currentUrl.searchParams.has(field);
                });

            /*
             * A tracked URL represents a new acquisition touch.
             * Replace stale session attribution with the campaign
             * currently present in the URL.
             *
             * If the current page has no UTM parameters, retain the
             * existing attribution so it survives normal navigation.
             */
            if (hasCurrentCampaign) {

                attribution = {};

                attributionFields.forEach(function (field) {

                    const value =
                        currentUrl.searchParams.get(field);

                    attribution[field] =
                        value
                            ? value
                            : '';
                });

                attribution.landing_page =
                    window.location.href;

                attribution.referrer =
                    document.referrer || '';
            } else {

                if (!attribution.landing_page) {
                    attribution.landing_page =
                        window.location.href;
                }

                if (!attribution.referrer) {
                    attribution.referrer =
                        document.referrer || '';
                }
            }


            window.sessionStorage.setItem(
                attributionStorageKey,
                JSON.stringify(attribution)
            );

        } catch (error) {

            /*
             * Attribution must never prevent the assessment
             * experience from loading.
             */
        }


        if (!startButton || !app) {
            return;
        }

        const questionEl = app.querySelector('[data-assessment-question]');
        const sectionEl = app.querySelector('[data-assessment-section]');
        const helpEl = app.querySelector('[data-assessment-help]');
        const optionsEl = app.querySelector('[data-assessment-options]');
        const backButton = app.querySelector('[data-assessment-back]');
        const nextButton = app.querySelector('[data-assessment-next]');
        const progressLabel = app.querySelector('[data-assessment-progress-label]');
        const progressPercent = app.querySelector('[data-assessment-progress-percent]');
        const progressBar = app.querySelector('[data-assessment-progress-bar]');

        const questions = [
            {
                id: 'visibility-01',
                section: 'Strategy & Planning',
                question: 'How consistently can leaders see current operational performance without manual report assembly?',
                help: 'Think about whether decisions rely on live information or competing spreadsheets and delayed packs.'
            },
            {
                id: 'reporting-03',
                section: 'Strategy & Planning',
                question: 'How clearly are your KPIs defined around the decisions leadership needs to make?',
                help: 'Consider whether reports answer priority business questions instead of simply reproducing system exports.'
            },
            {
                id: 'process-01',
                section: 'Process & Automation',
                question: 'How well are your recurring processes documented with clear ownership and outcomes?',
                help: 'Consider the work that happens weekly or monthly and whether it depends on individual memory.'
            },
            {
                id: 'automation-01',
                section: 'Process & Automation',
                question: 'How much high-volume, rules-based work is already automated?',
                help: 'Consider notifications, approvals, data movement and repetitive administration.'
            },
            {
                id: 'integration-01',
                section: 'Technology',
                question: 'How automatically do your core business systems exchange data?',
                help: 'Think about CRM, finance, operations, sales and customer systems.'
            },
            {
                id: 'ai-01',
                section: 'Technology',
                question: 'How reliable and well-managed is the data foundation available for AI?',
                help: 'Consider data quality, ownership and whether AI output can be checked against trusted sources.'
            },
            {
                id: 'process-03',
                section: 'People & Culture',
                question: 'How resilient would important processes be if a key person were unavailable?',
                help: 'Think about shared knowledge, documented procedures and cross-trained team members.'
            },
            {
                id: 'risk-01',
                section: 'People & Culture',
                question: 'How comfortably can your teams absorb a spike in workload or customer demand?',
                help: 'Imagine a busy period without relying on overtime, workarounds or one key person.'
            },
            {
                id: 'data-01',
                section: 'Data & Reporting',
                question: 'How consistently is important information captured once and reused across systems?',
                help: 'Consider customer, order, finance and operational data that staff may re-enter.'
            },
            {
                id: 'reporting-02',
                section: 'Data & Reporting',
                question: 'How confidently can leaders act on reports without rechecking the numbers offline?',
                help: 'Think about trust in dashboards, definitions and sources of truth.'
            },
            {
                id: 'visibility-03',
                section: 'Customer Experience',
                question: 'How reliably are service exceptions identified before customers need to escalate them?',
                help: 'Consider proactive alerts, case visibility and ownership of customer issues.'
            },
            {
                id: 'automation-03',
                section: 'Customer Experience',
                question: 'How automatically are customer updates and internal handoffs triggered?',
                help: 'Think about onboarding, service delivery, notifications and follow-up.'
            },
            {
                id: 'governance-01',
                section: 'Governance & Compliance',
                question: 'How clear are the approval rights for changes to critical processes and systems?',
                help: 'Consider decision ownership, access control and change management.'
            },
            {
                id: 'compliance-02',
                section: 'Governance & Compliance',
                question: 'How consistently is compliance or audit evidence captured during normal work?',
                help: 'Consider whether evidence is built into workflows or assembled manually before reviews.'
            }
        ];

        const choices = [
            { value: 1, label: 'Not at all' },
            { value: 2, label: 'Limited' },
            { value: 3, label: 'Partially' },
            { value: 4, label: 'Mostly' },
            { value: 5, label: 'Fully' }
        ];

        let current = 0;
        const answers = {};

        function renderQuestion() {
            const item = questions[current];

            sectionEl.textContent = item.section;
            questionEl.textContent = item.question;
            helpEl.textContent = item.help;

            optionsEl.innerHTML = '';

            choices.forEach(function (choice) {
                const button = document.createElement('button');

                button.type = 'button';
                button.className = 'sk-assessment-option';
                button.dataset.value = choice.value;

                button.innerHTML =
                    '<span class="sk-assessment-option__score">' +
                    choice.value +
                    '</span>' +
                    '<span>' +
                    choice.label +
                    '</span>';

                if (answers[item.id] === choice.value) {
                    button.classList.add('is-selected');
                }

                button.addEventListener('click', function () {

                    answers[item.id] = choice.value;

                    optionsEl
                        .querySelectorAll('.sk-assessment-option')
                        .forEach(function (option) {
                            option.classList.remove('is-selected');
                        });

                    button.classList.add('is-selected');
                    nextButton.disabled = false;
                });

                optionsEl.appendChild(button);
            });

            const completed = current + 1;
            const percent = Math.round(
                (completed / questions.length) * 100
            );

            progressLabel.textContent =
                'Question ' + completed + ' of ' + questions.length;

            progressPercent.textContent = percent + '%';
            progressBar.style.width = percent + '%';

            backButton.disabled = current === 0;
            nextButton.disabled = !answers[item.id];

            nextButton.textContent =
                current === questions.length - 1
                    ? 'Complete Assessment'
                    : 'Continue';
        }

        startButton.addEventListener('click', function () {

            const startedAtField = document.querySelector(
                '[data-assessment-started-at]'
            );

            if (startedAtField) {
                startedAtField.value = Math.floor(Date.now() / 1000);
            }

            startButton.closest('.sk-assessment-hero').hidden = true;

            app.hidden = false;

            renderQuestion();

            app.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        backButton.addEventListener('click', function () {

            if (current > 0) {
                current -= 1;
                renderQuestion();
            }
        });

        nextButton.addEventListener('click', function () {

            const item = questions[current];

            if (!answers[item.id]) {
                return;
            }

            if (current < questions.length - 1) {

                current += 1;
                renderQuestion();

            } else {

                app.hidden = true;
                leadSection.hidden = false;

                window.softkomAssessmentAnswers = answers;

                leadSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });


        /*
         * Funnel V2 lead submission.
         */
        if (leadForm && resultsSection) {

            leadForm.addEventListener('submit', async function (event) {

                event.preventDefault();

                const submitButton = leadForm.querySelector(
                    'button[type="submit"]'
                );

                const originalButtonText = submitButton
                    ? submitButton.textContent
                    : '';

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Generating Your Results...';
                }

                try {

                    if (
                        typeof softkomAssessment === 'undefined' ||
                        !softkomAssessment.ajaxUrl ||
                        !softkomAssessment.nonce
                    ) {
                        throw new Error(
                            'Assessment connection is not available.'
                        );
                    }

                    const completedAtField = document.querySelector(
                        '[data-assessment-completed-at]'
                    );

                    if (completedAtField) {
                        completedAtField.value =
                            Math.floor(Date.now() / 1000);
                    }

                    const formData = new FormData(leadForm);


                    /*
                     * Traffic Attribution V1.
                     *
                     * Preserve first-touch campaign attribution in
                     * sessionStorage so it survives movement through
                     * the assessment before the lead is submitted.
                     */
                    const attributionStorageKey =
                        'softkomTrafficAttributionV1';

                    let attribution = {};

                    try {

                        const storedAttribution =
                            window.sessionStorage.getItem(
                                attributionStorageKey
                            );

                        if (storedAttribution) {
                            attribution =
                                JSON.parse(storedAttribution) || {};
                        }

                    } catch (error) {
                        attribution = {};
                    }


                    const currentUrl =
                        new URL(window.location.href);

                    const attributionFields = [
                        'utm_source',
                        'utm_medium',
                        'utm_campaign',
                        'utm_content',
                        'utm_term'
                    ];

                    const hasCurrentCampaign =
                        attributionFields.some(function (field) {
                            return currentUrl.searchParams.has(field);
                        });

                    /*
                     * If the current page contains campaign parameters,
                     * they represent the active acquisition touch and
                     * replace stale attribution from an earlier visit.
                     */
                    if (hasCurrentCampaign) {

                        attribution = {};

                        attributionFields.forEach(function (field) {

                            const value =
                                currentUrl.searchParams.get(field);

                            attribution[field] =
                                value
                                    ? value
                                    : '';
                        });

                        attribution.landing_page =
                            window.location.href;

                        attribution.referrer =
                            document.referrer || '';

                    } else {

                        if (!attribution.landing_page) {
                            attribution.landing_page =
                                window.location.href;
                        }

                        if (!attribution.referrer) {
                            attribution.referrer =
                                document.referrer || '';
                        }
                    }


                    try {
                        window.sessionStorage.setItem(
                            attributionStorageKey,
                            JSON.stringify(attribution)
                        );
                    } catch (error) {
                        /*
                         * Storage can be unavailable in restricted
                         * browser modes. Submission must still work.
                         */
                    }


                    attributionFields.forEach(function (field) {

                        formData.append(
                            field,
                            attribution[field] || ''
                        );
                    });

                    formData.append(
                        'landing_page',
                        attribution.landing_page || ''
                    );

                    formData.append(
                        'referrer',
                        attribution.referrer || ''
                    );


                    formData.append(
                        'action',
                        softkomAssessment.action ||
                        'softkom_assessment_submit'
                    );

                    formData.append(
                        'nonce',
                        softkomAssessment.nonce
                    );

                    formData.append(
                        'answers',
                        JSON.stringify(
                            window.softkomAssessmentAnswers || {}
                        )
                    );

                    const response = await fetch(
                        softkomAssessment.ajaxUrl,
                        {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        }
                    );

                    const payload = await response.json();

                    if (
                        !response.ok ||
                        !payload ||
                        payload.success !== true ||
                        !payload.data
                    ) {

                        const message =
                            payload &&
                            payload.data &&
                            payload.data.message
                                ? payload.data.message
                                : 'We could not generate your results. Please try again.';

                        throw new Error(message);
                    }

                    const result = payload.data;
                    const scores = result.scores || {};
                    const maturityScore =
                        Math.round(Number(scores.maturity) || 0);
                    const aiScore =
                        Math.round(Number(scores.ai_opportunity) || 0);
                    const commercialScore =
                        Math.round(Number(scores.commercial_fit) || 0);
                    const intentScore =
                        Math.round(Number(scores.purchase_intent) || 0);
                    const overallScore =
                        Math.round(Number(scores.overall_lead) || 0);

                    if (scoreEl) {
                        scoreEl.textContent = maturityScore;
                    }

                    if (maturityEl) {
                        maturityEl.textContent =
                            result.maturity_level &&
                            result.maturity_level.title
                                ? result.maturity_level.title
                                : 'Your Systems Maturity';
                    }

                    if (summaryEl) {
                        summaryEl.textContent =
                            result.personalized_summary ||
                            ('Your Business Systems Maturity Score is ' +
                                maturityScore +
                                '/100. Your AI & Automation Opportunity Score is ' +
                                aiScore +
                                '/100.');
                    }

                    if (aiScoreEl) {
                        aiScoreEl.textContent = aiScore + '/100';
                    }

                    if (commercialScoreEl) {
                        commercialScoreEl.textContent = commercialScore + '/100';
                    }

                    if (intentScoreEl) {
                        intentScoreEl.textContent = intentScore + '/100';
                    }

                    if (overallScoreEl) {
                        overallScoreEl.textContent = overallScore + '/100';
                    }

                    if (temperatureEl) {
                        temperatureEl.textContent =
                            result.lead_temperature || '--';
                    }

                    if (prioritiesEl) {
                        prioritiesEl.innerHTML = '';

                        const priorities =
                            Array.isArray(result.priority_opportunities)
                                ? result.priority_opportunities
                                : [];

                        priorities.forEach(function (item) {
                            const row = document.createElement('div');
                            const title = document.createElement('span');
                            const score = document.createElement('strong');

                            row.className = 'sk-assessment-priority';
                            title.textContent = item.title || 'Priority opportunity';
                            score.textContent =
                                Math.round(Number(item.score) || 0) + '/100';

                            row.appendChild(title);
                            row.appendChild(score);
                            prioritiesEl.appendChild(row);
                        });

                        if (!priorities.length) {
                            const fallback = document.createElement('p');
                            fallback.textContent =
                                'No urgent gaps were identified. Focus on maintaining and continuously improving your connected systems foundations.';
                            prioritiesEl.appendChild(fallback);
                        }
                    }

                    if (recommendationsEl) {

                        recommendationsEl.innerHTML = '';

                        const recommendations =
                            Array.isArray(result.recommendations)
                                ? result.recommendations
                                : [];

                        if (recommendations.length) {

                            recommendations.forEach(function (item) {

                                const card =
                                    document.createElement('div');

                                card.className =
                                    'sk-assessment-recommendation';

                                const title =
                                    document.createElement('h3');

                                const description =
                                    document.createElement('p');

                                /*
                                 * Support either structured recommendation
                                 * arrays or simple recommendation strings.
                                 */
                                if (
                                    item &&
                                    typeof item === 'object'
                                ) {

                                    title.textContent =
                                        item.name ||
                                        item.title ||
                                        item.service ||
                                        'Recommended Softkom Solution';

                                    description.textContent =
                                        item.description ||
                                        item.summary ||
                                        item.reason ||
                                        'This opportunity is a strong match for your assessment results.';

                                } else {

                                    title.textContent =
                                        String(item);

                                    description.textContent =
                                        'This solution is a strong match for the opportunities identified in your assessment.';
                                }

                                card.appendChild(title);
                                card.appendChild(description);

                                recommendationsEl.appendChild(card);
                            });

                        } else {

                            const fallback =
                                document.createElement('p');

                            fallback.textContent =
                                'Your detailed Softkom recommendations will be included in your personalised systems roadmap.';

                            recommendationsEl.appendChild(fallback);
                        }
                    }

                    if (result.next_step) {
                        if (nextStepTitleEl && result.next_step.title) {
                            nextStepTitleEl.textContent = result.next_step.title;
                        }

                        if (nextStepBodyEl && result.next_step.body) {
                            nextStepBodyEl.textContent = result.next_step.body;
                        }

                        if (nextStepLinkEl) {
                            if (result.next_step.label) {
                                nextStepLinkEl.textContent = result.next_step.label;
                            }

                            if (result.next_step.url) {
                                nextStepLinkEl.href = result.next_step.url;
                            }
                        }
                    }

                    leadSection.hidden = true;
                    resultsSection.hidden = false;

                    resultsSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                } catch (error) {

                    console.error(
                        'Softkom assessment submission failed:',
                        error
                    );

                    window.alert(
                        error && error.message
                            ? error.message
                            : 'We could not generate your results. Please try again.'
                    );

                } finally {

                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent =
                            originalButtonText || 'Show My Results';
                    }
                }
            });
        }
    });

})();




