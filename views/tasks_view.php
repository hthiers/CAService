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
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var current_time = "";
    
    $('.input_box').attr('disabled', 'disabled');
//    $('#btn_play').attr('disabled', 'disabled');

    $("#btn_play").click(function (event){
        iniTrabajo();
    });

     $("#btn_pause").click(function (event){
        pausaTrabajo();
    });

    $("#btn_stop").click(function (event){
        //$("#formModule").attr("action", "?controller=Projects&action=projectsStop");
        //window.location.replace("<?php #echo $rootPath;?>?controller=Projects&action=projectsDt"); 
        $('#formModule').submit();
    });

    var total_db = <?php if($time_total == null): echo 0; else: echo $time_total; endif; ?>;

    if(total_db > 0){
        var tiempo_array = secondsToTime(total_db);
        var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];

        $("#inptTiempoTotal").val(tiempo_string);
    }

    var total_progress = <?php if($total_progress == null): echo 0; else: echo $total_progress; endif;?>;
    var status = <?php echo $status_task; ?>;

    // Set timer
    if(total_progress > 0){
        //active
        if(status === 1){
            console.log("continued");
            console.log("$pasued_date: <?php echo $paused_date;?>");
            console.log("$time_paused: <?php echo $time_paused;?>");
            
            var tiempo_array = secondsToTime(total_progress);
            var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];
            customClock(tiempo_string);
        }
        //paused
        else if(status === 3){
            console.log("paused");
            console.log("$pasued_date: <?php echo $paused_date;?>");
            console.log("$time_paused: <?php echo $time_paused;?>");
            
            var paused_seconds = <?php if($paused_date == null): echo 0; else: echo $paused_date; endif;?>;
            console.log(paused_seconds);
            
            var paused_array = secondsToTime(paused_seconds);
            var paused_string = paused_array['h']+':'+paused_array['m']+':'+paused_array['s'];
            $('#progress_clock').val(paused_string);
        }
    }
});

function iniTrabajo(){
    $('#btn_play').attr('disabled', 'disabled');
    $('#btn_pause').removeAttr('disabled');
    
    var id_task = "<?php echo $id_task;?>";
    
    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksContinue",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            console.log(response);
            if(response[0] === "0"){
                $('#btn_pause').removeAttr('disabled');
                $('#btn_play').attr('disabled', 'disabled');

                console.log("Trabajo activado!");
                current_time = $('#progress_clock').val();
                customClock(current_time);
            }
            else{
                alert("sql error: "+response[1]);
            }
        }
        else{
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}

function pausaTrabajo(){
    var id_task = "<?php echo $id_task;?>";

    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksPause",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            console.log(response);
            if(response[0] === "0"){
//                $("#flash").hide();
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');
                
                console.log("task paused!");
                current_time = $('#progress_clock').val();
                clearTimeout(timeout);
            }
            else{
                alert("sql error");
            }
        }
        else{
            console.log(response);
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}
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
                                  
            print(strtotime($date_ini));print('<br />');
            print(strtotime($currentTime));print('<br />');
            print($total_progress);print('<br />');
            print("paused_date: ".$paused_date);print('<br />');
            
            print('<br />'); print("system: ".$system_message); print('<br />');
            
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <?php #if(isset($pdo)): $values = $pdo->fetch(PDO::FETCH_ASSOC); ?>
        
        <p class="titulos-form"><?php echo $titulo.$code_task; ?></p>

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
                <?php if($date_end == null && strtotime($currentTime) > strtotime($date_ini)): ?>
                <form id="formModule" name="formModule" method="post" action="?controller=tasks&action=tasksStop">
                <?php else: ?>
                <form>
                <?php endif; ?>
                    <table class="table_left">
                        <tr>
                            <td class="middle">Responsable</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="resp" type="text" value="<?php echo $name_user; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Cliente</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="cliente" type="text" value="<?php echo $label_customer; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Etiqueta</td>
                            <td class="middle">
                                <input type="text" class="input_box" readonly="readonly" name="etiqueta" value="<?php echo $label_task; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Descripci&oacute;n</td>
                            <td>
                                <textarea readonly="readonly" class="input_box" name="descripcion"><?php echo $desc_task;?></textarea>
                            </td>
                        </tr>
                    </table>
                    <table class="table_right">
                        <tr>
                            <td class="middle">Fecha inicio</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="fecha_ini" type="text" value="<?php echo $date_ini; ?>" /></td>
                        </tr>
                        <?php if($status_task == 1 && strtotime($currentTime) > strtotime($date_ini)): ?>
                        <tr>
                            <td class="middle">Tiempo transcurrido</td>
                            <td class="middle">
                                <input id="progress_clock" readonly="readonly" class="input_box" name="tiempo_progress" type="text" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">Control de tiempo 
                                <br /><br />
                                <input id="btn_play" class="time_control" type="button" value="INICIO" disabled="disabled" />
                                <input id="btn_pause" class="time_control" type="button" value="PAUSA" />
                                <!--<input id="btn_stop" class="time_control" type="button" value="TERMINAR" />-->
                            </td>
                        </tr>
                        <?php elseif($status_task == 1 && strtotime($currentTime) < strtotime($date_ini)):?>
                        <tr>
                            <td colspan="2" style="text-align: center;">Control de tiempo 
                                <br /><br />
                                <input id="btn_play" class="time_control" type="button" value="INICIO" />
                                <input id="btn_pause" class="time_control" type="button" value="PAUSA" disabled="disabled" />
                                <!--<input id="btn_stop" class="time_control" type="button" value="TERMINAR" disabled="disabled" />-->
                            </td>
                        </tr>
                        <?php elseif($status_task == 3 && strtotime($currentTime) > strtotime($date_ini)):?>
                        <tr>
                            <td class="middle">Tiempo transcurrido</td>
                            <td class="middle">
                                <input id="progress_clock" readonly="readonly" class="input_box" name="tiempo_progress" type="text" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">Control de tiempo 
                                <br /><br />
                                <input id="btn_play" class="time_control" type="button" value="INICIO" />
                                <input id="btn_pause" class="time_control" type="button" value="PAUSA" disabled="disabled" />
                                <!--<input id="btn_stop" class="time_control" type="button" value="TERMINAR" />-->
                            </td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td class="middle">Fecha fin</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="fecha_fin" type="text" value="<?php echo $date_end; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Tiempo total</td>
                            <td class="middle">
                                <input id="inptTiempoTotal" readonly="readonly" class="input_box" name="tiempo_total" type="text" value="" />

                                <input type="hidden" id="time_total_s" name="time_total_s" value="<?php echo $time_s; ?>" />
                                <input type="hidden" id="time_total_m" name="time_total_m" value="<?php echo $time_m; ?>" />
                                <input type="hidden" id="time_total_h" name="time_total_h" value="<?php echo $time_h; ?>" />
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <div style="clear: both;">
                        <input type="hidden" name="id_task" value="<?php echo $id_task; ?>" />
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