<?php
/**
 * Softkom V3 â€” Business Systems Assessment
 *
 * Public lead-generation assessment experience.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$questions = function_exists( 'softkom_v3_assessment_question_bank' )
    ? softkom_v3_assessment_question_bank()
    : array();

$sections = function_exists( 'softkom_v3_assessment_sections' )
    ? softkom_v3_assessment_sections()
    : array();
?>

<main class="sk-assessment">

    <section class="sk-assessment-hero">
        <div class="container">

            <div class="sk-assessment-hero__content">

                <span class="sk-assessment-eyebrow">
                    Free Business Systems Assessment
                </span>

                <h1>
                    How mature are your business systems?
                </h1>

                <p class="sk-assessment-lead">
                    Discover where disconnected tools, manual processes,
                    reporting gaps and missed automation opportunities may
                    be holding your business back.
                </p>

                <div class="sk-assessment-benefits">
                    <span>3-minute assessment</span>
                    <span>Personalised maturity score</span>
                    <span>Actionable recommendations</span>
                </div>

                <button
                    type="button"
                    class="sk-btn sk-btn-primary"
                    data-assessment-start
                >
                    Start My Free Assessment
                </button>

                <p class="sk-assessment-note">
                    No obligation. Your results are designed to help you
                    identify practical opportunities for improvement.
                </p>

            </div>

        </div>
    </section>


    <section
        class="sk-assessment-app"
        data-assessment-app
        hidden
    >
        <div class="container">

            <div class="sk-assessment-progress">

                <div class="sk-assessment-progress__top">
                    <span data-assessment-progress-label>
                        Question 1
                    </span>

                    <span data-assessment-progress-percent>
                        0%
                    </span>
                </div>

                <div class="sk-assessment-progress__track">
                    <div
                        class="sk-assessment-progress__bar"
                        data-assessment-progress-bar
                    ></div>
                </div>

            </div>


            <div class="sk-assessment-card">

                <div class="sk-assessment-card__header">

                    <span
                        class="sk-assessment-section"
                        data-assessment-section
                    >
                        Business Systems
                    </span>

                    <h2 data-assessment-question>
                        Let's understand how your business operates today.
                    </h2>

                    <p data-assessment-help>
                        Select the answer that best reflects your current situation.
                    </p>

                </div>


                <div
                    class="sk-assessment-options"
                    data-assessment-options
                >
                    <!-- JavaScript will render answer choices here. -->
                </div>


                <div class="sk-assessment-actions">

                    <button
                        type="button"
                        class="sk-btn sk-btn-secondary"
                        data-assessment-back
                        disabled
                    >
                        Back
                    </button>

                    <button
                        type="button"
                        class="sk-btn sk-btn-primary"
                        data-assessment-next
                        disabled
                    >
                        Continue
                    </button>

                </div>

            </div>

        </div>
    </section>


    <section
        class="sk-assessment-lead-capture"
        data-assessment-lead
        hidden
    >
        <div class="container">

            <div class="sk-assessment-card">

                <span class="sk-assessment-eyebrow">
                    Assessment Complete
                </span>

                <h2>
                    Your Business Systems Score is ready.
                </h2>

                <p>
                    Enter your details to see your maturity level,
                    key opportunity areas and recommended next steps.
                </p>

                <form data-assessment-lead-form>

                    <div
                        class="sk-assessment-honeypot"
                        aria-hidden="true"
                    >
                        <label>
                            Leave this field empty
                            <input
                                type="text"
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </label>
                    </div>

                    <input
                        type="hidden"
                        name="started_at"
                        data-assessment-started-at
                        value=""
                    >

                    <input
                        type="hidden"
                        name="completed_at"
                        data-assessment-completed-at
                        value=""
                    >

                    <div class="sk-form-grid">

                        <label>
                            First name
                            <input
                                type="text"
                                name="first_name"
                                required
                                autocomplete="given-name"
                            >
                        </label>

                        <label>
                            Last name
                            <input
                                type="text"
                                name="last_name"
                                required
                                autocomplete="family-name"
                            >
                        </label>

                        <label>
                            Business email
                            <input
                                type="email"
                                name="email"
                                required
                                autocomplete="email"
                            >
                        </label>

                        <label>
                            Company
                            <input
                                type="text"
                                name="company"
                                required
                                autocomplete="organization"
                            >
                        </label>

                    </div>

                    <fieldset class="sk-assessment-qualification">
                        <legend>Tailor your assessment</legend>
                        <p>
                            These answers personalize your recommendations and
                            do not change your Business Systems Maturity Score.
                        </p>

                        <div class="sk-form-grid">
                            <label>
                                Organisation size
                                <select name="qualification[company_size]" required>
                                    <option value="">Select one</option>
                                    <option value="1-5">1â€“5 people</option>
                                    <option value="6-20">6â€“20 people</option>
                                    <option value="21-50">21â€“50 people</option>
                                    <option value="51-200">51â€“200 people</option>
                                    <option value="201-plus">201+ people</option>
                                </select>
                            </label>

                            <label>
                                Your decision role
                                <select name="qualification[decision_role]" required>
                                    <option value="">Select one</option>
                                    <option value="researcher">Researcher</option>
                                    <option value="influencer">Influencer</option>
                                    <option value="decision-maker">Decision-maker</option>
                                    <option value="owner-executive">Owner or executive</option>
                                </select>
                            </label>

                            <label>
                                Importance of solving these challenges
                                <select name="qualification[urgency]" required>
                                    <option value="">Select one</option>
                                    <option value="exploring">Exploring</option>
                                    <option value="useful">Useful</option>
                                    <option value="important">Important</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </label>

                            <label>
                                Ideal start timeframe
                                <select name="qualification[timeframe]" required>
                                    <option value="">Select one</option>
                                    <option value="12-plus-months">12+ months</option>
                                    <option value="6-12-months">6â€“12 months</option>
                                    <option value="3-6-months">3â€“6 months</option>
                                    <option value="1-3-months">1â€“3 months</option>
                                    <option value="immediately">Immediately</option>
                                </select>
                            </label>

                            <label>
                                Investment readiness
                                <select name="qualification[budget_readiness]" required>
                                    <option value="">Select one</option>
                                    <option value="research-only">Research only</option>
                                    <option value="budget-unknown">Budget unknown</option>
                                    <option value="budget-planning">Budget planning</option>
                                    <option value="budget-available">Budget available</option>
                                </select>
                            </label>

                            <label>
                                Readiness to change processes
                                <select name="qualification[change_readiness]" required>
                                    <option value="">Select one</option>
                                    <option value="resistant">Resistant</option>
                                    <option value="cautious">Cautious</option>
                                    <option value="open">Open</option>
                                    <option value="ready">Ready</option>
                                </select>
                            </label>

                            <label>
                                Discuss your results with Softkom?
                                <select name="qualification[consultation_intent]" required>
                                    <option value="">Select one</option>
                                    <option value="roadmap-only">Roadmap only</option>
                                    <option value="maybe-later">Maybe later</option>
                                    <option value="yes">Yes</option>
                                    <option value="book-now">Book now</option>
                                </select>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="sk-assessment-qualification">
                        <legend>AI &amp; automation opportunities</legend>

                        <div class="sk-form-grid">
                            <label>
                                Manual sales prospecting and follow-up
                                <select name="qualification[sales_process]" required>
                                    <option value="">Select one</option>
                                    <option value="automated">Automated</option>
                                    <option value="mostly-automated">Mostly automated</option>
                                    <option value="mixed">Mixed</option>
                                    <option value="mostly-manual">Mostly manual</option>
                                    <option value="fully-manual">Fully manual</option>
                                </select>
                            </label>

                            <label>
                                Time spent on repetitive customer enquiries
                                <select name="qualification[customer_enquiries]" required>
                                    <option value="">Select one</option>
                                    <option value="very-little">Very little</option>
                                    <option value="some">Some</option>
                                    <option value="significant">Significant</option>
                                    <option value="very-high">Very high</option>
                                </select>
                            </label>

                            <label>
                                Ease of finding internal knowledge and SOPs
                                <select name="qualification[knowledge_access]" required>
                                    <option value="">Select one</option>
                                    <option value="very-easy">Very easy</option>
                                    <option value="mostly-easy">Mostly easy</option>
                                    <option value="mixed">Mixed</option>
                                    <option value="difficult">Difficult</option>
                                    <option value="very-hard">Very hard</option>
                                </select>
                            </label>

                            <label>
                                Client-facing delivery model
                                <select name="qualification[partner_type]" required>
                                    <option value="">Select one</option>
                                    <option value="no">Not applicable</option>
                                    <option value="sometimes">Sometimes</option>
                                    <option value="consultant">Consultancy</option>
                                    <option value="agency">Agency</option>
                                    <option value="technology-partner">Technology partner</option>
                                </select>
                            </label>
                        </div>
                    </fieldset>

                    <button
                        type="submit"
                        class="sk-btn sk-btn-primary"
                    >
                        Show My Results
                    </button>

                </form>

            </div>

        </div>
    </section>


    <section
        class="sk-assessment-results"
        data-assessment-results
        hidden
    >
        <div class="container">

            <div class="sk-assessment-card">

                <span class="sk-assessment-eyebrow">
                    Your Results
                </span>

                <div class="sk-assessment-score">
                    <strong data-assessment-score>--</strong>
                    <span>/100</span>
                </div>

                <h2 data-assessment-maturity>
                    Your Systems Maturity
                </h2>

                <p data-assessment-summary>
                    Your personalised assessment results will appear here.
                </p>

                <div class="sk-assessment-metrics" aria-label="Assessment scores">
                    <div>
                        <strong data-assessment-ai-score>--</strong>
                        <span>AI &amp; Automation Opportunity</span>
                    </div>
                    <div>
                        <strong data-assessment-commercial-score>--</strong>
                        <span>Commercial Fit</span>
                    </div>
                    <div>
                        <strong data-assessment-intent-score>--</strong>
                        <span>Purchase Intent</span>
                    </div>
                    <div>
                        <strong data-assessment-overall-score>--</strong>
                        <span>Overall Lead Score</span>
                    </div>
                    <div>
                        <strong data-assessment-temperature>--</strong>
                        <span>Lead Temperature</span>
                    </div>
                </div>

                <h3>Top priority opportunities</h3>

                <div
                    class="sk-assessment-priorities"
                    data-assessment-priorities
                ></div>

                <h3>Recommended Softkom solutions</h3>

                <div
                    class="sk-assessment-recommendations"
                    data-assessment-recommendations
                ></div>

                <div class="sk-assessment-result-cta">

                    <h3 data-assessment-next-step-title>
                        Want help turning these opportunities into a practical roadmap?
                    </h3>

                    <p data-assessment-next-step-body>
                        Book a Solution Mapping Session with Softkom Solutions.
                    </p>

                    <a
                        href="/contact/"
                        class="sk-btn sk-btn-primary"
                        data-assessment-next-step-link
                    >
                        Book a Strategy Call
                    </a>

                </div>

            </div>

        </div>
    </section>

</main>

