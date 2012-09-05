<?php
/**
* AJAX PHP SCRIPT FOR JQUERY DATATABLES
* MODELS: INSERT/UPDATE
* 
* @author Hernán Thiers
* @return json | serialized data array
* data=1: extract all models data by filters
* data=2: extract segments data list
*/

/* Database connection information */
$gaSql['user']       = "root";
$gaSql['password']   = "lgecl";
$gaSql['db']         = "lg_som_v2";
$gaSql['server']     = "localhost";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* If you just want to use the basic configuration for DataTables with PHP server-side, there is
* no need to edit below this line
*/

/* 
* MySQL connection
*/
$gaSql['link'] =  mysql_connect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
    die( 'Could not open connection to server' );

mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
    die( 'Could not select database '. $gaSql['db'] );

if($_GET['action'] == 1)
{
    /*
    * PRODUCT FIELDS UPDATE
    */

    $sTable = "t_product";
    $gbu = $_REQUEST['gbu'];
    $user = $_REQUEST['user'];
    $newcode = $_REQUEST['value'];
    $cod_model = $_REQUEST['idData'];
    
    $target_column = '';
    
    if($_GET['target_col'] == 1)
        $target_column = "COD_SEGMENT";
    else if($_GET['target_col'] == 2)
        $target_column = "COD_SUB_SEGMENT";
    else if($_GET['target_col'] == 3)
        $target_column = "COD_MICRO_SEGMENT";
    #else if($_GET['target_col'] == 4)
    #   $target_column = "COD_GBU";
    else if($_GET['target_col'] == 5)
        $target_column = "COD_BRAND";
    else if($_GET['target_col'] == 6)
        $target_column = "COD_ESTADO";

    $sQuery = "UPDATE $sTable SET $target_column = '$newcode' WHERE COD_MODEL = '$cod_model'";

    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

    //For debug
    #echo 'query result: '.$rResult;

    /*
     * EVENT LOG
     */
    saveLog($sQuery, $user, 'modelos', $gaSql['link']);
}
else
    echo 'value: '.$_POST['value'].', id: '.$_POST['idData'].' - (answer from ajaxModelsModel.php)';


/*
 * Save to log
 */
function saveLog($event_sql,$usuario,$modulo, $connection)
{
    #date_default_timezone_set('America/Santiago');
    date_default_timezone_set('America/Buenos_Aires'); //Por cambio de hora
    $fecha=date( 'Y-m-d H:i:s');
    $ip=$_SERVER["REMOTE_ADDR"];
    $log_sql=str_replace("'"," ",$event_sql);
    $host_name=gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $sql = "INSERT INTO t_seguimiento (USUARIO,FECHA,MODIFICACION,IP_CLIENTE,HOST_NAME,MODULO) 
    VALUES ('$usuario','$fecha','$log_sql','$ip','$host_name','$modulo');";

    mysql_query( $sql, $connection ) or die(mysql_error());
}
?>