<?php

/**
 * Asistente que permite manejar los moldes de metadatos y código sin asumir que se generará una operación
 */
class toba_asistente_adhoc extends toba_asistente
{

	function generar()
	{
		
	}
	
	
	function ejecutar()
	{
		try {
			abrir_transaccion();
			
			foreach ($this->moldes as $molde) {
				$molde->generar();
			}
			$this->generar_archivos_consultas();
			$this->guardar_log_elementos_generados();
			
			cerrar_transaccion();
			toba::notificacion()->agregar('La generación se realizó exitosamente','info');
			return $this->log_elementos_creados;
		} catch (toba_error $e) {
			toba::logger()->error($e);
			toba::notificacion()->agregar("Fallo en la generación: ".$e->getMessage(), 'error');
			abortar_transaccion();
		}		
	}
	
}

?>