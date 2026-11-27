<?php
/**
 * Plugin Name: Softkom Partner Programme
 * Description: Private partner applications with moderated intake.
 */
if (!defined('ABSPATH')) exit;
add_action('init', function () {
 register_post_type('softkom_partner', [
 'label'=>'Partner Applications','public'=>false,'show_ui'=>true,
 'show_in_rest'=>false,'supports'=>['title'],
 'capability_type'=>'post','map_meta_cap'=>true,
 'capabilities'=>array_fill_keys(['edit_posts','edit_others_posts','publish_posts','read_private_posts','delete_posts','edit_post','read_post','delete_post','create_posts','edit_private_posts','edit_published_posts','delete_private_posts','delete_published_posts','delete_others_posts'],'manage_options')
 ]);
});
function softkom_partner_privacy_ready(){
 $id=(int)get_option('wp_page_for_privacy_policy');
 return $id>0 && get_post_status($id)==='publish' && (bool)get_privacy_policy_url();
}
function softkom_partner_form_html(){
 if(!softkom_partner_privacy_ready()) return '<p>Applications temporarily unavailable. Please contact Softkom Solutions.</p>';
 ob_start(); ?>
 <section><h2>Become a Softkom Solutions partner</h2>
 <p>Join our referral, sales, specialist delivery or strategic partner network. Applications are reviewed before approval.</p>
 <?php if (isset($_GET['partner_received'])) echo '<p>Thank you. Your application has been received.</p>'; ?>
 <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
 <input type="hidden" name="action" value="softkom_partner_apply">
 <?php wp_nonce_field('softkom_partner_apply'); ?>
 <p><label>Company <input name="company" required maxlength="150"></label></p>
 <p><label>Contact name <input name="contact" required maxlength="150"></label></p>
 <p><label>Business email <input type="email" name="email" required maxlength="254"></label></p>
 <p><label>Partner type <select name="kind"><option value="referral">Referral</option><option value="sales">Sales</option><option value="delivery">Delivery</option><option value="strategic">Strategic</option></select></label></p>
 <p><label>Capabilities and experience <textarea name="experience" required maxlength="2000"></textarea></label></p>
 <p hidden><label>Leave blank <input name="website_check" tabindex="-1" autocomplete="off"></label></p>
 <p><label><input type="checkbox" name="consent" value="1" required> I agree to be contacted about this application and have read the <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">privacy policy</a>.</label></p>
 <button type="submit">Apply to partner</button></form></section>
 <?php return ob_get_clean();
}
add_shortcode('softkom_partner_application',function(){return '<a href="'.esc_url(admin_url('admin-post.php?action=softkom_partner_form')).'">Apply to become a Softkom partner</a>';});
function softkom_partner_form_route(){
 if(!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
 nocache_headers();
 header('Cache-Control: private, no-store, max-age=0',true);
 header('Content-Type: text/html; charset=UTF-8',true);
 if(($_SERVER['REQUEST_METHOD'] ?? '')!=='GET'){status_header(405);exit;}
 if(!softkom_partner_privacy_ready()) status_header(503);
 echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Softkom partner application</title><main>'.softkom_partner_form_html().'</main></html>';exit;
}
add_action('admin_post_softkom_partner_form','softkom_partner_form_route');
add_action('admin_post_nopriv_softkom_partner_form','softkom_partner_form_route');
function softkom_partner_apply() {
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') wp_die('Invalid request', '', ['response'=>405]);
 if(!softkom_partner_privacy_ready()) wp_die('Applications temporarily unavailable.', '', ['response'=>503]);
 check_admin_referer('softkom_partner_apply');
 $read = function($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? wp_unslash($_POST[$key]) : ''; };
 $company=sanitize_text_field($read('company')); $contact=sanitize_text_field($read('contact'));
 $email=sanitize_email($read('email')); $kind=$read('kind'); $experience=sanitize_textarea_field($read('experience'));
 if ($read('website_check') || $read('consent') !== '1' || !$company || !$contact || !is_email($email) || !$experience || !in_array($kind,['referral','sales','delivery','strategic'],true) || strlen($company)>150 || strlen($contact)>150 || strlen($email)>254 || strlen($experience)>2000) wp_die('Invalid application', '', ['response'=>400]);
 $key='sk_partner_'.hash_hmac('sha256',strtolower($email),wp_salt('nonce'));
 if (get_transient($key)) wp_die('Please wait before submitting again.', '', ['response'=>429]);
 $ip=isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
 $ipkey=$ip ? 'sk_partner_ip_'.hash_hmac('sha256',$ip,wp_salt('nonce')) : '';
 if($ipkey && (int)get_transient($ipkey)>=10) wp_die('Please try again later.', '', ['response'=>429]);
 $id=wp_insert_post(['post_type'=>'softkom_partner','post_status'=>'private','post_title'=>$company,'meta_input'=>['contact'=>$contact,'email'=>$email,'partner_kind'=>$kind,'experience'=>$experience,'consent_at'=>current_time('mysql',true),'review_status'=>'pending']],true);
 if (is_wp_error($id) || !$id) wp_die('Unable to save application.', '', ['response'=>500]);
 set_transient($key,1,HOUR_IN_SECONDS);
 if($ipkey) set_transient($ipkey,1+(int)get_transient($ipkey),HOUR_IN_SECONDS);
 wp_mail(get_option('admin_email'),'New Softkom partner application','A new application is available in the WordPress administration area.');
 wp_safe_redirect(add_query_arg('partner_received','1',admin_url('admin-post.php?action=softkom_partner_form')),303); exit;
}
add_action('admin_post_softkom_partner_apply','softkom_partner_apply');
add_action('admin_post_nopriv_softkom_partner_apply','softkom_partner_apply');
add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_partner_details','Application details',function($post){
 if(!current_user_can('manage_options')) return;
 foreach (['contact','email','partner_kind','experience','consent_at','review_status'] as $key) echo '<p><strong>'.esc_html($key).':</strong> '.nl2br(esc_html(get_post_meta($post->ID,$key,true))).'</p>';
 },'softkom_partner');
});

