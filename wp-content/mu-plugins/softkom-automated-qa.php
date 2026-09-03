<?php
/**
 * Plugin Name: Softkom Automated QA
 * Description: Scheduled, admin-visible QA for the live acquisition funnel and commercial pipeline.
 */
if (!defined('ABSPATH')) { exit; }

function softkom_qa_result($checks,$started){
    $passed=0;$failed=0;foreach($checks as $c){!empty($c['ok'])?$passed++:$failed++;}
    return array('ran_gmt'=>current_time('mysql',true),'started_gmt'=>$started,'passed'=>$passed,'failed'=>$failed,'checks'=>$checks,'status'=>$failed?'FAIL':'PASS');
}
function softkom_qa_check(&$checks,$ok,$label,$detail=''){$checks[]=array('ok'=>(bool)$ok,'label'=>$label,'detail'=>$detail);}
/* WordPress pre_wp_mail passes two arguments; both must be optional for cross-version compatibility. */
function softkom_qa_suppress_mail($return=null,$atts=array()){return true;}

function softkom_qa_run(){
    $started=current_time('mysql',true);$checks=array();$created=array();$mail_filter='softkom_qa_suppress_mail';
    try{
        softkom_qa_check($checks,function_exists('softkom_v3_store_assessment_lead'),'Assessment lead-storage handler loaded');
        softkom_qa_check($checks,function_exists('softkom_v3_auto_pipeline_hot_lead'),'HOT lead auto-pipeline handler loaded');
        softkom_qa_check($checks,function_exists('softkom_attribution_persist_lead'),'Acquisition attribution persistence loaded');
        softkom_qa_check($checks,function_exists('softkom_indexnow_submit_urls'),'IndexNow submission handler loaded');
        $slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');
        foreach($slugs as $slug){$p=get_page_by_path($slug,OBJECT,'page');softkom_qa_check($checks,$p&&'publish'===$p->post_status,'Published /'.$slug.'/');}
        softkom_qa_check($checks,(bool)get_option('blog_public'),'Search engine visibility enabled');
        if(function_exists('softkom_v3_load_data'))softkom_v3_load_data();
        if(function_exists('softkom_v3_store_assessment_lead')){
            add_filter('pre_wp_mail',$mail_filter,999,2);
            $stamp=time();$email='qa-'.$stamp.'@example.com';$campaign='__softkom_auto_qa_'.$stamp.'__';
            $result=array(
                'lead'=>array('first_name'=>'Automated','last_name'=>'QA','email'=>$email,'company'=>'__SOFTKOM_AUTOMATED_QA__'),
                'scores'=>array('maturity'=>30,'ai_opportunity'=>85,'commercial_fit'=>80,'purchase_intent'=>90,'overall_lead'=>82),
                'lead_temperature'=>'HOT','maturity_level'=>array('key'=>'spreadsheet-dependent','title'=>'Spreadsheet Dependent'),
                'priority_opportunities'=>array(array('title'=>'AI Automation','score'=>85)),
                'recommendations'=>array(array('id'=>'managed_automation','title'=>'Managed Automation')),
                'lead_routing'=>array('sales_eligible'=>true),
                'attribution'=>array('utm_source'=>'automated-qa','utm_medium'=>'qa','utm_campaign'=>$campaign)
            );
            $security=array('risk_score'=>0,'risk_level'=>'LOW RISK');
            softkom_v3_store_assessment_lead($result,array('visibility-01'=>1),array('company_size'=>'51-200','urgency'=>'critical'),$security);
            $leads=get_posts(array('post_type'=>'softkom_lead','post_status'=>'any','posts_per_page'=>1,'meta_key'=>'_softkom_email','meta_value'=>$email));
            $lead_id=$leads?(int)$leads[0]->ID:0;
            if($lead_id){$created[]=$lead_id;update_post_meta($lead_id,'_softkom_runtime_test_marker','__SOFTKOM_AUTOMATED_QA__');}
            softkom_qa_check($checks,$lead_id>0,'Synthetic assessment creates a lead');
            if($lead_id){
                if(function_exists('softkom_v3_auto_pipeline_hot_lead'))softkom_v3_auto_pipeline_hot_lead($lead_id,$result,$security);
                softkom_qa_check($checks,'HOT'===get_post_meta($lead_id,'_softkom_lead_temperature',true),'Synthetic lead remains HOT');
                softkom_qa_check($checks,'qualified'===get_post_meta($lead_id,'_softkom_pipeline_stage',true),'Synthetic lead reaches qualified pipeline stage');
                softkom_qa_check($checks,''!==get_post_meta($lead_id,'_softkom_assigned_offer',true),'Commercial offer assigned');
                softkom_qa_check($checks,(float)get_post_meta($lead_id,'_softkom_estimated_mrr',true)>0,'Estimated MRR generated');
                $routing=get_post_meta($lead_id,'_softkom_lead_routing',true);if(is_string($routing))$routing=json_decode($routing,true);
                softkom_qa_check($checks,is_array($routing)&&!empty($routing['sales_eligible']),'Lead routing marks sales eligible');
                softkom_qa_check($checks,'automated-qa'===get_post_meta($lead_id,'_softkom_utm_source',true),'Attribution survives into lead');
            }
            remove_filter('pre_wp_mail',$mail_filter,999);
        }
        $idx=get_option('softkom_indexnow_last_code','');softkom_qa_check($checks,in_array((int)$idx,array(200,202),true),'IndexNow last response accepted',(string)$idx);
        $idx_urls=get_option('softkom_indexnow_last_urls',array());softkom_qa_check($checks,count((array)$idx_urls)===9,'IndexNow last submission contains 9 URLs',(string)count((array)$idx_urls));
    }catch(Throwable $e){softkom_qa_check($checks,false,'Unhandled QA exception',$e->getMessage());}
    finally{
        remove_filter('pre_wp_mail',$mail_filter,999);
        foreach(array_unique($created) as $id){if($id)wp_delete_post($id,true);}
        $orphans=get_posts(array('post_type'=>'softkom_lead','post_status'=>'any','posts_per_page'=>-1,'meta_key'=>'_softkom_runtime_test_marker','meta_value'=>'__SOFTKOM_AUTOMATED_QA__'));
        foreach($orphans as $o)wp_delete_post($o->ID,true);
    }
    $out=softkom_qa_result($checks,$started);update_option('softkom_automated_qa_last',$out,false);update_option('softkom_automated_qa_last_status',$out['status'],false);
    if($out['failed']>0){$admin=sanitize_email(get_option('admin_email'));if(is_email($admin)){wp_mail($admin,'[Softkom QA] Automated QA failed',"Softkom automated QA reported {$out['failed']} failure(s). Open Tools > Softkom Automated QA for details.");}}
    return $out;
}

