<?php
	
	require_once("nucleo/browser/clases/objeto_esquema.php");
		
	$proyecto = $this->hilo->obtener_parametro("proyecto");
	$objeto = $this->hilo->obtener_parametro("objeto");

    $esquema =& new objeto_esquema( array($proyecto,$objeto) ); 
    //echo $esquema->obtener_dot();
	$esquema->obtener_imagen();
	
?>