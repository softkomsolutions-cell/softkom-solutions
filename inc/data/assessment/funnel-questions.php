<?php
/**
 * Softkom Funnel V2 assessment configuration.
 *
 * Maps the detailed diagnostic question bank into the seven
 * customer-facing Business Systems Maturity areas.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Seven customer-facing assessment areas.
 */
function softkom_v3_funnel_assessment_areas() {

    return array(

        'strategy-planning' => array(
            'label' => 'Strategy & Planning',
            'sections' => array(
                'business-visibility',
                'reporting',
            ),
        ),

        'process-automation' => array(
            'label' => 'Process & Automation',
            'sections' => array(
                'process-maturity',
                'automation',
            ),
        ),

        'technology' => array(
            'label' => 'Technology',
            'sections' => array(
                'systems-integration',
                'ai-readiness',
            ),
        ),

        'people-culture' => array(
            'label' => 'People & Culture',
            'sections' => array(
                'process-maturity',
                'operational-risk',
            ),
        ),

        'data-reporting' => array(
            'label' => 'Data & Reporting',
            'sections' => array(
                'data-quality',
                'reporting',
            ),
        ),

        'customer-experience' => array(
            'label' => 'Customer Experience',
            'sections' => array(
                'business-visibility',
                'automation',
                'operational-risk',
            ),
        ),

        'governance-compliance' => array(
            'label' => 'Governance & Compliance',
            'sections' => array(
                'governance',
                'compliance',
                'ai-readiness',
            ),
        ),
    );
}


/**
 * Question ids used by the public Funnel V2 assessment.
 *
 * Each customer-facing area contributes two diagnostic answers. Keeping this
 * list server-side lets the AJAX handler reject missing or invented answers.
 */
function softkom_v3_funnel_public_question_ids() {

    return array(
        'visibility-01',
        'reporting-03',
        'process-01',
        'automation-01',
        'integration-01',
        'ai-01',
        'process-03',
        'risk-01',
        'data-01',
        'reporting-02',
        'visibility-03',
        'automation-03',
        'governance-01',
        'compliance-02',
    );
}


/**
 * Funnel V2 commercial qualification questions.
 *
 * These are not part of the customer's maturity score.
 * They provide sales qualification signals.
 */
function softkom_v3_funnel_qualification_questions() {

    return array(

        'company_size' => array(
            'label' => 'How many people work in your organisation?',
            'type'  => 'select',
        ),

        'decision_role' => array(
            'label' => 'What role do you play in technology or operational decisions?',
            'type'  => 'select',
        ),

        'urgency' => array(
            'label' => 'How important is solving this challenge?',
            'type'  => 'select',
        ),

        'timeframe' => array(
            'label' => 'When would you ideally like to start improving this area?',
            'type'  => 'select',
        ),

        'budget_readiness' => array(
            'label' => 'How would you describe your readiness to invest in the right solution?',
            'type'  => 'select',
        ),

        'change_readiness' => array(
            'label' => 'How open is your organisation to changing existing processes?',
            'type'  => 'select',
        ),

        'consultation_intent' => array(
            'label' => 'Would you like Softkom to discuss your results with you?',
            'type'  => 'select',
        ),

        'sales_process' => array(
            'label' => 'How much of your prospecting and sales follow-up is currently manual?',
            'type'  => 'select',
        ),

        'customer_enquiries' => array(
            'label' => 'How much staff time is spent answering repetitive customer enquiries?',
            'type'  => 'select',
        ),

        'knowledge_access' => array(
            'label' => 'How easy is it for employees to find accurate internal information?',
            'type'  => 'select',
        ),

        'partner_type' => array(
            'label' => 'Do you deliver technology, consulting or digital services to clients?',
            'type'  => 'select',
        ),
    );
}
