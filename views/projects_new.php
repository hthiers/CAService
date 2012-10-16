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
<script type="text/javascript" charset="utf-8">
    var windowSizeArray = [ "width=200,height=200","width=300,height=400,scrollbars=yes" ];
    
    $(document).ready(function(){
        var myDate = new Date();
        var displayDate = myDate.getFullYear() + '/' + (myDate.getMonth()+1) + '/' + (myDate.getDate());
        var outStr = myDate.getHours()+':'+myDate.getMinutes()
       
        $("#hora_ini").val(outStr);
        $("#hdnPicker").val(displayDate);
        
        // pop-ups en caso de ser necesario
        $('#pop_newcliente').click(function (event){
            var url = $(this).attr("href");
            var windowName = "popUp";//$(this).attr("name");
            var windowSize = windowSizeArray;

            window.open(url, windowName, windowSize);

            event.preventDefault();
        });
        
        $("#btn_play").click(function (event){
            iniTrabajo();
        });
        $('#btn_pause').attr('disabled', 'disabled');
        
        $("#btn_pause").click(function (event){
            pausaTrabajo();
        });
        
        $("#btn_stop").click(function (event){
           window.location.replace("<?php echo $rootPath;?>?controller=projects&action=projectsDt");
        });
        $('#btn_stop').attr('disabled', 'disabled');
    });
    
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
    
    function pausaTrabajo(){
        $('#btn_play').removeAttr('disabled');
        $('#btn_pause').attr('disabled', 'disabled');
    }
    
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
                modal: true,
                buttons: {
                    "Crear cliente": function() {
                        var bValid = false;
                        allFields.removeClass( "ui-state-error" );

                        bValid = bValid && checkLength( name, "username", 3, 16 );
                        bValid = bValid && checkLength( email, "email", 6, 80 );

                        bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
                        // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
                        bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );

                        if ( bValid ) {
                            $( "#users tbody" ).append( "<tr>" +
                                    "<td>" + name.val() + "</td>" + 
                                    "<td>" + email.val() + "</td>" +
                            "</tr>" ); 
                            $( this ).dialog( "close" );
                        }
                    },
                    "Cancelar": function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                        allFields.val( "" ).removeClass( "ui-state-error" );
                }
        });

        $( "#create-user" )
            .click(function() {
                $( "#dialog-form" ).dialog( "open" );
            });
    });
</script>

</head>
<body id="dt_example" class="ex_highlight_row">

    <div id="dialog-form" title="Nuevo Cliente">
        <form>
            <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="name">Nombre</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
            <label for="email">Encargado</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
        </form>
    </div>
    
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
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <p style="font-size: 12px; color: #999;">Nota: Esta pantalla permitir&iacute;a crear un
            nuevo registro de trabajo que tras hacer clic en el boton "INICIO" bajo el control de tiempo, guardar&iacute;a la fecha y la hora en
            que fue creado. Se puede notar que tras presionar el boton de inicio se bloquean los campos anteriores.
            El bot&oacute;n de "PAUSA" permite ignorar el tiempo durante el cual el registro permanece en pausa. Para terminar
            el trabajo habr&iacute;a que presionar el boton "TERMINAR" de color rojo, registrando el momento en que finalizó la tarea.
            El campo "responsable" corresponde al usuario en sesi&oacute;n sin posibilidad de alterar este valor.
        </p>
        
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
                                <!--<button id="create-user">Nuevo</button>-->
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
                            <input id="btn_pause" class="time_control" type="button" value="PAUSA" />
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