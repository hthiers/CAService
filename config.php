<?php
$config = Config::singleton();

$config->set('apachePath', 'E:/xampp/');
$config->set('rootPath', '/tiempo_trabajo/');
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

$config->set('dbhost', 'localhost');
#$config->set('dbname', 'suyay_registrotrabajo');
#$config->set('dbuser', 'suyay_regtrabajo');
#$config->set('dbpass', 'I2_;(A@{TNy+');
$config->set('dbname', 'tiempo_trabajo');
$config->set('dbuser', 'root');
$config->set('dbpass', 'walkirias');

$config->set('timezone', 'America/Santiago');
$config->set('debug', false);
#$config->set('token', '3756a4505914c97f76b8557a688466a4');
?>