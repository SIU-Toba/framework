<?php
class ci_analizador_sql extends toba_ci
{
	protected $s__formateado;
	
	function ini__operacion()
	{
		$this->s__formateado = false;
	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$info = toba::memoria()->get_dato_instancia('previsualizacion_consultas');
		if (! isset($info)) {
			throw new toba_error('No se encontró información de consultas ejecutadas');
		}
		$datos = $info['datos'];
		require_once('3ros/Text_Highlighter/Highlighter.php');
		$hlSQL = Text_Highlighter::factory('SQL');
		$i = 1;
		foreach (array_keys($datos) as $id) {
			$datos[$id]['numero'] = $i;
			$datos[$id]['id'] = $id;
			if (isset($datos[$id]['fin'])) {
				$datos[$id]['tiempo'] = ($datos[$id]['fin'] - $datos[$id]['inicio']);
			} else {
				$datos[$id]['tiempo'] = 0;
			}
			$datos[$id]['sql'] = $hlSQL->highlight($datos[$id]['sql']);
			$i++; 
		}
		if ($this->s__formateado) {
			$cuadro->set_formateo_columna('sql', 'pre');
		}
		$cuadro->set_formateo_columna('tiempo', 'tiempo_ms');		
		$cuadro->set_datos($datos);
	}
	
	function evt__cuadro__formateado()
	{
		$this->s__formateado = ! $this->s__formateado;
	}

	function evt__cuadro__explain($seleccion)
	{
		$info = toba::memoria()->get_dato_instancia('previsualizacion_consultas');
		if (! isset($info)) {
			throw new toba_error('No se encontró información de consultas ejecutadas');
		}
		if (isset($info['datos'][$seleccion['id']])) {
			$fuente = $info['fuente'];
			$base = toba_admin_fuentes::instancia()->get_fuente($fuente, toba_editor::get_proyecto_cargado())->get_db();
			$sql = 'EXPLAIN ANALYZE  '.$info['datos'][$seleccion['id']]['sql'];
			$base->abrir_transaccion();
			$datos = $base->consultar($sql, toba_db_fetch_num);
			$base->abortar_transaccion();
			$salida = '';
			foreach ($datos as $fila) {
				$salida .= $fila[0]."\n   ";
			}
			$salida = '<pre>'.$salida.'</pre>';
			toba::notificacion()->info($salida);		
		}
	}
	
	function generar_html()
	{
		echo toba_recurso::link_css('highlighter', null, true);		
		parent::generar_html();
	}

}

?>