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
    var status = <?php echo $status_project; ?>;

    // Set timer
    if(total_progress > 0){
        var tiempo_array = secondsToTime(total_progress);
        var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];
        
        if(status == 1)
            customClock(tiempo_string);
        else if(status == 3){
            var paused_seconds = <?php if($paused_date == null): echo 0; else: echo $paused_date; endif;?>;
            console.log(paused_seconds);
            var paused_array = secondsToTime(paused_seconds);
            var paused_string = paused_array['h']+':'+paused_array['m']+':'+paused_array['s'];
            $('#progress_clock').val(paused_string);
        }
    }

    // JQDialog open link
    $( "#link-dialog" ).click(function() {
        $( "#dialog-projectTask" ).dialog( "open" );
    });

    // JQDialog Submit - Add new task to project
    $(".dlgSbmCstr").click(function(){
        var label = $("#dlgSbm_name_task").val();
        var desc = $("#dlgSbm_desc_task").val();
        var id_project = <?php echo $id_project;?>;
        if(label=='')
        {
            alert("Debe ingresar un nombre");
        }
        else
        {
            //$("#flash").show();
            //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
            $.ajax({
                type: "POST",
                url: "?controller=projects&action=ajaxProjectsAddTask",
                data: {label:label, desc:desc, id_project:id_project},
                cache: false,
                dataType: "json"
            }).done(function(response){
                if(response != null){
                    if(response[0] != 0){
//                            console.log('resp:'+response[0]+', '+response[1]);
//                            console.log(response);
                        window.location.replace("?controller=projects&action=projectsView&id_project=<?php echo $id_project;?>");
//                            location.reload();
//                            $("#cbocustomers").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');
                        //$("#flash").hide();
//                            alert("Tarea agregada!");
                    }
                    else
                        alert("Error: "+response[1]);
                }
                else{
//                        console.log(response);
                    alert("Ha ocurrido un error!..."+response);
                    $("#dialog-form").dialog("close");
                }
                $("#dialog-form").dialog("close");
            }).fail(function(){
                alert("Ha ocurrido un error!");
                $("#dialog-form").dialog("close");
            });
        }

        return false;
    });
});

function iniTrabajo(){
    $('#btn_play').attr('disabled', 'disabled');
    $('#btn_pause').removeAttr('disabled');
    
    var id_project = "<?php echo $id_project;?>";
    
    $.ajax({
        type: "POST",
        url: "?controller=projects&action=projectsContinue",
        data: {id_project:id_project},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            console.log(response);
            if(response[0] === "0"){
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');

//                alert("Trabajo activado!");
                window.location.replace("?controller=projects&action=projectsView&id_project=<?php echo $id_project;?>");
            }
            else{
                alert("sql error");
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
    var id_project = "<?php echo $id_project;?>";

    $.ajax({
        type: "POST",
        url: "?controller=projects&action=projectsPause",
        data: {id_project:id_project},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            console.log(response);
            if(response[0] === "0"){
//                $("#flash").hide();
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');

                window.location.replace("?controller=projects&action=projectsView&id_project=<?php echo $id_project;?>");
            }
            else{
                alert("sql error");
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
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <?php #if(isset($pdo)): $values = $pdo->fetch(PDO::FETCH_ASSOC); ?>
        
        <p class="titulos-form"><?php echo $titulo.$code_project; ?></p>

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
                <form id="formModule" name="formModule" method="post" action="?controller=Projects&action=projectsStop">
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
                                <input type="text" class="input_box" readonly="readonly" name="etiqueta" value="<?php echo $label_project; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Descripci&oacute;n</td>
                            <td>
                                <textarea readonly="readonly" class="input_box" name="descripcion"><?php echo $desc_project;?></textarea>
                            </td>
                        </tr>
                    </table>
                    <table class="table_right">
                        <tr>
                            <td class="middle">Fecha inicio</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="fecha_ini" type="text" value="<?php echo $date_ini; ?>" /></td>
                        </tr>
                        <?php if($status_project == 1 && strtotime($currentTime) > strtotime($date_ini)): ?>
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
                                <input id="btn_stop" class="time_control" type="button" value="TERMINAR" />
                            </td>
                        </tr>
                        <?php elseif($status_project == 1 && strtotime($currentTime) < strtotime($date_ini)):?>
                        <tr>
                            <td colspan="2" style="text-align: center;">Control de tiempo 
                                <br /><br />
                                <input id="btn_play" class="time_control" type="button" value="INICIO" />
                                <input id="btn_pause" class="time_control" type="button" value="PAUSA" disabled="disabled" />
                                <input id="btn_stop" class="time_control" type="button" value="TERMINAR" disabled="disabled" />
                            </td>
                        </tr>
                        <?php elseif($status_project == 3 && strtotime($currentTime) > strtotime($date_ini)):?>
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
                                <input id="btn_stop" class="time_control" type="button" value="TERMINAR" />
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
                        <input type="hidden" name="id_project" value="<?php echo $id_project; ?>" />
                    </div>
                </form>
            </div>
            <div id="project-tasks-box" style="margin-top:10px">
                <table style="float:none;width:100%;border-top:1px solid #ccc;">
                    <tr>
                        <td colspan="6" style="text-align: center;">Lista de tareas asignadas</td>
                    </tr>
                    <?php if($status_project == 1):?>
                    <tr>
                        <td colspan="6" style="text-align: center;"><a id="link-dialog" href="#">Nueva</a></td>
                    </tr>
                    <?php endif;?>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <?php 
                        if($tasksList != null && $tasksList->rowCount() > 0){
                            echo "
                            <tr>
                                <td style='font-weight:bold;'>Etiqueta</td>
                                <td style='font-weight:bold;'>Descripci&oacute;n</td>
                                <td style='font-weight:bold;'>Fecha inicio</td>
                                <td style='font-weight:bold;'>Fecha fin</td>
                                <td style='font-weight:bold;'>Tiempo total</td>
                                <td>&nbsp;</td>
                            </tr>";
                            
                            while($item = $tasksList->fetch(PDO::FETCH_ASSOC)){
                                echo "<tr>";
                                echo "<td>".$item['label_task']."</td>\n";
                                echo "<td>".$item['desc_task']."</td>\n";
                                echo "<td>".$item['date_ini']."</td>\n";
                                echo "<td>".$item['date_end']."</td>\n";
                                echo "<td>".Utils::formatTime($item['time_total'])."</td>\n";
                                
                                $actionLink = "";
                                $actionUrl = "";
                                if($item['status_task'] == 1){
                                    $actionLink = "terminar";
                                    $actionUrl = "?controller=tasks&amp;action=tasksStop&amp;task=".$item['id_task']."&amp;project=".$id_project;
                                }
                                
                                echo "<td style='text-align:left;'><a href='".$actionUrl."'>".$actionLink."</a></td>\n";
                                echo "</tr>";
                            }
                        }
                        elseif($tasksList->rowCount() < 1)
                            echo "<td colspan='5' style='text-align:center;'>No hay tareas asignadas a este proyecto.</td>";
                        else
                            echo "<td colspan='5'>Ha ocurrido un error!</td>";
                        ?>
                    </tr>
                </table>
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