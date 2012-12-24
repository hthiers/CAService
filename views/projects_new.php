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
    input.input_box, textarea.input_box, select.input_box{
        border: 1px solid #989898;
        border-radius: 4px
    }
    #dt_filtres table {
        float: left;
    }
    #dt_filtres input, #dt_filtres textarea, #dt_filtres select {
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
    #datepicker {
        margin-left: 5px;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
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
           window.location.replace("<?php echo $rootPath;?>?controller=projects&action=projectsDt");
        });
        $('#btn_stop').attr('disabled', 'disabled');
        
        // JQDialog popup event
//        $('#pop_newcliente').click(function (event){
//            var url = $(this).attr("href");
//            var windowName = "popUp";//$(this).attr("name");
//            var windowSize = windowSizeArray;
//
//            window.open(url, windowName, windowSize);
//
//            event.preventDefault();
//        });
        
        // JQDialog Submit - Add new customer
        $(".dlgSbmCstr").click(function(){
            var name = $("#dlgSbm_name").val();
            var contact = $("#dlgSbm_contact").val();
            //var dataString = 'name='+ name + '&contact=' + contact;
            if(name=='')
            {
                alert("Please Enter Some Text");
            }
            else
            {
                //$("#flash").show();
                //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
                $.ajax({
                    type: "POST",
                    url: "?controller=customers&action=ajaxCustomersAdd",
                    data: {name:name, contact:contact},
                    cache: false,
                    dataType: "json"
                }).done(function(response){
                    if(response != null){
                        if(response[0] != 0){
                            $("#cbocustomers").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');       
                            //$("#flash").hide();
                            alert("Cliente agregado!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error!");
                    }
                    $("#dialog-form").dialog("close");
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});
    });
    
    // JQDatepicker
    $(function() {
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
    });
    
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
    
    // JQDialog new customer
    $(function() {
        // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        var name = $( "#name" ),
            email = $( "#email" ),
            allFields = $( [] ).add( name ).add( email ),
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

        $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });

        $( "#create-user" ).click(function() {
                $( "#dialog-form" ).dialog( "open" );
        });
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
            print_r($titulo); print('<br />');
            print($current_date); print('<br />');
            print($current_time); print('<br />');
            
            if(isset($error)){
                print($error); 
                print('<br />');
            }

            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

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

        <div id="dt_filtres">
            <form id="formModule" name="formModule" method="post" action="?controller=projects&amp;action=projectsAdd">
                <div id="trabajo_info" style="float: left;">
                    <table class="table_left">
                        <tr>
                            <td class="middle">Responsable</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="resp" type="text" value="<?php echo $name_user; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Cliente</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbocustomers' name='cbocustomer'>\n";
                                echo "<option value='' selected='selected'>SELECCIONAR</option>\n";
                                while($row = $pdoCustomer->fetch(PDO::FETCH_ASSOC))
                                {
                                    echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
                                }
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <a id="create-user" href="#">Nuevo</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Etiqueta</td>
                            <td class="middle">
                                <input type="text" class="input_box" name="etiqueta" />
                            </td>
                        </tr>
                        <tr>
                            <td>Descripci&oacute;n</td>
                            <td>
                                <textarea class="input_box" name="descripcion"></textarea>
                            </td>
                        </tr>
                    </table>
                    <table class="table_right">
                        <tr>
                            <td class="middle">Fecha inicio</td>
                            <td class="middle"><div id="datepicker"></div></td>
                        </tr>
                        <tr>
                            <td class="middle">Hora inicio</td>
                            <td class="middle"><input id="hora_ini" class="input_box" name="hora_ini" type="text" value="" /></td>
                        </tr>
                    </table>
                </div>
                <table id="trabajo_timing" style="float: none; width: 100%; border-top: 1px solid #CCC;">
                    <tr>
                        <td colspan="2" style="text-align: center;">Control de tiempo 
                            <br /><br />
                            <input id="btn_play" class="time_control" type="button" value="INICIO" />
                            <!--<input id="btn_pause" class="time_control" type="button" value="PAUSA" />-->
                            <input id="btn_stop" class="time_control" type="button" value="TERMINAR" />
                            <!--
                            <br />
                            <input type="text" class="time_status" value="tiempo..." />
                            -->
                        </td>
                    </tr>                    
                </table>
                <div style="clear: both;">
                    <input id="hdnPicker" type="hidden" name="fecha" value="" />
                    <input id="hdnCode" type="hidden" name="new_code" value="<?php echo $new_code; ?>" />
                </div>
            </form>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>