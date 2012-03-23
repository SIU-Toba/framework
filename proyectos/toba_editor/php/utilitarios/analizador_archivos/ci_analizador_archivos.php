<?php

class formateo_tamano extends toba_formateo 
{
	function formato_tamano($tamano)
	{
		return file_size($tamano, 0);
	}
}

class ci_analizador_archivos extends toba_ci
{

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$archivos = toba::memoria()->get_dato_instancia('previsualizacion_archivos');
		if (! isset($archivos)) {
			throw new toba_error('No se encontraron los archivos a mostrar');
		}
		$path_proyecto = toba_manejador_archivos::path_a_unix(toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()));
		$path_php = $path_proyecto.'/php/';
		$cuadro->set_formateo_columna('tamano', 'tamano', 'formateo_tamano');
		$datos = array();
		foreach ($archivos as $i => $archivo) {
			$datos[$i]['path'] = substr($archivo, strlen($path_proyecto) + 1);
			$datos[$i]['tamano'] = filesize($archivo);
		}
		$cuadro->set_datos($datos);
	}

}

?>