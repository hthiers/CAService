<?php
$config = Config::singleton();

$config->set('apachePath', '');
$config->set('rootPath', '/CAService/');
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

$config->set('dbhost', 'localhost');
$config->set('dbname', 'tiempo_trabajo');
$config->set('dbuser', '');
$config->set('dbpass', '');

$config->set('timezone', 'America/Santiago');
$config->set('debug', false);
#$config->set('token', '3756a4505914c97f76b8557a688466a4');
?>