add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_partner_review','Partner review',function($post){
 if (!current_user_can('manage_options')) return;
 wp_nonce_field('sk_partner_review_'.$post->ID,'sk_partner_review_nonce');
 $status=get_post_meta($post->ID,'review_status',true) ?: 'pending';
 echo '<p><label>Decision <select name="sk_partner_status">';
 foreach (['pending','vetting','approved','rejected','suspended'] as $option) echo '<option value="'.esc_attr($option).'" '.selected($status,$option,false).'>'.esc_html(ucfirst($option)).'</option>';
 echo '</select></label></p>';
 echo '<p><label>Internal vetting notes<textarea name="sk_partner_notes" rows="5" style="width:100%">'.esc_textarea(get_post_meta($post->ID,'review_notes',true)).'</textarea></label></p>';
 echo '<p><label><input type="checkbox" name="sk_partner_verified" value="1" '.checked(get_post_meta($post->ID,'vetting_verified',true),'1',false).'> References, commercial terms and privacy obligations checked</label></p>';
 },'softkom_partner','normal','high');
});
add_action('save_post_softkom_partner',function($id,$post){
 if (wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('manage_options') || !current_user_can('edit_post',$id)) return;
 if (!isset($_POST['sk_partner_review_nonce']) || !is_string($_POST['sk_partner_review_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_partner_review_nonce'])),'sk_partner_review_'.$id)) return;
 $status=isset($_POST['sk_partner_status']) && is_string($_POST['sk_partner_status']) ? sanitize_key(wp_unslash($_POST['sk_partner_status'])) : '';
 if (!in_array($status,['pending','vetting','approved','rejected','suspended'],true)) return;
 $verified=isset($_POST['sk_partner_verified']) && $_POST['sk_partner_verified']==='1';
 if ($status==='approved' && !$verified) $status='vetting';
 $notes=isset($_POST['sk_partner_notes']) && is_string($_POST['sk_partner_notes']) ? sanitize_textarea_field(wp_unslash($_POST['sk_partner_notes'])) : '';
 $old=get_post_meta($id,'review_status',true);
 update_post_meta($id,'review_status',$status);
 update_post_meta($id,'review_notes',$notes);
 update_post_meta($id,'vetting_verified',$verified?'1':'0');
 if ($old!==$status) add_post_meta($id,'review_history',['from'=>$old,'to'=>$status,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
},10,2);

add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_partner_services','Service and commercial terms',function($post){
 if (!current_user_can('manage_options')) return;
 wp_nonce_field('sk_partner_terms_'.$post->ID,'sk_partner_terms_nonce');
 $fields=['service_name'=>'Service / package','service_scope'=>'Scope and exclusions','wholesale_cents'=>'Wholesale price in ZAR cents, excluding VAT','retail_cents'=>'Proposed retail price in ZAR cents, excluding VAT','billing_period'=>'Billing period (project or monthly)','terms_reference'=>'Signed agreement reference'];
 foreach($fields as $key=>$label) {
 echo '<p><label>'.esc_html($label).'<br><input style="width:100%" name="sk_terms['.esc_attr($key).']" value="'.esc_attr(get_post_meta($post->ID,'sk_'.$key,true)).'"></label></p>';
 }
 $cost=(int)get_post_meta($post->ID,'sk_wholesale_cents',true);
 $price=(int)get_post_meta($post->ID,'sk_retail_cents',true);
 if($price>0) echo '<p>Indicative gross margin: '.esc_html(number_format(100*($price-$cost)/$price,1)).'% (before commissions and overheads).</p>';
 },'softkom_partner');
});
add_action('save_post_softkom_partner',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_partner_terms_nonce'])||!is_string($_POST['sk_partner_terms_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_partner_terms_nonce'])),'sk_partner_terms_'.$id)) return;
 if(!isset($_POST['sk_terms'])||!is_array($_POST['sk_terms'])) return;
 $input=wp_unslash($_POST['sk_terms']); $clean=[];
 foreach(['service_name','service_scope','wholesale_cents','retail_cents','billing_period','terms_reference'] as $key){
 if(!isset($input[$key])||!is_string($input[$key])) return;
 $value=trim($input[$key]);
 if(in_array($key,['wholesale_cents','retail_cents'],true)&&$value!==''&&!preg_match('/^[0-9]{1,9}$/D',$value)) return;
 if($key==='billing_period'&&!in_array($value,['','project','monthly'],true)) return;
 $clean[$key]=sanitize_text_field($value);
 }
 foreach($clean as $key=>$value) update_post_meta($id,'sk_'.$key,$value);
});

