<?php
require_once("nucleo/browser/marco_aplicacion.php");

$datos_proyecto = toba::get_hilo()->obtener_proyecto_datos();
if ($datos_proyecto['con_frames']) {
	if (isset($datos_proyecto['frames_archivo'])) {
		require_once($datos_proyecto['frames_archivo']);
	}
	//¿Se personaliza el marco?
	if (isset($datos_proyecto['frames_clase'])) {
		//Marco personalizado
		$marco = new $datos_proyecto['frames_clase']();		
	} else {
		//Marco por defecto
		$marco = new marco_aplicacion();
	}
	$marco->frameset();
	
} else {
	//No utilizar frames implica que desde aquí se debe redireccionar al item inicial	
		$vinculo_contenido = explode(apex_qs_separador,apex_pa_item_inicial_contenido);
		$vinculo_item = toba::get_vinculador()->crear_vinculo($vinculo_contenido[0], $vinculo_contenido[1], 
																array(), array('validar' => false));
		header("Location: $vinculo_item");
		die;
}

?>