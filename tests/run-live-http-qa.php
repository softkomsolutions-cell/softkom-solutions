<?php
/** Softkom public live HTTP QA. Run: php tests/run-live-http-qa.php */
$base=rtrim(getenv('SOFTKOM_QA_BASE_URL')?:'https://softkomsolutions.com','/');$pass=0;$fail=0;
function live_check($ok,$label){global $pass,$fail;if($ok){$pass++;echo "[PASS] $label\n";}else{$fail++;echo "[FAIL] $label\n";}}
function live_get($url){$ctx=stream_context_create(array('http'=>array('timeout'=>20,'ignore_errors'=>true,'user_agent'=>'Softkom-Automated-QA/1.0','follow_location'=>1,'max_redirects'=>5)));$body=@file_get_contents($url,false,$ctx);$headers=$http_response_header??array();$code=0;foreach($headers as $h){if(preg_match('#^HTTP/\S+\s+(\d{3})#',$h,$m))$code=(int)$m[1];}return array($code,$body===false?'':$body,$headers);}
$slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa');
foreach($slugs as $slug){list($code,$body)=live_get($base.'/'.$slug.'/');live_check($code===200,'HTTP 200 /'.$slug.'/');live_check(strlen($body)>1000,'Substantial HTML /'.$slug.'/');live_check(stripos($body,'noindex')===false,'Indexable HTML /'.$slug.'/');}
list($code,$sitemap)=live_get($base.'/softkom-sitemap.xml');live_check($code===200,'Acquisition sitemap HTTP 200');live_check(substr_count($sitemap,'<loc>')===9,'Live sitemap contains exactly 9 URLs');foreach($slugs as $slug)live_check(strpos($sitemap,'/'.$slug.'/')!==false,'Live sitemap covers /'.$slug.'/');
list($code,$robots)=live_get($base.'/robots.txt');live_check($code===200,'robots.txt HTTP 200');live_check(strpos($robots,'softkom-sitemap.xml')!==false,'robots.txt advertises acquisition sitemap');
list($code,$home)=live_get($base.'/');live_check($code===200,'Homepage HTTP 200');live_check(strlen($home)>1000,'Homepage returns substantial HTML');
echo "\nLive HTTP QA: $pass passed, $fail failed.\n";exit($fail?1:0);
