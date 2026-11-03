<?php
/**
 * Softkom Visibility Engine Admin V1.
 *
 * Internal operating dashboard for commercial search opportunities,
 * publishing status and funnel mapping.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function softkom_v3_visibility_engine_rows() {
    return array(
        array(
            'priority' => 1,
            'topic' => 'Automate Quote Follow-Up South Africa',
            'slug' => 'automate-quote-follow-up-south-africa',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Efficiency Review / Lost Lead Recovery',
            'cluster' => 'Revenue Recovery',
        ),
        array(
            'priority' => 2,
            'topic' => 'Reduce Manual Admin in Your Business',
            'slug' => 'reduce-manual-admin-business',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Efficiency Review / Business Process Review',
            'cluster' => 'Business Efficiency',
        ),
        array(
            'priority' => 3,
            'topic' => 'Reduce Software Subscription Costs',
            'slug' => 'reduce-software-subscription-costs',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Software Cost Check',
            'cluster' => 'Business Efficiency',
        ),
        array(
            'priority' => 4,
            'topic' => 'Recover Old Sales Leads South Africa',
            'slug' => 'recover-old-sales-leads-south-africa',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Lost Lead Recovery',
            'cluster' => 'Revenue Recovery',
        ),
        array(
            'priority' => 5,
            'topic' => 'Business Process Automation Johannesburg',
            'slug' => 'business-process-automation-johannesburg',
            'intent' => 'Commercial local',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Process Review',
            'cluster' => 'Automation',
        ),
        array(
            'priority' => 6,
            'topic' => 'Invoice Chasing Automation South Africa',
            'slug' => 'invoice-chasing-automation-south-africa',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Process Review',
            'cluster' => 'Revenue Recovery',
        ),
        array(
            'priority' => 7,
            'topic' => 'Automate Management Reporting South Africa',
            'slug' => 'automate-management-reporting-south-africa',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Process Review',
            'cluster' => 'Reporting & Visibility',
        ),
        array(
            'priority' => 8,
            'topic' => 'Reduce Duplicate Data Entry',
            'slug' => 'reduce-duplicate-data-entry',
            'intent' => 'Commercial',
            'status' => 'Built / pending deploy',
            'funnel' => 'Business Efficiency Review / Systems Integration',
            'cluster' => 'Systems Integration',
        ),
        array(
            'priority' => 9,
            'topic' => 'AI Policy for Small Business South Africa',
            'slug' => 'ai-policy-small-business-south-africa',
            'intent' => 'Decision support',
            'status' => 'Research next',
            'funnel' => 'AI Usage & Risk Review',
            'cluster' => 'Practical AI',
        ),
        array(
            'priority' => 10,
            'topic' => 'Business Depends on One Employee',
            'slug' => 'business-depends-on-one-employee',
            'intent' => 'Problem awareness',
            'status' => 'Research next',
            'funnel' => 'Business Memory & Continuity',
            'cluster' => 'Business Continuity',
        ),
        array(
            'priority' => 11,
            'topic' => 'Website Enquiries Not Being Followed Up',
            'slug' => 'website-enquiries-not-being-followed-up',
            'intent' => 'Commercial',
            'status' => 'Research next',
            'funnel' => 'Revenue Lab / Lost Lead Recovery',
            'cluster' => 'Revenue Recovery',
        ),
        array(
            'priority' => 12,
            'topic' => 'When to Replace Spreadsheets in a Business',
            'slug' => 'when-to-replace-spreadsheets-business',
            'intent' => 'Decision support',
            'status' => 'Research next',
            'funnel' => 'Business Efficiency Review / Custom Software',
            'cluster' => 'Business Systems',
        ),
    );
}

function softkom_v3_register_visibility_engine_admin() {
    add_menu_page(
        'Softkom Visibility Engine',
        'Visibility Engine',
        'manage_options',
        'softkom-visibility-engine',
        'softkom_v3_render_visibility_engine_admin',
        'dashicons-chart-line',
        26
    );
}
add_action( 'admin_menu', 'softkom_v3_register_visibility_engine_admin' );

function softkom_v3_visibility_status_badge( $status ) {
    $class = 'pending';
    if ( false !== stripos( $status, 'built' ) ) {
        $class = 'built';
    } elseif ( false !== stripos( $status, 'live' ) ) {
        $class = 'live';
    }

    return '<span class="sk-ve-badge sk-ve-badge--' . esc_attr( $class ) . '">' . esc_html( $status ) . '</span>';
}

function softkom_v3_render_visibility_engine_admin() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $rows = softkom_v3_visibility_engine_rows();
    $built = 0;
    $research = 0;
    foreach ( $rows as $row ) {
        if ( false !== stripos( $row['status'], 'built' ) ) {
            $built++;
        }
        if ( false !== stripos( $row['status'], 'research' ) ) {
            $research++;
        }
    }

    $clusters = array();
    foreach ( $rows as $row ) {
        $clusters[ $row['cluster'] ][] = $row;
    }

    ?>
    <div class="wrap sk-ve-wrap">
        <h1>Softkom Visibility Engine</h1>
        <p class="sk-ve-intro">
            Find commercial search demand, publish useful problem-led pages and route each visitor
            into a measurable Softkom diagnostic or sales action.
        </p>

        <div class="sk-ve-kpis">
            <div class="sk-ve-kpi"><strong><?php echo esc_html( count( $rows ) ); ?></strong><span>Tracked opportunities</span></div>
            <div class="sk-ve-kpi"><strong><?php echo esc_html( $built ); ?></strong><span>Built / pending deployment</span></div>
            <div class="sk-ve-kpi"><strong><?php echo esc_html( $research ); ?></strong><span>Research next</span></div>
            <div class="sk-ve-kpi"><strong>8</strong><span>Current funnel entry pages</span></div>
        </div>

        <section class="sk-ve-panel">
            <div class="sk-ve-panel-head">
                <div>
                    <h2>Content Opportunity Queue</h2>
                    <p>Prioritised by commercial intent, Softkom fit and funnel clarity.</p>
                </div>
            </div>
            <div class="sk-ve-table-wrap">
                <table class="widefat striped sk-ve-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Topic / Search Intent</th>
                            <th>Cluster</th>
                            <th>Intent</th>
                            <th>Funnel Destination</th>
                            <th>Status</th>
                            <th>Page</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $rows as $row ) : ?>
                        <tr>
                            <td><?php echo esc_html( $row['priority'] ); ?></td>
                            <td><strong><?php echo esc_html( $row['topic'] ); ?></strong><br><code><?php echo esc_html( $row['slug'] ); ?></code></td>
                            <td><?php echo esc_html( $row['cluster'] ); ?></td>
                            <td><?php echo esc_html( $row['intent'] ); ?></td>
                            <td><?php echo esc_html( $row['funnel'] ); ?></td>
                            <td><?php echo wp_kses_post( softkom_v3_visibility_status_badge( $row['status'] ) ); ?></td>
                            <td><a href="<?php echo esc_url( home_url( '/' . $row['slug'] . '/' ) ); ?>" target="_blank" rel="noopener">Open</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sk-ve-panel">
            <h2>Content Calendar</h2>
            <p>Use this as the operating cadence, not an article quota. Publish when a topic has commercial value.</p>
            <div class="sk-ve-calendar">
                <?php
                $slots = array(
                    array( 'Week 1', 'Quote follow-up', 'Revenue Recovery', 'Built' ),
                    array( 'Week 1', 'Manual admin', 'Business Efficiency', 'Built' ),
                    array( 'Week 2', 'Software cost', 'Business Efficiency', 'Built' ),
                    array( 'Week 2', 'Old lead recovery', 'Revenue Recovery', 'Built' ),
                    array( 'Week 3', 'Invoice chasing', 'Revenue Recovery', 'Built' ),
                    array( 'Week 3', 'Management reporting', 'Reporting & Visibility', 'Built' ),
                    array( 'Week 4', 'Duplicate data entry', 'Systems Integration', 'Built' ),
                    array( 'Week 4', 'AI policy for SMEs', 'Practical AI', 'Research' ),
                );
                foreach ( $slots as $slot ) :
                    ?>
                    <article class="sk-ve-calendar-card">
                        <span><?php echo esc_html( $slot[0] ); ?></span>
                        <h3><?php echo esc_html( $slot[1] ); ?></h3>
                        <p><?php echo esc_html( $slot[2] ); ?></p>
                        <?php echo wp_kses_post( softkom_v3_visibility_status_badge( $slot[3] ) ); ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="sk-ve-panel">
            <h2>Content Strategy Map</h2>
            <div class="sk-ve-map">
                <?php foreach ( $clusters as $cluster => $items ) : ?>
                    <div class="sk-ve-cluster">
                        <h3><?php echo esc_html( $cluster ); ?></h3>
                        <ul>
                            <?php foreach ( $items as $item ) : ?>
                                <li><?php echo esc_html( $item['topic'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="sk-ve-panel sk-ve-measurement">
            <h2>Measurement</h2>
            <p><strong>Primary:</strong> impressions, non-brand clicks, CTR, qualified leads, meetings, paid reviews and project revenue.</p>
            <p><strong>Current limitation:</strong> first-party Search Console data is not available through the connected GSC Wizard until its subscription is restored. The dashboard is ready for that data layer when access returns.</p>
        </section>
    </div>

    <style>
        .sk-ve-wrap{max-width:1500px}
        .sk-ve-intro{font-size:16px;max-width:900px}
        .sk-ve-kpis{display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:16px;margin:22px 0}
        .sk-ve-kpi{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px}
        .sk-ve-kpi strong{display:block;font-size:30px;color:#0b1e38}
        .sk-ve-kpi span{color:#646970}
        .sk-ve-panel{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:22px;margin:20px 0}
        .sk-ve-panel h2{margin-top:0}
        .sk-ve-table-wrap{overflow:auto}
        .sk-ve-table td{vertical-align:middle}
        .sk-ve-badge{display:inline-block;padding:5px 9px;border-radius:999px;font-size:12px;font-weight:600}
        .sk-ve-badge--built{background:#dcfce7;color:#166534}
        .sk-ve-badge--live{background:#dbeafe;color:#1d4ed8}
        .sk-ve-badge--pending{background:#fff7ed;color:#c2410c}
        .sk-ve-calendar{display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:14px}
        .sk-ve-calendar-card{border:1px solid #e2e4e7;border-radius:10px;padding:15px;background:#f8fafc}
        .sk-ve-calendar-card span{font-size:12px;color:#646970}
        .sk-ve-calendar-card h3{margin:6px 0}
        .sk-ve-calendar-card p{margin:0 0 10px;color:#646970}
        .sk-ve-map{display:grid;grid-template-columns:repeat(3,minmax(220px,1fr));gap:16px}
        .sk-ve-cluster{border:1px solid #e2e4e7;border-radius:10px;padding:16px}
        .sk-ve-cluster h3{margin-top:0;color:#0b1e38}
        .sk-ve-cluster ul{margin-bottom:0}
        @media (max-width:1000px){
            .sk-ve-kpis,.sk-ve-calendar,.sk-ve-map{grid-template-columns:1fr 1fr}
        }
        @media (max-width:700px){
            .sk-ve-kpis,.sk-ve-calendar,.sk-ve-map{grid-template-columns:1fr}
        }
    </style>
    <?php
}
