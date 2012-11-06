<?php
/**
 * HTML for JQuery Dialogs template
 * @author Hernan Thiers
 */
?>
<!-- Confirm action -->
<div id="dialog-confirm" title="Confirmar acci&oacute;n" style="visibility: hidden;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>Este usuario ser&aacute; eliminado para siempre. &iquest;Desea seguir?</p>
</div>
<!-- END Confirm action -->

<!-- New Customer -->
<div id="dialog-form" title="Nuevo Cliente">
    <form action="?controller=customers&amp;action=customersAdd" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="name">Nombre Organización</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name" id="dlgSbm_name" class="text ui-widget-content ui-corner-all" />
            <label for="email">Contacto</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_contact" id="dlgSbm_contact" class="text ui-widget-content ui-corner-all" />
            
            <input class="dlgSbmCstr" type="submit" value="GUARDAR" style="font-family: Verdana; font-size: 15px; padding: 2px;" />
        </fieldset>
    </form>
</div>
<!-- END New Customer -->

<!-- Project view -->
<div id="dialog-project" title="Proyecto #xx asdfg">
    <form>
        <fieldset style="padding:0; border:0; margin-top:25px;">
        <label for="name">Nombre</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
        <label for="email">Encargado</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
    </fieldset>
    </form>
</div>
<!-- END Project view -->

<!-- KEEP DIALOGS CLOSED -->
<script type="text/javascript" language="javascript">
    $("#dialog-confirm").dialog({ autoOpen: false});
    $("#dialog-form").dialog({ autoOpen: false});
    $("#dialog-project").dialog({ autoOpen: false});
</script>