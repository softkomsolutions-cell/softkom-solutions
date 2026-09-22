<?php
/**
 * Standalone static QA for the Softkom acquisition release.
 * Run: php tests/run-static-acquisition-qa.php
 */
$root=dirname(__DIR__);$pass=0;$fail=0;
function qa_check($ok,$label){global $pass,$fail;if($ok){$pass++;echo "[PASS] $label\n";}else{$fail++;echo "[FAIL] $label\n";}}
function qa_read($path){return is_readable($path)?file_get_contents($path):'';}
$slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa','replace-excel-with-custom-software-south-africa','custom-software-vs-spreadsheets','automate-manual-business-processes','business-system-integration-south-africa','automate-data-entry-south-africa','automate-business-reporting-south-africa','automate-approvals-workflows-south-africa','connect-business-software-south-africa','automate-lead-follow-up-south-africa','operations-management-system-south-africa');
$required=array(
 'wp-content/mu-plugins/softkom-organic-ai-discovery.php',
 'wp-content/mu-plugins/softkom-organic-growth-pages.php',
 'wp-content/mu-plugins/softkom-organic-growth-expansion.php',
 'wp-content/mu-plugins/softkom-organic-traffic-sprint.php',
 'wp-content/mu-plugins/softkom-acquisition-reinforcement.php',
 'wp-content/mu-plugins/softkom-search-discovery.php',
 'wp-content/mu-plugins/softkom-organic-attribution.php',
 'wp-content/mu-plugins/softkom-indexnow.php',
 'wp-content/mu-plugins/softkom-public-acquisition.php',
 'wp-content/mu-plugins/softkom-commercial-persistence.php',
 'softkom-sitemap.xml','robots.txt','scripts/build-softkom-live-bundle.ps1'
);
foreach($required as $rel)qa_check(is_readable($root.'/'.$rel),'Required release file: '.$rel);
$search=qa_read($root.'/wp-content/mu-plugins/softkom-search-discovery.php');$attr=qa_read($root.'/wp-content/mu-plugins/softkom-organic-attribution.php');$indexnow=qa_read($root.'/wp-content/mu-plugins/softkom-indexnow.php');$sitemap=qa_read($root.'/softkom-sitemap.xml');$bundle=qa_read($root.'/scripts/build-softkom-live-bundle.ps1');$reinforcement=qa_read($root.'/wp-content/mu-plugins/softkom-acquisition-reinforcement.php');$robots=qa_read($root.'/robots.txt');$growth=qa_read($root.'/wp-content/mu-plugins/softkom-organic-growth-pages.php');$expansion=qa_read($root.'/wp-content/mu-plugins/softkom-organic-growth-expansion.php');
foreach($slugs as $slug){qa_check(strpos($search,$slug)!==false,'Search discovery covers /'.$slug.'/');qa_check(strpos($attr,$slug)!==false,'Attribution covers /'.$slug.'/');qa_check(strpos($indexnow,$slug)!==false||strpos($indexnow,'softkom_search_money_slugs')!==false,'IndexNow covers /'.$slug.'/');qa_check(strpos($sitemap,'/'.$slug.'/')!==false,'Physical sitemap covers /'.$slug.'/');}
qa_check(substr_count($sitemap,'<loc>')===19,'Physical sitemap contains exactly 19 acquisition URLs');
qa_check(strpos($robots,'softkom-sitemap.xml')!==false,'robots.txt references acquisition sitemap');
qa_check(strpos($bundle,'softkom-indexnow.php')!==false,'Production bundle includes IndexNow');
qa_check(strpos($bundle,'softkom-organic-traffic-sprint.php')!==false,'Production bundle includes organic traffic sprint');
qa_check(strpos($bundle,'softkom-organic-growth-expansion.php')!==false,'Production bundle includes growth expansion');
qa_check(strpos($bundle,'softkom-acquisition-reinforcement.php')!==false,'Production bundle includes Sprint 2 acquisition reinforcement');
qa_check(strpos($reinforcement,'automate-data-entry-south-africa')!==false&&strpos($reinforcement,'automate-business-reporting-south-africa')!==false&&strpos($reinforcement,'automate-approvals-workflows-south-africa')!==false&&strpos($reinforcement,'connect-business-software-south-africa')!==false&&strpos($reinforcement,'automate-lead-follow-up-south-africa')!==false&&strpos($reinforcement,'operations-management-system-south-africa')!==false,'Sprint 2 acquisition reinforcement covers all six problem-led pages');
qa_check(strpos($search,"'ai-automation-south-africa'=>array('assessment','ai-automation-for-smes-south-africa','ai-readiness-assessment-south-africa','whatsapp-customer-service-automation-south-africa','automate-manual-business-processes')")!==false,'AI authority page reinforces four weak-discovery acquisition pages');
qa_check(strpos($search,"'business-process-automation-south-africa'=>array('assessment','automate-manual-business-processes','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','replace-spreadsheets-manual-processes-south-africa')")!==false,'BPA authority page reinforces four weak-discovery acquisition pages');
qa_check(strpos($search,"'custom-business-systems-south-africa'=>array('assessment','replace-excel-with-custom-software-south-africa','custom-software-vs-spreadsheets','business-system-integration-south-africa','replace-spreadsheets-manual-processes-south-africa')")!==false,'Custom systems authority page reinforces four weak-discovery acquisition pages');
qa_check(strpos($search,'FAQPage')!==false||strpos(qa_read($root.'/wp-content/mu-plugins/softkom-organic-growth-expansion.php'),'FAQPage')!==false,'AI/search FAQ structured data is present');
qa_check(strpos($attr,'ai-search')!==false&&strpos($attr,'chatgpt.com')!==false&&strpos($attr,'perplexity.ai')!==false,'AI-search attribution sources are configured');
qa_check(strpos($growth,"utm_campaign'=>'buyer-intent'")!==false&&strpos($growth,"utm_campaign'=>'strategy-call'")!==false,'Priority buyer pages preserve conversion attribution');
qa_check(strpos($growth,'No obligation')!==false,'Priority buyer pages include low-friction assessment reassurance');
qa_check(strpos($expansion,"utm_campaign'=>'buyer-intent'")!==false&&strpos($expansion,"utm_campaign'=>'strategy-call'")!==false,'Expansion buyer pages preserve conversion attribution');
qa_check(strpos($indexnow,'api.indexnow.org/indexnow')!==false,'IndexNow endpoint configured');
qa_check(strpos($indexnow,'Queue All 19 Acquisition URLs')!==false&&strpos($indexnow,'softkom_indexnow_submit_all')!==false,'Manual IndexNow recovery control present');
qa_check(strpos($indexnow,'wp_schedule_single_event')!==false&&strpos($indexnow,'spawn_cron')!==false,'Manual IndexNow recovery uses queued background submission');
echo "\nStatic Acquisition QA: $pass passed, $fail failed.\n";exit($fail?1:0);