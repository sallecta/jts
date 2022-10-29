<?php
defined('_JEXEC') or die;;
// Subtemplate
// check link: index.php?&tmpl=component
use Joomla\CMS\Language\Text;
$paramsSubtheme = $this->params->get('subthemes', 'cassi');
$includefile=JPATH_THEMES."/jsallecta/subthemes/$paramsSubtheme/component.php";
if ( !file_exists($includefile)) {
    die(Text::_("JSALLECTA_ERR_SUBTEMPL_MISSING"));
}
include_once $includefile;
// end Subtemplate
