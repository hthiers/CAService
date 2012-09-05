<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio == 1):
?>

<!-- AGREGAR JS & CSS AQUI -->
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
                    required: true,
                    minlength: 5
                },
                password_b: {equalTo: "#inptpassa"},
                privi_user: {required: true}
            },
           messages: {
               name_user:  {
                   minlength: jQuery.format("M&iacute;nimo {0} caracteres")
               },
               password_b: {
                   equalTo: "Las contrase&ntilde;as no coinciden!"
               }
            }
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
            
            <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
              <table border="0" align="center" class="texto">
                <tr>
                  <td width="56">Nombre</td>
                  <td width="3">:</td>
                  <td width="380">
                      <input name="name_user" type="text" id="inptcode" size="40" />
                  </td>
                </tr>
                <tr>
                  <td>Apellido Paterno</td>
                  <td>:</td>
                  <td>
                      <input name="apellidop_user" type="text" id="inptapellidop" size="40" />
                  </td>
                </tr>
                <tr>
                  <td>Apellido Materno</td>
                  <td>:</td>
                  <td><input name="apellidom_user" type="text" id="inptapellidom" size="40" /></td>
                </tr>
                <tr>
                  <td>Nombre de Usuario</td>
                  <td>:</td>
                  <td><input name="nick_user" type="text" id="inptusername" size="40" /></td>
                </tr>
                <tr>
                    <td>Contrase&ntilde;a</td>
                  <td>:</td>
                  <td><input name="password_a" type="password" id="inptpassa" size="40" /></td>
                </tr>
                <tr>
                  <td>Repetir Contrase&ntilde;a</td>
                  <td>:</td>
                  <td><input name="password_b" type="password" id="inptpassb" size="40" /></td>
                </tr>
                <tr>
                  <td>Perfil</td>
                  <td>:</td>
                  <td>
                        <?php
                        echo "<select id='cboprivilegios' name='privi_user'>\n";
                        echo "<option value='' selected='selected'>SELECCIONAR</option>\n";
                        while($row = $lista_privs->fetch(PDO::FETCH_ASSOC))
                        {
                            echo "<option name='cbo_priv' value='$row[COD_PRIVILEGIO]'>$row[NAME_PRIVILEGIO]</option>\n";
                        }
                        echo "</select>\n";
                        ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                      <br />
                      <?php $session->orig_timestamp = microtime(true); ?>
                      <input name="form_timestamp" type="hidden" value="<?php echo $session->orig_timestamp; ?>" />
                  </td>
                </tr>
                <tr>
                    <td colspan="3" class="submit">
                        <input name="Atras" type="reset" class="input" id="Atras"  onclick="window.location = '<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'"  value="Cancelar" />
                        &nbsp;&nbsp;
                        <input name="button" type="submit" class="input" id="button" value="Guardar" />
                    </td>
                </tr>
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