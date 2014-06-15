<?php
require('templates/header_clean.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<link rel="stylesheet" href="views/css/bootstrap.min.css">
<link rel="stylesheet" href="views/css/custom.css">
<link rel="stylesheet" href="views/css/dataTables.bootstrap.css">

<script type="text/javascript" src="views/lib/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="views/lib/jquery.dataTables.1.10.0.js"></script>
<script type="text/javascript" src="views/lib/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var oTable = $('#tasksData').DataTable({
        serverSide: true,
        ajax: {
            url: '?controller=tasks&action=ajaxTasksDt',
            type: 'GET'
        },
        columnDefs: [
            {visible: false, tagets: [7,8,9,10]}
        ]
    });
    
    /*oTable.column(7).visible(false);
    oTable.column(8).visible(false);
    oTable.column(9).visible(false);
    oTable.column(10).visible(false);*/
    
    /*oTable.column(11).data();*/
    
    
    //$('#cboCliente').change(function() { oTable.fnDraw(); } );
    //$('#cboMes').change(function() { oTable.fnDraw(); } );
    //$('#cboEstado').change(function() { oTable.fnDraw(); } );
    
    // form submition handling
    $('form').submit( function() {
        var sData = oTable.$('input').serialize();
        var actionType = $('#action_type').val();
        var urlAction = "";
        
        if(actionType == 'edit_form'){
            urlAction = "<?php echo "?controller=".$controller."&amp;action=".$action;?>";
            $('#action_type').val("");
            
            return true;
        }
    });
});

//Getting needed value from dt row
function fnFormatDetails (oTable, nTr){
    var aData = oTable.fnGetData( nTr );
    return aData[6];
}

function submitToForm(){
    $('#action_type').val("view");

    return true;
}
</script>

</head>
<body>
    
    <?php
    require('templates/navbar_clean.tpl.php'); #banner & menu
    ?>
    
    <!-- CENTRAL -->
    <div class="container">
        
        <?php
        require('templates/menu_clean.tpl.php'); #banner & menu
        ?>

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

        <h4><?php echo $titulo; ?></h4>
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>
        
        <!-- DATATABLE -->
            <form method="POST" action="<?php echo "?controller=".$controller."&amp;action=".$action;?>">
                <table id="tasksData" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ETIQUETA</th>
                            <th>CLIENTE</th>
                            <th>RESPONSABLE</th>
                            <th>PROYECTO</th>
                            <th>INICIO</th>
                            <th>FIN</th>
                            <th>TIEMPO</th>
                            <th>ID TASK</th>
                            <th>ID TENANT</th>
                            <th>ID PROJECT</th>
                            <th>ID CUSTOMER</th>
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
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer_clean.tpl.php');
?>