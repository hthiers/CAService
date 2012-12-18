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
        width: 800px;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return false;
}
    
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=customers&action=ajaxCustomersDt'";?>,
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
//        "fnServerParams": function ( aoData ){
//            aoData.push(
//                { "name": "filCliente", "value": $('#cboCliente').val() },
//                { "name": "filMes", "value": $('#cboMes').val() },
//                { "name": "filEstado", "value": $('#cboEstado').val() }
//            );
//        },
        
        "aoColumnDefs": [
//            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [0,2] },
//            {
//                "fnRender": function ( oObj ) {
//                    //var string = '<button id=\"button\" class=\"input\" name=\"id_project\" onclick=\"submitToForm()\" value="'+oObj.aData[0]+'">EDITAR</button>';
//                    //var string = '<button id=\"button\" class=\"input\" name=\"id_project\" onclick=\"submitToForm()\" value="'+oObj.aData[0]+'">EDITAR</button>';
//                    //string += '&nbsp; <button id=\"button\" class=\"input\" name=\"id_project\" onclick=\"submitToForm()\" value="'+oObj.aData[0]+'">REVISAR</button>';
//                    
//                    return string;
//                },
//                "aTargets": [5]
//            }
//            {
//                "fnRender": function ( oObj ) {
//                    if(oObj.aData[5] != null){
//                        var seconds = oObj.aData[5];
//                        var total = secondsToTime(seconds);
//
//                        return total['h']+':'+total['m']+':'+total['s'];
//                    }
//                    else{
//                        return '';
//                    }
//                },
//                "aTargets": [5]
//            },
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });
    
//    $('#cboCliente').change(function() { oTable.fnDraw(); } );
//    $('#cboMes').change(function() { oTable.fnDraw(); } );
//    $('#cboEstado').change(function() { oTable.fnDraw(); } );
//    
//    // form submition handling
//    $('#dt_form').submit( function() {
//        var sData = oTable.$('input').serialize();
//        var actionType = $('#action_type').val();
//        var urlAction = "";
//        
//        if(actionType == 'edit_form'){
//            urlAction = "<?php #echo "?controller=".$controller."&amp;action=".$action;?>";
//            $('#action_type').val("");
//            
//            return true;
//        }
//    });
});
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
//            print_r($arrayDates);print('<br />');
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
        else
            echo "aaaa";
        ?>

        <!-- CUSTOM FILTROS -->
        <!-- END CUSTOM FILTROS -->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=".$controller."&amp;action=".$action;?>">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>ID</th>
                            <th>NUMERO</th>
                            <th>TENANT</th>
                            <th>NOMBRE</th>
                            <th>DATOS</th>
<!--                            <th>CONTACTO</th>
                            <th>TELEFONO</th>
                            <th>CORREO</th>-->
<!--                            <th>OPCIONES</th>-->
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