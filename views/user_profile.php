<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id != null):

#privs
if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
	@import "views/css/datatable.css";
</style>

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
            print_r($titulo); print('<br />');print_r($user_data); print('<br />');
            print_r($controller); print('<br />');print_r($action); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print($session->privilegio);
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

        <?php
        // $listado es una variable asignada desde el controlador SegmentsController.		
        while($item = $user_data->fetch(PDO::FETCH_ASSOC)):
        ?>
        <form method="post"  action="<?php echo $rootPath.'?controller='.$controller.'&amp;action='.$action.'';?>">
        <table id="normaltable" class="texto">
            <thead>
                <tr class="headers">
                    <th colspan="2">MIS DATOS: <?php echo $item['USUARIO'];?></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>NOMBRE</td>
                <td><?php echo $item['NOMBRE'];?></td>
            </tr>
            <tr>
                <td>APELLIDO PATERNO</td>
                <td><?php echo $item['APELLIDOP'];?></td>
            <tr>
                <td>APELLIDO MATERNO</td>
                <td><?php echo $item['APELLIDOM'];?></td>
            </tr>
            <tr>
                <td>CONTRASE&Ntilde;A</td>
                <td>*****</td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php 
                        echo "<input name='id_user' type='hidden' value='$item[ID]' />\n";
                        echo "<input name='nick_user' type='hidden' value='$item[USUARIO]' />\n";
                        echo "<input name='name_user' type='hidden' value='$item[NOMBRE]' />\n";
                        echo "<input name='apellidop_user' type='hidden' value='$item[APELLIDOP]' />\n";
                        echo "<input name='apellidom_user' type='hidden' value='$item[APELLIDOM]' />\n";

                        echo "<input class='input' type='submit' value='EDITAR' />\n";
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        </form>
        <?php
        endwhile;
        ?>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>