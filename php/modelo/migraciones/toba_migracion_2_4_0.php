<?php
class toba_migracion_2_4_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();		

		$sql[] = "UPDATE apex_columna_formato SET descripcion_corta = 'Decimal 2 posiciones (opcionales)' WHERE columna_formato = '9';" ;
		$sql[] = "INSERT INTO  apex_columna_formato (funcion, descripcion_corta, estilo_defecto) VALUES ('decimal_estricto', 'Decimal 2 posiciones (100,00)', '0');" ;
		$sql[] = "INSERT INTO apex_fuente_datos_motor (fuente_datos_motor, nombre, version) VALUES ('sqlserver', 'SQLServer', '2005');";						
		$sql[] = 'ALTER TABLE apex_fuente_datos_schemas  ALTER COLUMN fuente_datos TYPE VARCHAR(20);';
				
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function instancia__migracion_tablas_log()
	{		
		$schema_actual = 'toba_logs';
		$schema_logs = $this->elemento->get_db()->get_schema() . '_logs';
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
		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();		
		
		//Primero creo el nuevo schema
		$sql[] = "CREATE SCHEMA $schema_logs;";				
		
		//Ahora cambio la calificacion en tablas y columnas, de lo contrario hay que crear todo de cero
		$columnas_secuencia = $this->elemento->get_db()->get_secuencia_tablas(array_keys($secuencias), 'toba_logs');			
		foreach($secuencias as $tabla => $secuencia) {									//Genero las SQL para mover las tablas de schema
			$sql[] = "ALTER TABLE $schema_actual.$tabla SET SCHEMA $schema_logs;" ;
			if (! is_null($secuencia)) {
				$columna = $columnas_secuencia[$tabla];				
				$sql[] = "ALTER SEQUENCE $schema_actual.$secuencia SET SCHEMA $schema_logs;" ;
				$sql[] = "ALTER TABLE $schema_logs.$tabla ALTER COLUMN $columna SET DEFAULT nextval(('$schema_logs.\"$secuencia\"'::text)::regclass);";
			}
			$this->manejador_interface->progreso_avanzar();
		}
		
		//Creo la tabla nueva de logs para web services
		$sql[] = "CREATE TABLE \"$schema_logs\".\"apex_solicitud_web_service\"
				(
				   proyecto			VARCHAR(15) NOT NULL, 
				   solicitud			BIGINT	NOT NULL, 
				   metodo			TEXT	NULL, 
				   ip				VARCHAR(20)	NULL, 
				   CONSTRAINT \"$schema_logs.apex_solicitud_web_service_pk\" PRIMARY KEY (\"solicitud\", \"proyecto\"), 
				   CONSTRAINT \"$schema_logs.apex_sol_web_service_solicitud_fk\" FOREIGN KEY (\"solicitud\", \"proyecto\") REFERENCES \"$schema_logs\".\"apex_solicitud\" (\"solicitud\", \"proyecto\") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE
				); ";
		
		//Finalmente elimino el schema viejo
		$sql[] = "DROP SCHEMA toba_logs CASCADE;";				
		
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function proyecto__agregar_configuracion_ini()
	{
		$destino_ini = $this->elemento->get_dir().'/proyecto.ini';
		if (file_exists($destino_ini)) {
			$editor = new toba_ini($destino_ini);
			$conf = $editor->get_datos_entrada('proyecto');
			$conf['permite_cambio_perfil_funcional'] = '0';
			$conf['mostrar_resize_fuente'] = '0';
			$editor->set_datos_entrada('proyecto', $conf);
			$editor->guardar();
		}
	}
	
}
?>