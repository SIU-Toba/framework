<?php

toba::get_logger()->ocultar();

$archivo = toba::get_hilo()->obtener_parametro('archivo');
$tipo_salida = toba::get_hilo()->obtener_parametro('tipo_salida');
$dir_temp = toba::get_hilo()->obtener_path_temp();
$path_completo = $dir_temp . "/" . $archivo;
if (file_exists($path_completo)) {
	$fp = fopen($path_completo, 'rb');

	if (isset($tipo_salida)) {
		header("Content-type: $tipo_salida");
	}
	header("Content-Length: " . filesize($path_completo));	
	fpassthru($fp);
	fclose($fp);
	unlink($path_completo);
} 

?>