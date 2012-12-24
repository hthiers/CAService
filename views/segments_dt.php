<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio > 0):
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
                "oTableTools": {
                    "aButtons": [ "xls", "csv" ],
                    "sSwfPath": "views/media/swf/copy_csv_xls_pdf.swf"
                },
                "oLanguage": {
                    "sInfo": "_TOTAL_ records",
                    "sInfoEmpty": "0 records",
                    "sInfoFiltered": "(from _MAX_ total records)"
                },
                "aoColumnDefs": [{
                        "bSortable": false, "aTargets": [3]
                        <?php if($permiso_editar == 0){ echo ',"bVisible": false, "aTargets": [3]';}?>
                }],
                "sPaginationType": "full_numbers",
                "aaSorting": [[1, "asc"]]
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

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print_r($permiso_editar); print('<br />');
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
                <th>COD SEGMENT</th>
                <th>NAME SEGMENT</th>
                <th>GBU</th>
                <th>OPTIONS</th>
            </tr>
        </thead>
        <tbody>
        <?php
        while($item = $listado->fetch(PDO::FETCH_ASSOC))
        {
        ?>
        <tr>
            <td><?php echo $item['COD_SEGMENT'];?></td>
            <td><?php echo $item['NAME_SEGMENT'];?></td>
            <td><?php echo $item['GBU_NAME_GBU'];?></td>
            <td>
                <form method="post"  action="<?php echo $rootPath.'?controller=segments&amp;action=segmentsEditForm';?>">
                    <?php 
                    echo "<input name='txtcodigo' type='hidden' value='$item[COD_SEGMENT]' />\n";
                    echo "<input name='txtnombre' type='hidden' value='$item[NAME_SEGMENT]' />\n";
                    echo "<input name='txtgbu' type='hidden' value='$item[GBU_COD_GBU]' />\n";

                    echo "<input class='input' type='submit' value='EDITAR' />\n";
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
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>