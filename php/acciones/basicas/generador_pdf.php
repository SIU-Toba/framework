<?php
	require_once("nucleo/browser/clases/objeto_cuadro.php");
	
	$proyecto = $this->hilo->obtener_parametro("proyecto");
	$objeto = $this->hilo->obtener_parametro("objeto");
    $cuadro =& new objeto_cuadro(array($proyecto,$objeto), $this); 
		
	if ($this->hilo->obtener_parametro("saltear_paginacion") == 1) {
		$cuadro->cargar_datos(null,null,true);
	}else{
		$cuadro->cargar_datos(null,null,false);
	}
    $cuadro->obtener_pdf();
?>