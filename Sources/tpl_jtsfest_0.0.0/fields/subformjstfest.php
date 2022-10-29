<?php
defined('_JEXEC') or die;

// https://docs.joomla.org/Advanced_form_guide
// https://docs.joomla.org/Standard_form_field_types

use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\SubformField;
use Joomla\Registry\Registry;

class JFormFieldSubformjtsfest extends SubformField
{
	protected $type = 'subformjtsfest';
	
	private static $instance_counter = 0;
	private static $results = null;
	private static $subtheme_default = 'default';
	private static $subtheme_requested = '';
	private static $subtheme = '';
	private static $subthemes_available = null;
	private static $path_subthemes = JPATH_ROOT. '/templates/jsallecta/subthemes'; 
	private static $path_jsallecta = JPATH_ROOT. '/templates/jsallecta';  
	private static $path_cfg_shared = JPATH_ROOT. '/templates/jsallecta/config_shared.xml';
	
	private static $client_action = '';
	private static $cfg = null; //stdClass Object
	
	private static $dbgj_counter = 0; 

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$me=__FUNCTION__;
		self::$instance_counter=self::$instance_counter+1;
		if ($value)
		{
			self::$cfg= (object)($value['params']);
			if(JDEBUG){self::dbgj($me,__LINE__, 'Reading data. Copying $value[params] to self::$cfg', self::$cfg );}
		}
		if(JDEBUG){self::dbgj($me,__LINE__, 'calling parent::setup', '...' );}
		return parent::setup($element, $value, $group);
	}
	
	public function loadSubForm()
	{
		$me=__FUNCTION__;
		$tmpl=parent::loadSubForm();
		self::_form_field_add_note($tmpl,'note1','CFG_SHARED_HEADER');
		self::_form_load_file($tmpl, self::$path_cfg_shared);
		self::_form_create_subthemes_selector($tmpl);
		self::_form_field_add_note($tmpl,'note2','CFG_SUBTHEME_HEADER');
		if(JDEBUG){self::dbgj($me,__LINE__, 'local: loading subtheme config file', self::$cfg->subtheme );}//$path_subthemes
		
		self::_form_load_file($tmpl, self::$path_subthemes.'/'.self::$cfg->subtheme . '/'.self::$cfg->subtheme.'.jsallecta.xml');
		
		self::language_set();
		return $tmpl;
		
	} //  loadSubForm()
	
	public function filter($value, $group = null, Registry $input = null)
	{
		$me=__FUNCTION__;
		self::$cfg= $value->params;
		if(JDEBUG){self::dbgj($me,__LINE__, 'Saving data. Copying $value->params to self::$cfg, and calling loadSubform() by parent', self::$cfg );}
		$parentfilter = parent::filter($value, $group, $input);
		return $parentfilter;
	}
	
	protected function getInput()
	{
		$me=__FUNCTION__;

		if(JDEBUG){self::dbgj($me,__LINE__, 'Reading data. Calling loadSubform() by parent', self::$cfg );}
		
		return parent::getInput();
		
	}
	
	private static function _form_add_spacer(&$arg_form_reference, $arg_name, $arg_label)
	{
		$me=__FUNCTION__;
		if(JDEBUG){self::dbgj($me,__LINE__, 'me', 'started');}
		$str="<field name='".$arg_name."' type='text' label='".$arg_label."' />";
		$xmlobj = new SimpleXMLElement($str);
		$arg_form_reference->setField($xmlobj, null, true, "advanced");

	}	
	private static function _form_field_add_note(&$arg_form_reference, $arg_name, $arg_label)
	{
		$me=__FUNCTION__;
		$str="<field name='".$arg_name."' type='note' label='".$arg_label."' />";
		$xmlobj = new SimpleXMLElement($str);
		$arg_form_reference->setField($xmlobj, null, true, "advanced");
		if(JDEBUG){self::dbgj($me,__LINE__, 'me', json_encode($xmlobj) );}
	}
		
	private static function _form_load_file(&$arg_form_reference,$arg_name)
	{
		$me=__FUNCTION__;
		$file_path=$arg_name;
		if (!file_exists($file_path))
		{
			if (JDEBUG){ self::dbgj($me,__LINE__, '$file_path not found', $file_path ); }
			return;
		}
		///$xml = simplexml_load_file($file_path);
		
		$arg_form_reference->loadFile($file_path, $reset = true, $xpath = null); //loads as sub sub form i
		if (JDEBUG){ self::dbgj($me,__LINE__, 'loaded', $file_path ); }
	}
	
	private static function _form_create_subthemes_selector(&$arg_form_reference)
	{
		$me=__FUNCTION__;
		self::_getAvailableSubthemes();
		$xmlform=new SimpleXMLElement('<form></form>');
		$xmlfield=$xmlform->addChild('field');
		$xmlfield->addAttribute('name', "subtheme");
		$xmlfield->addAttribute('type', 'list');
		$xmlfield->addAttribute('label', 'CFG_SUBTHEMESELECTOR');
		$xmlfield->addAttribute('description', 'CFG_SUBTHEMESELECTOR_DESCR');
		$xmlfield->addAttribute('onchange', "Joomla.submitbutton('style.apply', null, true)");
		
		//if (JDEBUG){self::dbgj($me,__LINE__, 'adding selector options from self::$subthemes_available)', gettype(self::$subthemes_available) );}
		foreach (self::$subthemes_available as $subtheme_value)
		{
			//if (JDEBUG){self::dbgj($me,__LINE__, '   adding)', $subtheme_value);}
			$xmlOption=$xmlfield->addChild('option',$subtheme_value);
			$xmlOption->addAttribute('value', $subtheme_value);
		}
		
		$xmlhiddenfield = $xmlform->addChild('field');
		$xmlhiddenfield->addAttribute('name', "subthemes");
		$xmlhiddenfield->addAttribute('type', "hidden");
		if (JDEBUG){self::dbgj($me,__LINE__, 'adding subtheme hidden', self::$subthemes_available);}
		$xmlhiddenfield->addAttribute('default', json_encode(self::$subthemes_available) );
		$arg_form_reference->setField($xmlform, null, true, "advanced");
		if (JDEBUG){ self::dbgj($me,__LINE__, 'updated form', json_encode($arg_form_reference-> getXml()) ); } 
		
	}
	
	private static function _getAvailableSubthemes()
	{
		$me=__FUNCTION__;
		if ( self::$instance_counter > 1 ) {return self::$results['_getAvailableSubthemes'];}
		if(JDEBUG){self::dbgj($me,__LINE__, 'self::$cfg->subthemes', self::$cfg->subthemes );}

		$fs_rescan=true;
		if( self::$cfg->subthemes_rescan == false )
		{
			if(JDEBUG){self::dbgj($me,__LINE__, '$cfg->subthemes_rescan  is', 'false' );}
			if ( !self::$cfg->subthemes ) 
			{
				$fs_rescan=true;if(JDEBUG){self::dbgj($me,__LINE__, 'no db subthemes', 'will rescan fs' );}
			}
			if ( self::$cfg->subthemes  ) 
			{
				$fs_rescan=false;
				self::$subthemes_available = json_decode(self::$cfg->subthemes);
				if (JDEBUG){self::dbgj($me,__LINE__, '$self::$subthemes_available (from self::$cfg->subthemes)', self::$subthemes_available);}
			}
		}
		
		if ($fs_rescan)
		{
			$fs_subthemes=array();
			$paths=glob(self::$path_subthemes."/*/*.jsallecta.xml");
			foreach ($paths as $path)
			{
				$namefile = basename("$path", ".jsallecta.xml");
				$namedir = basename(dirname("$path", 1));
				if ($namefile == $namedir) { $fs_subthemes[]=$namedir; } 
			}
			
			self::$subthemes_available = $fs_subthemes;
			if (JDEBUG){self::dbgj($me,__LINE__, '$self::$subthemes_available (from fs_subthemes)', self::$subthemes_available);}
			self::$cfg->subthemes = json_encode(self::$subthemes_available);
		}
		
		if ( !self::$subthemes_available )
		{
			if (JDEBUG){self::dbgj($me,__LINE__, 'no $subthemes_available', self::$subthemes_available);}
			self::msgj(Text::_('JSALLECTA_E_SUBTHEMES'), $arg_msg_type='error', __LINE__);
			self::$results['_getAvailableSubthemes']=false;
			return self::$results['_getAvailableSubthemes'];
		}
		self::$results['_getAvailableSubthemes']=true;
		return self::$results['_getAvailableSubthemes'];
	}
	
	private static function dbg ($arg_caller, $arg_line, $arg_title, $arg_object)
	{
		/*
			Usage template:
			if (JDEBUG){ self::dbg($me,__LINE__, 'title', object); } 
		*/
		if(!JDEBUG){ return; }
		$title="$arg_caller: $arg_line: $arg_title:\n";
		echo "<textarea rows='4'>$title". print_r($arg_object, true) .'</textarea>';
	}
	private static function dbgj ($arg_caller, $arg_line, $arg_title, $arg_object)
	{
		/*
			Usage template:
			if (JDEBUG){ self::dbgj($me,__LINE__, 'title', object); } 
		*/
		if(!JDEBUG){ return; }
		if (!self::$cfg->debug_messages) {return;}
		
		if ( (self::$dbgj_counter)<1 ) { self::$dbgj_counter=1; } else { self::$dbgj_counter = self::$dbgj_counter +1 ;}
		
		$me=basename(__FILE__).': '. __FUNCTION__ .', JDEBUG true';
		$msg="<h5>".self::$dbgj_counter. "<h5><h5>$arg_caller: line: $arg_line. ($me)</h5><h6>$arg_title:</h6>" . "<p>" . print_r($arg_object, true) .'</p>';
		//https://docs.joomla.org/Display_error_messages_and_notices
		Factory::getApplication()->enqueueMessage($msg, 'Notice');//Message, Notice, Warning. Error
	}
	private static function msgj ($arg_msg, $arg_msg_type='notice', $arg_line=null)
	{
		if ($arg_line) { $arg_msg="$arg_msg ($arg_line)";}
		Factory::getApplication()->enqueueMessage($arg_msg, $arg_msg_type);//Message, Notice, Warning. Error
	}
	
	private static function language_set()
	{
		$me=__FUNCTION__;
		
		$lang = \Joomla\CMS\Factory::getApplication()->getLanguage();
		$extension = 'tpl_jsallecta';
		$base_dir =  self::$path_jsallecta.'/config_shared_lang';
		$base_dir_subtheme = self::$path_subthemes.'/'.self::$cfg->subtheme;
		$language_tag = null;//null - for current lang; 'en-GB';
		if(JDEBUG){self::dbgj($me,__LINE__, "loading language from: ", $base_dir);}
		$lang->load($extension, $base_dir, $language_tag, $reload = true, $default = false);
		if(JDEBUG){self::dbgj($me,__LINE__, "loading language from: ", $base_dir_subtheme );}
		$lang->load($extension, $base_dir_subtheme, $language_tag, $reload = true, $default = false);
	} 
}
