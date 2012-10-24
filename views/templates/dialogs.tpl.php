<?php
/**
 * JQuery Dialogs Template
 * @author Hernan Thiers
 */
?>
<div id="dialog-confirm" title="Confirmar acci&oacute;n" style="visibility: hidden;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>Este usuario ser&aacute; eliminado para siempre. &iquest;Desea seguir?</p>
</div>

<div id="dialog-form" title="Nuevo Cliente">
    <form>
        <fieldset style="padding:0; border:0; margin-top:25px;">
        <label for="name">Nombre</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
        <label for="email">Encargado</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
    </fieldset>
    </form>
</div>

<!-- KEEP DIALOGS CLOSED -->
<script type="text/javascript" language="javascript">
    $("#dialog-confirm").dialog({ autoOpen: false});
    $("#dialog-form").dialog({ autoOpen: false});
</script>