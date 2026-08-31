<?php
/**
 * Plugin Name: Softkom Organic & AI Attribution
 * Description: Persists first-touch organic and AI-search acquisition data through the assessment into Softkom leads.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_attribution_detect() {
    $source = isset( $_GET['utm_source'] ) ? sanitize_key( wp_unslash( $_GET['utm_source'] ) ) : '';
    $medium = isset( $_GET['utm_medium'] ) ? sanitize_key( wp_unslash( $_GET['utm_medium'] ) ) : '';
    $campaign = isset( $_GET['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_campaign'] ) ) : '';
    $content = isset( $_GET['utm_content'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_content'] ) ) : '';
    $term = isset( $_GET['utm_term'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_term'] ) ) : '';
    $ref = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
    if ( ! $source ) {
        $host = strtolower( (string) wp_parse_url( $ref, PHP_URL_HOST ) );
        $map = array(
            'google.' => array('google','organic'), 'bing.' => array('bing','organic'),
            'chatgpt.com' => array('chatgpt','ai-search'), 'perplexity.ai' => array('perplexity','ai-search'),
            'copilot.microsoft.com' => array('copilot','ai-search'), 'gemini.google.com' => array('gemini','ai-search'),
            'claude.ai' => array('claude','ai-search'), 'you.com' => array('you','ai-search'),
        );
        foreach ( $map as $needle => $pair ) {
            if ( $host && false !== strpos( $host, $needle ) ) { $source=$pair[0]; $medium=$pair[1]; break; }
        }
    }
    if ( ! $source ) { $source='direct'; $medium='unknown'; }
    return array('source'=>$source,'medium'=>$medium ?: 'unknown','campaign'=>$campaign,'content'=>$content,'term'=>$term,'referrer'=>$ref,'landing'=>home_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ),'captured_gmt'=>current_time('mysql',true));
}
function softkom_attribution_cookie_name(){ return 'softkom_acq'; }
function softkom_attribution_capture(){
    if ( is_admin() || wp_doing_ajax() ) return;
    $path=(string)wp_parse_url( wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH );
    $is_target=(bool)preg_match('#/(assessment|ai-automation-south-africa|business-process-automation-south-africa|custom-business-systems-south-africa)/?$#',$path);
    if(!$is_target)return;
    $data=softkom_attribution_detect();
    $existing=isset($_COOKIE[softkom_attribution_cookie_name()]) ? json_decode(base64_decode(sanitize_text_field(wp_unslash($_COOKIE[softkom_attribution_cookie_name()]))),true) : array();
    /* Preserve meaningful first touch; upgrade direct/unknown when a real source arrives. */
    if(is_array($existing)&&!empty($existing['source'])&&'direct'!==$existing['source']) return;
    $value=base64_encode(wp_json_encode($data));
    setcookie(softkom_attribution_cookie_name(),$value,array('expires'=>time()+DAY_IN_SECONDS*90,'path'=>COOKIEPATH ?: '/','domain'=>COOKIE_DOMAIN,'secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Lax'));
    $_COOKIE[softkom_attribution_cookie_name()]=$value;
}
add_action('template_redirect','softkom_attribution_capture',1);
function softkom_attribution_read(){
    if(empty($_COOKIE[softkom_attribution_cookie_name()]))return array();
    $raw=base64_decode(sanitize_text_field(wp_unslash($_COOKIE[softkom_attribution_cookie_name()])),true);
    $data=$raw ? json_decode($raw,true) : array();
    return is_array($data)?$data:array();
}
function softkom_attribution_persist_lead($lead_id){
    if(!$lead_id||'softkom_lead'!==get_post_type($lead_id))return;
    $a=softkom_attribution_read(); if(!$a)return;
    $map=array('source'=>'_softkom_traffic_source','medium'=>'_softkom_traffic_medium','campaign'=>'_softkom_utm_campaign','content'=>'_softkom_utm_content','term'=>'_softkom_utm_term','referrer'=>'_softkom_referrer','landing'=>'_softkom_landing_page','captured_gmt'=>'_softkom_attribution_captured_gmt');
    foreach($map as $key=>$meta){if(isset($a[$key])&&''!==$a[$key])update_post_meta($lead_id,$meta,sanitize_text_field((string)$a[$key]));}
    $channel=('ai-search'===($a['medium']??''))?'AI Search':(('organic'===($a['medium']??''))?'Organic Search':ucwords(str_replace('-',' ',(string)($a['medium']??'Unknown'))));
    update_post_meta($lead_id,'_softkom_acquisition_channel',$channel);
    update_post_meta($lead_id,'_softkom_attribution_persisted_at_gmt',current_time('mysql',true));
}
add_action('softkom_v3_assessment_lead_stored','softkom_attribution_persist_lead',5,1);
