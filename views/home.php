<?php
require('templates/header_index.tpl.php');

if(isset($_GET['errorCode']))
    $error_code = $_GET['errorCode'];
?>

<!-- AGREGAR JS & CSS AQUI -->
<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
    $("input#txtuser").focus();
});
</script>

</head>

<body>

    <noscript>
    <div>
        <h4>¡Espera un momento!</h4>
        <p>La página que estás viendo requiere JavaScript.
        Si lo has deshabilitado intencionalmente, por favor vuelve a activarlo o comunicate con soporte.</p>
    </div>
    </noscript>

    <!-- CABECERA -->
	<div class="Estilo2" id="banner"></div>
    <!-- END CABECERA -->
    
    <!-- CENTRAL -->
    <div id="central">
        <div id="contenido">
            
            <?php if(isset($error) && $error == 1) echo "<div id='errorbox_failure'>Usuario o contraseña inválido!</div>"; ?>
            
            <h2 class="menuinicio">Inicio de sesión</h2>
            <p class="submenu">
                Demostraci&oacute;n versi&oacute;n 0.2.
            </p>
            
            <div style="margin-top: 10px;">
                <form id="form1" name="form1" method="post" action="?controller=Users&action=logIn">
                    <table>
                        <tr>
                            <td>Usuario</td>
                            <td><input name="txtusername" type="text" class="bien" id="txtuser" size="50" /></td>
                        </tr>
                        <tr>
                            <td>Contraseña</td>
                            <td><input name="txtpassword" type="password" class="bien" id="txtpass" size="50" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 10px;">
                                <input name="button" type="submit" class="boton" id="button" value="Acceder" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <!-- END CENTRAL -->
    
    <div id="ads">
        <a href="http://www.freedomain.co.nr/" target="_blank"><img src="views/img/freedomain_banner.gif" width="88" height="31" border="0" alt="Free Domain Name - www.YOU.co.nr!"></a>
    </div>

<!-- FOOTER -->
<?php require('templates/footer_index.tpl.php'); ?>
<!-- END FOOTER -->