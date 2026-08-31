<?php
/**
 * Softkom commercial recommendation persistence.
 * Persists the concrete catalogue selection and exact pricing used by the funnel.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Exact catalogue monthly price wins; legacy midpoint is only a fallback. */
function softkom_v3_recurring_estimated_mrr( $recommendation ) {
    if ( ! is_array( $recommendation ) ) { return 0.0; }
    $exact = isset( $recommendation['commercial_monthly'] ) ? (float) $recommendation['commercial_monthly'] : 0.0;
    if ( $exact > 0 ) { return $exact; }
    $min = isset( $recommendation['suggested_mrr_min'] ) ? (float) $recommendation['suggested_mrr_min'] : 0.0;
    $max = isset( $recommendation['suggested_mrr_max'] ) ? (float) $recommendation['suggested_mrr_max'] : 0.0;
    if ( $min <= 0 && $max <= 0 ) { return 0.0; }
    if ( $max <= 0 ) { return $min; }
    if ( $min <= 0 ) { return $max; }
    return (float) round( ( $min + $max ) / 2 );
}

function softkom_v3_persist_commercial_recommendation( $lead_id, $recommendation = null ) {
 $lead_id=absint($lead_id);if(!$lead_id||'softkom_lead'!==get_post_type($lead_id))return array();
 if(!is_array($recommendation)){if(!function_exists('softkom_v3_calculate_recurring_recommendation'))return array();$recommendation=softkom_v3_calculate_recurring_recommendation($lead_id);}if(empty($recommendation))return array();
 $service_key=isset($recommendation['service_key'])?sanitize_key($recommendation['service_key']):'';$service_name=isset($recommendation['managed_service'])?sanitize_text_field($recommendation['managed_service']):'';$plan_key=isset($recommendation['commercial_plan_key'])?sanitize_key($recommendation['commercial_plan_key']):'';$plan_name=isset($recommendation['commercial_plan_name'])?sanitize_text_field($recommendation['commercial_plan_name']):'';$implementation_name=isset($recommendation['implementation_offer'])?sanitize_text_field($recommendation['implementation_offer']):'';$implementation_price=isset($recommendation['implementation_price_from'])?max(0,(float)$recommendation['implementation_price_from']):0;$monthly_price=isset($recommendation['commercial_monthly'])?max(0,(float)$recommendation['commercial_monthly']):0;$category=isset($recommendation['commercial_category'])?sanitize_text_field($recommendation['commercial_category']):'';
 $meta=array('_softkom_service_key'=>$service_key,'_softkom_service_name'=>$service_name,'_softkom_commercial_plan_key'=>$plan_key,'_softkom_commercial_plan_name'=>$plan_name,'_softkom_implementation_offer'=>$implementation_name,'_softkom_implementation_price_from'=>$implementation_price,'_softkom_commercial_monthly'=>$monthly_price,'_softkom_commercial_category'=>$category,'_softkom_commercial_persisted_at_gmt'=>current_time('mysql',true));foreach($meta as $key=>$value)update_post_meta($lead_id,$key,$value);$resolved_mrr=softkom_v3_recurring_estimated_mrr($recommendation);if($resolved_mrr>0)update_post_meta($lead_id,'_softkom_estimated_mrr',$resolved_mrr);update_post_meta($lead_id,'_softkom_recurring_recommendation',wp_json_encode($recommendation));return $recommendation;
}
function softkom_v3_persist_commercial_on_lead_stored($lead_id,$result,$security){unset($result,$security);softkom_v3_persist_commercial_recommendation($lead_id);}add_action('softkom_v3_assessment_lead_stored','softkom_v3_persist_commercial_on_lead_stored',30,3);
/* Persist before the legacy manual handler, then restore exact catalogue MRR after that handler writes its midpoint. */
function softkom_v3_persist_commercial_before_manual_apply(){
 $lead_id=isset($_GET['post_id'])?absint($_GET['post_id']):0;if(!$lead_id||'softkom_lead'!==get_post_type($lead_id)||!current_user_can('edit_post',$lead_id))return;if(!isset($_REQUEST['_wpnonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])),'softkom_apply_recurring_'.$lead_id))return;softkom_v3_persist_commercial_recommendation($lead_id);
}
add_action('admin_post_softkom_apply_recurring_recommendation','softkom_v3_persist_commercial_before_manual_apply',5);
function softkom_v3_restore_exact_commercial_mrr_manual_apply(){
 $lead_id=isset($_GET['post_id'])?absint($_GET['post_id']):0;if(!$lead_id||'softkom_lead'!==get_post_type($lead_id)||!current_user_can('edit_post',$lead_id))return;if(!isset($_REQUEST['_wpnonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])),'softkom_apply_recurring_'.$lead_id))return;$recommendation=get_post_meta($lead_id,'_softkom_recurring_recommendation',true);$recommendation=is_string($recommendation)?json_decode($recommendation,true):$recommendation;$resolved=softkom_v3_recurring_estimated_mrr(is_array($recommendation)?$recommendation:array());if($resolved>0)update_post_meta($lead_id,'_softkom_estimated_mrr',$resolved);
}
/* Priority 20 runs after the recurring engine's default priority-10 manual handler in normal execution. */
add_action('admin_post_softkom_apply_recurring_recommendation','softkom_v3_restore_exact_commercial_mrr_manual_apply',20);
