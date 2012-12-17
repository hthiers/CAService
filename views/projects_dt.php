<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
    @import "views/css/datatable.css";
    table.dataTable, table.filtres {
        width: 100%;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return true;
}
    
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": '?controller=projects&action=ajaxProjectsDt',
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.ajax({
                "dataType": 'json', 
                "type": "GET", 
                "url": sSource, 
                "data": aoData, 
                "success": fnDrawCallback
            });
        },
        
        "sDom": '<"top"lpf>rt<"clear">',
        
        "oLanguage": {
            "sInfo": "_TOTAL_ registros",
            "sInfoEmpty": "0 registros",
            "sInfoFiltered": "(de _MAX_ registros)",
            "sLengthMenu": "_MENU_ por p&aacute;gina",
            "sZeroRecords": "No hay registros",
            "sInfo": "_START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sSearch": "Buscar",
            "oPaginate": {
                "sFirst": "Primera",
                "sNext": "Siguiente",
                "sPrevious": "Anterior",
                "sLast": "&Uacute;ltima"
            }
        },
        
        //Custom filters params
        "fnServerParams": function ( aoData ){
            aoData.push(
                { "name": "filCliente", "value": $('#cboCliente').val() },
                { "name": "filMes", "value": $('#cboMes').val() },
                { "name": "filEstado", "value": $('#cboEstado').val() }
            );
        },
        
        "aoColumnDefs": [
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [6,7,8,9,10,11,12,13] },
            {
                "fnRender": function ( oObj ) {
                    return '<button id=\"button\" class=\"input\" name=\"id_project\" onclick=\"submitToForm()\" value="'+oObj.aData[6]+'">VER</button>';
                },
                "aTargets": [-1]
            },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[5] != null){
                        var seconds = oObj.aData[5];
                        var total = secondsToTime(seconds);

                        return total['h']+':'+total['m']+':'+total['s'];
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [5]
            },
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });
    
    $('#cboCliente').change(function() { oTable.fnDraw(); } );
    $('#cboMes').change(function() { oTable.fnDraw(); } );
    $('#cboEstado').change(function() { oTable.fnDraw(); } );
    
    // form submition handling
    $('#dt_form').submit( function() {
        var sData = oTable.$('input').serialize();
        var actionType = $('#action_type').val();
        var urlAction = "";
        
        if(actionType == 'edit_form'){
            urlAction = "<?php echo "?controller=".$controller."&amp;action=".$action;?>";
            $('#action_type').val("");
            
            return true;
        }
    });
    
    /* 
    * Filas de detalles para Dt Tables.
    * Add event listener for opening and closing details
    * Note that the indicator for showing which row is open is not controlled by DataTables,
    * rather it is done here
    */
    $('#example tbody td').live('click', function (){
        var nTr = $(this).parents('tr')[0];
        var kids = $(this).children();
        
        // ignore custom columns with action objects (children elements)
        if(kids.length == 0){
            if ( oTable.fnIsOpen(nTr) )
            {
                /* This row is already open - close it */
                oTable.fnClose( nTr );
            }
            else
            {
                /* Open this row */
                //oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
                var sID = fnFormatDetails(oTable, nTr);
                var newRow = oTable.fnOpen( nTr, "Cargando...", "details");
                
                $.ajax({
                    dataType: "json",
                    type: "GET",
                    url: "?controller=tasks&action=ajaxTasksList",
                    data: {id_project: sID}
                })
                .done(function(data){
                    var newHtml = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;width:100%;">';
                    var newHeader = '<thead><th style="color:#000;cursor:default;font-weight:bold;">Tarea</th>';
                    newHeader += '<th style="color:#000;cursor:default;font-weight:bold">Inicio</th>';
                    newHeader += '<th style="color:#000;cursor:default;font-weight:bold">Fin</th>';
                    newHeader += '<th style="color:#000;cursor:default;font-weight:bold">Tiempo Total</th>';
                    newHeader += '</thead>';
                    
                    if(data != null){
                        newHtml += newHeader;
                        
                        $.each(data, function(i,item){
                            console.log(item);
                            newHtml += '<tbody>';
                            newHtml += '<tr>';
                            newHtml += '<td>'+item['label_task']+'</td>';
                            newHtml += '<td style="text-align:center;">'+item['date_ini']+'</td>';
                            newHtml += '<td style="text-align:center;">'+item['date_end']+'</td>';
                            newHtml += '<td style="text-align:center;">'+formatTime(item['time_total'])+'</td>';
                            newHtml += '</tr>';
                        });
                    }
                    else{
                        newHtml += '<tbody>';
                        newHtml += '<tr><td colspan="4" style="text-align:center;">Sin tareas asignadas</td></tr>';
                    }
                        
                    newHtml += '</tbody></table>';
                    
                    $('td', newRow).html(newHtml);
                })
                .fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }
        }
    });
});

