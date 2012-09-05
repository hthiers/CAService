<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio == 1):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
	@import "views/css/datatable.css";
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min-custom.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $('#example').dataTable({
            "sDom": '<"top"lpfi>rt<"clear">',
            "oLanguage": {
                "sInfo": "_TOTAL_ records",
                "sInfoEmpty": "0 records",
                "sInfoFiltered": "(from _MAX_ total records)"
            },
            "aaSorting": [[1, "asc"]],
            "aoColumnDefs": [{ 
                "bSortable": false, "aTargets": [6]
            },
            { "sWidth": "5%", "aTargets": [ 0 ] },
            { "sWidth": "20%", "aTargets": [ 1 ] },
            { "sWidth": "20%", "aTargets": [ 2 ] },
            { "sWidth": "20%", "aTargets": [ 3 ] },
            { "sWidth": "20%", "aTargets": [ 4 ] },
            { "sWidth": "10%", "aTargets": [ 5 ] },
            { "sWidth": "5%", "aTargets": [ 6 ] }],
            "sPaginationType": "full_numbers"
        });

        $("#dialog-confirm").dialog({ autoOpen: false});

        $('.linkDelete').each(function(){
             var self = $(this);
        }).click(function(e){
            var link = $(this).attr("href");
            //console.log(link);

            $("#dialog-confirm").css("visibility","visible");

            $("#dialog-confirm").dialog({
                autoOpen: true,
                resizable: false,
                height: 180,
                modal: true,
                buttons: {
                    "Aceptar": function() {
                        $( this ).dialog( "close" );

                        document.location = link;
                    },
                    "Cancelar": function() {
                        $( this ).dialog( "close" );
                    }
                }
            });

            return false;
        });

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

        <?php require('templates/dialogs.tpl.php'); #session & header ?>

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print('<br />');print('<br />');
            if(isset($error))
                print_r($error->errorInfo());
            
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <?php 
        if (isset($error_flag))
            if(strlen($error_flag) > 0)
                echo $error_flag;
        ?>

        <table class="display" id="example">
        <thead>
            <tr class="headers">
                <th>ID</th>
                <th>NOMBRE</th>
                <th>APELLIDO P</th>
                <th>APELLIDO M</th>
                <th>USUARIO</th>
                <th>PRIVILEGIO</th>
                <th>OPCIONES</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // $listado es una variable asignada desde el controlador SegmentsController.		
        $session->orig_timestamp = microtime(true);
        while($item = $listado->fetch(PDO::FETCH_ASSOC))
        {
        ?>
        <tr>
            <td><?php echo $item['ID'];?></td>
            <td><?php echo $item['NOMBRE'];?></td>
            <td><?php echo $item['APELLIDOP'];?></td>
            <td><?php echo $item['APELLIDOM'];?></td>
            <td><?php echo $item['USUARIO'];?></td>
            <td><?php echo $item['NAME_PRIVILEGIO'];?></td>
            <td class="td_options">
                <a id="imgEdit" href="<?php echo $rootPath.'?controller=admin&amp;action=userEditForm&amp;id_user='.$item['ID'];?>">
                    <img alt="editar" src="views/img/edit-icon.png" />
                </a>
                <?php if($session->id != $item['ID'] && $item['USUARIO'] != "administrador"): ?>
                &nbsp;
                <a id="<?php echo $item['ID'];?>" class="linkDelete" href="<?php echo $rootPath.'?controller=admin&amp;action=userDelete&amp;id_user='.$item['ID'].'&amp;form_timestamp='.$session->orig_timestamp;?>">
                    <img alt="eliminar" src="views/img/delete-icon.png" />
                </a>
                <?php endif; ?>
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
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>