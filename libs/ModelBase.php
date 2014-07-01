<?php
/**
 * Base Model 
 */
abstract class ModelBase 
{
    protected $db;
    protected $ssp;
    protected $apache;
    protected $root;

    public function __construct()
    {
        $this->db = SPDO::singleton();

        $config = Config::singleton();
        $this->apache = $config->get('apachePath');
        $this->root = $config->get('rootPath');
    }

    /* TODO:
        * AGREGAR FUNCIONES GENERALES DE MODELO:
        * 
        * 1- FUNCION CUSTOM QUERY
        * 2- FUNCION GET TABLE NAME (SOBREESCRIBIR)
        * 3- FUNCION GET TABLE COLUMNS (SOBREESCRIBIR)
        */
}
?>