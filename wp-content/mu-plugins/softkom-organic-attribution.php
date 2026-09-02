<?php
/**
 * Plugin Name: Softkom Organic & AI Attribution
 * Description: Persists organic, AI-search and campaign acquisition data through the assessment into Softkom leads.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function softkom_attribution_acquisition_slugs(){return array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');}
function softkom_attribution_detect(){
 $source=isset($_GET['utm_source'])?sanitize_key(wp_unslash($_GET['utm_source'])):'';$medium=isset($_GET['utm_medium'])?sanitize_key(wp_unslash($_GET['utm_medium'])):'';$campaign=isset($_GET['utm_campaign'])?sanitize_text_field(wp_unslash($_GET['utm_campaign'])):'';$content=isset($_GET['utm_content'])?sanitize_text_field(wp_unslash($_GET['utm_content'])):'';$term=isset($_GET['utm_term'])?sanitize_text_field(wp_unslash($_GET['utm_term'])):'';$ref=isset($_SERVER['HTTP_REFERER'])?esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])):'';
 if(!$source){$host=strtolower((string)wp_parse_url($ref,PHP_URL_HOST));$map=array('google.'=>array('google','organic'),'bing.'=>array('bing','organic'),'chatgpt.com'=>array('chatgpt','ai-search'),'perplexity.ai'=>array('perplexity','ai-search'),'copilot.microsoft.com'=>array('copilot','ai-search'),'gemini.google.com'=>array('gemini','ai-search'),'claude.ai'=>array('claude','ai-search'),'you.com'=>array('you','ai-search'));foreach($map as $needle=>$pair){if($host&&false!==strpos($host,$needle)){$source=$pair[0];$medium=$pair[1];break;}}}
 if(!$source){$source='direct';$medium='unknown';}
 $request=isset($_SERVER['REQUEST_URI'])?wp_unslash($_SERVER['REQUEST_URI']):'/';
 return array('source'=>$source,'medium'=>$medium?:'unknown','campaign'=>$campaign,'content'=>$content,'term'=>$term,'referrer'=>$ref,'landing'=>esc_url_raw(home_url($request)),'captured_gmt'=>current_time('mysql',true));
}
function softkom_attribution_cookie_name(){return 'softkom_acq';}
function softkom_attribution_encode($data){return rtrim(strtr(base64_encode(wp_json_encode($data)),'+/','-_'),'=');}
function softkom_attribution_decode($value){$value=preg_replace('/[^A-Za-z0-9_-]/','',(string)$value);if(!$value)return array();$pad=strlen($value)%4;if($pad)$value.=str_repeat('=',4-$pad);$raw=base64_decode(strtr($value,'-_','+/'),true);$data=$raw?json_decode($raw,true):array();return is_array($data)?$data:array();}
function softkom_attribution_capture(){
 if(is_admin()||wp_doing_ajax())return;$path=trim((string)wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']??''),PHP_URL_PATH),'/');if(!in_array($path,softkom_attribution_acquisition_slugs(),true))return;
 $data=softkom_attribution_detect();$existing=!empty($_COOKIE[softkom_attribution_cookie_name()])?softkom_attribution_decode(wp_unslash($_COOKIE[softkom_attribution_cookie_name()])):array();
 if(is_array($existing)&&!empty($existing['source'])&&'direct'!==$existing['source'])return;
 $value=softkom_attribution_encode($data);$domain=defined('COOKIE_DOMAIN')?(string)COOKIE_DOMAIN:'';$cookie_path=(defined('COOKIEPATH')&&COOKIEPATH)?COOKIEPATH:'/';setcookie(softkom_attribution_cookie_name(),$value,array('expires'=>time()+DAY_IN_SECONDS*90,'path'=>$cookie_path,'domain'=>$domain,'secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Lax'));$_COOKIE[softkom_attribution_cookie_name()]=$value;
}
add_action('template_redirect','softkom_attribution_capture',1);
function softkom_attribution_read(){return empty($_COOKIE[softkom_attribution_cookie_name()])?array():softkom_attribution_decode(wp_unslash($_COOKIE[softkom_attribution_cookie_name()]));}
function softkom_attribution_persist_lead($lead_id){
 if(!$lead_id||'softkom_lead'!==get_post_type($lead_id))return;$a=softkom_attribution_read();if(!$a)return;
 $map=array('source'=>'_softkom_traffic_source','medium'=>'_softkom_traffic_medium','campaign'=>'_softkom_utm_campaign','content'=>'_softkom_utm_content','term'=>'_softkom_utm_term','referrer'=>'_softkom_referrer','landing'=>'_softkom_landing_page','captured_gmt'=>'_softkom_attribution_captured_gmt');foreach($map as $key=>$meta){if(isset($a[$key])&&''!==$a[$key])update_post_meta($lead_id,$meta,sanitize_text_field((string)$a[$key]));}
 foreach(array('source','medium','campaign','content','term','referrer','landing') as $key){if(!isset($a[$key])||''===$a[$key])continue;$meta='_softkom_first_'.$key;if(''===get_post_meta($lead_id,$meta,true))update_post_meta($lead_id,$meta,sanitize_text_field((string)$a[$key]));}
 $channel=('ai-search'===($a['medium']??''))?'AI Search':(('organic'===($a['medium']??''))?'Organic Search':ucwords(str_replace('-',' ',(string)($a['medium']??'Unknown'))));update_post_meta($lead_id,'_softkom_acquisition_channel',$channel);update_post_meta($lead_id,'_softkom_attribution_persisted_at_gmt',current_time('mysql',true));
}
add_action('softkom_v3_assessment_lead_stored','softkom_attribution_persist_lead',5,1);
