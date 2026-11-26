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
