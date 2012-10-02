<?php
$config = Config::singleton();

$config->set('apachePath', '/home/hernan/public_html/');
$config->set('rootPath', '/~hernan/CAService/');
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

$config->set('dbhost', 'localhost');
$config->set('dbname', 'cas_mt_db');
$config->set('dbuser', 'root');
$config->set('dbpass', 'walkirias84');

$config->set('timezone', 'America/Santiago');
$config->set('debug', true);
#$config->set('token', '3756a4505914c97f76b8557a688466a4');
?>