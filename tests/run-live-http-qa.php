<?php
/** Softkom public live HTTP QA. Run: php tests/run-live-http-qa.php */
$base=rtrim(getenv('SOFTKOM_QA_BASE_URL')?:'https://softkomsolutions.com','/');
$pass=0;$fail=0;$transport_failures=0;
function live_check($ok,$label){global $pass,$fail;if($ok){$pass++;echo "[PASS] $label\n";}else{$fail++;echo "[FAIL] $label\n";}}
function live_get($url){
    global $transport_failures;
    $attempts=3;$last=array(0,'',array(),'');
    for($i=1;$i<=$attempts;$i++){
        if(function_exists('curl_init')){
            $ch=curl_init($url);
            curl_setopt_array($ch,array(
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_FOLLOWLOCATION=>true,
                CURLOPT_MAXREDIRS=>5,
                CURLOPT_CONNECTTIMEOUT=>10,
                CURLOPT_TIMEOUT=>30,
                CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/152.0 Safari/537.36 SoftkomQA/2.0',
                CURLOPT_HTTPHEADER=>array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8','Accept-Language: en-ZA,en;q=0.9','Cache-Control: no-cache'),
                CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,
                CURLOPT_ENCODING=>'',
            ));
            $body=curl_exec($ch);
            $code=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);
            $error=curl_error($ch);
            curl_close($ch);
            $last=array($code,$body===false?'':$body,array(),$error);
        }else{
            $ctx=stream_context_create(array('http'=>array('timeout'=>30,'ignore_errors'=>true,'user_agent'=>'Mozilla/5.0 SoftkomQA/2.0','follow_location'=>1,'max_redirects'=>5)));
            $body=@file_get_contents($url,false,$ctx);$headers=$http_response_header??array();$code=0;
            foreach($headers as $h){if(preg_match('#^HTTP/\\S+\\s+(\\d{3})#',$h,$m))$code=(int)$m[1];}
            $last=array($code,$body===false?'':$body,$headers,$body===false?'stream fetch failed':'');
        }
        if($last[0]>=200 && $last[0]<500)return $last;
        if($i<$attempts){sleep($i*2);}
    }
    $transport_failures++;
    fwrite(STDERR,"[TRANSPORT] $url code={$last[0]} error=".($last[3]?:'no response')."\n");
    return $last;
}
$slugs=array('assessment','ai-automation-south-africa','business-process-automation-south-africa','custom-business-systems-south-africa','ai-automation-for-smes-south-africa','replace-spreadsheets-manual-processes-south-africa','sales-lead-generation-automation-south-africa','whatsapp-customer-service-automation-south-africa','ai-readiness-assessment-south-africa','replace-excel-with-custom-software-south-africa','custom-software-vs-spreadsheets','automate-manual-business-processes','business-system-integration-south-africa','automate-data-entry-south-africa','automate-business-reporting-south-africa','automate-approvals-workflows-south-africa','connect-business-software-south-africa','automate-lead-follow-up-south-africa','operations-management-system-south-africa');

list($home_code,$home)=live_get($base.'/');
if($home_code===0){
    fwrite(STDERR,"\nLive HTTP QA stopped: base site is unreachable from the runner. This is a transport/DNS/WAF problem, not 63 independent page failures.\n");
    exit(2);
}
live_check($home_code===200,'Homepage HTTP 200');
live_check(strlen($home)>1000,'Homepage returns substantial HTML');

foreach($slugs as $slug){
    list($code,$body)=live_get($base.'/'.$slug.'/');
    live_check($code===200,'HTTP 200 /'.$slug.'/');
    live_check(strlen($body)>1000,'Substantial HTML /'.$slug.'/');
    live_check(stripos($body,'noindex')===false,'Indexable HTML /'.$slug.'/');
}
list($code,$sitemap)=live_get($base.'/softkom-sitemap.xml');
live_check($code===200,'Acquisition sitemap HTTP 200');
live_check(substr_count($sitemap,'<loc>')===19,'Live sitemap contains exactly 19 URLs');
foreach($slugs as $slug)live_check(strpos($sitemap,'/'.$slug.'/')!==false,'Live sitemap covers /'.$slug.'/');
list($code,$robots)=live_get($base.'/robots.txt');
live_check($code===200,'robots.txt HTTP 200');
live_check(strpos($robots,'softkom-sitemap.xml')!==false,'robots.txt advertises acquisition sitemap');

echo "\nLive HTTP QA: $pass passed, $fail failed";
if($transport_failures)echo ", $transport_failures transport failures";
echo ".\n";
exit($fail?1:0);
