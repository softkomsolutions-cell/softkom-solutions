<?php
/**
 * Softkom Commercial Catalogue V1.
 *
 * Central commercial configuration for implementation projects
 * and recurring managed services.
 *
 * The assessment/recommendation engine should read commercial
 * information from this catalogue rather than owning pricing.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Return the Softkom commercial catalogue.
 *
 * Prices are internal commercial guidance and may be adjusted
 * by sales before a proposal is issued.
 */
function softkom_v3_commercial_catalogue() {

    $defaults = array(

        'managed_growth' => array(

            'name' => 'Managed Growth Automation',

            'category' => 'Growth & Sales',

            'implementation' => array(
                'name'       => 'Lead Generation & Sales Automation Implementation',
                'price_from' => 15000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 6000,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 10500,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 15000,
                ),
            ),
        ),


        'managed_ai' => array(

            'name' => 'Managed AI Operations',

            'category' => 'AI',

            'implementation' => array(
                'name'       => 'AI Automation Implementation',
                'price_from' => 20000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 7500,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 11250,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 15000,
                ),
            ),
        ),


        'managed_automation' => array(

            'name' => 'Managed Business Automation',

            'category' => 'Automation',

            'implementation' => array(
                'name'       => 'Business Process Automation Implementation',
                'price_from' => 15000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 5000,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 8500,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 12000,
                ),
            ),
        ),


        'managed_systems' => array(

            'name' => 'Managed Business Systems',

            'category' => 'Business Systems',

            'implementation' => array(
                'name'       => 'Business Systems Implementation',
                'price_from' => 15000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 4500,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 7250,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 10000,
                ),
            ),
        ),


        'managed_compliance' => array(

            'name' => 'Managed Compliance Operations',

            'category' => 'Compliance',

            'implementation' => array(
                'name'       => 'Compliance Platform Implementation',
                'price_from' => 15000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 5000,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 8500,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 12000,
                ),
            ),
        ),


        'managed_digital' => array(

            'name' => 'Managed Digital Platform',

            'category' => 'Digital',

            'implementation' => array(
                'name'       => 'Digital Platform Implementation',
                'price_from' => 12000,
            ),

            'plans' => array(

                'starter' => array(
                    'name'    => 'Starter',
                    'monthly' => 3500,
                ),

                'growth' => array(
                    'name'    => 'Growth',
                    'monthly' => 6000,
                ),

                'scale' => array(
                    'name'    => 'Scale',
                    'monthly' => 8500,
                ),
            ),
        ),
    );

    $saved = get_option(
        'softkom_v3_commercial_catalogue',
        array()
    );

    if ( ! is_array( $saved ) || empty( $saved ) ) {
        return $defaults;
    }

    /*
     * Merge saved commercial settings over safe PHP defaults.
     *
     * array_replace_recursive preserves any missing default fields,
     * allowing the PHP catalogue to remain the fallback source.
     */
    return array_replace_recursive(
        $defaults,
        $saved
    );
}


/**
 * Get one commercial service.
 */
function softkom_v3_commercial_service( $service_key ) {

    $catalogue = softkom_v3_commercial_catalogue();

    return isset( $catalogue[ $service_key ] )
        ? $catalogue[ $service_key ]
        : array();
}


/**
 * Get one commercial plan.
 */
function softkom_v3_commercial_plan(
    $service_key,
    $plan_key = 'growth'
) {

    $service = softkom_v3_commercial_service(
        $service_key
    );

    if (
        empty( $service ) ||
        empty( $service['plans'] ) ||
        ! isset( $service['plans'][ $plan_key ] )
    ) {
        return array();
    }

    return $service['plans'][ $plan_key ];
}


/**
 * Get the minimum and maximum MRR for a service.
 */
function softkom_v3_commercial_mrr_range( $service_key ) {

    $service = softkom_v3_commercial_service(
        $service_key
    );

    if (
        empty( $service ) ||
        empty( $service['plans'] )
    ) {
        return array(
            'min' => 0,
            'max' => 0,
        );
    }

    $monthly_values = array();

    foreach ( $service['plans'] as $plan ) {

        if ( isset( $plan['monthly'] ) ) {

            $monthly_values[] =
                (int) $plan['monthly'];
        }
    }

    if ( empty( $monthly_values ) ) {

        return array(
            'min' => 0,
            'max' => 0,
        );
    }

    return array(
        'min' => min( $monthly_values ),
        'max' => max( $monthly_values ),
    );
}


/**
 * Determine a recommended commercial plan from readiness.
 *
 * This is intentionally separate from the assessment engine so
 * the commercial rules can evolve independently.
 */
function softkom_v3_commercial_recommended_plan(
    $readiness_score
) {

    $readiness_score = (int) $readiness_score;

    if ( $readiness_score >= 80 ) {
        return 'scale';
    }

    if ( $readiness_score >= 60 ) {
        return 'growth';
    }

    return 'starter';
}


/**
 * Build a complete commercial offer.
 */
function softkom_v3_commercial_offer(
    $service_key,
    $readiness_score
) {

    $service = softkom_v3_commercial_service(
        $service_key
    );

    if ( empty( $service ) ) {
        return array();
    }

    $plan_key =
        softkom_v3_commercial_recommended_plan(
            $readiness_score
        );

    $plan = softkom_v3_commercial_plan(
        $service_key,
        $plan_key
    );

    $range = softkom_v3_commercial_mrr_range(
        $service_key
    );

    return array(

        'service_key' => $service_key,

        'service_name' => isset( $service['name'] )
            ? $service['name']
            : '',

        'category' => isset( $service['category'] )
            ? $service['category']
            : '',

        'implementation_name' =>
            isset( $service['implementation']['name'] )
                ? $service['implementation']['name']
                : '',

        'implementation_price_from' =>
            isset( $service['implementation']['price_from'] )
                ? (int) $service['implementation']['price_from']
                : 0,

        'plan_key' => $plan_key,

        'plan_name' => isset( $plan['name'] )
            ? $plan['name']
            : '',

        'monthly' => isset( $plan['monthly'] )
            ? (int) $plan['monthly']
            : 0,

        'mrr_min' => isset( $range['min'] )
            ? (int) $range['min']
            : 0,

        'mrr_max' => isset( $range['max'] )
            ? (int) $range['max']
            : 0,
    );
}