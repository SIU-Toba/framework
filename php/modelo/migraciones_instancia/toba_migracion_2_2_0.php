<?php
class toba_migracion_2_2_0 extends toba_migracion
{
	function instancia__migracion_tablas_log()
	{
		$secuencias = array('apex_log_error_login' => 'apex_log_error_login_seq',
						'apex_log_ip_rechazada' => null,
						'apex_log_objeto' =>'apex_log_objeto_seq',
						'apex_log_sistema' => 'apex_log_sistema_seq',
						'apex_log_tarea' => 'apex_log_tarea_seq',
						'apex_sesion_browser' => 'apex_sesion_browser_seq',
						'apex_solicitud' => 'apex_solicitud_seq',
						'apex_solicitud_browser' => null,
						'apex_solicitud_consola' => null,
						'apex_solicitud_cronometro' => null,
						'apex_solicitud_observacion' => 'apex_solicitud_observacion_seq'
						);
		$schema_actual = $this->elemento->get_db()->get_schema();
		if (! isset($schema_actual)) {
			$schema_actual = 'public';
		}
		$this->manejador_interface->mensaje('Migrando las tablas de logs.', false);
			
		//Hay que crear el schema para las tablas de log
		$sql[] = 'CREATE SCHEMA toba_logs;';
				
		$columnas_secuencia = $this->elemento->get_db()->get_secuencia_tablas(array_keys($secuencias));
		foreach($secuencias as $tabla => $secuencia) {									//Genero las SQL para mover las tablas de schema
			$sql[] = "ALTER TABLE  $schema_actual.$tabla  SET SCHEMA toba_logs;" ;
			if (! is_null($secuencia)) {
				$columna = $columnas_secuencia[$tabla];
				$sql[] = "ALTER SEQUENCE $schema_actual.$secuencia  SET SCHEMA toba_logs;" ;
				$sql[] = 'ALTER TABLE toba_logs.' . $tabla . " ALTER COLUMN $columna SET DEFAULT nextval(('toba_logs.\"$secuencia\"'::text)::regclass);";
			}
			$this->manejador_interface->progreso_avanzar();
		}
		$this->elemento->get_db()->ejecutar($sql);		
				
		//Despues hay que borrar todos los archivos de log que existen actualmente
		$instancia = $this->elemento->eliminar_archivos_log();
	}
	
}
?>