add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_partner_commission','Commission policy',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_commission_'.$post->ID,'sk_commission_nonce');
 $basis=get_post_meta($post->ID,'sk_commission_basis',true);
 echo '<p><label>Commission basis <select name="sk_commission_basis">';
 foreach(['none'=>'None','collected_net'=>'Collected net service revenue','realized_margin'=>'Realized project gross profit'] as $key=>$label) echo '<option value="'.esc_attr($key).'" '.selected($basis,$key,false).'>'.esc_html($label).'</option>';
 echo '</select></label></p>';
 echo '<p><label>Rate (%) <input type="number" min="0" max="100" step="0.01" name="sk_commission_rate" value="'.esc_attr(get_post_meta($post->ID,'sk_commission_rate',true)).'"></label></p>';
 echo '<p>Policy only. No payable commission is generated by saving this record. Actual payments require reconciled receipts, refunds, delivery costs and agreement verification.</p>';
 },'softkom_partner');
});
add_action('save_post_softkom_partner',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_commission_nonce'])||!is_string($_POST['sk_commission_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_commission_nonce'])),'sk_commission_'.$id)) return;
 if(!isset($_POST['sk_commission_basis'],$_POST['sk_commission_rate'])||!is_string($_POST['sk_commission_basis'])||!is_string($_POST['sk_commission_rate'])) return;
 $basis=wp_unslash($_POST['sk_commission_basis']); $rate=trim(wp_unslash($_POST['sk_commission_rate']));
 if(!in_array($basis,['none','collected_net','realized_margin'],true)) return;
 if($basis==='none') $rate='0';
 if(!preg_match('/^[0-9]{1,3}(\.[0-9]{1,2})?$/D',$rate)||(float)$rate>100) return;
 $old=[get_post_meta($id,'sk_commission_basis',true),get_post_meta($id,'sk_commission_rate',true)];
 $new=[$basis,number_format((float)$rate,2,'.','')];
 if($old!==$new){
 update_post_meta($id,'sk_commission_basis',$new[0]);
 update_post_meta($id,'sk_commission_rate',$new[1]);
 add_post_meta($id,'sk_commission_history',['old'=>$old,'new'=>$new,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
 }
});

add_action('init',function(){
 register_post_type('softkom_delivery',[
 'label'=>'Partner Projects','public'=>false,'show_ui'=>true,'show_in_rest'=>false,'supports'=>['title'],
 'map_meta_cap'=>true,'capabilities'=>array_fill_keys(['edit_posts','edit_others_posts','publish_posts','read_private_posts','delete_posts','edit_post','read_post','delete_post','create_posts','edit_private_posts','edit_published_posts','delete_private_posts','delete_published_posts','delete_others_posts'],'manage_options')
 ]);
});
add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_delivery_details','Project allocation',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_delivery_'.$post->ID,'sk_delivery_nonce');
 $assigned=(int)get_post_meta($post->ID,'sk_partner_id',true);
 $partners=get_posts(['post_type'=>'softkom_partner','post_status'=>'private','numberposts'=>-1,'meta_query'=>[['key'=>'review_status','value'=>'approved'],['key'=>'partner_kind','value'=>'delivery']]]);
 echo '<p><label>Approved delivery partner <select name="sk_partner_id"><option value="0">Unassigned</option>';
 foreach($partners as $partner) echo '<option value="'.esc_attr($partner->ID).'" '.selected($assigned,$partner->ID,false).'>'.esc_html($partner->post_title).'</option>';
 echo '</select></label></p>';
 if($assigned&&!in_array($assigned,array_map(function($p){return $p->ID;},$partners),true)) echo '<p>Previously assigned partner is no longer eligible. Reassign before proceeding.</p>';
 foreach(['client_reference'=>'Internal client reference','scope'=>'Agreed scope','due_date'=>'Delivery deadline (YYYY-MM-DD)'] as $key=>$label) echo '<p><label>'.esc_html($label).'<br><input style="width:100%" name="sk_delivery['.esc_attr($key).']" value="'.esc_attr(get_post_meta($post->ID,'sk_'.$key,true)).'"></label></p>';
 echo '<p><label>Status <select name="sk_delivery_status">';
 foreach(['draft','allocated','in_progress','quality_review','accepted','cancelled'] as $status) echo '<option value="'.esc_attr($status).'" '.selected(get_post_meta($post->ID,'sk_delivery_status',true),$status,false).'>'.esc_html($status).'</option>';
 echo '</select></label></p>';
 echo '<p><label><input type="checkbox" name="sk_qa" value="1" '.checked(get_post_meta($post->ID,'sk_qa',true),'1',false).'> Quality review and client acceptance documented</label></p>';
 },'softkom_delivery');
});
add_filter('wp_insert_post_data',function($data){
 if(in_array($data['post_type'],['softkom_delivery','softkom_partner'],true)&&in_array($data['post_status'],['publish','future'],true)) $data['post_status']='private';
 return $data;
});
add_action('save_post_softkom_delivery',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_delivery_nonce'])||!is_string($_POST['sk_delivery_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_delivery_nonce'])),'sk_delivery_'.$id)) return;
 if(!isset($_POST['sk_partner_id'],$_POST['sk_delivery_status'],$_POST['sk_delivery'])||!is_string($_POST['sk_partner_id'])||!is_string($_POST['sk_delivery_status'])||!is_array($_POST['sk_delivery'])) return;
 $partner=absint($_POST['sk_partner_id']); $status=sanitize_key(wp_unslash($_POST['sk_delivery_status']));
 if(!in_array($status,['draft','allocated','in_progress','quality_review','accepted','cancelled'],true)) return;
 if($partner&&(get_post_type($partner)!=='softkom_partner'||get_post_status($partner)!=='private'||get_post_meta($partner,'review_status',true)!=='approved'||get_post_meta($partner,'partner_kind',true)!=='delivery')) return;
 if(!$partner&&!in_array($status,['draft','cancelled'],true)) return;
 $qa=isset($_POST['sk_qa'])&&$_POST['sk_qa']==='1';
 if($status==='accepted'&&!$qa) return;
 $fields=wp_unslash($_POST['sk_delivery']);$clean=[];
 foreach(['client_reference','scope','due_date'] as $key){
 if(!isset($fields[$key])||!is_string($fields[$key])) return;
 $clean[$key]=sanitize_text_field($fields[$key]);
 }
 if($clean['due_date']!==''){
 $d=DateTimeImmutable::createFromFormat('!Y-m-d',$clean['due_date']);
 if(!$d||$d->format('Y-m-d')!==$clean['due_date']) return;
 }
 if(!in_array($status,['draft','cancelled'],true)&&(!$clean['client_reference']||!$clean['scope']||!$clean['due_date'])) return;
 $old=[get_post_meta($id,'sk_partner_id',true),get_post_meta($id,'sk_delivery_status',true)];
 foreach($clean as $key=>$value) update_post_meta($id,'sk_'.$key,$value);
 update_post_meta($id,'sk_partner_id',$partner);update_post_meta($id,'sk_delivery_status',$status);update_post_meta($id,'sk_qa',$qa?'1':'0');
 add_post_meta($id,'sk_delivery_history',['previous'=>$old,'partner'=>$partner,'status'=>$status,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
});

add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_finance','Financial reconciliation (ZAR cents, excluding VAT)',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_finance_'.$post->ID,'sk_finance_nonce');
 $fields=['receipts'=>'Reconciled customer receipts','refunds'=>'Customer refunds','delivery_cost'=>'Total incurred delivery cost','commission'=>'Verified commission liability','supplier_paid'=>'Supplier payments','commission_paid'=>'Commission payments','reference'=>'Accounting reconciliation reference'];
 $v=[];
 foreach($fields as $key=>$label){
 $v[$key]=get_post_meta($post->ID,'sk_fin_'.$key,true);
 echo '<p><label>'.esc_html($label).' <input name="sk_fin['.esc_attr($key).']" value="'.esc_attr($v[$key]).'"></label></p>';
 }
 if($v['reference']!==''){
 $net=(int)$v['receipts']-(int)$v['refunds'];
 $contribution=$net-(int)$v['delivery_cost']-(int)$v['commission'];
 echo '<p>Recorded contribution: R '.esc_html(number_format($contribution/100,2)).' before overheads and taxes.</p>';
 echo '<p>Outstanding supplier liability: R '.esc_html(number_format(((int)$v['delivery_cost']-(int)$v['supplier_paid'])/100,2)).'</p>';
 echo '<p>Outstanding commission: R '.esc_html(number_format(((int)$v['commission']-(int)$v['commission_paid'])/100,2)).'</p>';
 }
 echo '<p>Manual reconciliation snapshot only; does not initiate payments or replace accounting records.</p>';
 },'softkom_delivery');
});
add_action('save_post_softkom_delivery',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_finance_nonce'])||!is_string($_POST['sk_finance_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_finance_nonce'])),'sk_finance_'.$id)) return;
 if(!isset($_POST['sk_fin'])||!is_array($_POST['sk_fin'])) return;
 $raw=wp_unslash($_POST['sk_fin']);$v=[];$old=[];
 foreach(['receipts','refunds','delivery_cost','commission','supplier_paid','commission_paid'] as $key){
 if(!isset($raw[$key])||!is_string($raw[$key])||!preg_match('/^[0-9]{1,9}$/D',$raw[$key])) return;
 $v[$key]=(int)$raw[$key];
 }
 if(!isset($raw['reference'])||!is_string($raw['reference'])) return;
 $v['reference']=sanitize_text_field($raw['reference']);
 if(!$v['reference']||$v['refunds']>$v['receipts']||$v['supplier_paid']>$v['delivery_cost']||$v['commission_paid']>$v['commission']) return;
 foreach($v as $key=>$value) $old[$key]=get_post_meta($id,'sk_fin_'.$key,true);
 foreach($v as $key=>$value) update_post_meta($id,'sk_fin_'.$key,$value);
 add_post_meta($id,'sk_fin_history',['previous'=>$old,'current'=>$v,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
});

function softkom_partner_private_dashboard(){
 if(!is_user_logged_in()) return '<p><a href="'.esc_url(wp_login_url(admin_url('admin-post.php?action=softkom_partner_view'))).'">Sign in to view your partner projects</a></p>';
 $uid=get_current_user_id();
 $partners=get_posts(['post_type'=>'softkom_partner','post_status'=>'private','numberposts'=>2,'meta_key'=>'sk_portal_user','meta_value'=>$uid]);
 if(count($partners)!==1 || get_post_meta($partners[0]->ID,'review_status',true)!=='approved' || get_post_meta($partners[0]->ID,'vetting_verified',true)!=='1' || strcasecmp(wp_get_current_user()->user_email,(string)get_post_meta($partners[0]->ID,'email',true))!==0 || array_values(wp_get_current_user()->roles)!==['subscriber']) return '<p>No active partner access. Contact Softkom Solutions.</p>';
 $projects=get_posts(['post_type'=>'softkom_delivery','post_status'=>'private','numberposts'=>100,'meta_key'=>'sk_partner_id','meta_value'=>$partners[0]->ID]);
 $html='<h2>Your allocated projects</h2><p>Showing up to 100 recent projects.</p><ul>';
 foreach($projects as $project) if(get_post_status($project->ID)==='private' && (int)get_post_meta($project->ID,'sk_partner_id',true)===(int)$partners[0]->ID) $html.='<li>Project #'.esc_html($project->ID).' — '.esc_html(get_post_meta($project->ID,'sk_delivery_status',true)).' — Due '.esc_html(get_post_meta($project->ID,'sk_due_date',true)).'</li>';
 return $html.'</ul>';
}
add_shortcode('softkom_partner_dashboard',function(){return '<a href="'.esc_url(admin_url('admin-post.php?action=softkom_partner_view')).'">Open partner dashboard</a>';});
function softkom_partner_view(){
 if(!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
 nocache_headers();
 header('Cache-Control: private, no-store, max-age=0',true);
 header('X-Robots-Tag: noindex, nofollow',true);
 header('Content-Type: text/html; charset=UTF-8',true);
 if(($_SERVER['REQUEST_METHOD'] ?? '')!=='GET'){status_header(405);exit;}
 if(!is_user_logged_in()) status_header(401);
 echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Partner dashboard</title><main>'.softkom_partner_private_dashboard().'</main></html>';exit;
}
add_action('admin_post_softkom_partner_view','softkom_partner_view');
add_action('admin_post_nopriv_softkom_partner_view','softkom_partner_view');
add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_portal_access','Partner portal access',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_portal_'.$post->ID,'sk_portal_nonce');
 echo '<p><label>Verified WordPress subscriber user ID (0 revokes access) <input type="number" min="0" name="sk_portal_user" value="'.esc_attr(get_post_meta($post->ID,'sk_portal_user',true)).'"></label></p>';
 },'softkom_partner');
});
add_action('save_post_softkom_partner',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_portal_nonce'],$_POST['sk_portal_user'])||!is_string($_POST['sk_portal_nonce'])||!is_string($_POST['sk_portal_user'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_portal_nonce'])),'sk_portal_'.$id)) return;
 $uid=absint($_POST['sk_portal_user']);
 if(!$uid){delete_post_meta($id,'sk_portal_user');return;}
 $user=get_userdata($uid);
 if(!$user||array_values($user->roles)!==['subscriber']||get_post_meta($id,'review_status',true)!=='approved'||get_post_meta($id,'vetting_verified',true)!=='1'||strcasecmp($user->user_email,(string)get_post_meta($id,'email',true))!==0) return;
 $other=get_posts(['post_type'=>'softkom_partner','post_status'=>'any','numberposts'=>1,'post__not_in'=>[$id],'meta_key'=>'sk_portal_user','meta_value'=>$uid]);
 if($other) return;
 update_post_meta($id,'sk_portal_user',$uid);
});

