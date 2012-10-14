<?php
#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>
<div id="info">
  <p class="Estilo1">
      Control tiempos de trabajo - demo no funcional v0.2
      <br />
      El demo de esta aplicaci&oacute;n ha sido realizado con el fin de apoyar la toma de requerimientos de un sistema de control de tiempos de trabajo.
  </p>
</div>

</body>
</html>
<?php
//else:
//	echo '<script language="JavaScript">alert("Usted No Posee Privilegios Suficientes "); document.location = "'.$rootPath.'"</script>';
//endif; #privileges
else:
	session_destroy();
	echo '<script language="JavaScript">alert("Debe Identificarse"); document.location = "'.$rootPath.'"</script>';
endif; #session
?>