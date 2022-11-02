<?php
defined('_JEXEC') or die;

//if (!defined('JTSD')) { define('JTSD', true); } //or false
if (!defined('JTSD')) { define('JTSD', false); } //or true

// https://docs.joomla.org/Advanced_form_guide
// https://docs.joomla.org/Standard_form_field_types

require_once( dirname(__DIR__) . '/libs/jts/jts.php' );


use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\SubformField;
use Joomla\Registry\Registry;


use \Joomla\CMS\Table\Table;





class JFormFieldSubformJts extends SubformField
{
	protected $type = 'subformjts';
	
	private static $setup_counter = 0;
	private static $results = null;
	private static $path_extension = null;  
	private static $path_cfg_shared = null;
	private static $dbgj_counter = 0; 
	
	private static $cfg = null;
	

	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		
		if (!parent::setup($element, $value, $group)) { return false; }
		self::$setup_counter=self::$setup_counter+1;
		if ( self::$setup_counter > 1 ) { return true; } //I do not need this code to be called more than 1 time, also no reason to return false to somewhere
		
		$cnt=self::$setup_counter;
		
		if(JTSD){jts::dbgj( $cnt.': setup_started.  self::$setup_counter', self::$setup_counter) ;}
		/*---->>*/
		/****/
		if ( isset($this->element->form) == false )
		{
			if(JTSD){jts::dbgj('isset($this->element->form = false', 'Main field \'jts_params\' has no  <form></form> section in templateDetails.xml','raw') ;}
			if(JTSD){jts::dbgj('Execution stopped', isset($this->element->form) ) ;}
			jts::msgj($arg_msg=Text::_("ERR_XMLDET_NOFORMTAG"), $arg_msg_type='error', $arg_line=__LINE__);
			return false;
		}
		foreach (array('formsource', 'min', 'max', 'layout', 'groupByFieldset', 'buttons') as $attributeName)
		{
			$this->__set($attributeName, $element[$attributeName]);
		}
		if ((string) $element['fieldname'])
		{
			$this->__set('fieldname', $element['fieldname']);
		}
		if (!$this->formsource && $element->form)
		{
			// Set the xml file name that contains sub form definition (default is null)
			$this->formsource = $element->form->asXML();
		}
		/*<-<-*/
		/*-------*/
		if ( !self::$cfg )
		{
			self::_create_dynamic_cfg();
			if(JTSD){jts::dbgj( $cnt.': created_dyn_conf (self::$cfg)', self::$cfg ) ;}
		}
		