add_action('softkom_automated_qa_daily','softkom_qa_run');
add_action('init',function(){if(!wp_next_scheduled('softkom_automated_qa_daily'))wp_schedule_event(time()+300,'daily','softkom_automated_qa_daily');},100);
add_action('admin_menu',function(){add_management_page('Softkom Automated QA','Softkom Automated QA','manage_options','softkom-automated-qa','softkom_qa_admin_page');});
add_action('admin_post_softkom_qa_run_now',function(){if(!current_user_can('manage_options'))wp_die('Not permitted.');check_admin_referer('softkom_qa_run_now');$r=softkom_qa_run();wp_safe_redirect(add_query_arg(array('page'=>'softkom-automated-qa','qa_run'=>strtolower($r['status'])),admin_url('tools.php')));exit;});
function softkom_qa_admin_page(){
    if(!current_user_can('manage_options'))return;$r=get_option('softkom_automated_qa_last',array());
    echo '<div class="wrap"><h1>Softkom Automated QA</h1><p>Runs automatically every day and creates then removes an isolated synthetic lead to verify the live assessment → qualification → commercial pipeline path.</p>';
    if($r){$status=esc_html($r['status']);echo '<h2>Status: '.$status.'</h2><p>Last run (GMT): '.esc_html($r['ran_gmt']).' &nbsp; Passed: '.(int)$r['passed'].' &nbsp; Failed: '.(int)$r['failed'].'</p><table class="widefat striped" style="max-width:1100px"><thead><tr><th>Result</th><th>Check</th><th>Detail</th></tr></thead><tbody>';foreach((array)$r['checks'] as $c){echo '<tr><td>'.(!empty($c['ok'])?'PASS':'FAIL').'</td><td>'.esc_html($c['label']).'</td><td>'.esc_html($c['detail']).'</td></tr>';}echo '</tbody></table>';}
    else echo '<p>No automated QA run has completed yet.</p>';
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" style="margin-top:20px"><input type="hidden" name="action" value="softkom_qa_run_now">';wp_nonce_field('softkom_qa_run_now');submit_button('Run Full QA Now','primary','submit',false);echo '</form></div>';
}
