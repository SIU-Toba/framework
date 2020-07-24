<?php
class ci_jasper extends toba_ci
{
	protected $s__paths;
	

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$path = toba::proyecto()->get_path(). '/exportaciones/jasper';
		$archivos = toba_manejador_archivos::get_archivos_directorio($path, '/.jasper$/', true);
		
		$datos = array();
		$i = 0;
		foreach ($archivos  as $archivo) {
			$this->s__paths[$i] = $archivo;
			$datos[$i]['path'] = $i;
			$datos[$i]['reporte'] = ucwords(str_replace('_', ' ', basename($archivo, '.jasper')));
			$i++;
		}
		$cuadro->set_datos($datos);
		
	}
	
	function vista_jasperreports(toba_vista_jasperreports $report) 
	{
		$path = toba::memoria()->get_parametro('path');
		if (! isset($path) || ! is_numeric($path) || ! isset($this->s__paths[$path])) {
			throw new toba_error_def("Parámetro no definido");
		}
		$report->set_path_reporte($this->s__paths[$path]);
		$db = toba::instancia()->get_db();
		$report->set_conexion($db);
	}
	
	function vista_araireportes(toba_vista_araireportes $report)
	{		
		$report->set_path_reporte('/reportes/toba_referencia/operaciones/listado_1');
		$data = toba::instancia()->get_db()->consultar('SELECT
												apex_item."proyecto" AS apex_item_proyecto,
												apex_item."item" AS apex_item_item,
												apex_item."nombre" AS apex_item_nombre
										   FROM
												"desarrollo"."apex_item" apex_item');
		//Datos enviados en formato xml
		/*$xml = new SimpleXMLElement('<operaciones></operaciones>');
		foreach ($data as $linea) {
			$renglon = $xml->addChild ('operacion');
			foreach($linea as $key => $campo) {
				$renglon->addChild(utf8_e_seguro($key), utf8_e_seguro($campo));
			}
		}	
		$report->set_xml($xml->asXML());*/

		//Datos enviados en formato JSON
		//$report->set_json(rest_encode($data));
	}	
	
	/**
	 * Atrapa el evento seleccion del cuadro e invoca manualmente el serviccio vista_jasperreports pasandole el hash por parámetro
	 */
	function extender_objeto_js()
	{
		if ($this->get_id_pantalla() == 'pant_estaticos') {
			echo 
				toba::escaper()->escapeJs($this->dep('cuadro')->objeto_js).".evt__imprimir = function(params) {
					location.href = vinculador.get_url(null, null, 'vista_jasperreports', {'path': params});
					return false;
				}
			";
		}
	}

}

?>