/*
* Getting needed value from dt row
 */
function fnFormatDetails (oTable, nTr){
    var aData = oTable.fnGetData( nTr );
    return aData[6];
}
</script>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
require('templates/menu.tpl.php'); #banner & menu
?>
    <!-- CENTRAL -->
    <div id="central">
    <div id="contenido">

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print_r($arrayDates);print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a gestionar todos los registros existentes en el sistema, en principio, solo para el usuario en sesi&oacute;n. 
            Una barra azul en la cabecera de la p&aacute;gina muestra diferentes opciones de men&uacute;. En este caso solo funcionan como v&iacute;nculos 
            el item de "TRABAJOS" y "NUEVO TRABAJO".
            <br />
            Sobre la tabla de abajo se encuentran los filtros de informaciÃ³n en la tabla.
            Un bot&oacute;n de exportar permitir&iacute;a crear un documento Excel con todos los trabajos en vista.
            Una columna de opciones permitir&iacute;a ejecutar ciertas acciones sobre un trabajo, en este caso se encuentra un v&iacute;nculo "ver"
            para abrir un registro.
            Haciendo clic en las cabeceras de la tabla es posible cambiar el orden por columna.
        </p>
        -->
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>

        <!-- CUSTOM FILTROS -->
        <div id="dt_filtres">
        <table class="filtres">
            <tr>
                <th>Cliente</th>
                <th>Mes</th>
                <th>Estado</th>
<!--                <th>Exportar</th>-->
            </tr>
            <tr>
                <td>
                    <?php
                    echo "<select name='cboCliente' id='cboCliente'>\n";
                    echo "<option selected value=''>TODOS</option>";
                    while($row = $pdoCustomers->fetch(PDO::FETCH_ASSOC))
                    {
                        echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
                <td>
                    <?php
                    echo "<select name='cboMes' id='cboMes'>\n";
                    echo "<option selected value=''>TODOS</option>";
                    foreach ($arrayDates as $key => $value) {
                        echo "<option value='".$key."'>".$value."</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
                <td>
                    <?php
                    echo "<select name='cboEstado' id='cboEstado'>\n";
                        echo "<option selected='selected' value=''>TODOS</option>\n";
                        echo "<option value='1'>Activo</option>\n";
                        echo "<option value='2'>Finalizado</option>\n";
                    echo "</select>\n";
                    ?>
                </td>
<!--                <td>
                    <?php #echo "<a title='excel' id='exp_excel' href='#'><img alt='excel' src='views/img/excel07.png' /></a>"; ?>
                </td>-->
            </tr>
        </table>
        </div>
        <!-- END CUSTOM FILTROS -->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=".$controller."&amp;action=".$action;?>">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>CLIENTE</th>
                            <th>RESPONSABLE</th>
                            <th>ETIQUETA</th>
                            <th>INICIO</th>
                            <th>FIN</th>
                            <th>TIEMPO</th>
                            <th>ID PROJECT</th>
                            <th>CODE PROJECT</th>
                            <th>ID TENANT</th>
                            <th>ID USER</th>
                            <th>CODE USER</th>
                            <th>ID CUSTOMER</th>
                            <th>DESC PROJECT</th>
                            <th>STATUS PROJECT</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Loading data from server</td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td><input id="action_type" type="hidden" name="action_type" value="" /></td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>