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
<script type="text/javascript" charset="utf-8">
//$.fn.dataTableExt.afnFiltering.push(
//    function( oSettings, aData, iDataIndex ) {
//        var iCliente = $('#cboCliente option:selected').val();
//        var iEstado = $('#cboEstado option:selected').val();
//        var iVersion = aData[3] == "-" ? 0 : aData[3]*1;
//        
//        if ( iCliente == "" && iEstado == "" )
//        {
//            return true;
//        }
//        else if ( iCliente == "" && aData[4] == iEstado )
//        {
//            return true;
//        }
//        else if ( iCliente == aData[0] && "" == iEstado )
//        {
//            return true;
//        }
////        else if ( iCliente < iVersion && iVersion < iEstado )
////        {
////            return true;
////        }
//        return false;
//    }
//);
    
$(document).ready(function() {
    oTable = $('#example').dataTable({
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
            "sPaginationType": "full_numbers",
            "aaSorting": [[0, "asc"]]
    });
    
    $('#cboCliente').change(function() { oTable.fnDraw(); } );
    $('#cboEstado').change(function() { oTable.fnDraw(); } );
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
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a gestionar todos los registros existentes en el sistema, en principio, solo para el usuario en sesi&oacute;n. 
            Una barra azul en la cabecera de la p&aacute;gina muestra diferentes opciones de men&uacute;. En este caso solo funcionan como v&iacute;nculos 
            el item de "TRABAJOS" y "NUEVO TRABAJO".
            <br />
            Sobre la tabla de abajo se encuentran los filtros de información en la tabla.
            Un bot&oacute;n de exportar permitir&iacute;a crear un documento Excel con todos los trabajos en vista.
            Una columna de opciones permitir&iacute;a ejecutar ciertas acciones sobre un trabajo, en este caso se encuentra un v&iacute;nculo "ver"
            para abrir un registro.
            Haciendo clic en las cabeceras de la tabla es posible cambiar el orden por columna.
        </p>
        
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
                <th>Exportar</th>
            </tr>
            <tr>
                <td>
                    <?php
                    echo "<select name='cboCliente' id='cboCliente'>\n";
                    echo "<option selected value=''>Todos</option>";
                        echo "<option value='Cliente A'>Cliente A</option>\n";
                        echo "<option value='Cliente B'>Cliente B</option>\n";
                        echo "<option value='Cliente C'>Cliente C</option>\n";
                        echo "<option value='Cliente D'>Cliente D</option>\n";
                    echo "</select>\n";
                    ?>
                </td>
                <td>
                    <?php
                    echo "<select name='cboMes' id='cboMes'>\n";
                        echo "<option value='07'>Julio</option>\n";
                        echo "<option value='08'>Agosto</option>\n";
                        echo "<option selected value='09'>Septiembre</option>\n";
                        echo "<option value='10'>Octubre</option>\n";
                        echo "<option value='11'>Noviembre</option>\n";
                        echo "<option value='12'>Diciembre</option>\n";
                    echo "</select>\n";
                    ?>
                </td>
                <td>
                    <?php
                    echo "<select name='cboEstado' id='cboEstado'>\n";
                        echo "<option selected='selected' value=''>Todos</option>\n";
                        echo "<option value='Activo'>Activos</option>\n";
                        echo "<option value='Finalizado'>Finalizados</option>\n";
                    echo "</select>\n";
                    ?>
                </td>
                <td>
                    <?php echo "<a title='excel' id='exp_excel' href='#'><img alt='excel' src='views/img/excel07.png' /></a>"; ?>
                </td>
            </tr>
        </table>
        </div>
        <!-- END CUSTOM FILTROS -->

        <table class="display" id="example">
        <thead>
            <tr class="headers">
                <th>CLIENTE</th>
                <th>RESPONSABLE</th>
                <th>ETIQUETA</th>
                <th>INICIO</th>
                <th>FIN</th>
                <th>DESCRIPCION</th>
                <th>OPCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($item = $listado->fetch(PDO::FETCH_ASSOC))
            {
            ?>
            <tr>
                <td><?php echo $item['name_customer'];?></td>
                <td><?php echo $item['name_user'];?></td>
                <td><?php echo $item['label_project'];?></td>
                <td><?php echo $item['date_ini'];?></td>
                <td><?php echo $item['date_end'];?></td>
                <td><?php if($item['status_project'] == 1){ echo "activo";} else { echo "finalizado";};?></td>
                <td>
                    <form method="post"  action="?controller=projects&amp;action=projectsView">
                        <?php 
                        echo "<input name='id_project' type='hidden' value='$item[id_project]' />\n";
                        echo "<input class='input' type='submit' value='VER' />\n";
                        ?>
                    </form>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
        </table>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>