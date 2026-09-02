<?php
/**
 * Plugin Name: Softkom IndexNow
 * Description: Notifies IndexNow when Softkom acquisition pages are published or updated.
 */
if (!defined('ABSPATH')) { exit; }

function softkom_indexnow_slugs(){
    if(function_exists('softkom_search_money_slugs')) return softkom_search_money_slugs();
    return array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');
}
function softkom_indexnow_key(){
    $key=(string)get_option('softkom_indexnow_key','');
    if(!$key){$key=wp_generate_password(32,false,false);update_option('softkom_indexnow_key',$key,false);}
    return preg_replace('/[^A-Za-z0-9\-]/','',$key);
}
function softkom_indexnow_key_location(){return home_url('/'.softkom_indexnow_key().'.txt');}
function softkom_indexnow_cluster_urls(){
    $urls=array();foreach(softkom_indexnow_slugs() as $slug){$page=get_page_by_path($slug,OBJECT,'page');if($page&&'publish'===$page->post_status)$urls[]=get_permalink($page);}return array_values(array_unique(array_filter($urls)));
}
add_action('template_redirect',function(){
    $path=trim((string)wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']??''),PHP_URL_PATH),'/');$key=softkom_indexnow_key();if($path!==$key.'.txt')return;
    status_header(200);nocache_headers();header('Content-Type: text/plain; charset=UTF-8');echo $key;exit;
},-2000);
function softkom_indexnow_submit_urls($urls){
    $urls=array_values(array_unique(array_filter(array_map('esc_url_raw',(array)$urls))));if(!$urls)return false;
    $host=(string)wp_parse_url(home_url('/'),PHP_URL_HOST);if(!$host)return false;
    $body=array('host'=>$host,'key'=>softkom_indexnow_key(),'keyLocation'=>softkom_indexnow_key_location(),'urlList'=>$urls);
    $response=wp_remote_post('https://api.indexnow.org/indexnow',array('timeout'=>10,'headers'=>array('Content-Type'=>'application/json; charset=utf-8'),'body'=>wp_json_encode($body),'user-agent'=>'SoftkomSolutions-IndexNow/1.1'));
    if(is_wp_error($response)){update_option('softkom_indexnow_last_error',$response->get_error_message(),false);return false;}
    $code=(int)wp_remote_retrieve_response_code($response);update_option('softkom_indexnow_last_code',$code,false);update_option('softkom_indexnow_last_submit_gmt',current_time('mysql',true),false);update_option('softkom_indexnow_last_urls',$urls,false);delete_option('softkom_indexnow_last_error');return in_array($code,array(200,202),true);
}
function softkom_indexnow_post_saved($post_id,$post,$update){
    if(wp_is_post_revision($post_id)||wp_is_post_autosave($post_id)||'page'!==$post->post_type||'publish'!==$post->post_status)return;if(!in_array($post->post_name,softkom_indexnow_slugs(),true))return;$url=get_permalink($post_id);if(!$url)return;wp_schedule_single_event(time()+30,'softkom_indexnow_submit_event',array(array($url)));
}
add_action('save_post_page','softkom_indexnow_post_saved',30,3);
add_action('softkom_indexnow_submit_event','softkom_indexnow_submit_urls',10,1);
add_action('init',function(){
    $version='1.0.0';if(get_option('softkom_indexnow_cluster_version')===$version)return;$urls=softkom_indexnow_cluster_urls();if($urls)wp_schedule_single_event(time()+60,'softkom_indexnow_submit_event',array($urls));update_option('softkom_indexnow_cluster_version',$version,false);
},99);

add_action('admin_menu',function(){add_management_page('Softkom IndexNow','Softkom IndexNow','manage_options','softkom-indexnow','softkom_indexnow_diagnostics_page');});
add_action('admin_post_softkom_indexnow_submit_all',function(){
    if(!current_user_can('manage_options'))wp_die('Not permitted.');check_admin_referer('softkom_indexnow_submit_all');$urls=softkom_indexnow_cluster_urls();$ok=softkom_indexnow_submit_urls($urls);$status=$ok?'accepted':'failed';wp_safe_redirect(add_query_arg(array('page'=>'softkom-indexnow','indexnow_submit'=>$status),admin_url('tools.php')));exit;
});
function softkom_indexnow_diagnostics_page(){
    if(!current_user_can('manage_options'))return;$key=softkom_indexnow_key();$location=softkom_indexnow_key_location();$code=get_option('softkom_indexnow_last_code','Not submitted yet');$when=get_option('softkom_indexnow_last_submit_gmt','Not submitted yet');$urls=get_option('softkom_indexnow_last_urls',array());$error=get_option('softkom_indexnow_last_error','');$result=isset($_GET['indexnow_submit'])?sanitize_key(wp_unslash($_GET['indexnow_submit'])):'';
    echo '<div class="wrap"><h1>Softkom IndexNow</h1>';if('accepted'===$result)echo '<div class="notice notice-success is-dismissible"><p>IndexNow accepted the acquisition cluster submission.</p></div>';elseif('failed'===$result)echo '<div class="notice notice-error"><p>IndexNow submission did not return an accepted response. See diagnostics below.</p></div>';
    echo '<table class="widefat striped" style="max-width:1000px"><tbody>';
    echo '<tr><th style="width:220px">Verification key</th><td><code>'.esc_html($key).'</code></td></tr><tr><th>Verification URL</th><td><a href="'.esc_url($location).'" target="_blank" rel="noopener">'.esc_html($location).'</a></td></tr><tr><th>Last HTTP response</th><td><strong>'.esc_html((string)$code).'</strong> <span style="color:#64748b">(200 or 202 = accepted)</span></td></tr><tr><th>Last submission (GMT)</th><td>'.esc_html((string)$when).'</td></tr><tr><th>Last URL count</th><td>'.esc_html((string)count((array)$urls)).'</td></tr>';if($error)echo '<tr><th>Last error</th><td style="color:#b91c1c">'.esc_html((string)$error).'</td></tr>';echo '</tbody></table>';
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" style="margin-top:20px"><input type="hidden" name="action" value="softkom_indexnow_submit_all">';wp_nonce_field('softkom_indexnow_submit_all');submit_button('Submit All 9 Acquisition URLs Now','primary','submit',false);echo '</form><p>This diagnostics screen is visible only to WordPress administrators. IndexNow also continues to run automatically when acquisition pages are published or updated.</p></div>';
}
