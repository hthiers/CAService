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

<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $session->name_user;?></a></li>
                <li><a href="?controller=users&amp;action=logOut">Cerrar Sesi&oacute;n</a></li>
                <li><a href="#">Ayuda</a></li>
            </ul>
        </div>
    </div>
</nav>