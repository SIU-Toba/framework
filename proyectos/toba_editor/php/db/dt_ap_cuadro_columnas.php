<?php
class dt_ap_cuadro_columnas extends toba_ap_tabla_db_s
{

	function evt__pre_insert($id_registro)
	{
		$this->sincronizar_columnas_eventos($id_registro);
	}

	function evt__pre_update($id_registro)
	{
		$this->sincronizar_columnas_eventos($id_registro);
	}

	function sincronizar_columnas_eventos($id_registro)
	{
		$dt_eventos = $this->objeto_tabla->controlador()->tabla('eventos');
		$datos_actuales = $this->datos[$id_registro];
		if (isset($datos_actuales['evento_asociado'])) {
			$id_dt_evento = $dt_eventos->get_id_fila_condicion(array('identificador' => $datos_actuales['evento_asociado']), false);
			if (! empty($id_dt_evento)) {
				$id_real_evento = $dt_eventos->get_fila_columna(current($id_dt_evento), 'evento_id');
				$this->datos[$id_registro]['evento_asociado'] = $id_real_evento;
			} else {
				throw new toba_error_def('No se encuentra el evento especificado para la columna '. $datos_actuales['clave']);
			}
		}
	}
}
?>