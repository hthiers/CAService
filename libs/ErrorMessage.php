<?php
/**
 * System errors
 * @author Hernán Thiers 
 */
class ErrorMessage
{
	function __construct() 
	{
	}
	
	/**
         * Get pre defined or custom message error by type and message
         * @param int $type
         * @param string $message
         * @return string 
         */
	public function getError($type = 0, $message = "")
	{
		if($type == 1)
		{
			$string = "<div id='errorbox_success'>";
			$string .= "Resultado exitoso.";
			$string .= "</div>\n";
		}
		elseif($type == 2)
		{
			$string = "<div id='errorbox_failure'>";
			$string .= "Ha ocurrido un error.";
			$string .= "</div>\n";
		}
                elseif($type == 3)
		{
			$string = "<div id='errorbox_failure'>";
			$string .= "Código ingresado ya existe.";
			$string .= "</div>\n";
		}
                elseif($type == 4)
		{
			$string = "<div id='errorbox_failure'>";
			$string .= "Contraseña actual incorrecta.";
			$string .= "</div>\n";
		}
                elseif($type == 5)
		{
			$string = "<div id='errorbox_failure'>";
			$string .= "Nueva contraseña mal ingresada.";
			$string .= "</div>\n";
		}
                elseif($type == 10)
		{
			$string = "<div id='errorbox_failure'>";
			$string .= $message;
			$string .= "</div>\n";
		}
                elseif($type == 11)
		{
			$string = "<div id='errorbox_success'>";
			$string .= $message;
			$string .= "</div>\n";
		}
		else
			$string = "";
		
		return $string;
	}
}
?>