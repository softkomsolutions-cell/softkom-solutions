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
