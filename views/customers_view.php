<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
    .table_left {}
    .table_right {
        margin-left: 70px;
    }
    td.middle {
        padding-bottom: 15px;
        text-align: left;
    }
    input.input_box, textarea.input_box{
        border: 1px solid #989898;
        border-radius: 4px
    }
    #dt_filtres table {
        float: left;
    }
    #dt_filtres input, #dt_filtres textarea {
        margin-left: 5px;
        width: 155px;
        height: 20px;
    }
    
    #dt_filtres textarea{
        width: 300px;
        height: 100px;
    }
    #dt_filtres td {
        text-align: left;
    }
    #dt_filtres {
        padding: 10px;
        /*height: 200px;*/
    }
    #btn_stop {
        border: 1px solid #989898;
        border-radius: 4px;
        background-color: orangered;
    }
    #btn_stop:active {
        background-color: brown;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var action_type = 0;
    action_type = <?php echo $action_type;?>;
    console.log("valor action " + action_type);
    
    if(action_type === 1){
        console.log("entre por 1");
        $('.input_box').attr('disabled', 'disabled');
        console.log("pasé por 1");
    }
    else if(action_type === 2){
        console.log("entre por 2");
        $('.input_box').removeAttr('disabled');
        $('.input_box').removeAttr('readonly');
        console.log("pasé por 2");
    }
//    alert(action_type);
});
</script>

</head>
<body id="dt_example" class="ex_highlight_row">

    <?php require('templates/dialogs.tpl.php'); #session & header ?>
    <?php require('templates/menu.tpl.php'); #banner & menu ?>
    
    <!-- CENTRAL -->
    <div id="central">
    <div id="contenido">

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print($titulo); print('<br />');
            print_r($pdo); print('<br />');
            print_r($label_customer); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <?php #if(isset($pdo)): $values = $pdo->fetch(PDO::FETCH_ASSOC); ?>
        
        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a revisar un trabajo existente que seg&uacute;n su estado activo o finalizado, podr&iacute;a ser
            pausado o terminado. En este caso aparece un trabajo activo con los campos bloqueados y los botones de pausa y termino disponibles.
        </p>
        -->

        <?php 
//        if (isset($error_flag)){
//            if(strlen($error_flag) > 0)
//                echo $error_flag;
//        }
        ?>

        <div id="dt_filtres">

            <div>
                <form id="formModule" name="formModule" method="post" action="">
                    <table class="table_left">
                        <tr>
                            <td class="middle">ID Cliente</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="id_customer" type="text" value="<?php echo $id_customer; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Código Cliente</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="code_customer" type="text" value="<?php echo $code_customer; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">ID Empresa</td>
                            <td class="middle">
                                <input type="text" class="input_box" readonly="readonly" name="id_tenant" value="<?php echo $id_tenant; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Nombre Cliente</td>
                            <td>
                                <input readonly="readonly" class="input_box" name="label_customer" value="<?php echo $label_customer;?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Descripción Cliente</td>
                            <td>
                                <textarea readonly="readonly" class="input_box" name="detail_customer"><?php echo $detail_customer;?></textarea>
                            </td>
                        </tr>
                        <?php if($action_type == 2) 
                            echo "
                                <tr>
                                    <td>
                                        <input type='submit' value='Editar' name='editar' />
                                    </td>
                                </tr>";
                        ?>
                    </table>
                    
                    <div style="clear: both;">
                        <input type="hidden" name="id_task" value="<?php echo "$id_task"; ?>" />
                    </div>
                </form>
            </div>
            <div id="project-tasks-box" style="margin-top:10px">
                <!--
                <table style="float:none;width:100%;border-top:1px solid #ccc;">
                    <tr>
                        <td colspan="6" style="text-align: center;">Lista de tareas asignadas</td>
                    </tr>
                    
                </table>
                -->
            </div>
            <?php
            #else:
            #    echo "<h4>Ha ocurrido un error grave</h4>";
            #endif;
            ?>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>