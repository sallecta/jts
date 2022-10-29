<?php

use Joomla\CMS\Factory;
class jsll
{
	private static $dbgj_counter=0;
	private static $objref=null; 
	public static function dbgj ($arg_title, $arg_object)
	{
		/*
			Usage template:
			if (JDEBUG){ jsll::dbgj('title', object); } 
		*/
		if( (!JDEBUG) or (!JSALLECTA_DEBUG_MESSAGES_SITE) ){ return; }
		
		if (!self::$objref) {self::$objref = Factory::getDocument();}
		
		$caller_file=debug_backtrace()[0]['file'];
		$caller_file=substr($caller_file,strlen(JPATH_THEMES),strlen(JPATH_THEMES));
		$caller_line=debug_backtrace()[0]['line'];
		//echo '<h1>file:::'.$caller_file.'</h1>';
		//echo '<h1>line:::'.json_encode(debug_backtrace()[0]['line']).'</h1>';
		//echo '<h1>emm:::'.json_encode(debug_backtrace()[2]).'</h1>';
		
		if ( (self::$dbgj_counter)<1 ) { self::$dbgj_counter=1; } else { self::$dbgj_counter = self::$dbgj_counter +1 ;}
		
		$me=basename(__FILE__).': '. __FUNCTION__ .', JDEBUG true';
		$msg='<p> '.self::$dbgj_counter.", line: $caller_line, caller: $caller_file: ($me)</p><h6>$arg_title:</h6>" . "<p>" . print_r($arg_object, true) .'</p>';
		////https://docs.joomla.org/Display_error_messages_and_notices
		Factory::getApplication()->enqueueMessage($msg, 'Notice');//Message, Notice, Warning. Error
	}

	public static function params_to_array (\stdClass $arg_obj)
	{
		$result=array();
		
		//echo "<ul>";
		foreach ($arg_obj as $arr)
		{
			foreach ($arr as $key => $value)
			{
				//echo '<li>';
					//echo 'key: '. gettype($key).': '. $key .'; value: '. gettype($value) . ': ' . $value;
				//echo '</li>';
				$result[$key]=$value;
			}
		}
		//echo "</ul>";
		return $result;
	} 
	
	public static function msgj ($arg_msg, $arg_msg_type='notice', $arg_line=null)
	{
		if ($arg_line) { $arg_msg="$arg_msg ($arg_line)";}
		Factory::getApplication()->enqueueMessage($arg_msg, $arg_msg_type);//Message, Notice, Warning. Error
	}

}
