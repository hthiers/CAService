<?php 
/**
 * Utilities Class 
 */
class Utils
{
	function __construct() 
	{
	}
	
	//Avoid SQL Injection
	public function cleanQuery($string)
	{
	  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
	  {
		$string = stripslashes($string);
	  }
	  if (phpversion() >= '4.3.0')
	  {
		$string = mysql_real_escape_string($string);
	  }
	  else
	  {
		$string = mysql_escape_string($string);
	  }
	  return $string;
	}
}
?>