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
        $("#moduleForm").validate({
            debug: false,
            rules: {
                name_user: {
                    required: true,
                    minlength: 3
                },
                apellidop_user: {required: true},
                apellidom_user: {required: false},
                nick_user: {
                    required: true,
                    minlength: 3
                },
                password_a: {
                    minlength: 5
                },
                password_b: {equalTo: "#inptpassa"}
            },
           messages: {
               name_user:  {
                   minlength: jQuery.format("Mínimo {0} caracteres")
               },
               password_b: {
                   equalTo: "Las contrase&ntilde;as no coinciden!"
               }
            }
        });
});
</script>

</head>
<body id="dt_example">

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
            print_r($titulo); print('<br />');print_r($controller); print('<br />');
            print_r($action); print('<br />');print_r($action_b); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print("session: ".$orig_timestamp);
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

        <form id="moduleForm" method="post" action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
        <table id="normaltable" class="texto">
            <thead>
                <tr class="headers">
                    <th colspan="2">MODIFICANDO CUENTA DE: <b><?php echo $nick_user;?></b></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>NOMBRE</td>
                <td><input name="name_user" type="text" value="<?php echo $name_user;?>" /></td>
            </tr>
            <tr>
                <td>NOMBRE USUARIO</td>
                <td><input name="nick_user" type="text" value="<?php echo $nick_user;?>" <?php if($nick_user == "administrador"){echo "readonly='readonly'";} ?> /></td>
            </tr>
            <tr>
                <td>APELLIDO PATERNO</td>
                <td><input name="apellidop_user" type="text" value="<?php echo $apellidop_user;?>" /></td>
            <tr>
                <td>APELLIDO MATERNO</td>
                <td><input name="apellidom_user" type="text" value="<?php echo $apellidom_user;?>" /></td>
            </tr>
            <tr>
                <td>PERFIL</td>
                <td>
                    <?php
                    echo "<select id='cboprivilegios' name='cbo_priv'>\n";
                    if($nick_user == "administrador"){
                        echo "<option name='cbo_priv' value='1' selected='selected'>administrador</option>\n";
                    }
                    else{
                        while($row = $lista_privs->fetch(PDO::FETCH_ASSOC))
                        {
                            if($row['COD_PRIVILEGIO'] == $priv_user)
                                echo "<option name='cbo_priv' value='$row[COD_PRIVILEGIO]' selected='selected'>$row[NAME_PRIVILEGIO]</option>\n";
                            else
                                echo "<option name='cbo_priv' value='$row[COD_PRIVILEGIO]'>$row[NAME_PRIVILEGIO]</option>\n";
                        }
                    }
                    echo "</select>\n";
                    ?>
                </td>
            </tr>
            <tr>
                <td>NUEVA CONTRASE&Ntilde;A <br /> (en blanco para ignorar)</td>
                <td><input type="password" name="password_nuevo_a" value="" /></td>
            </tr>
            <tr>
                <td>REPETIR CONTRASE&Ntilde;A <br /> (en blanco para ignorar)</td>
                <td><input type="password" name="password_nuevo_b" value="" /></td>
            </tr>
            <tr>
                <td>
                    <?php $session->orig_timestamp = microtime(true); ?>
                    <input name="id_user" type="hidden" value="<?php echo $id_user;?>" />
                    <input name="form_timestamp" type="hidden" value="<?php echo $session->orig_timestamp; ?>" />
                    <input class="input" type="reset" value="CANCELAR" onclick="window.location = '<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'" />
                </td>
                <td>
                    <input class="input" type="submit" value="GUARDAR" />
                </td>
            </tr>
            </tbody>
        </table>
        </form>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>