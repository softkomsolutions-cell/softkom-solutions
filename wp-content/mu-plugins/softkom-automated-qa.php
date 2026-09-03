<?php
/**
 * Plugin Name: Softkom Automated QA
 * Description: Scheduled, admin-visible, read-only QA for the live acquisition funnel and commercial pipeline.
 */
if (!defined('ABSPATH')) { exit; }

function softkom_qa_result($checks,$started){
    $passed=0;$failed=0;foreach($checks as $c){!empty($c['ok'])?$passed++:$failed++;}
    return array('ran_gmt'=>current_time('mysql',true),'started_gmt'=>$started,'passed'=>$passed,'failed'=>$failed,'checks'=>$checks,'status'=>$failed?'FAIL':'PASS');
}
function softkom_qa_check(&$checks,$ok,$label,$detail=''){$checks[]=array('ok'=>(bool)$ok,'label'=>$label,'detail'=>$detail);}

function softkom_qa_run(){
    $started=current_time('mysql',true);$checks=array();
    try{
        softkom_qa_check($checks,function_exists('softkom_v3_store_assessment_lead'),'Assessment lead-storage handler loaded');
        softkom_qa_check($checks,function_exists('softkom_v3_auto_pipeline_hot_lead'),'HOT lead auto-pipeline handler loaded');
        softkom_qa_check($checks,function_exists('softkom_attribution_persist_lead'),'Acquisition attribution persistence loaded');
        softkom_qa_check($checks,function_exists('softkom_indexnow_submit_urls'),'IndexNow submission handler loaded');
        softkom_qa_check($checks,function_exists('softkom_v3_recurring_estimated_mrr'),'Commercial MRR helper loaded');
        softkom_qa_check($checks,has_action('softkom_v3_assessment_lead_stored','softkom_attribution_persist_lead')!==false,'Attribution hook attached to lead storage');
        softkom_qa_check($checks,has_action('softkom_v3_assessment_lead_stored','softkom_public_acquisition_fast_track')!==false,'Public acquisition fast-track hook attached');
        softkom_qa_check($checks,has_action('softkom_v3_assessment_lead_stored','softkom_public_acquisition_record_submission')!==false,'Assessment activity hook attached');
        $slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');
        foreach($slugs as $slug){$p=get_page_by_path($slug,OBJECT,'page');softkom_qa_check($checks,$p&&'publish'===$p->post_status,'Published /'.$slug.'/');}
        softkom_qa_check($checks,(bool)get_option('blog_public'),'Search engine visibility enabled');
        if(function_exists('softkom_search_money_slugs')){
            $search_slugs=softkom_search_money_slugs();
            softkom_qa_check($checks,count(array_intersect($slugs,(array)$search_slugs))===count($slugs),'Search discovery cluster contains all 9 pages');
        }else softkom_qa_check($checks,false,'Search discovery cluster loaded');
        if(function_exists('softkom_v3_recurring_estimated_mrr')){
            $exact=(float)softkom_v3_recurring_estimated_mrr(array('commercial_monthly'=>15000,'suggested_mrr_min'=>6000,'suggested_mrr_max'=>15000));
            $fallback=(float)softkom_v3_recurring_estimated_mrr(array('commercial_monthly'=>0,'suggested_mrr_min'=>6000,'suggested_mrr_max'=>15000));
            softkom_qa_check($checks,15000.0===$exact,'Exact commercial monthly price remains authoritative',(string)$exact);
            softkom_qa_check($checks,10500.0===$fallback,'Legacy MRR midpoint fallback remains valid',(string)$fallback);
        }
        $idx=(int)get_option('softkom_indexnow_last_code',0);softkom_qa_check($checks,in_array($idx,array(200,202),true),'IndexNow last response accepted',(string)$idx);
        $idx_urls=get_option('softkom_indexnow_last_urls',array());softkom_qa_check($checks,count((array)$idx_urls)===9,'IndexNow last submission contains 9 URLs',(string)count((array)$idx_urls));
        $last_lead_ids=get_posts(array('post_type'=>'softkom_lead','post_status'=>'any','posts_per_page'=>1,'fields'=>'ids','orderby'=>'date','order'=>'DESC'));
        softkom_qa_check($checks,is_array($last_lead_ids),'Lead repository query succeeds');
    }catch(Throwable $e){softkom_qa_check($checks,false,'Unhandled QA exception',$e->getMessage());}
    $out=softkom_qa_result($checks,$started);update_option('softkom_automated_qa_last',$out,false);update_option('softkom_automated_qa_last_status',$out['status'],false);return $out;
}

add_action('softkom_automated_qa_daily','softkom_qa_run');
add_action('init',function(){if(!wp_next_scheduled('softkom_automated_qa_daily'))wp_schedule_event(time()+300,'daily','softkom_automated_qa_daily');},100);
add_action('admin_menu',function(){add_management_page('Softkom Automated QA','Softkom Automated QA','manage_options','softkom-automated-qa','softkom_qa_admin_page');});
add_action('admin_post_softkom_qa_run_now',function(){if(!current_user_can('manage_options'))wp_die('Not permitted.');check_admin_referer('softkom_qa_run_now');$status='error';try{$r=softkom_qa_run();$status=strtolower($r['status']);}catch(Throwable $e){update_option('softkom_automated_qa_admin_error',$e->getMessage(),false);}wp_safe_redirect(add_query_arg(array('page'=>'softkom-automated-qa','qa_run'=>$status),admin_url('tools.php')));exit;});
function softkom_qa_admin_page(){
    if(!current_user_can('manage_options'))return;$r=get_option('softkom_automated_qa_last',array());$admin_error=get_option('softkom_automated_qa_admin_error','');
    echo '<div class="wrap"><h1>Softkom Automated QA</h1><p>Production-safe daily QA. It is read-only: it does not create leads, send emails, alter pipeline records or submit transactions.</p>';
    if($admin_error){echo '<div class="notice notice-error"><p>'.esc_html($admin_error).'</p></div>';delete_option('softkom_automated_qa_admin_error');}
    if($r){$status=esc_html($r['status']);echo '<h2>Status: '.$status.'</h2><p>Last run (GMT): '.esc_html($r['ran_gmt']).' &nbsp; Passed: '.(int)$r['passed'].' &nbsp; Failed: '.(int)$r['failed'].'</p><table class="widefat striped" style="max-width:1100px"><thead><tr><th>Result</th><th>Check</th><th>Detail</th></tr></thead><tbody>';foreach((array)$r['checks'] as $c){echo '<tr><td>'.(!empty($c['ok'])?'PASS':'FAIL').'</td><td>'.esc_html($c['label']).'</td><td>'.esc_html($c['detail']).'</td></tr>';}echo '</tbody></table>';}
    else echo '<p>No automated QA run has completed yet.</p>';
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" style="margin-top:20px"><input type="hidden" name="action" value="softkom_qa_run_now">';wp_nonce_field('softkom_qa_run_now');submit_button('Run Safe QA Now','primary','submit',false);echo '</form><p><strong>Deep transactional QA:</strong> continue using the existing WP-CLI runtime smoke suite before deployment. It creates isolated test records and cleans them up outside the public admin request.</p></div>';
}
