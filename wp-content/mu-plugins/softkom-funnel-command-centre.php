<?php
/**
 * Plugin Name: Softkom Funnel Command Centre
 * Description: Read-only sales funnel and pipeline reporting for Softkom leads.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_funnel_cc_leads() {
    return get_posts( array(
        'post_type' => 'softkom_lead', 'post_status' => 'any',
        'posts_per_page' => -1, 'fields' => 'ids',
        'orderby' => 'date', 'order' => 'DESC',
    ) );
}
function softkom_funnel_cc_text( $id, $key ) { return trim( (string) get_post_meta( $id, $key, true ) ); }
function softkom_funnel_cc_money( $value ) { return 'R ' . number_format_i18n( (float) $value, 0 ); }
function softkom_funnel_cc_is_qualified( $id ) {
    $routing = get_post_meta( $id, '_softkom_lead_routing', true );
    if ( is_string( $routing ) ) { $routing = json_decode( $routing, true ); }
    return is_array( $routing ) && ! empty( $routing['sales_eligible'] );
}
function softkom_funnel_cc_snapshot() {
    $out = array(
        'leads'=>0,'qualified'=>0,'hot'=>0,'warm'=>0,'pipeline'=>0,
        'estimated_mrr'=>0.0,'implementation_value'=>0.0,'score_total'=>0.0,
        'sources'=>array(),'stages'=>array(),'recent'=>array(),
    );
    foreach ( softkom_funnel_cc_leads() as $id ) {
        $out['leads']++;
        $qualified = softkom_funnel_cc_is_qualified( $id );
        if ( $qualified ) { $out['qualified']++; }
        $temperature = strtoupper( softkom_funnel_cc_text( $id, '_softkom_lead_temperature' ) );
        if ( 'HOT' === $temperature ) { $out['hot']++; }
        if ( 'WARM' === $temperature ) { $out['warm']++; }
        $stage = softkom_funnel_cc_text( $id, '_softkom_pipeline_stage' );
        if ( $stage ) { $out['pipeline']++; $out['stages'][ $stage ] = isset($out['stages'][$stage]) ? $out['stages'][$stage]+1 : 1; }
        $out['estimated_mrr'] += (float) get_post_meta( $id, '_softkom_estimated_mrr', true );
        $out['implementation_value'] += (float) get_post_meta( $id, '_softkom_implementation_price_from', true );
        $out['score_total'] += (float) get_post_meta( $id, '_softkom_score_overall_lead', true );
        $source = softkom_funnel_cc_text( $id, '_softkom_traffic_source' );
        if ( ! $source ) { $source = 'direct'; }
        if ( ! isset( $out['sources'][$source] ) ) { $out['sources'][$source] = array('leads'=>0,'qualified'=>0,'mrr'=>0.0); }
        $out['sources'][$source]['leads']++;
        if ( $qualified ) { $out['sources'][$source]['qualified']++; }
        $out['sources'][$source]['mrr'] += (float) get_post_meta( $id, '_softkom_estimated_mrr', true );
        if ( count( $out['recent'] ) < 12 ) {
            $out['recent'][] = array(
                'id'=>$id, 'title'=>get_the_title($id), 'date'=>get_the_date('Y-m-d H:i',$id),
                'temperature'=>$temperature ?: '—', 'score'=>(float)get_post_meta($id,'_softkom_score_overall_lead',true),
                'source'=>$source, 'channel'=>softkom_funnel_cc_text($id,'_softkom_acquisition_channel'),
                'plan'=>softkom_funnel_cc_text($id,'_softkom_commercial_plan_name'),
                'mrr'=>(float)get_post_meta($id,'_softkom_estimated_mrr',true), 'stage'=>$stage ?: 'New',
            );
        }
    }
    $out['average_score'] = $out['leads'] ? round($out['score_total']/$out['leads']) : 0;
    $out['qualification_rate'] = $out['leads'] ? round(($out['qualified']/$out['leads'])*100,1) : 0;
    $out['pipeline_rate'] = $out['leads'] ? round(($out['pipeline']/$out['leads'])*100,1) : 0;
    uasort($out['sources'],function($a,$b){ return $b['mrr'] <=> $a['mrr']; });
    arsort($out['stages']);
    return $out;
}
function softkom_funnel_cc_menu() {
    add_menu_page('Funnel Command Centre','Funnel','edit_posts','softkom-funnel','softkom_funnel_cc_render','dashicons-chart-line',25);
}
add_action('admin_menu','softkom_funnel_cc_menu');

function softkom_funnel_cc_render() {
    if ( ! current_user_can('edit_posts') ) { return; }
    $s=softkom_funnel_cc_snapshot();
    $cards=array(
        'Total Leads'=>number_format_i18n($s['leads']),
        'Qualified'=>number_format_i18n($s['qualified']).' ('.$s['qualification_rate'].'%)',
        'HOT / WARM'=>number_format_i18n($s['hot']).' / '.number_format_i18n($s['warm']),
        'Pipeline'=>number_format_i18n($s['pipeline']).' ('.$s['pipeline_rate'].'%)',
        'Estimated MRR'=>softkom_funnel_cc_money($s['estimated_mrr']),
        'Implementation Value'=>softkom_funnel_cc_money($s['implementation_value']),
        'Average Lead Score'=>number_format_i18n($s['average_score']),
    );
    ?>
    <div class="wrap"><h1>Softkom Funnel Command Centre</h1>
    <p>One view of lead quality, commercial value, acquisition and pipeline. This screen is read-only.</p>
    <style>
    .skcc-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin:18px 0 24px}.skcc-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px}.skcc-label{font-size:11px;text-transform:uppercase;color:#646970;font-weight:700}.skcc-value{font-size:25px;font-weight:700;margin-top:5px}.skcc-panels{display:grid;grid-template-columns:1.4fr 1fr;gap:18px}.skcc-panel{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:18px}.skcc-panel h2{margin-top:0}@media(max-width:900px){.skcc-panels{grid-template-columns:1fr}}.skcc-hot{font-weight:700}.skcc-muted{color:#646970}
    </style>
    <div class="skcc-grid"><?php foreach($cards as $label=>$value): ?><div class="skcc-card"><div class="skcc-label"><?php echo esc_html($label); ?></div><div class="skcc-value"><?php echo esc_html($value); ?></div></div><?php endforeach; ?></div>
    <div class="skcc-panels"><div class="skcc-panel"><h2>Acquisition → Revenue</h2><table class="widefat striped"><thead><tr><th>Source</th><th>Leads</th><th>Qualified</th><th>Qual. rate</th><th>Estimated MRR</th></tr></thead><tbody>
    <?php if(!$s['sources']): ?><tr><td colspan="5">No attributed leads yet.</td></tr><?php else: foreach($s['sources'] as $source=>$row): $rate=$row['leads']?round(($row['qualified']/$row['leads'])*100,1):0; ?><tr><td><strong><?php echo esc_html(ucwords(str_replace('-',' ',$source))); ?></strong></td><td><?php echo esc_html(number_format_i18n($row['leads'])); ?></td><td><?php echo esc_html(number_format_i18n($row['qualified'])); ?></td><td><?php echo esc_html($rate.'%'); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($row['mrr'])); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div><div class="skcc-panel"><h2>Pipeline Stages</h2><?php if(!$s['stages']): ?><p class="skcc-muted">No leads have entered a pipeline stage yet.</p><?php else: ?><table class="widefat striped"><thead><tr><th>Stage</th><th>Leads</th></tr></thead><tbody><?php foreach($s['stages'] as $stage=>$count): ?><tr><td><?php echo esc_html($stage); ?></td><td><?php echo esc_html(number_format_i18n($count)); ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div></div>
    <div class="skcc-panel" style="margin-top:18px"><h2>Latest Leads</h2><table class="widefat striped"><thead><tr><th>Lead</th><th>Created</th><th>Heat</th><th>Score</th><th>Source</th><th>Channel</th><th>Plan</th><th>MRR</th><th>Stage</th></tr></thead><tbody>
    <?php if(!$s['recent']): ?><tr><td colspan="9">No assessment leads yet.</td></tr><?php else: foreach($s['recent'] as $lead): ?><tr><td><a href="<?php echo esc_url(get_edit_post_link($lead['id'])); ?>"><strong><?php echo esc_html($lead['title'] ?: '#'.$lead['id']); ?></strong></a></td><td><?php echo esc_html($lead['date']); ?></td><td class="<?php echo 'HOT'===$lead['temperature']?'skcc-hot':''; ?>"><?php echo esc_html($lead['temperature']); ?></td><td><?php echo esc_html(number_format_i18n($lead['score'])); ?></td><td><?php echo esc_html($lead['source']); ?></td><td><?php echo esc_html($lead['channel'] ?: '—'); ?></td><td><?php echo esc_html($lead['plan'] ?: '—'); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($lead['mrr'])); ?></td><td><?php echo esc_html($lead['stage']); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div>
    <p class="description" style="margin-top:14px">Commercial values use the exact persisted catalogue recommendation where available. Acquisition uses the lead attribution already captured by the Softkom funnel.</p></div>
    <?php
}
