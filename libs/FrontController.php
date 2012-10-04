<?php
/**
 * Initializer Controller
 * Inicializa motor del sistema invocando todas las librerias necesarias.
 */
class FrontController
{   
	static function main()
	{
		//Incluimos algunas clases:
		
		require 'libs/Config.php'; //de configuracion
                require 'libs/FR_Session.php'; //de sesiones
		require 'libs/SPDO.php'; //PDO con singleton
		require 'libs/ControllerBase.php'; //Clase controlador base
		require 'libs/ModelBase.php'; //Clase modelo base
		require 'libs/View.php'; //Mini motor de plantillas
		require 'libs/Utils.php'; //Clase de utilidades
		require 'libs/ErrorMessage.php'; //Clase de utilidades
		
		require 'config.php'; //Archivo con configuraciones.
		
		//Con el objetivo de no repetir nombre de clases, nuestros controladores
		//terminaran todos en Controller. Por ej, la clase controladora Items, ser� ItemsController
		
		//Formamos el nombre del Controlador o en su defecto, tomamos que es el IndexController
		if(! empty($_GET['controller']))
		{
			$controllerName = ucfirst($_GET['controller']) . 'Controller';
		}
		else
		{
			$controllerName = "IndexController";
		}
		
		//Lo mismo sucede con las acciones, si no hay accion, tomamos index como accion
		if(! empty($_GET['action']))
		      $actionName = $_GET['action'];
		else
		      $actionName = "index";
		
		$controllerPath = $config->get('controllersFolder') . $controllerName . '.php';
			
		//Incluimos el fichero que contiene nuestra clase controladora solicitada	
		if(is_file($controllerPath))
		      require $controllerPath;
		else
		      die('El controlador no existe - 404 not found: '.$controllerPath.", ".$controllerName);
		
		//Si no existe la clase que buscamos y su accion, tiramos un error 404
		if (is_callable(array($controllerName, $actionName)) == false) 
		{
			trigger_error ($controllerName . '->' . $actionName . '` no existe', E_USER_NOTICE);
			return false;
		}
		//Si todo esta bien, creamos una instancia del controlador y llamamos a la accion
		$controller = new $controllerName();
		$controller->$actionName();
	}
}
?>