		return true;
	}
	
	private function _create_dynamic_cfg()
	{
		self::$cfg['path_extension'] = dirname(dirname(__DIR__));
		self::$cfg['extension_name'] = basename(self::$cfg['path_extension']);  
		self::$cfg['path_subthemes'] = self::$cfg['path_extension'] .'/jts_code/subthemes'; 
		self::$cfg['path_xmlshared'] = self::$cfg['path_extension'] . '/jts_code/config_shared.xml';
	}
	
	private function cfg_get($arg_key)
	{ 	if ( !array_key_exists($arg_key, self::$cfg) )
		{ 
			echo basename(__FILE__) . ": "  . __LINE__ . ": " . __FUNCTION__ . '(): key "'.$arg_key.'" not exists';
			if(JTSD){ print_r("<h1></h1><textarea>".json_encode(debug_backtrace(),JSON_PRETTY_PRINT).'</textarea>'); }
			exit;
		}
		return self::$cfg[$arg_key];
	}
	
	public function loadSubForm() //
	{
		/****/
		$control = $this->name;
		if ($this->multiple)
		{
			$control .= '[' . $this->fieldname . 'X]';
		}
		// Prepare the form template
		$formname = 'subform.' . str_replace(array('jform[', '[', ']'), array('', '.', ''), $this->name);
		$tmpl     = Form::getInstance($formname, $this->formsource, array('control' => $control));
        //return $tmpl;
		/*---*/
		
		self::_form_field_add_note($tmpl,'note1','PHP_MAIN_OPTIONS');
		self::_form_add_rescan($tmpl);
		self::_form_create_subthemes_selector($tmpl);
		self::_form_field_add_note($tmpl,'note2','PHP_SHARED_OPTIONS');
		self::_form_load_from_xml_file($tmpl, self::cfg_get('path_xmlshared'));
		self::_form_field_add_note($tmpl,'note3',Text::sprintf('PHP_SUBTHEME_OPTIONS', ucfirst($this->subtheme) )  );
		if(JTSD){jts::dbgj( '$this->subtheme = ', $this->subtheme )  ;}
		if ($this->subtheme) { self::_form_load_from_xml_file($tmpl, self::_subtheme_path_file($this->subtheme)); }
		self::_language_load();
		if(JTSD){jts::dbgj( 'returning $tmpl', $tmpl, 'vars' );}
		return $tmpl;
	} //  loadSubForm()
	
	public function filter($value, $group = null, Registry $input = null)
	{
		if(JTSD){jts::dbgj( self::$setup_counter.'$input, $value', '$input = '.json_encode($input). '   $value=' . json_encode($value),'raw' )  ;}
		/***/
		// Make sure there is a valid SimpleXMLElement.
		if (!($this->element instanceof \SimpleXMLElement))
		{
			throw new \UnexpectedValueException(sprintf('%s::filter `element` is not an instance of SimpleXMLElement', \get_class($this)));
		}
		// Get the field filter type.
		$filter = (string) $this->element['filter'];
		//if(JTSD){jts::dbgj( self::$setup_counter.'$this->element[\'filter\']', $this->element['filter'] )  ;}
		if ($filter !== '')
		{
			return parent::filter($value, $group, $input);
		}
		// Dirty way of ensuring required fields in subforms are submitted and filtered the way other fields are
		$subForm = $this->loadSubForm();
		// Subform field may have a default value, that is a JSON string
		if ($value && is_string($value))
		{
			$value = json_decode($value, true);
			// The string is invalid json
			if (!$value) {return null;}
		}
		if ($this->multiple)
		{
			$filtered = [];
			if ($value)
			{
				foreach ($value as $key => $val)
				{
					$filtered[$key] = $subForm->filter($val);
				}
			}
		}
		else
		{
			$filtered = $subForm->filter($value);
		}
		//return $filtered;
		/*---*/
		if(JTSD){jts::dbgj( 'returning $filtered', $filtered );}
		return $filtered;
	}
	
	protected function getInput()
	{
		if(JTSD){jts::dbgj( self::$setup_counter.': collecting data and sending it to client. $this->value', $this->value );}
		
		$rescan=false;
		
		if(JTSD){jts::dbgj( '$this->value', $this->value, 'type' ) ;}
		if ( !is_array($this->value) )
		{
			if(JTSD){jts::dbgj( 'warning', 'db subthemes not set in template_styles->params, going to scan fs to prevent app crash' ) ;}
			jts::msgj($arg_msg=Text::_("WR_PHP_NODB"), $arg_msg_type='warning', $arg_line=__LINE__);
			$this->value=array();
			$rescan=true;
		}
		else
		{
			if ( array_key_exists('rescan',$this->value) )
			{
				$rescan=true;
			}
			else if ( array_key_exists('subtheme',$this->value) and !$rescan )
			{
				$st_path_from_db = self::_subtheme_path_file($this->value['subtheme']);
				if ( $st_path_from_db ) 
				{ 
					$rescan=false;
				}
				else
				{
					$rescan=true;
					$msg=Text::sprintf("WRNG_PHP_ST_PATH_NOT_EXISTS", $this->value['subtheme']);
					jts::msgj($msg, $type='warning', $line=__LINE__);
				}
			}
		}
		if ( $rescan )
		{
			$this->subthemes = self::_ret_subthemes_from_fs(); $this->value['subthemes'] = json_encode($this->subthemes); 
			$this->subtheme = $this->subthemes[0]; $this->value['subtheme'] = $this->subtheme;
			if(JTSD){jts::dbgj( 'loaded data from fs', 'subtheme = '. $this->subtheme .';  subthemes ='. json_encode($this->subthemes), 'raw') ;}
			$msg=Text::sprintf("MSG_PHP_LOAD_FS", $this->value['subtheme']);
			jts::msgj($msg, $type='message', $line=__LINE__);
		}
		else
		{
			$this->subthemes = json_decode($this->value['subthemes']);
			$this->subtheme = $this->value['subtheme'];
			if(JTSD){jts::dbgj( 'loaded data from db', 'subthemes = '. json_encode($this->subthemes) .';  subtheme ='. $this->subtheme, 'raw') ;}
			$msg=Text::sprintf("MSG_PHP_LOAD_DB", $this->value['subtheme']);
			jts::msgj($msg, $type='message', $line=__LINE__);
		}
		
		/*---*/
		// Prepare data for renderer
		$data    = $this->getLayoutData();
		$tmpl    = null;
		$control = $this->name;
		
		try
		{
			$tmpl  = $this->loadSubForm();
			$forms = $this->loadSubFormData($tmpl);
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}
		$data['tmpl']      = $tmpl;
		$data['forms']     = $forms;
		$data['min']       = $this->min;
		$data['max']       = $this->max;
		$data['control']   = $control;
		$data['buttons']   = $this->buttons;
		$data['fieldname'] = $this->fieldname;
		$data['fieldId']   = $this->id;
		$data['groupByFieldset'] = $this->groupByFieldset;
		/**
		* For each rendering process of a subform element, we want to have a
		* separate unique subform id present to could distinguish the eventhandlers
		* regarding adding/moving/removing rows from nested subforms from their parents.
		*/
		static $unique_subform_id = 0;
		$data['unique_subform_id'] = ('sr-' . ($unique_subform_id++));
		// Prepare renderer
		$renderer = $this->getRenderer($this->layout);
		// Allow to define some Layout options as attribute of the element
		if ($this->element['component'])
		{
			$renderer->setComponent((string) $this->element['component']);
		}
		if ($this->element['client'])
		{
			$renderer->setClient((string) $this->element['client']);
		}
		// Render
		$html = $renderer->render($data);
		// Add hidden input on front of the subform inputs, in multiple mode
		// for allow to submit an empty value
		if ($this->multiple)
		{
			$html = '<input name="' . $this->name . '" type="hidden" value="">' . $html;
		}
		//return $html;
		/*---*/
		////if(JTSD){jts::dbgj( 'Returning $html', $html );}
		return $html;
	}
	
	private static function _form_add_spacer(&$arg_form_reference, $arg_name, $arg_label)
	{
		//if(JTSD){jts::dbgj( 'me', 'started');}
		$str="<field name='".$arg_name."' type='text' label='".$arg_label."' />";
		$xmlobj = new SimpleXMLElement($str);
		$arg_form_reference->setField($xmlobj, null, true, "advanced");

	}	
	private function _form_field_add_note(&$arg_form_reference, $arg_name, $arg_label, $arg_descr=null)
	{
		////if(JTSD){jts::dbgj( '$this', $this, 'vars' );}
		if ($arg_descr)
		{
			$str="<field name='" . $arg_name . "' type='note' label='" . $arg_label . "'  description='" . $arg_descr . "'   />";
		}
		else
		{
			$str="<field name='".$arg_name."' type='note' label='".$arg_label."' />";
		}
		$xmlobj = new SimpleXMLElement($str);
		$arg_form_reference->setField($xmlobj, null, true, "advanced");
		//if(JTSD){jts::dbgj( 'field added', $xmlobj );}
		//if(JTSD){jts::dbgj( 'check form', $arg_form_reference->getXML() );}
	}
		
	private static function _form_load_from_xml_file(&$arg_form_reference,$arg_file_path)
	{
		if(JTSD){jts::dbgj( '$arg_file_path', $arg_file_path );}
		if (!file_exists($arg_file_path))
		{
			if (JTSD){ jts::dbgj('$arg_file_path not found', $arg_file_path ); }
			return;
		}
		$arg_form_reference->loadFile($arg_file_path, $reset = true, $xpath = null); //loads as sub sub form i
		//if (JTSD){ jts::dbgj('loaded', $arg_file_path ); }
	}
	
	private static function _form_add_rescan(&$arg_form_reference)
	{
		//<field name="rescan" type="checkbox" label="SHAREDXML_RESCAN" description="SHAREDXML_RESCAN_DESCR" value="0" checked="0" />
		$str="<field name='rescan' type='checkbox' label='SHAREDXML_RESCAN' description='SHAREDXML_RESCAN_DESCR' value='0' checked='0' />";
		$xmlobj = new SimpleXMLElement($str);
		$arg_form_reference->setField($xmlobj, null, true, "advanced");

	}		
	private function _form_create_subthemes_selector(&$arg_form_reference)
	{
		if(JTSD){jts::dbgj( self::$setup_counter.'form before', $arg_form_reference->getXml() );}
		$xmlfield=new SimpleXMLElement('<field></field>');
		$xmlfield->addAttribute('name', "subtheme");
		$xmlfield->addAttribute('type', 'list');
		$xmlfield->addAttribute('label', 'PHP_SUBTHEMESELECTOR');
		$xmlfield->addAttribute('description', 'PHP_SUBTHEMESELECTOR_DESCR');
		$xmlfield->addAttribute('onchange', "Joomla.submitbutton('style.apply', null, true)");
		
		//if (JTSD){jts::dbgj( 'adding selector options from self::$subthemes_available)', gettype(self::$subthemes_available) );}
		$subthemes_available=$this->subthemes;
		//if(JTSD){jts::dbgj( '$subthemes_available', $subthemes_available );}
		foreach ($subthemes_available as $subtheme_value)
		{
			//$xmlOption=$xmlfield->addChild('option',$subtheme_value);
			//$xmlOption->addAttribute('value', $subtheme_value);
			$xmlOption=$xmlfield->addChild('option',$subtheme_value);
			$xmlOption->addAttribute('value', $subtheme_value);
		}
		//if(JTSD){jts::dbgj( '$xmlOption', $xmlOption );}
		//if (JTSD){ jts::dbgj('xmlfield to set', $xmlfield ); } 
		
		$xmlhiddenfield = new SimpleXMLElement('<field></field>');
		$xmlhiddenfield->addAttribute('name', "subthemes");
		$xmlhiddenfield->addAttribute('type', "hidden");
		$xmlhiddenfield->addAttribute('default', json_encode($subthemes_available) );
		
		//$xmlhiddenfield->addAttribute('default', $subthemes_available[0] );
		//if (JTSD){ jts::dbgj('hidden field to set', $xmlhiddenfield ); } 
		$arg_form_reference->setField($xmlfield, null, true, "advanced");
		$arg_form_reference->setField($xmlhiddenfield, null, true, "advanced");
		if (JTSD){ jts::dbgj(self::$setup_counter.'form after', $arg_form_reference->getXml() ); } 
		
	}
	
	private function _ret_subthemes_from_fs()
	{
		$fs_subthemes=array();
		$extension_name=self::cfg_get('extension_name');
		$pattern= self::cfg_get('path_subthemes') . "/*/*." . $extension_name . '.xml';
		//if (JTSD){jts::dbgj( '$pattern', $pattern);}
		$paths=glob($pattern);
		foreach ($paths as $path)
		{
			$namefile = basename("$path", '.' . $extension_name . '.xml');
			$namedir = basename(dirname("$path", 1));
			if ($namefile == $namedir) { $fs_subthemes[]=$namedir; } 
		}
		//if (JTSD){jts::dbgj( '$fs_subthemes', $fs_subthemes);}
		if ( count($fs_subthemes) < 1 )
		{
			if (JTSD){jts::dbgj( 'no $fs_subthemes', $fs_subthemes);}
			return null;
		}
		return $fs_subthemes;
	}
	
	private function _subtheme_path_file( string $arg_subtheme_name )
	{
		$extension_name=self::cfg_get('extension_name');
		$st_path_file=self::cfg_get('path_subthemes') . "/$arg_subtheme_name/$arg_subtheme_name.$extension_name.xml";
		if (!file_exists($st_path_file))
		{
			if (JTSD){ jts::dbgj('$st_path_file not exists', $st_path_file ); }
			return null;
		}
		return $st_path_file;
	}
	
	private static function dbg ($arg_caller, $arg_line, $arg_title, $arg_object)
	{
		/*
			Usage template:
			if (JTSD){ self::dbg($me,__LINE__, 'title', object); } 
		*/
		if(!JTSD){ return; }
		$title="$arg_caller: $arg_line: $arg_title:\n";
		echo "<textarea rows='4'>$title". print_r($arg_object, true) .'</textarea>';
	}

	private static function msgj ($arg_msg, $arg_msg_type='notice', $arg_line=null)
	{
		if ($arg_line) { $arg_msg="$arg_msg ($arg_line)";}
		Factory::getApplication()->enqueueMessage($arg_msg, $arg_msg_type);//Message, Notice, Warning. Error
	}
	
	private function _language_load()
	{
		$lang = \Joomla\CMS\Factory::getApplication()->getLanguage();
		$tpl_extension = 'tpl_' . self::cfg_get('extension_name');
		$language_tag = null;//null - for current lang; 'en-GB';
		
		$base_dir = self::cfg_get('path_subthemes').'/'.$this->subtheme;
		if(JTSD){jts::dbgj( self::$setup_counter.': loading language for [' . $tpl_extension . '] from: ', $base_dir);}
		$lang->load($tpl_extension, $base_dir, $language_tag, $reload = true, $default = false);
	} 
}