add_action('admin_menu',function(){
 add_submenu_page('edit.php?post_type=softkom_partner','Commercial summary','Commercial summary','manage_options','softkom-commercial-summary','softkom_partner_report');
});
function softkom_partner_report(){
 if(!current_user_can('manage_options')) wp_die('Forbidden','',['response'=>403]);
 $counts=array_fill_keys(['pending','vetting','approved','rejected','suspended'],0);
 $projects=0;$reconciled=0;$totals=array_fill_keys(['receipts','refunds','delivery_cost','commission','supplier_paid','commission_paid'],0);
 foreach(['softkom_partner','softkom_delivery'] as $type){
 $page=1;
 do{
 $q=new WP_Query(['post_type'=>$type,'post_status'=>'private','posts_per_page'=>100,'paged'=>$page,'orderby'=>'ID','order'=>'ASC','fields'=>'ids']);
 foreach($q->posts as $id){
 if($type==='softkom_partner'){
 $s=get_post_meta($id,'review_status',true);if(isset($counts[$s])) $counts[$s]++;
 }else{
 $projects++;
 if(!get_post_meta($id,'sk_fin_reference',true)) continue;
 $values=[];foreach($totals as $key=>$unused){$v=get_post_meta($id,'sk_fin_'.$key,true);if(!is_scalar($v)||!preg_match('/^[0-9]{1,9}$/D',(string)$v)) continue 2;$values[$key]=(int)$v;}
 if($values['refunds']>$values['receipts']||$values['supplier_paid']>$values['delivery_cost']||$values['commission_paid']>$values['commission']) continue;
 $reconciled++;foreach($values as $key=>$v) $totals[$key]+=$v;
 }
 }
 $page++;
 }while($page<=$q->max_num_pages);
 }
 echo '<div class="wrap"><h1>Commercial summary</h1><p>Private records only. Financial totals cover manually reconciled projects, not the entire sales pipeline.</p><ul>';
 foreach($counts as $key=>$n) echo '<li>'.esc_html(ucfirst($key)).': '.esc_html((string)$n).'</li>';
 echo '</ul><p>Projects: '.esc_html((string)$projects).' | Reconciled: '.esc_html((string)$reconciled).'</p><ul>';
 $totals['retained_contribution']=$totals['receipts']-$totals['refunds']-$totals['delivery_cost']-$totals['commission'];
 $totals['supplier_outstanding']=$totals['delivery_cost']-$totals['supplier_paid'];
 $totals['commission_outstanding']=$totals['commission']-$totals['commission_paid'];
 foreach($totals as $key=>$v) echo '<li>'.esc_html(ucfirst(str_replace('_',' ',$key))).': '.($reconciled?'R '.esc_html(number_format($v/100,2)):'Not recorded').'</li>';
 echo '</ul><p>Excludes VAT, overheads and taxes. No payments are initiated.</p></div>';
}

