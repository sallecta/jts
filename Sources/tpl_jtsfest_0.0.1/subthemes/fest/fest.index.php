<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
//if (JDEBUG){ jsll::dbgj('params class', get_class($this->params)); } 
//if (JDEBUG){ jsll::dbgj('params methods', get_class_methods($this->params)); } 


$app = Factory::getApplication();
$doc = $app->getDocument();


//$doc->addStyleSheet($cfg['path_c'].'/pure/pure.css');
//$doc->addStyleSheet($cfg['path_c'].'/pure/grids-responsive.css');
//$doc->addStyleSheet($cfg['path_c'].'/jsll/jsll.css');
//$doc->addStyleSheet($cfg['path_c'].'/template.css');
//$doc->addStyleDeclaration("
		//:root {
			//--jsll_back_top: url(\"/templates/jsallecta/subthemes/fest/client/images/back_top.jpg\");
		//}
	//");

//$doc->addStyleSheet($cfg['path_c'].'/'.'joomla_alert.css');
//$doc->addStyleSheet($cfg['path_c'].'/colors/'.$cfg['colorName'].'.css');
//$doc->addStyleSheet($cfg['path_c'].'/bootstrap/bootstrap.css');
//$doc->addStyleSheet($cfg['path_c'].'/template.css');

//$doc->addScript($cfg['path_c'].'/jsll/jsll.js');



if (JDEBUG){ jsll::dbgj('$app ', json_encode(get_class_methods($app->getDocument() )) ); }  

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';


// Color Theme
$paramsColorName = $cfg['colorName'] ;
$assetColorName  = 'theme.' . $paramsColorName;
$css_color_path=$cfg['path_c'].'/'.$cfg['colorName'] . '.css';

// Use a font scheme if set in the template style options
$paramsFontScheme = $this->params->get('useFontScheme', false);
$fontStyles       = '';



// Logo file or site title param
if ( $cfg['logoFile'] and $cfg['brand'] and !$cfg['factoryLogofile'] )
{
	if ( !file_exists(JPATH_ROOT.'/'.$cfg['logoFile']) )
	{
		if (JDEBUG){ jsll::dbgj('file not exists', JPATH_ROOT.'/'.$cfg['logoFile'] ); } 
		$logo = '<img src="' . $cfg['path_c_logo_factory'] . '" alt="' . $app->get('sitename'). '">';
	}
	else
	{
		if (JDEBUG){ jsll::dbgj('file exists', JPATH_ROOT.'/'.$cfg['logoFile'] ); } 
		$logo = '<img src="' . $cfg['logoFile'] . '" alt="' .$app->get('sitename') . '">';
	}
}
elseif ( $cfg['factoryLogofile'] and $cfg['brand'] )
{
	$logo = '<img src="' . $cfg['path_c_logo_factory'] . '" alt="' . $app->get('sitename') . '">';
}
elseif ($cfg['siteTitle'])
{
	$logo = '<span title="' . $sitename . '">' . $app->get('sitename') . '</span>';
}
else
{
	$logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block'], true, 0);
}

$hasClass = '';

if ($this->countModules('sidebar-left', true))
{
	$hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true))
{
	$hasClass .= ' has-sidebar-right';
}

// Container
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

$stickyHeader = $this->params->get('stickyHeader') ? 'position-sticky sticky-top' : '';

// Defer fontawesome for increased performance. Once the page is loaded javascript changes it to a stylesheet.
//$wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
?>
<?php include_once $cfg['path_s'].'/factory.html.php';?>


