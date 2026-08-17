<?php
/**
 * Softkom Funnel V2 solution matching.
 *
 * Maps diagnostic signals to commercially actionable
 * Softkom AI & Automation Solutions.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Softkom Funnel V2 solution catalogue.
 */
function softkom_v3_funnel_solution_catalogue() {

    return array(

        'ai-business-automation' => array(
            'name'        => 'AI Business Automation',
            'category'    => 'AI & Automation',
            'description' => 'Automate repetitive administrative and operational workflows.',
            'signals'     => array(
                'manual_processes',
                'repetitive_admin',
                'workflow_delays',
                'manual_handoffs',
            ),
        ),

        'ai-lead-engine' => array(
            'name'        => 'AI Lead Engine',
            'category'    => 'AI & Automation',
            'description' => 'AI-assisted prospecting, lead research, personalisation and sales automation.',
            'signals'     => array(
                'sales_automation_opportunity',
                'manual_lead_research',
                'manual_follow_up',
                'lead_generation_gap',
            ),
        ),

        'ai-customer-service-agent' => array(
            'name'        => 'AI Customer Service Agent',
            'category'    => 'AI & Automation',
            'description' => 'AI-powered customer enquiries, lead capture, support and escalation.',
            'signals'     => array(
                'customer_service_opportunity',
                'repetitive_enquiries',
                'slow_response',
                'manual_customer_support',
            ),
        ),

        'ai-knowledge-assistant' => array(
            'name'        => 'AI Knowledge Assistant',
            'category'    => 'AI & Automation',
            'description' => 'Allow employees to query company documentation, policies, SOPs and internal knowledge.',
            'signals'     => array(
                'knowledge_access_opportunity',
                'tribal_knowledge',
                'documentation_gap',
                'knowledge_search',
            ),
        ),

        'white-label-ai-solutions' => array(
            'name'        => 'White-Label AI Solutions',
            'category'    => 'AI & Automation',
            'description' => 'AI implementation and fulfilment services for agencies, consultants and technology partners.',
            'signals'     => array(
                'partner_delivery',
                'agency_ai_delivery',
                'white_label_opportunity',
            ),
        ),

        'process-automation' => array(
            'name'        => 'Process Automation',
            'category'    => 'Business Systems',
            'description' => 'Reduce repetitive work, manual handoffs and operational bottlenecks through workflow automation.',
            'signals'     => array(
                'manual_processes',
                'repetitive_admin',
                'workflow_delays',
                'manual_handoffs',
            ),
        ),

        'systems-integration' => array(
            'name'        => 'Systems Integration',
            'category'    => 'Business Systems',
            'description' => 'Connect disconnected systems and reduce duplicate data entry and manual transfer of information.',
            'signals'     => array(
                'disconnected_data',
                'duplicate_entry',
                'integration_gap',
            ),
        ),

        'business-intelligence' => array(
            'name'        => 'Business Intelligence',
            'category'    => 'Business Systems',
            'description' => 'Improve management visibility, reporting and access to reliable operational information.',
            'signals'     => array(
                'reporting_gap',
                'visibility_gap',
                'disconnected_data',
            ),
        ),

        'compliance-platforms' => array(
            'name'        => 'Compliance Platforms',
            'category'    => 'Business Systems',
            'description' => 'Improve compliance workflows, evidence collection, controls and operational governance.',
            'signals'     => array(
                'compliance_gap',
                'audit_evidence_gap',
                'governance_gap',
            ),
        ),

        'custom-software' => array(
            'name'        => 'Custom Software & Platforms',
            'category'    => 'Business Systems',
            'description' => 'Purpose-built software for operational requirements that generic tools cannot adequately support.',
            'signals'     => array(
                'solution_gap',
                'legacy_system_gap',
                'specialised_workflow',
            ),
        ),
    );
}


/**
 * Match scored opportunity signals to Softkom solutions.
 *
 * Signal values are expected to be 0-100.
 */
function softkom_v3_match_funnel_solutions( $signals ) {

    $catalogue = softkom_v3_funnel_solution_catalogue();
    $matches   = array();

    foreach ( $catalogue as $solution_id => $solution ) {

        $total = 0;
        $count = 0;

        foreach ( $solution['signals'] as $signal ) {

            if ( isset( $signals[ $signal ] ) ) {
                $total += softkom_v3_funnel_clamp_score(
                    $signals[ $signal ]
                );

                $count++;
            }
        }

        if ( 0 === $count ) {
            continue;
        }

        $opportunity_score = softkom_v3_funnel_clamp_score(
            $total / $count
        );

        if ( $opportunity_score < 40 ) {
            continue;
        }

        $matches[] = array(
            'id'                => $solution_id,
            'name'              => $solution['name'],
            'category'          => $solution['category'],
            'description'       => $solution['description'],
            'opportunity_score' => $opportunity_score,
        );
    }

    usort(
        $matches,
        function ( $a, $b ) {
            return $b['opportunity_score']
                <=> $a['opportunity_score'];
        }
    );

    return $matches;
}


/**
 * Return the top Softkom recommendations.
 */
function softkom_v3_funnel_top_recommendations(
    $signals,
    $limit = 3
) {

    $matches = softkom_v3_match_funnel_solutions(
        $signals
    );

    return array_slice(
        $matches,
        0,
        max( 1, (int) $limit )
    );
}