add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_customer_access','Customer portal access',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_customer_'.$post->ID,'sk_customer_nonce');
 echo '<p>Verify customer identity and project ownership before assigning access. Zero revokes access.</p><label>Customer subscriber ID <input type="number" min="0" name="sk_customer_user" value="'.esc_attr(get_post_meta($post->ID,'sk_customer_user',true)).'"></label>';
 },'softkom_delivery');
});
add_action('save_post_softkom_delivery',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_customer_nonce'],$_POST['sk_customer_user'])||!is_string($_POST['sk_customer_nonce'])||!is_string($_POST['sk_customer_user'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_customer_nonce'])),'sk_customer_'.$id)) return;
 $raw=wp_unslash($_POST['sk_customer_user']);if(!preg_match('/^[0-9]{1,10}$/D',$raw)) return;
 $uid=(int)$raw;
 if(!$uid){delete_post_meta($id,'sk_customer_user');delete_post_meta($id,'sk_customer_email');return;}
 $user=get_userdata($uid);if(!$user||array_values($user->roles)!==['subscriber']||!is_email($user->user_email)) return;
 update_post_meta($id,'sk_customer_email',$user->user_email);
 update_post_meta($id,'sk_customer_user',$uid);
 add_post_meta($id,'sk_customer_access_history',['user'=>$uid,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
});
add_shortcode('softkom_customer_dashboard',function(){
 return '<a href="'.esc_url(admin_url('admin-post.php?action=softkom_customer_view')).'">Open customer dashboard</a>';
});
function softkom_customer_view(){
 if(!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
 nocache_headers();header('Cache-Control: private, no-store, max-age=0',true);
 header('X-Robots-Tag: noindex, nofollow',true);header('Content-Type: text/html; charset=UTF-8',true);
 if(($_SERVER['REQUEST_METHOD']??'')!=='GET'){status_header(405);exit;}
 $html='';
 if(!is_user_logged_in()){
 status_header(401);$html='<p><a href="'.esc_url(wp_login_url(admin_url('admin-post.php?action=softkom_customer_view'))).'">Sign in</a></p>';
 }else{
 $user=wp_get_current_user();$uid=$user->ID;
 if(array_values($user->roles)!==['subscriber']){status_header(403);$html='<p>No customer access.</p>';}
 else{
 $projects=get_posts(['post_type'=>'softkom_delivery','post_status'=>'private','numberposts'=>100,'meta_key'=>'sk_customer_user','meta_value'=>$uid]);
 $labels=['allocated'=>'Scheduled','in_progress'=>'In progress','quality_review'=>'Quality review','accepted'=>'Completed','cancelled'=>'Cancelled'];
 $html='<p>Showing up to 100 recent projects.</p><ul>';
 foreach($projects as $project){
 $id=$project->ID;
 if(get_post_status($id)!=='private'||(int)get_post_meta($id,'sk_customer_user',true)!==(int)$uid||strcasecmp($user->user_email,(string)get_post_meta($id,'sk_customer_email',true))!==0) continue;
 $status=get_post_meta($id,'sk_delivery_status',true);if(!isset($labels[$status])) continue;
 $html.='<li>Project #'.esc_html((string)$id).' — '.esc_html($labels[$status]).'</li>';
 }
 $html.='</ul>';
 }
 }
 echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Customer dashboard</title><main><h1>Your projects</h1>'.$html.'</main></html>';exit;
}
add_action('admin_post_softkom_customer_view','softkom_customer_view');
add_action('admin_post_nopriv_softkom_customer_view','softkom_customer_view');

add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_crm_link','Sales CRM connection',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_crm_'.$post->ID,'sk_crm_nonce');
 $id=(int)get_post_meta($post->ID,'sk_crm_lead_id',true);
 echo '<p><label>Existing private CRM lead ID (0 unlinks) <input type="number" min="0" name="sk_crm_lead_id" value="'.esc_attr((string)$id).'"></label></p>';
 if($id&&get_post_type($id)==='softkom_lead'&&get_post_status($id)==='private'&&current_user_can('edit_post',$id)){
 echo '<p><a href="'.esc_url(get_edit_post_link($id)).'">Open linked CRM lead</a></p>';
 echo '<p>Pipeline stage: '.esc_html((string)get_post_meta($id,'_softkom_pipeline_stage',true)).'</p>';
 }
 echo '<p>Verify that this is the correct paying customer before linking. This does not change sales stages or create new leads.</p>';
 },'softkom_delivery');
});
add_action('save_post_softkom_delivery',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_crm_nonce'],$_POST['sk_crm_lead_id'])||!is_string($_POST['sk_crm_nonce'])||!is_string($_POST['sk_crm_lead_id'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_crm_nonce'])),'sk_crm_'.$id)) return;
 $raw=wp_unslash($_POST['sk_crm_lead_id']);if(!preg_match('/^[0-9]{1,10}$/D',$raw)) return;
 $lead=(int)$raw;
 if($lead&&(get_post_type($lead)!=='softkom_lead'||get_post_status($lead)!=='private'||!current_user_can('edit_post',$lead))) return;
 $old=(int)get_post_meta($id,'sk_crm_lead_id',true);if($old===$lead) return;
 if($lead) update_post_meta($id,'sk_crm_lead_id',$lead);else delete_post_meta($id,'sk_crm_lead_id');
 add_post_meta($id,'sk_crm_link_history',['from'=>$old,'to'=>$lead,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
});

