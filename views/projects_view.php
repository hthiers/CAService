<?php
require('templates/header.tpl.php'); #session & header

#session
#if($session->id != null):

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
    #dt_filtres input.time_control {
        width: 80px;
        height: 30px;
    }
    #dt_filtres input.time_status {
        margin-top: 10px;
        height: 30px;
        width: 250px;
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
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $('.input_box').attr('disabled', 'disabled');
        $('#btn_play').attr('disabled', 'disabled');
        
        $("#btn_play").click(function (event){
            iniTrabajo();
        });
        
         $("#btn_pause").click(function (event){
            pausaTrabajo();
        });
        
        $("#btn_stop").click(function (event){
           window.location.replace("<?php echo $rootPath;?>?controller=Projects&action=projectsDt"); 
        });
    });

    function iniTrabajo(){
        $('#btn_play').attr('disabled', 'disabled');
        $('#btn_pause').removeAttr('disabled');
    }

    function pausaTrabajo(){
        $('#btn_play').removeAttr('disabled');
        $('#btn_pause').attr('disabled', 'disabled');
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
            print_r($titulo); print('<br />');
            print_r($pdo); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a revisar un trabajo existente que seg&uacute;n su estado activo o finalizado, podr&iacute;a ser
            pausado o terminado. En este caso aparece un trabajo activo con los campos bloqueados y los botones de pausa y termino disponibles.
        </p>
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>

        <div id="dt_filtres">
            
            <?php if(isset($pdo)): $values = $pdo->fetch(PDO::FETCH_ASSOC); ?>
            <form>
                <table class="table_left">
                    <tr>
                        <td class="middle">Responsable</td>
                        <td class="middle"><input readonly="readonly" class="input_box" name="resp" type="text" value="<?php echo $values['name_user']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="middle">Cliente</td>
                        <td class="middle"><input readonly="readonly" class="input_box" name="cliente" type="text" value="Cliente A" /></td>
                    </tr>
                    <tr>
                        <td>Descripci&oacute;n</td>
                        <td>
                            <textarea readonly="readonly" class="input_box" name="descripcion"></textarea>
                        </td>
                    </tr>
                </table>
                <table class="table_right">
                    <tr>
                        <td class="middle">Fecha inicio</td>
                        <td class="middle"><input readonly="readonly" class="input_box" name="fecha_ini" type="text" value="06/05/2012" /></td>
                    </tr>
                    <tr>
                        <td class="middle">Hora inicio</td>
                        <td class="middle"><input readonly="readonly" class="input_box" name="hora_ini" type="text" value="10:00" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">Control de tiempo 
                            <br /><br />
                            <input id="btn_play" class="time_control" type="button" value="INICIO" />
                            <input id="btn_pause" class="time_control" type="button" value="PAUSA" />
                            <input id="btn_stop" class="time_control" type="button" value="TERMINAR" />
                            <!--
                            <br />
                            <input type="text" class="time_status" value="tiempo..." />
                            -->
                        </td>
                    </tr>
                </table>
                <div style="clear: both;"></div>
            </form>
            <?php
            else:
                echo "<h4>Ha ocurrido un error grave</h4>";
            endif;
            ?>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
#endif; #session
require('templates/footer.tpl.php');
?>