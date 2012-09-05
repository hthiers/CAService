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
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" charset="utf-8">
$(document).ready(function() {
    $('#inptprivilegio').val($('#cboprivilegios').val());

    $('#cboprivilegios').change( function() {
        $('#inptprivilegio').val($('#cboprivilegios').val());

        var privi_value = $('#cboprivilegios').val();
        var url = "<?php echo $rootPath.'?controller=admin&action=privilegiosPanel&privilegio=';?>"+privi_value;
        window.location.href = url;
    });

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

function selectAllVer(status, target){
    $("input.chk_"+target).each(function(index, item){
        $(item).attr("checked", status);
    });
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

        <?php require('templates/dialogs.tpl.php'); #session & header ?>

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($lista_permisos);
            print('<br />');
            print_r($lista_privilegios);
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

        <!-- CUSTOM FILTROS -->
        <table id="dt_filtres">
            <tr>
                <td>Perfil</td>
                <td>
                    <?php
                    echo "<select id='cboprivilegios' name='privilegio'>\n";
                    while($row = $lista_privilegios->fetch(PDO::FETCH_ASSOC))
                    {
                        if($row['COD_PRIVILEGIO'] == $default_privilegio)
                            echo "<option value='$row[COD_PRIVILEGIO]' selected='selected'>$row[NAME_PRIVILEGIO]</option>\n";
                        else
                            echo "<option value='$row[COD_PRIVILEGIO]'>$row[NAME_PRIVILEGIO]</option>\n";
                    }
                    echo "</select>\n";
                    ?>
                </td>
            </tr>
        </table>
        <!-- END CUSTOM FILTROS -->
        
        <!-- CONTENT -->
        <form name="permisos_tienda" method="POST" action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
        <table id="normaltable">
        <thead>
            <tr class="headers">
                <th>MODULO</th>
                <th>PRIVILEGIOS DE ACCESO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td class="options">
                    <input type="checkbox" id="chkall_ver" name="chkall_ver" /> todo
                    <input type="checkbox" id="chkall_agregar" name="chkall_agregar" /> todo
                    <input type="checkbox" id="chkall_editar" name="chkall_editar" /> todo
                </td>
            </tr>
            <?php 
            while($item_permiso = $lista_permisos->fetch(PDO::FETCH_ASSOC)){
                $checked = "";
                $checked_write = "";
                $checked_edit = "";
                $disabled = "";
                
                if($item_permiso['VER'] == 1)
                    $checked = "checked";
                if($item_permiso['ESCRIBIR'] == 1)
                    $checked_write = "checked";
                if($item_permiso['EDITAR'] == 1)
                    $checked_edit = "checked";
                #if($item_permiso['NAME_MODULO'] == 'mimenu' || $item_permiso['NAME_MODULO'] == 'mimenuuser')
                #    $disabled = "disabled='disabled'";

                if($item_permiso['NAME_MODULO'] == 'mimenu' || $item_permiso['NAME_MODULO'] == 'mimenuuser')
                {
                    echo "<tr>";
                    echo "<td>".$item_permiso['LABEL_MODULO']."</td>";
                    echo "<td class='options'> \n";
                        echo "<input type='radio' id='radio_".$item_permiso['NAME_MODULO']."' name='radio_menu' value='".$item_permiso['COD_MODULO']."' $checked />";
                    echo "</td>";
                    echo "</tr>";
                }
                elseif($item_permiso['NAME_MODULO'] == 'exportar')
                {
                    echo "<tr>";
                    echo "<td>".$item_permiso['LABEL_MODULO']."</td>";
                    echo "<td class='options'> \n";
                        echo "<input type='checkbox' id='chk_".$item_permiso['NAME_MODULO']."' name='chk_".$item_permiso['NAME_MODULO']."' value='".$item_permiso['COD_MODULO']."' $checked />";
                    echo "</td>";
                    echo "</tr>";
                }
                else
                {
                    echo "<tr>";
                    echo "<td>".$item_permiso['LABEL_MODULO']."</td>";
                    echo "<td class='options'> \n";
                        echo "<input type='checkbox' class='chk_ver' title='ver registros' id='optver_".$item_permiso['NAME_MODULO']."' name='chkver_".$item_permiso['NAME_MODULO']."' ".$checked." ".$disabled." /> ver"; 
                        echo "<input type='checkbox' class='chk_agregar' title='agregar nuevos registros' id=''optescribir_".$item_permiso['NAME_MODULO']."' name='chkescribir_".$item_permiso['NAME_MODULO']."' ".$checked_write." ".$disabled." /> agregar";
                        echo "<input type='checkbox' class='chk_editar' title='editar registros desde tablas' id='opteditar_".$item_permiso['NAME_MODULO']."' name='chkeditar_".$item_permiso['NAME_MODULO']."' ".$checked_edit." ".$disabled." /> editar";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
            <tr>
                <td colspan="2" class="centered_buttons">
                    <input type="hidden" id="inptprivilegio" name="cod_privilegio" value="" />
                    <?php $session->orig_timestamp = microtime(true);?>
                    <input type="hidden" name="form_timestamp" value="<?php echo $session->orig_timestamp; ?>" />
                    <input class="input" type="submit" value="Grabar">
                </td>
            </tr>
        </tbody>
        </table>
        </form>
        <!-- END CONTENT -->

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>