function softkom_commission_estimate($basis,$rate,$receipts,$refunds,$cost){
 if(!in_array($basis,['none','collected_net','realized_margin'],true)||!is_scalar($rate)||!preg_match('/^[0-9]{1,3}(\.[0-9]{1,2})?$/D',(string)$rate)||(float)$rate>100) return null;
 foreach([$receipts,$refunds,$cost] as $v) if(!is_scalar($v)||!preg_match('/^[0-9]{1,9}$/D',(string)$v)) return null;
 if((int)$refunds>(int)$receipts) return null;
 $net=(int)$receipts-(int)$refunds;
 $base=$basis==='none'?0:($basis==='collected_net'?$net:max(0,$net-(int)$cost));
 return (int)round($base*(int)round((float)$rate*100)/10000);
}
add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_commission_review','Commission reconciliation review',function($post){
 if(!current_user_can('manage_options')) return;
 $id=$post->ID;$partner=(int)get_post_meta($id,'sk_partner_id',true);
 if(!$partner||get_post_type($partner)!=='softkom_partner'||get_post_status($partner)!=='private'||get_post_meta($partner,'review_status',true)!=='approved'||!get_post_meta($id,'sk_fin_reference',true)){
 echo '<p>Not ready: approved partner and reconciled accounting reference required.</p>';return;
 }
 $basis=get_post_meta($partner,'sk_commission_basis',true);$rate=get_post_meta($partner,'sk_commission_rate',true);
 $estimate=softkom_commission_estimate($basis,$rate,get_post_meta($id,'sk_fin_receipts',true),get_post_meta($id,'sk_fin_refunds',true),get_post_meta($id,'sk_fin_delivery_cost',true));
 if($estimate===null){echo '<p>Incomplete or invalid policy or financial data.</p>';return;}
 $recorded=get_post_meta($id,'sk_fin_commission',true);
 if(!preg_match('/^[0-9]{1,9}$/D',(string)$recorded)){echo '<p>Recorded liability missing.</p>';return;}
 echo '<p>Current assigned delivery partner policy: '.esc_html($basis).' at '.esc_html($rate).'%.</p>';
 foreach(['Calculated estimate'=>$estimate,'Recorded liability'=>(int)$recorded,'Difference'=>$estimate-(int)$recorded] as $label=>$amount) echo '<p>'.esc_html($label).': R '.esc_html(number_format($amount/100,2)).'</p>';
 echo '<p>Review against the signed agreement and historical rate before updating the verified liability. This estimate does not accrue or pay commission and does not cover separate referral beneficiaries.</p>';
 },'softkom_delivery');
});

