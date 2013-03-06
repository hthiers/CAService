<?php
#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>
<div id="info">
  <p class="Estilo1">
      Control tiempos de trabajo - Beta v0.1.1
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