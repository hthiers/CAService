<!-- NO SCRIPT WARNING -->
<noscript>
<div>
    <h4>¡Espera un momento!</h4>
    <p>La página que estás viendo requiere JavaScript activado.
    Si lo has deshabilitado intencionalmente, por favor vuelve a activarlo o comunicate con soporte.</p>
</div>
</noscript>

<?php
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>

<div id="cabecera" align="left">
</div>

<div class="fin" id="banner">
    <div class="banner_welcome">
        Bienvenido <?php echo "<a href='#'>Usuario</a>"; ?>
        | 
        <a href="?controller=users&amp;action=logOut">Cerrar Sesi&oacute;n</a>
        |
        <a href="#">Ayuda</a>
    </div>
    <div class="banner_title"></div>
</div>

<!-- MENU -->
<div id="menu_div" class="menu">
<?php
#echo "<!-- debug navegador:".$navegador."-->\n";
include 'libs/Menu.php';
$menu = new Menu();
#$menu->loadMenu($session,$navegador,$rootPath,$controller);
$menu->loadTestMenu();
?>
</div>
<!-- END MENU -->