add_action('add_meta_boxes_softkom_delivery',function(){
 add_meta_box('sk_referral_ledger','Referral and sales commission agreement',function($post){
 if(!current_user_can('manage_options')) return;
 wp_nonce_field('sk_referral_'.$post->ID,'sk_referral_nonce');
 $v=get_post_meta($post->ID,'sk_referral_snapshot',true);if(!is_array($v)) $v=[];
 foreach(['partner'=>'Approved referral or sales partner ID','rate'=>'Agreed percentage','agreement'=>'Signed agreement reference'] as $key=>$label){
 echo '<p><label>'.esc_html($label).' <input name="sk_referral['.esc_attr($key).']" value="'.esc_attr($v[$key]??'').'"></label></p>';
 }
 echo '<p><label>Basis <select name="sk_referral[basis]">';
 foreach(['collected_net','realized_margin'] as $basis) echo '<option value="'.esc_attr($basis).'" '.selected($v['basis']??'',$basis,false).'>'.esc_html($basis).'</option>';
 echo '</select></label></p><p>Record a separate beneficiary only after verifying the signed agreement. Leave blank if none. Changes are audited.</p>';
 if($v&&get_post_meta($post->ID,'sk_fin_reference',true)){
 $estimate=softkom_commission_estimate($v['basis'],$v['rate'],get_post_meta($post->ID,'sk_fin_receipts',true),get_post_meta($post->ID,'sk_fin_refunds',true),get_post_meta($post->ID,'sk_fin_delivery_cost',true));
 if($estimate!==null) echo '<p>Referral estimate: R '.esc_html(number_format($estimate/100,2)).'. Included in total verified commission only after accounting review; not an additional automatic liability.</p>';
 }
 },'softkom_delivery');
});
add_action('save_post_softkom_delivery',function($id){
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('manage_options')||!current_user_can('edit_post',$id)) return;
 if(!isset($_POST['sk_referral_nonce'],$_POST['sk_referral'])||!is_string($_POST['sk_referral_nonce'])||!is_array($_POST['sk_referral'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sk_referral_nonce'])),'sk_referral_'.$id)) return;
 $raw=wp_unslash($_POST['sk_referral']);foreach(['partner','rate','agreement','basis'] as $key) if(!isset($raw[$key])||!is_string($raw[$key])) return;
 if(!preg_match('/^[1-9][0-9]{0,9}$/D',$raw['partner'])) return;
 $partner=(int)$raw['partner'];
 if(get_post_type($partner)!=='softkom_partner'||get_post_status($partner)!=='private'||get_post_meta($partner,'review_status',true)!=='approved'||get_post_meta($partner,'vetting_verified',true)!=='1'||!in_array(get_post_meta($partner,'partner_kind',true),['referral','sales','strategic'],true)) return;
 if(!in_array($raw['basis'],['collected_net','realized_margin'],true)||softkom_commission_estimate($raw['basis'],$raw['rate'],0,0,0)===null) return;
 $agreement=sanitize_text_field($raw['agreement']);if(!$agreement||strlen($agreement)>200) return;
 $new=['partner'=>$partner,'rate'=>$raw['rate'],'basis'=>$raw['basis'],'agreement'=>$agreement];
 $old=get_post_meta($id,'sk_referral_snapshot',true);if($old===$new) return;
 if((int)get_post_meta($id,'sk_fin_commission_paid',true)>0) return;
 update_post_meta($id,'sk_referral_snapshot',$new);
 add_post_meta($id,'sk_referral_history',['previous'=>$old,'current'=>$new,'reviewer'=>get_current_user_id(),'at'=>current_time('mysql',true)]);
});
