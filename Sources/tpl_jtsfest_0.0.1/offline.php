<?php


//var_export( $this->params['jsallecta_params']->params->debug_messages_site);

if ($this->params['jsallecta_params']->params->debug_messages_site )
{
	define("JSALLECTA_DEBUG_MESSAGES_SITE", true);
}
else
{
	define("JSALLECTA_DEBUG_MESSAGES_SITE", false);
}


include_once JPATH_THEMES."/jsallecta/code/site.jsll.php";


$cfg=jsll::params_to_array($this->params['jsallecta_params']);



$cfg['path_s']=JPATH_THEMES.'/jsallecta';
$cfg['path_c']='/templates/jsallecta/subthemes/'.$cfg['subtheme'].'/client';
$cfg['dir_client_main']=$cfg['path_c'].'/main';


if ( array_key_exists('logoFile',$cfg) )
{
	$normalpath=strstr($cfg['logoFile'], '#', true);
	$cfg['logoFile'] = $normalpath;
	
}


if (JDEBUG){ jsll::dbgj('$cfg', $cfg); } 


if (JDEBUG){ jsll::dbgj('$debug_messages_site', $cfg['debug_messages_site']); } 
$includefile=JPATH_THEMES."/jsallecta/subthemes/{$cfg['subtheme']}/{$cfg['subtheme']}.offline.php"; 
if ( !file_exists($includefile))
{
    exit("Jsallecta: {$cfg['subtheme']} SUBTHEME FILE MISSING. Please re/un/install Jsallecta template extension");
}
else
{
	include_once $includefile;
}


