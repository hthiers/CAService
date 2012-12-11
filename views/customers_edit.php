<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- JS & CSS -->
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
        /*float: left;*/
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
</style>
<!-- END JS & CSS -->

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
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!-- FORM -->
        <div id="dt_filtres">
        <form id="moduleForm" name="form1" method="post"  action="<?php echo $rootPath.'?controller=customers&amp;action=customersEdit';?>">
          <table border="0" align="center" class="texto">
            <tr>
                <td>Nombre</td>
                <td width="50">:</td>
                <td>
                    <input class="input_box" name="customer_name" type="text" id="customer_name" size="40" value="<?php #echo $item['COD_TIENDA'];?>" />
                </td>
            </tr>
            <tr>
                <td>Datos contacto</td>
              <td>:</td>
              <td>
                  <input class="input_box" name="customer_detail" type="text" id="customer_detail" size="40" value="<?php #echo $item['COD_BTK']; ?>" />
              </td>
            </tr>
<!--            <tr>
                <td colspan="3" class="submit">
                    <br />
                    <input type="hidden" name="txtcodigo" id="hdncodigo" value="<?php #echo $item['COD_TIENDA']; ?>" />
                    <input type="hidden" name="txtcodigobtk" id="hdncod_btk" value="<?php #echo $item['COD_BTK']; ?>" />
                    <input type="hidden" name="prename" id="hdnprename" value="<?php e#cho $nombre_tienda[0]; ?>" />
                    <?php #$session->orig_timestamp = microtime(true); ?>
                    <input type="hidden" name="form_timestamp" value="<?php #echo $session->orig_timestamp; ?>" />

                    <input name="Atras" type="reset" class="input" id="Atras"  onclick="window.location = '<?php #echo $rootPath.'?controller='.$controller.'&amp;action='.$action_b.'';?>'"  value="Cancelar" />
                    &nbsp;&nbsp;
                    <input name="button" type="submit" class="input" id="button" value="Guardar" />
                </td>
            </tr>-->
          </table>
        </form>
        </div>
        <!-- END FORM -->
        
    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>