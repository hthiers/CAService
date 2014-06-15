<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

//session vars
$session = FR_Session::singleton();

#system vars for view level
$config = Config::singleton();
$rootPath = $config->get('rootPath');
$debugMode = $config->get('debug');

#session vars
if($session->id_tenant != null && $session->id_user != null):
    
$navegador = $_SERVER['HTTP_USER_AGENT'];
$navegador = substr($navegador,25,8);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
<title>Control tiempos de trabajo - v0.1.1</title>
<?php
endif; #session
?>