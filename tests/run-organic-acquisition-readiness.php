<?php
/**
 * Softkom Organic Acquisition release readiness checks.
 * Run: wp eval-file tests/run-organic-acquisition-readiness.php
 */
if ( ! defined( 'ABSPATH' ) ) { fwrite(STDERR,"Run with WP-CLI.\n"); exit(1); }
$pass=0;$fail=0;
function sk_oa_check($ok,$label){global $pass,$fail;if($ok){$pass++;echo "[PASS] $label\n";}else{$fail++;echo "[FAIL] $label\n";}}
$required=array(
 'softkom-organic-ai-discovery.php','softkom-organic-growth-pages.php','softkom-search-discovery.php','softkom-organic-attribution.php','softkom-public-acquisition.php','softkom-commercial-persistence.php'
);
foreach($required as $f) sk_oa_check(is_readable(WPMU_PLUGIN_DIR.'/'.$f),'MU plugin present: '.$f);
$pages=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa');
foreach($pages as $slug){$p=get_page_by_path($slug,OBJECT,'page');sk_oa_check($p&&'publish'===$p->post_status,'Published page: /'.$slug.'/');}
sk_oa_check(function_exists('softkom_organic_source'),'AI/organic source detector loaded');
sk_oa_check(function_exists('softkom_attribution_detect'),'Persistent attribution detector loaded');
sk_oa_check(has_action('softkom_v3_assessment_lead_stored','softkom_attribution_persist_lead')!==false,'Attribution persistence hooked to lead storage');
sk_oa_check(function_exists('softkom_growth_pages_definition')&&count(softkom_growth_pages_definition())>=3,'Commercial organic page definitions loaded');
sk_oa_check(function_exists('softkom_search_money_slugs')&&in_array('assessment',softkom_search_money_slugs(),true),'Assessment included in search acquisition cluster');
sk_oa_check((bool)get_option('blog_public'),'WordPress search engine visibility enabled');
echo "\nOrganic Acquisition Readiness: $pass passed, $fail failed.\n";
if($fail) exit(1);
