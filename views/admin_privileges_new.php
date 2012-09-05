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
<script language="javascript">
$(document).ready(function(){
    //uppercase obligatorio
    $('input').focusout(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });
    $('textarea').focusout(function(){
        $(this).val(function( i, val ) {
            return val.toUpperCase();
        });
    });
    
    $("#modulesForm").validate({
        debug: false,
        rules: {
            name_privilegio: {
                required: true,
                minlength: 3,
                remote: {
                    url: <?php echo "'".$rootPath."?controller=admin&action=verifyNamePrivilege'";?>,
                    type: "POST",
                    data: {
                        txtnombre: function() {
                            return $("#inptname").val();
                        }
                    }
                }
            }
        },
        messages: {
            name_privilegio: {required: "Campo requerido.", minlength: "Ingresa al menos 3 caracteres", remote: "Nombre de usuario ya existe"}
        }
    });
    
    function selectAllVer(status, target){
        $("input.chk_"+target).each(function(index, item){
            $(item).attr("checked", status);
        });
    }
    
    $('#chkall_ver').change(function() {
        if($('#chkall_ver').is(':checked'))
            selectAllVer(true, "ver");
        else
            selectAllVer(false, "ver");
    });
    
    $('#chkall_agregar').change(function() {
        if($('#chkall_agregar').is(':checked'))
            selectAllVer(true, "agregar");
        else
            selectAllVer(false, "agregar");
    });
    
    $('#chkall_editar').change(function() {
        if($('#chkall_editar').is(':checked'))
            selectAllVer(true, "editar");
        else
            selectAllVer(false, "editar");
    });
});
</script>

</head>
<body>

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
                print_r($titulo);
                print("<br />");
                print_r($new_code);print("<br />");
                print_r($controller);print("<br />");print_r($action);print("<br />");
                print_r($action_b);print("<br />");
                print_r($lista_modulos);print("<br />");
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
            
            <form id="modulesForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
              <table id="normaltable">
                <thead>
                    <tr class="headers">
                        <th colspan="2">DATOS NUEVO PERFIL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>C&Oacute;DIGO</td>
                        <td>
                            <input name="cod_privilegio" type="text" id="inptcode" size="5" value="<?php echo $new_code; ?>" readonly="readonly" />
                        </td>
                    </tr>
                    <tr>
                        <td>NOMBRE</td>
                        <td>
                            <input name="name_privilegio" type="text" id="inptname" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td class="headers" colspan="2" style="text-align: center; color: #000000;">PRIVILEGIOS DE ACCESO</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="options">
                            <input type="checkbox" id="chkall_ver" name="chkall_ver" /> todo
                            <input type="checkbox" id="chkall_agregar" name="chkall_agregar" /> todo
                            <input type="checkbox" id="chkall_editar" name="chkall_editar" /> todo
                        </td>
                    </tr>
                    <?php 
                    while($row = $lista_modulos->fetch(PDO::FETCH_ASSOC))
                    {
                        if($row['NAME_MODULO'] == 'mimenu' || $row['NAME_MODULO'] == 'mimenuuser')
                        {
                            echo "<tr>";
                            echo "<td>".$row['LABEL_MODULO']."</td>";
                            echo "<td class='options'> \n";
                                if($row['NAME_MODULO'] == 'mimenuuser')
                                    echo "<input type='radio' id='radio_".$row['NAME_MODULO']."' name='radio_menu' value='".$row['COD_MODULO']."' checked='checked' />";
                                else
                                    echo "<input type='radio' id='radio_".$row['NAME_MODULO']."' name='radio_menu' value='".$row['COD_MODULO']."' />";
                            echo "</td>";
                            echo "</tr>";
                        }
                        elseif($row['NAME_MODULO'] == 'exportar')
                        {
                            echo "<tr>";
                            echo "<td>".$row['LABEL_MODULO']."</td>";
                            echo "<td class='options'> \n";
                                echo "<input type='checkbox' id='chk_".$row['NAME_MODULO']."' name='chk_".$row['NAME_MODULO']."' value='".$row['COD_MODULO']."' />";
                            echo "</td>";
                            echo "</tr>";
                        }
                        else
                        {
                            echo "<tr>";
                            echo "<td>".$row['LABEL_MODULO']."</td>";
                            echo "<td class='options'> \n";
                                echo "<input type='checkbox' class='chk_ver' title='solo vista' id='optver_".$row['NAME_MODULO']."' name='chkver_".$row['NAME_MODULO']."' /> ver"; 
                                echo "<input type='checkbox' class='chk_agregar' title='agregar nuevos registros' id='optescribir_".$row['NAME_MODULO']."' name='chkescribir_".$row['NAME_MODULO']."' /> agregar";
                                echo "<input type='checkbox' class='chk_editar' title='editar registros desde tablas' id='opteditar_".$row['NAME_MODULO']."' name='chkeditar_".$row['NAME_MODULO']."' /> editar";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="2" class="centered_buttons">
                            <?php $session->orig_timestamp = microtime(true); ?>
                            <input type="hidden" name="form_timestamp" value="<?php echo $session->orig_timestamp; ?>" />
                            <input class="input" type="submit" value="Guardar">
                            &nbsp;&nbsp;
                            <input class="input" type="reset" value="Limpiar">
                        </td>
                    </tr>
                </tbody>
              </table>
            </form>
        
    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>