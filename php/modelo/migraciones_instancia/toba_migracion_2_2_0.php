<?php
class toba_migracion_2_2_0 extends toba_migracion
{
	function instancia__migracion_tablas_log()
	{
		$this->manejador_interface->mensaje('Migrando las tablas de logs.', false);
			
		//Hay que crear el schema para las tablas de log
		$sql[] = 'CREATE SCHEMA toba_logs;';
		
		//Recupero las tablas de log 
		$log_instancia = toba_db_tablas_instancia::get_lista_global_log();
		$log_proyecto = toba_db_tablas_instancia::get_lista_proyecto_log();
		$tablas = array_merge($log_instancia, $log_proyecto);
		
		$secuencias = $this->elemento->get_db()->get_secuencia_tablas($tablas);
		foreach($secuencias as $tabla => $secuencia) {									//Genero las SQL para mover las tablas de schema
			$sql[] = 'ALTER TABLE '. $tabla . ' SET SCHEMA toba_logs;' ;
			$sql[] = 'ALTER SEQUENCE '. $secuencia . ' SET SCHEMA toba_logs;' ;			
			$sql[] = 'ALTER TABLE toba_logs.' . $tabla . " ALTER COLUMN log_objeto SET DEFAULT nextval(('toba_logs.\"$secuencia\"'::text)::regclass);";
			$this->manejador_interface->progreso_avanzar();
		}
		$this->elemento->get_db()->ejecutar($sql);		
				
		//Despues hay que borrar todos los archivos de log que existen actualmente
		$instancia = $this->elemento->eliminar_archivos_log();
	}
	
}
?>
