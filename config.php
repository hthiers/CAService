<?php
$config = Config::singleton();

$config->set('apachePath', 'E:/xampp/');
#$config->set('rootPath', '/'); // para suyay.cl
$config->set('rootPath', '/CAService/');
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

$config->set('dbhost', 'localhost');
#$config->set('dbname', 'suyay_registrotrabajo'); // para suyay.cl
#$config->set('dbuser', 'suyay_regtrabajo'); // para suyay.cl
#$config->set('dbpass', 'I2_;(A@{TNy+'); // para suyay.cl
$config->set('dbname', 'cas_mt_db');
$config->set('dbuser', 'root');
$config->set('dbpass', 'walkirias');

$config->set('timezone', 'America/Santiago');
$config->set('debug', true);
#$config->set('token', '3756a4505914c97f76b8557a688466a4');
?>