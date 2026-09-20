<?php
/* Static contract checks for the Softkom Funnel Command Centre. */
$root=dirname(__DIR__);
$file=$root.'/wp-content/mu-plugins/softkom-funnel-command-centre.php';
$build=$root.'/scripts/build-softkom-live-bundle.ps1';
$errors=array();
if(!is_readable($file))$errors[]='command centre plugin missing';
if(!is_readable($build))$errors[]='bundle script missing';
$php=is_readable($file)?file_get_contents($file):'';
$ps=is_readable($build)?file_get_contents($build):'';
$need=array('softkom_funnel_cc_snapshot','_softkom_estimated_mrr','_softkom_implementation_price_from','_softkom_traffic_source','_softkom_acquisition_channel','_softkom_pipeline_stage','softkom_funnel_cc_priority','Priority Opportunities','Contact now','add_menu_page','Sales Pipeline','_softkom_next_follow_up','softkom_funnel_cc_save_pipeline','wp_verify_nonce','edit_post','Overdue','Due today','Active Pipeline Value','Follow-ups Due','softkom_funnel_cc_stage_probability','Weighted Forecast','Revenue Forecast','Won Revenue');
foreach($need as $token){if(false===strpos($php,$token))$errors[]='missing contract: '.$token;}
if(false===strpos($ps,'softkom-funnel-command-centre.php'))$errors[]='production bundle omits command centre';
if($errors){fwrite(STDERR,"Funnel command centre QA FAILED\n - ".implode("\n - ",$errors)."\n");exit(1);}
echo "Funnel command centre QA PASS\n";
