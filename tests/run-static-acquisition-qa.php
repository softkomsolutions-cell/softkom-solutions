<?php
/**
 * Standalone static QA for the Softkom acquisition release.
 * Run: php tests/run-static-acquisition-qa.php
 */
$root=dirname(__DIR__);$pass=0;$fail=0;
function qa_check($ok,$label){global $pass,$fail;if($ok){$pass++;echo "[PASS] $label\n";}else{$fail++;echo "[FAIL] $label\n";}}
function qa_read($path){return is_readable($path)?file_get_contents($path):'';}
$slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');
$required=array(
 'wp-content/mu-plugins/softkom-organic-ai-discovery.php',
 'wp-content/mu-plugins/softkom-organic-growth-pages.php',
 'wp-content/mu-plugins/softkom-organic-growth-expansion.php',
 'wp-content/mu-plugins/softkom-search-discovery.php',
 'wp-content/mu-plugins/softkom-organic-attribution.php',
 'wp-content/mu-plugins/softkom-indexnow.php',
 'wp-content/mu-plugins/softkom-public-acquisition.php',
 'wp-content/mu-plugins/softkom-commercial-persistence.php',
 'softkom-sitemap.xml','robots.txt','scripts/build-softkom-live-bundle.ps1'
);
foreach($required as $rel)qa_check(is_readable($root.'/'.$rel),'Required release file: '.$rel);
$search=qa_read($root.'/wp-content/mu-plugins/softkom-search-discovery.php');$attr=qa_read($root.'/wp-content/mu-plugins/softkom-organic-attribution.php');$indexnow=qa_read($root.'/wp-content/mu-plugins/softkom-indexnow.php');$sitemap=qa_read($root.'/softkom-sitemap.xml');$bundle=qa_read($root.'/scripts/build-softkom-live-bundle.ps1');$robots=qa_read($root.'/robots.txt');
foreach($slugs as $slug){qa_check(strpos($search,$slug)!==false,'Search discovery covers /'.$slug.'/');qa_check(strpos($attr,$slug)!==false,'Attribution covers /'.$slug.'/');qa_check(strpos($indexnow,$slug)!==false||strpos($indexnow,'softkom_search_money_slugs')!==false,'IndexNow covers /'.$slug.'/');qa_check(strpos($sitemap,'/'.$slug.'/')!==false,'Physical sitemap covers /'.$slug.'/');}
qa_check(substr_count($sitemap,'<loc>')===9,'Physical sitemap contains exactly 9 acquisition URLs');
qa_check(strpos($robots,'softkom-sitemap.xml')!==false,'robots.txt references acquisition sitemap');
qa_check(strpos($bundle,'softkom-indexnow.php')!==false,'Production bundle includes IndexNow');
qa_check(strpos($bundle,'softkom-organic-growth-expansion.php')!==false,'Production bundle includes growth expansion');
qa_check(strpos($search,'FAQPage')!==false||strpos(qa_read($root.'/wp-content/mu-plugins/softkom-organic-growth-expansion.php'),'FAQPage')!==false,'AI/search FAQ structured data is present');
qa_check(strpos($attr,'ai-search')!==false&&strpos($attr,'chatgpt.com')!==false&&strpos($attr,'perplexity.ai')!==false,'AI-search attribution sources are configured');
qa_check(strpos($indexnow,'api.indexnow.org/indexnow')!==false,'IndexNow endpoint configured');
qa_check(strpos($indexnow,'Queue All 9 Acquisition URLs')!==false&&strpos($indexnow,'softkom_indexnow_submit_all')!==false,'Manual IndexNow recovery control present');
qa_check(strpos($indexnow,'wp_schedule_single_event')!==false&&strpos($indexnow,'spawn_cron')!==false,'Manual IndexNow recovery uses queued background submission');
echo "\nStatic Acquisition QA: $pass passed, $fail failed.\n";exit($fail?1:0);
