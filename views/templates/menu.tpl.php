<!-- MENU -->
<div class="collapse navbar-collapse">
<?php
#echo "<!-- debug navegador:".$navegador."-->\n";
include 'libs/Menu.php';
$menu = new Menu();
#$menu->loadMenu($session,$navegador,$rootPath,$controller);
$menu->loadCleanTestMenu();
?>
</div>
<!-- END MENU -->