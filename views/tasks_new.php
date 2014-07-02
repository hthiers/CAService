<?php
require('templates/header.tpl.php'); #session & header

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
    // JQDialog window
    var windowSizeArray = [ "width=200,height=200","width=300,height=400,scrollbars=yes" ];
    
    $(document).ready(function(){
        //var myDate = new Date();
        //var displayDate = myDate.getFullYear() + '/' + (myDate.getMonth()+1) + '/' + (myDate.getDate());
        //var outStr = myDate.getHours()+':'+myDate.getMinutes()
       
        var displayDate = "<?php echo $current_date; ?>";
        var outStr = "<?php echo $current_time; ?>";
       
        $("#hora_ini").val(outStr);
        $("#hdnPicker").val(displayDate);
        
        // Btn play
        $("#btn_play").click(function (event){
            iniTrabajo();
        });
        
        // Btn pause
        $('#btn_pause').attr('disabled', 'disabled');
        $("#btn_pause").click(function (event){
            pausaTrabajo();
        });
        
        // Btn submit stop
        $("#btn_stop").click(function (event){
           window.location.replace("<?php echo $rootPath;?>?controller=tasks&action=tasksDt");
        });
        $('#btn_stop').attr('disabled', 'disabled');
                
        // JQDialog Submit - Add new project
        $(".dlgSbmCstr").click(function(){
            var name = $("#dlgSbm_name_project").val();
            var desc = $("#dlgSbm_desc_project").val();
            //var dataString = 'name='+ name + '&desc=' + desc;
            if(name == '')
            {
                alert("Ingrese nombre del proyecto");
            }
            else
            {
                //$("#flash").show();
                //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
                $.ajax({
                    type: "POST",
                    url: "?controller=projects&action=ajaxProjectsAdd",
                    data: {name:name, desc:desc},
                    cache: false,
                    dataType: "json"
                }).done(function(response){
                    if(response != null){
                        if(response[0] != 0){
                            $("#cboprojects").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');       
                            //$("#flash").hide();
                            console.log(response);
                            alert("Proyecto agregado!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#dialog-new-project").dialog("close");
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});
    });
    
    // JQDatepicker
    /*$(function() {
        $.datepicker.regional['es'] = {
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sábado'],
            dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $( "#datepicker" ).datepicker({
            firstDay: 1,
            dateFormat: "yy/mm/dd",
            onSelect: function(date, picker){
                $("#hdnPicker").val(date);
            }
        });
    });*/
    
    // Func submit new project
    function iniTrabajo(){
        $('.input_box').attr('readonly', true);
        $('#datepicker').datepicker().datepicker('disable');
        //$('#trabajo_info').hide();
        //$('#trabajo_timing').css({"border-top": "none"});
        
        $('#btn_play').attr('disabled', 'disabled');
        $('#btn_pause').removeAttr('disabled');
        $('#btn_stop').removeAttr('disabled');
        
        $('#formModule').submit();
    }
    
    // Func pause project (count paused time to discount after)
    function pausaTrabajo(){
        $('#btn_play').removeAttr('disabled');
        $('#btn_pause').attr('disabled', 'disabled');
    }
    
    // JQDialog new project
    /*
    $(function() {
        // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        var name = $( "#name" ),
            desc = $( "#desc" ),
            allFields = $( [] ).add( name ).add( desc ),
            tips = $( ".validateTips" );

        function updateTips( t ) {
                tips
                        .text( t )
                        .addClass( "ui-state-highlight" );
                setTimeout(function() {
                        tips.removeClass( "ui-state-highlight", 1500 );
                }, 500 );
        }

        function checkLength( o, n, min, max ) {
                if ( o.val().length > max || o.val().length < min ) {
                        o.addClass( "ui-state-error" );
                        updateTips( "Length of " + n + " must be between " +
                                min + " and " + max + "." );
                        return false;
                } else {
                        return true;
                }
        }

        function checkRegexp( o, regexp, n ) {
                if ( !( regexp.test( o.val() ) ) ) {
                        o.addClass( "ui-state-error" );
                        updateTips( n );
                        return false;
                } else {
                        return true;
                }
        }

        $( "#dialog-new-project" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });

//        $( "#create-user" ).click(function() {
//                $( "#dialog-form" ).dialog( "open" );
//        });
        $( "#create-project" ).click(function() {
//            console.log("dialog para project.");
            $( "#dialog-new-project" ).dialog( "open" );
        });
    });*/
</script>

</head>
<body>
    
    <?php
    require('templates/navbar.tpl.php'); #banner & menu
    ?>

    <?php #require('templates/dialogs.tpl.php'); #session & header ?>
    
    <!-- CENTRAL -->
    <div class="container">
        
        <?php require('templates/menu.tpl.php'); ?>

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($titulo); print('<br />');
            print($current_date); print('<br />');
            print($current_time); print('<br />');
            
            if(isset($error)){
                print($error); 
                print('<br />');
            }
            
            print('tenant: ');
            print($session->id_tenant);print('<br />');
            print_r($pdoProject);print('<br />');

            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h4><?php echo $titulo; ?></h4>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a crear un
            nuevo registro de trabajo que tras hacer clic en el boton "INICIO" bajo el control de tiempo, guardar&iacute;a la fecha y la hora en
            que fue creado. Se puede notar que tras presionar el boton de inicio se bloquean los campos anteriores.
            El bot&oacute;n de "PAUSA" permite ignorar el tiempo durante el cual el registro permanece en pausa. Para terminar
            el trabajo habr&iacute;a que presionar el boton "TERMINAR" de color rojo, registrando el momento en que finalizó la tarea.
            El campo "responsable" corresponde al usuario en sesi&oacute;n sin posibilidad de alterar este valor.
        </p>
        -->
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>

        <div>
            <div>
                <label>Responsable</label>
                <input readonly="readonly" name="resp" type="text" value="<?php echo $name_user; ?>" /></td>
                <label>Proyecto</label>
                <?php
                echo "<select class='input_box' id='cboprojects' name='cboprojects'>\n";
                echo "<option value='noaplica' selected='selected'>Sin Proyecto</option>\n";
                while($row = $pdoProject->fetch(PDO::FETCH_ASSOC))
                {
                    echo "<option value='$row[id_project]'>$row[label_project]</option>\n";
                }
                echo "</select>\n";
                ?>
                &nbsp;
                <a id="create-project" href="#">Nuevo</a>
                <label>Etiqueta</label>
                <input type="text" name="etiqueta" />
                <label>Descripci&oacute;n</label>
                <textarea class="input_box" name="descripcion"></textarea>
            </div>

            <div>
                <label>Fecha inicio</label>
                <div id="datepicker"></div>
                <label>Hora inicio</label>
                <input id="hora_ini" name="hora_ini" type="text" value="" />
            </div>
            
            <div>
                <label>Control de tiempo</label>
                <input id="btn_play" type="button" value="INICIO" />
                <!--<input id="btn_pause" class="time_control" type="button" value="PAUSA" />-->
                <!--<input id="btn_stop" class="time_control" type="button" value="TERMINAR" />-->
                <!--
                <br />
                <input type="text" class="time_status" value="tiempo..." />
                -->
            </div>

            <div style="clear: both;">
                <input id="hdnPicker" type="hidden" name="fecha" value="" />
                <input id="hdnCode" type="hidden" name="new_code" value="<?php echo $new_code; ?>" />
                <input id="hdnUser" type="hidden" name="id_user" value="<?php echo $id_user; ?>" />
            </div>
                
        </div>

    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>