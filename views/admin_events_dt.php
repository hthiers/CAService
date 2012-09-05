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
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/ColReorder.min.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
        //Datatable
        var oTable = $('#example').dataTable({
            //Initial server side params
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": <?php echo "'".$rootPath."?controller=admin&action=ajaxEventsDt'";?>,
            "fnServerData": function ( sSource, aoData, fnDrawCallback ) {
                $.ajax({
                    "dataType": 'json', 
                    "type": "GET", 
                    "url": sSource, 
                    "data": aoData, 
                    "success": fnDrawCallback
                });
            },

            //DOM mod params
            "sDom": '<"top"lpfi>rt<"clear">',
            "oLanguage": {
                "sInfo": "_TOTAL_ records",
                "sInfoEmpty": "0 records",
                "sInfoFiltered": "(from _MAX_ total records)"
            },

            //Col params
            "aaSorting": [[1,'desc']],
            "bAutoWidth": false,
            "aoColumnDefs": [
                {
                    "fnRender": function ( oObj ) {
                        if(oObj.aData[2] == null)
                            return oObj.aData[2];
                        else
                            return oObj.aData[2].substring(0,55);
                        },
                        "aTargets": [2]
                },
                { "sWidth": "5%", "aTargets": [ 0 ] },
                { "sWidth": "20%", "aTargets": [ 1 ] },
                { "sWidth": "45%", "aTargets": [ 2 ] },
                { "sWidth": "10%", "aTargets": [ 3 ] },
                { "sWidth": "10%", "aTargets": [ 4 ] },
                { "sWidth": "10%", "aTargets": [ 5 ] }
            ],
            "sPaginationType": "full_numbers"
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
                    print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
                    print('</div>');
            }
            ?>
            <!-- END DEBUG -->

            <p class="titulos-form"><?php echo $titulo; ?></p>

            <!-- ERRORES -->
            <?php
            if (isset($error_flag))
                    if(strlen($error_flag) > 0)
                            echo $error_flag;
            ?>
            <!-- END ERRORES -->

            <!-- DATATABLE -->
            <div id="dynamic">
            <table class="display" id="example">
                <thead>
                    <tr class="headers">
                        <th>USUARIO</th>
                        <th>FECHA</th>
                        <th>QUERY</th>
                        <th>IP</th>
                        <th>PC-NAME</th>
                        <th>MODULO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10" class="dataTables_empty">Loading data from server</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <!-- END DATATABLE -->

            <div class="spacer"></div>

    	</div>
	</div>
	<!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>