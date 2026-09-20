<?php
/**
 * Plugin Name: Softkom Funnel Command Centre
 * Description: Sales funnel reporting and lightweight pipeline management for Softkom leads.
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
function softkom_funnel_cc_stages() { return array('New','Contacted','Discovery','Proposal','Negotiation','Won','Lost','Nurture'); }
function softkom_funnel_cc_active_stage( $stage ) { return in_array($stage,array('Contacted','Discovery','Proposal','Negotiation'),true); }
function softkom_funnel_cc_stage_probability( $stage ) {
    $weights=array('Contacted'=>0.10,'Discovery'=>0.25,'Proposal'=>0.50,'Negotiation'=>0.75,'Won'=>1.00);
    return isset($weights[$stage])?$weights[$stage]:0.0;
}
function softkom_funnel_cc_due_state( $date, $stage ) {
    if ( ! $date || in_array($stage,array('Won','Lost'),true) ) { return ''; }
    $today=current_time('Y-m-d');
    if($date<$today)return 'Overdue';
    if($date===$today)return 'Due today';
    return '';
}
function softkom_funnel_cc_add_meta_box() {
    add_meta_box('softkom-sales-pipeline','Sales Pipeline','softkom_funnel_cc_meta_box','softkom_lead','side','high');
}
add_action('add_meta_boxes','softkom_funnel_cc_add_meta_box');
function softkom_funnel_cc_meta_box( $post ) {
    wp_nonce_field('softkom_funnel_cc_save_pipeline','softkom_funnel_cc_nonce');
    $stage=softkom_funnel_cc_text($post->ID,'_softkom_pipeline_stage') ?: 'New';
    $follow=softkom_funnel_cc_text($post->ID,'_softkom_next_follow_up');
    echo '<p><label for="softkom_pipeline_stage"><strong>Stage</strong></label><br><select id="softkom_pipeline_stage" name="softkom_pipeline_stage" style="width:100%">';
    foreach(softkom_funnel_cc_stages() as $option)echo '<option value="'.esc_attr($option).'" '.selected($stage,$option,false).'>'.esc_html($option).'</option>';
    echo '</select></p><p><label for="softkom_next_follow_up"><strong>Next follow-up</strong></label><br><input id="softkom_next_follow_up" name="softkom_next_follow_up" type="date" value="'.esc_attr($follow).'" style="width:100%"></p>';
}
function softkom_funnel_cc_save_pipeline( $post_id ) {
    if(!isset($_POST['softkom_funnel_cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['softkom_funnel_cc_nonce'])),'softkom_funnel_cc_save_pipeline'))return;
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)return;
    if(!current_user_can('edit_post',$post_id) || 'softkom_lead'!==get_post_type($post_id))return;
    $stage=isset($_POST['softkom_pipeline_stage'])?sanitize_text_field(wp_unslash($_POST['softkom_pipeline_stage'])):'New';
    if(!in_array($stage,softkom_funnel_cc_stages(),true))$stage='New';
    update_post_meta($post_id,'_softkom_pipeline_stage',$stage);
    $follow=isset($_POST['softkom_next_follow_up'])?sanitize_text_field(wp_unslash($_POST['softkom_next_follow_up'])):'';
    if($follow && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/',$follow))$follow='';
    if($follow)update_post_meta($post_id,'_softkom_next_follow_up',$follow);else delete_post_meta($post_id,'_softkom_next_follow_up');
}
add_action('save_post_softkom_lead','softkom_funnel_cc_save_pipeline');
function softkom_funnel_cc_is_qualified( $id ) {
    $routing = get_post_meta( $id, '_softkom_lead_routing', true );
    if ( is_string( $routing ) ) { $routing = json_decode( $routing, true ); }
    return is_array( $routing ) && ! empty( $routing['sales_eligible'] );
}
function softkom_funnel_cc_priority( $id, $temperature, $score, $qualified, $mrr, $implementation ) {
    $points = min( 100, max( 0, (float) $score ) );
    if ( 'HOT' === $temperature ) { $points += 35; } elseif ( 'WARM' === $temperature ) { $points += 18; }
    if ( $qualified ) { $points += 20; }
    $points += min( 25, (float) $mrr / 1000 );
    $points += min( 15, (float) $implementation / 10000 );
    return round( $points, 1 );
}
function softkom_funnel_cc_next_action( $temperature, $qualified, $stage ) {
    if ( $stage ) { return 'Progress ' . $stage; }
    if ( 'HOT' === $temperature && $qualified ) { return 'Contact now'; }
    if ( 'WARM' === $temperature && $qualified ) { return 'Follow up today'; }
    if ( $qualified ) { return 'Review and qualify'; }
    return 'Nurture';
}
function softkom_funnel_cc_snapshot() {
    $out = array(
        'leads'=>0,'qualified'=>0,'hot'=>0,'warm'=>0,'pipeline'=>0,
        'estimated_mrr'=>0.0,'implementation_value'=>0.0,'score_total'=>0.0,
        'active_pipeline_value'=>0.0,'weighted_pipeline_value'=>0.0,'won_value'=>0.0,'active_pipeline_mrr'=>0.0,'weighted_pipeline_mrr'=>0.0,'won_mrr'=>0.0,'overdue'=>0,'due_today'=>0,
        'sources'=>array(),'stages'=>array(),'recent'=>array(),'priority'=>array(),
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
        $follow_up=softkom_funnel_cc_text($id,'_softkom_next_follow_up');
        $due_state=softkom_funnel_cc_due_state($follow_up,$stage ?: 'New');
        if('Overdue'===$due_state)$out['overdue']++;
        if('Due today'===$due_state)$out['due_today']++;
        $lead_mrr = (float) get_post_meta( $id, '_softkom_estimated_mrr', true );
        $lead_implementation = (float) get_post_meta( $id, '_softkom_implementation_price_from', true );
        $lead_score = (float) get_post_meta( $id, '_softkom_score_overall_lead', true );
        $out['estimated_mrr'] += $lead_mrr;
        $out['implementation_value'] += $lead_implementation;
        if(softkom_funnel_cc_active_stage($stage)){
            $out['active_pipeline_value'] += $lead_implementation;
            $out['active_pipeline_mrr'] += $lead_mrr;
        }
        $probability=softkom_funnel_cc_stage_probability($stage);
        $out['weighted_pipeline_value'] += $lead_implementation*$probability;
        $out['weighted_pipeline_mrr'] += $lead_mrr*$probability;
        if('Won'===$stage){$out['won_value'] += $lead_implementation;$out['won_mrr'] += $lead_mrr;}
        $out['score_total'] += $lead_score;
        $source = softkom_funnel_cc_text( $id, '_softkom_traffic_source' );
        if ( ! $source ) { $source = 'direct'; }
        if ( ! isset( $out['sources'][$source] ) ) { $out['sources'][$source] = array('leads'=>0,'qualified'=>0,'mrr'=>0.0); }
        $out['sources'][$source]['leads']++;
        if ( $qualified ) { $out['sources'][$source]['qualified']++; }
        $out['sources'][$source]['mrr'] += $lead_mrr;
        $priority = softkom_funnel_cc_priority( $id, $temperature, $lead_score, $qualified, $lead_mrr, $lead_implementation );
        $out['priority'][] = array(
            'id'=>$id,'title'=>get_the_title($id),'temperature'=>$temperature ?: '—','score'=>$lead_score,
            'qualified'=>$qualified,'mrr'=>$lead_mrr,'implementation'=>$lead_implementation,'stage'=>$stage,
            'source'=>$source,'priority'=>$priority,'follow_up'=>$follow_up,'due_state'=>$due_state,'action'=>softkom_funnel_cc_next_action($temperature,$qualified,$stage),
        );
        if ( count( $out['recent'] ) < 12 ) {
            $out['recent'][] = array(
                'id'=>$id, 'title'=>get_the_title($id), 'date'=>get_the_date('Y-m-d H:i',$id),
                'temperature'=>$temperature ?: '—', 'score'=>$lead_score,
                'source'=>$source, 'channel'=>softkom_funnel_cc_text($id,'_softkom_acquisition_channel'),
                'plan'=>softkom_funnel_cc_text($id,'_softkom_commercial_plan_name'),
                'mrr'=>$lead_mrr, 'stage'=>$stage ?: 'New','follow_up'=>$follow_up,'due_state'=>$due_state,
            );
        }
    }
    $out['average_score'] = $out['leads'] ? round($out['score_total']/$out['leads']) : 0;
    $out['qualification_rate'] = $out['leads'] ? round(($out['qualified']/$out['leads'])*100,1) : 0;
    $out['pipeline_rate'] = $out['leads'] ? round(($out['pipeline']/$out['leads'])*100,1) : 0;
    uasort($out['sources'],function($a,$b){ return $b['mrr'] <=> $a['mrr']; });
    usort($out['priority'],function($a,$b){ return $b['priority'] <=> $a['priority']; });
    $out['priority']=array_slice($out['priority'],0,10);
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
        'Active Pipeline Value'=>softkom_funnel_cc_money($s['active_pipeline_value']),
        'Weighted Forecast'=>softkom_funnel_cc_money($s['weighted_pipeline_value']).' + '.softkom_funnel_cc_money($s['weighted_pipeline_mrr']).'/mo',
        'Won Revenue'=>softkom_funnel_cc_money($s['won_value']).' + '.softkom_funnel_cc_money($s['won_mrr']).'/mo',
        'Follow-ups Due'=>$s['due_today'].' today / '.$s['overdue'].' overdue',
        'Average Lead Score'=>number_format_i18n($s['average_score']),
    );
    ?>
    <div class="wrap"><h1>Softkom Funnel Command Centre</h1>
    <p>One view of lead quality, commercial value, acquisition and pipeline. Update stage and follow-up dates from each lead record.</p>
    <style>
    .skcc-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin:18px 0 24px}.skcc-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px}.skcc-label{font-size:11px;text-transform:uppercase;color:#646970;font-weight:700}.skcc-value{font-size:25px;font-weight:700;margin-top:5px}.skcc-panels{display:grid;grid-template-columns:1.4fr 1fr;gap:18px}.skcc-panel{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:18px}.skcc-panel h2{margin-top:0}@media(max-width:900px){.skcc-panels{grid-template-columns:1fr}}.skcc-hot{font-weight:700}.skcc-muted{color:#646970}
    </style>
    <div class="skcc-grid"><?php foreach($cards as $label=>$value): ?><div class="skcc-card"><div class="skcc-label"><?php echo esc_html($label); ?></div><div class="skcc-value"><?php echo esc_html($value); ?></div></div><?php endforeach; ?></div>
    <div class="skcc-panel" style="margin-bottom:18px"><h2>Priority Opportunities</h2><p class="skcc-muted">Automatically ordered using lead score, HOT/WARM status, sales eligibility and commercial value.</p><table class="widefat striped"><thead><tr><th>Priority</th><th>Lead</th><th>Heat</th><th>Score</th><th>Potential</th><th>Source</th><th>Follow-up</th><th>Next Action</th></tr></thead><tbody>
    <?php if(!$s['priority']): ?><tr><td colspan="8">No leads to prioritise yet.</td></tr><?php else: foreach($s['priority'] as $lead): ?><tr><td><strong><?php echo esc_html(number_format_i18n($lead['priority'],1)); ?></strong></td><td><a href="<?php echo esc_url(get_edit_post_link($lead['id'])); ?>"><strong><?php echo esc_html($lead['title'] ?: '#'.$lead['id']); ?></strong></a></td><td><?php echo esc_html($lead['temperature']); ?></td><td><?php echo esc_html(number_format_i18n($lead['score'])); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($lead['implementation']).' + '.softkom_funnel_cc_money($lead['mrr']).'/mo'); ?></td><td><?php echo esc_html($lead['source']); ?></td><td><?php echo esc_html($lead['follow_up'] ?: '—'); ?><?php if($lead['due_state']): ?> <strong>(<?php echo esc_html($lead['due_state']); ?>)</strong><?php endif; ?></td><td><strong><?php echo esc_html($lead['action']); ?></strong></td></tr><?php endforeach; endif; ?>
    </tbody></table></div>
    <div class="skcc-panel" style="margin-bottom:18px"><h2>Revenue Forecast</h2><p class="skcc-muted">Weighted by current sales stage: Contacted 10%, Discovery 25%, Proposal 50%, Negotiation 75%, Won 100%. Lost and Nurture are excluded.</p><table class="widefat striped"><thead><tr><th>Measure</th><th>Implementation</th><th>Recurring MRR</th></tr></thead><tbody><tr><td><strong>Active pipeline</strong></td><td><?php echo esc_html(softkom_funnel_cc_money($s['active_pipeline_value'])); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($s['active_pipeline_mrr'])); ?>/mo</td></tr><tr><td><strong>Weighted forecast</strong></td><td><?php echo esc_html(softkom_funnel_cc_money($s['weighted_pipeline_value'])); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($s['weighted_pipeline_mrr'])); ?>/mo</td></tr><tr><td><strong>Won</strong></td><td><?php echo esc_html(softkom_funnel_cc_money($s['won_value'])); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($s['won_mrr'])); ?>/mo</td></tr></tbody></table></div>
    <div class="skcc-panels"><div class="skcc-panel"><h2>Acquisition → Revenue</h2><table class="widefat striped"><thead><tr><th>Source</th><th>Leads</th><th>Qualified</th><th>Qual. rate</th><th>Estimated MRR</th></tr></thead><tbody>
    <?php if(!$s['sources']): ?><tr><td colspan="5">No attributed leads yet.</td></tr><?php else: foreach($s['sources'] as $source=>$row): $rate=$row['leads']?round(($row['qualified']/$row['leads'])*100,1):0; ?><tr><td><strong><?php echo esc_html(ucwords(str_replace('-',' ',$source))); ?></strong></td><td><?php echo esc_html(number_format_i18n($row['leads'])); ?></td><td><?php echo esc_html(number_format_i18n($row['qualified'])); ?></td><td><?php echo esc_html($rate.'%'); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($row['mrr'])); ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div><div class="skcc-panel"><h2>Pipeline Stages</h2><?php if(!$s['stages']): ?><p class="skcc-muted">No leads have entered a pipeline stage yet.</p><?php else: ?><table class="widefat striped"><thead><tr><th>Stage</th><th>Leads</th></tr></thead><tbody><?php foreach($s['stages'] as $stage=>$count): ?><tr><td><?php echo esc_html($stage); ?></td><td><?php echo esc_html(number_format_i18n($count)); ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div></div>
    <div class="skcc-panel" style="margin-top:18px"><h2>Latest Leads</h2><table class="widefat striped"><thead><tr><th>Lead</th><th>Created</th><th>Heat</th><th>Score</th><th>Source</th><th>Channel</th><th>Plan</th><th>MRR</th><th>Stage</th><th>Follow-up</th></tr></thead><tbody>
    <?php if(!$s['recent']): ?><tr><td colspan="10">No assessment leads yet.</td></tr><?php else: foreach($s['recent'] as $lead): ?><tr><td><a href="<?php echo esc_url(get_edit_post_link($lead['id'])); ?>"><strong><?php echo esc_html($lead['title'] ?: '#'.$lead['id']); ?></strong></a></td><td><?php echo esc_html($lead['date']); ?></td><td class="<?php echo 'HOT'===$lead['temperature']?'skcc-hot':''; ?>"><?php echo esc_html($lead['temperature']); ?></td><td><?php echo esc_html(number_format_i18n($lead['score'])); ?></td><td><?php echo esc_html($lead['source']); ?></td><td><?php echo esc_html($lead['channel'] ?: '—'); ?></td><td><?php echo esc_html($lead['plan'] ?: '—'); ?></td><td><?php echo esc_html(softkom_funnel_cc_money($lead['mrr'])); ?></td><td><?php echo esc_html($lead['stage']); ?></td><td><?php echo esc_html($lead['follow_up'] ?: '—'); ?><?php if($lead['due_state']): ?> <strong>(<?php echo esc_html($lead['due_state']); ?>)</strong><?php endif; ?></td></tr><?php endforeach; endif; ?>
    </tbody></table></div>
    <p class="description" style="margin-top:14px">Commercial values use the exact persisted catalogue recommendation where available. Acquisition uses the lead attribution already captured by the Softkom funnel.</p></div>
    <?php
}
