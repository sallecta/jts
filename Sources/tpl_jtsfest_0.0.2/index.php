<?php
defined('_JEXEC') or die;


//var_export( $this->params['jts_params']->params->debug_messages_site);

if ($this->params['jts_params']->params->debug_messages_site )
{
	define("JSALLECTA_DEBUG_MESSAGES_SITE", true);
}
else
{
	define("JSALLECTA_DEBUG_MESSAGES_SITE", false);
}


include_once JPATH_THEMES."/[extension_name]/code/site.jsll.php";


$cfg=jsll::params_to_array($this->params['jts_params']);



$cfg['path_s0']=JPATH_THEMES.'/[extension_name]';
$cfg['path_s']=JPATH_THEMES.'/[extension_name]/subthemes/'.$cfg['subtheme'];
$cfg['path_c']='/templates/[extension_name]/subthemes/'.$cfg['subtheme'].'/client';
$cfg['path_c_logo_factory']= $cfg['path_c'].'/images/logo.svg';
$cfg['dir_client_main']=$cfg['path_c'].'/main';

if ( array_key_exists('logoFile',$cfg) )
{
	$normalpath=strstr($cfg['logoFile'], '#', true);
	$cfg['logoFile'] = $normalpath;
	
}



if (JDEBUG){ jsll::dbgj('$cfg', $cfg); } 


if (JDEBUG){ jsll::dbgj('$debug_messages_site', $cfg['debug_messages_site']); } 


$path_include=JPATH_THEMES."/[extension_name]/subthemes/{$cfg['subtheme']}"; 

$includefile=JPATH_THEMES."/[extension_name]/subthemes/{$cfg['subtheme']}/{$cfg['subtheme']}.index.php"; 

if ( !file_exists($includefile))
{
    exit("Jsallecta: {$cfg['subtheme']} SUBTHEME FILE MISSING. Please re/un/install Jsallecta template extension");
}
else
{
	include_once $includefile;
}


