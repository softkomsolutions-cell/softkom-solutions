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
    $response=wp_remote_post('https://api.indexnow.org/indexnow',array('timeout'=>10,'headers'=>array('Content-Type'=>'application/json; charset=utf-8'),'body'=>wp_json_encode($body),'user-agent'=>'SoftkomSolutions-IndexNow/1.2'));
    if(is_wp_error($response)){update_option('softkom_indexnow_last_error',$response->get_error_message(),false);update_option('softkom_indexnow_last_code',0,false);return false;}
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
function softkom_indexnow_queue_token(){
    $token=wp_generate_password(32,false,false);
    update_option('softkom_indexnow_queue_token',array('token'=>$token,'expires'=>time()+600),false);
    return $token;
}
function softkom_indexnow_queue_token_valid($token){
    $stored=get_option('softkom_indexnow_queue_token',array());
    return is_array($stored)&&isset($stored['token'])&&is_string($token)&&!empty($stored['token'])&&hash_equals((string)$stored['token'],$token)&&time()<=(int)$stored['expires'];
}
function softkom_indexnow_handle_queue_submit(){
    if(!current_user_can('manage_options')||!isset($_POST['softkom_indexnow_submit_all']))return '';
    $nonce=isset($_POST['_wpnonce'])?sanitize_text_field(wp_unslash($_POST['_wpnonce'])):'';
    if(!wp_verify_nonce($nonce,'softkom_indexnow_submit_all'))return 'bad-nonce';
    $token=isset($_POST['softkom_indexnow_submit_token'])?sanitize_text_field(wp_unslash($_POST['softkom_indexnow_submit_token'])):'';
    if(!softkom_indexnow_queue_token_valid($token))return 'duplicate';
    $urls=softkom_indexnow_cluster_urls();
    if(!$urls)return 'no-urls';
    wp_schedule_single_event(time()+5,'softkom_indexnow_submit_event',array($urls));
    update_option('softkom_indexnow_last_queued_gmt',current_time('mysql',true),false);
    update_option('softkom_indexnow_last_queued_count',count($urls),false);
    if(function_exists('spawn_cron')){spawn_cron(time());}
    return 'queued';
}
function softkom_indexnow_diagnostics_page(){
    if(!current_user_can('manage_options'))return;
    $result=softkom_indexnow_handle_queue_submit();
    $key=softkom_indexnow_key();$location=softkom_indexnow_key_location();$code=get_option('softkom_indexnow_last_code','Not submitted yet');$when=get_option('softkom_indexnow_last_submit_gmt','Not submitted yet');$queued=get_option('softkom_indexnow_last_queued_gmt','');$queued_count=(int)get_option('softkom_indexnow_last_queued_count',0);$urls=get_option('softkom_indexnow_last_urls',array());$error=get_option('softkom_indexnow_last_error','');
    echo '<div class="wrap"><h1>Softkom IndexNow</h1>';
    if('queued'===$result)echo '<div class="notice notice-success is-dismissible"><p>All '.esc_html($queued_count).' acquisition URLs queued for background submission. Refresh this page in about a minute to see the response.</p></div>';
    elseif('duplicate'===$result)echo '<div class="notice notice-warning is-dismissible"><p>This submission was already queued; it was not submitted twice. Open this page fresh to queue again.</p></div>';
    elseif('bad-nonce'===$result)echo '<div class="notice notice-error is-dismissible"><p>Security check failed. Please open the page fresh and try again.</p></div>';
    elseif('no-urls'===$result)echo '<div class="notice notice-error is-dismissible"><p>No published acquisition URLs were found to queue.</p></div>';
    echo '<table class="widefat striped" style="max-width:1000px"><tbody>';
    echo '<tr><th style="width:220px">Verification key</th><td><code>'.esc_html($key).'</code></td></tr><tr><th>Verification URL</th><td><a href="'.esc_url($location).'" target="_blank" rel="noopener">'.esc_html($location).'</a></td></tr><tr><th>Last HTTP response</th><td><strong>'.esc_html((string)$code).'</strong> <span style="color:#64748b">(200 or 202 = accepted)</span></td></tr><tr><th>Last submission (GMT)</th><td>'.esc_html((string)$when).'</td></tr><tr><th>Last queued (GMT)</th><td>'.esc_html((string)$queued).'</td></tr><tr><th>Last queued count</th><td>'.esc_html((string)$queued_count).'</td></tr><tr><th>Last URL count</th><td>'.esc_html((string)count((array)$urls)).'</td></tr>';if($error)echo '<tr><th>Last error</th><td style="color:#b91c1c">'.esc_html((string)$error).'</td></tr>';echo '</tbody></table>';
    echo '<form method="post" action="" style="margin-top:20px"><input type="hidden" name="softkom_indexnow_submit_all" value="1">';wp_nonce_field('softkom_indexnow_submit_all');echo '<input type="hidden" name="softkom_indexnow_submit_token" value="'.esc_attr(softkom_indexnow_queue_token()).'">';submit_button('Queue All 13 Acquisition URLs','primary','submit',false);echo '</form><p>This Tools-page action verifies permissions and a CSRF nonce, then only queues a background submission. No long-running outbound IndexNow HTTP request happens inside the browser request, and refreshing cannot double-submit.</p></div>';
}
