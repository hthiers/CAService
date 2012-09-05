<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

#session vars
#$session = FR_Session::singleton();

#system vars for view level
$config = Config::singleton();
$rootPath = $config->get('rootPath');
$debugMode = $config->get('debug');

#session vars
#if($session->id != null):

$navegador = $_SERVER['HTTP_USER_AGENT'];
$navegador = substr($navegador,25,8);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Control tiempos de trabajo - demo no funcional v0.1</title>
<!-- @import "views/css/custom-theme/jquery-ui-1.8.20.custom.css"; -->
<style type="text/css">
    @import "views/css/custom-theme-blue/jquery-ui-1.8.23.custom.css";
    @import "views/css/estilo.css";
    @import "views/css/reset-min.css";
    @import "views/css/formularios.css";
    @import "views/css/texto.css";
    <?php 
    if($navegador == 'MSIE 6.0')  
            echo '@import "views/css/menuie6.css";';
    else
            echo '@import "views/css/menu2.css";';
    ?>
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.validate.messages.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" language="javascript">
// Prevent the backspace key from navigating back.
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD')) 
            || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
<?php
	if($navegador == 'MSIE 6.0')
		echo '<script type="text/javascript" language="javascript" src="views/lib/menuie6.js"></script>';
	
#endif; #session
?>