<?php
#System vars
$config = Config::singleton();
$rootPath = $config->get('rootPath');
$debugMode = $config->get('debug');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <title>Control tiempos de trabajo - demo no funcional v0.1</title>
    
    <style type="text/css" title="currentStyle">
		@import "views/css/estilo.css";
		@import "views/css/texto.css";
                @import "views/css/reset-min.css";
                @import "views/css/menu2.css";
	</style>
        
    <script type="text/javascript" language="javascript" src="views/lib/jquery.js"></script>