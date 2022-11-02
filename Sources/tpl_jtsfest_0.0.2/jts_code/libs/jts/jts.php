<?php
use Joomla\CMS\Factory;

$path_c_jts = substr (__DIR__, strlen(JPATH_ROOT) );

Factory::getApplication()->getDocument()->addStyleSheet($path_c_jts.'/client/jts.css');


class jts
{
	private static $dbgj_counter=0;
	private static $objref=null; 
	public static function dbgj ($arg_title, $arg_var, $arg_etc = null)
	{
		/*
			Usage template:
			if (JDEBUG){ self::dbgj($me,__LINE__, 'title', object); } 
		*/
		if(!JDEBUG){ return; }
		
		self::$dbgj_counter = self::$dbgj_counter +1 ;
		
		$full_trace=debug_backtrace();
		$caller_file=$full_trace[0]['file'];
		$caller_file=substr($caller_file,strlen($_SERVER['DOCUMENT_ROOT']),strlen($caller_file));
		$caller_line=$full_trace[0]['line'];
		$caller_func=$full_trace[1]['function'];
		
		$me=basename(__FILE__).': '. __FUNCTION__ .', JDEBUG true';
		
		$msgheader="<h5>".self::$dbgj_counter. " $arg_etc</h5><p>$caller_func, line: $caller_line @ $caller_file ($me)</p><p>$arg_title:</p>";
		
		if ($arg_etc == 'trace')
		{
			$msg = $msgheader . '<textarea>' . print_r(json_encode($full_trace,  JSON_PRETTY_PRINT),true) .'</textarea>';
		}
		else if ( $arg_etc == 'vars' )
		{
			$msg = $msgheader . '<textarea>' . json_encode(get_object_vars($arg_var),JSON_PRETTY_PRINT) .'</textarea>';
		}
		else if ( $arg_etc == 'class_vars' )
		{
			$msg = $msgheader . '<textarea>' . json_encode(get_class_vars($arg_var),JSON_PRETTY_PRINT) .'</textarea>';
		}
		else if ( $arg_etc == 'methods' )
		{
			$msg = $msgheader . '<textarea>' . json_encode(get_class_methods($arg_var),JSON_PRETTY_PRINT) .'</textarea>';
		}
		else if ( $arg_etc == 'type' )
		{
			$type='unknown';
			if ( is_object($arg_var) ) { $type = get_class($arg_var) ; } else { $type = gettype($arg_var) ; }
			$msg = $msgheader . '<textarea>' . $type .'</textarea>';
		}
		else if ( $arg_etc == 'isset' )
		{
			$msg = $msgheader . '<textarea>' . json_encode(isset($arg_var),JSON_PRETTY_PRINT) .'</textarea>';
		}
		else if ( $arg_etc == 'empty' )
		{
			$msg = $msgheader . '<textarea>' . json_encode(empty($arg_var),JSON_PRETTY_PRINT) .'</textarea>';
		}
		else if ( $arg_etc == 'printr' )
		{
			$msg = $msgheader . '<textarea>' . print_r($arg_var,true) .'</textarea>';
		}
		else if ( $arg_etc == 'raw' )
		{
			$msg = $msgheader . '<textarea>' . $arg_var .'</textarea>';
		}
		else if ( empty($arg_etc) or $arg_etc == 'json' )
		{
			$msg = $msgheader . '<textarea>' . json_encode($arg_var,JSON_PRETTY_PRINT) .'</textarea>';
		}
		else
		{
			$msg = $msgheader . '<textarea>' . __FUNCTION__  . ' Wrong argumennt: ' . print_r($arg_etc,true)  .'</textarea>';
		}
		
		//$msg='<textarea>' . json_encode(debug_backtrace()[1], true) .'</textarea>';
		
		//https://docs.joomla.org/Display_error_messages_and_notices
		Factory::getApplication()->enqueueMessage($msg, 'Notice');//Message, Notice, Warning. Error
		//\Joomla\CMS\Factory::getApplication()->enqueueMessage('parent: $this->name: '. $this->name, 'Notice');
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
	
	public static function fs ($arg=null)
	{
		$paths_s_jts_file = pathinfo('/www/htdocs/inc/lib.inc.php');
		//echo $paths_s_jts_file['dirname'], "\n";
		//echo $paths_s_jts_file['basename'], "\n";
		//echo $paths_s_jts_file['extension'], "\n";
		//echo $paths_s_jts_file['filename'], "\n";
		return '';
	}

}
