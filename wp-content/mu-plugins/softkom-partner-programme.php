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
 'capabilities'=>['edit_posts'=>'manage_options','edit_others_posts'=>'manage_options','publish_posts'=>'manage_options','read_private_posts'=>'manage_options','delete_posts'=>'manage_options','edit_post'=>'manage_options','read_post'=>'manage_options','delete_post'=>'manage_options']
 ]);
});
add_shortcode('softkom_partner_application', function () {
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
});
function softkom_partner_apply() {
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') wp_die('Invalid request', '', ['response'=>405]);
 check_admin_referer('softkom_partner_apply');
 $read = function($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? wp_unslash($_POST[$key]) : ''; };
 $company=sanitize_text_field($read('company')); $contact=sanitize_text_field($read('contact'));
 $email=sanitize_email($read('email')); $kind=$read('kind'); $experience=sanitize_textarea_field($read('experience'));
 if ($read('website_check') || $read('consent') !== '1' || !$company || !$contact || !is_email($email) || !$experience || !in_array($kind,['referral','sales','delivery','strategic'],true) || strlen($company)>150 || strlen($contact)>150 || strlen($email)>254 || strlen($experience)>2000) wp_die('Invalid application', '', ['response'=>400]);
 $key='sk_partner_'.hash_hmac('sha256',strtolower($email),wp_salt('nonce'));
 if (get_transient($key)) wp_die('Please wait before submitting again.', '', ['response'=>429]);
 $id=wp_insert_post(['post_type'=>'softkom_partner','post_status'=>'private','post_title'=>$company,'meta_input'=>['contact'=>$contact,'email'=>$email,'partner_kind'=>$kind,'experience'=>$experience,'consent_at'=>current_time('mysql',true),'review_status'=>'pending']],true);
 if (is_wp_error($id) || !$id) wp_die('Unable to save application.', '', ['response'=>500]);
 set_transient($key,1,HOUR_IN_SECONDS);
 wp_mail(get_option('admin_email'),'New Softkom partner application','A new application is available in the WordPress administration area.');
 wp_safe_redirect(add_query_arg('partner_received','1',wp_get_referer() ?: home_url('/'))); exit;
}
add_action('admin_post_softkom_partner_apply','softkom_partner_apply');
add_action('admin_post_nopriv_softkom_partner_apply','softkom_partner_apply');
add_action('add_meta_boxes_softkom_partner',function(){
 add_meta_box('sk_partner_details','Application details',function($post){
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
 if($data['post_type']==='softkom_delivery'&&in_array($data['post_status'],['publish','future'],true)) $data['post_status']='private';
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
