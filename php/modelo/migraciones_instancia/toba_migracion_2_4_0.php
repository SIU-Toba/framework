<?php
class toba_migracion_2_4_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();
		
		//Modifico la tabla de permisos.
		$sql[] = 'ALTER TABLE apex_item_permisos_tablas ADD COLUMN esquema text;';		
		$sql[] = 'ALTER TABLE apex_item_permisos_tablas ADD COLUMN tabla text;';
		$sql[] = 'ALTER TABLE apex_item_permisos_tablas ADD COLUMN permisos text;';
		
		//Agregado del ef_cbu
		$sql[] = "INSERT INTO apex_elemento_formulario VALUES ('ef_cbu', 'ef_editable', 'CBU', NULL, 'toba', NULL, 0, 0, 0);";
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
	
	function instancia__migracion_tabla_item_permisos()
	{
		$db = $this->elemento->get_db();
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$db->ejecutar($sql);
		$sql = array();		
		$sql[] = 'ALTER TABLE apex_item_permisos_tablas DROP CONSTRAINT apex_item_permisos_tablas_pk;';
		
		$sql_datos = ' SELECT * FROM apex_item_permisos_tablas;';
		$permisos = $db->consultar($sql_datos);
		foreach($permisos as $valores) {			
			$tablas = (trim($valores['tablas_modifica']) != '') ? explode(',' , $valores['tablas_modifica']) : false;
			$datos = $db->quote($valores);						
			$sql[] = "DELETE FROM apex_item_permisos_tablas WHERE proyecto = {$datos['proyecto']} AND item = {$datos['item']} AND fuente_datos = {$datos['fuente_datos']};" ;
			if ($tablas !== false) {
				$esquema = $db->consultar_fila("SELECT schema FROM apex_fuente_datos WHERE fuente_datos = {$datos['fuente_datos']};");
				$tablas = $db->quote($tablas);
				$esquema = $db->quote($esquema);
				foreach($tablas as $tabla) {
					$sql[] = "INSERT INTO apex_item_permisos_tablas (proyecto, item, fuente_datos, tabla, permisos, esquema )  VALUES 
						({$datos['proyecto']}, {$datos['item']}, {$datos['fuente_datos']}, $tabla, 'select,insert,update,delete', {$esquema['schema']});";
				}				
			}
		}

		$sql[] = 'ALTER TABLE apex_item_permisos_tablas ADD CONSTRAINT apex_item_permisos_tablas_pk PRIMARY KEY (proyecto, item, fuente_datos, tabla);';
		$sql[] = 'ALTER TABLE apex_item_permisos_tablas DROP COLUMN  tablas_modifica;';
		$this->elemento->get_db()->ejecutar($sql);
		//$this->manejador_interface->dump_arbol($sql, 'sentencias');
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);		
	}
	
	function instancia__migracion_variables_entorno()
	{	
		//Busco los archivos ya sean PHP o de lotes
		$dir_base = toba_dir(). '/bin';
		$archivos_php = toba_manejador_archivos::get_archivos_directorio($dir_base, '|.php|', true);
		$archivos_sh = toba_manejador_archivos::get_archivos_directorio($dir_base, '|.sh|', true);
		$archivos_bat = toba_manejador_archivos::get_archivos_directorio($dir_base, '|.bat|', true);
		
		//Proceso los cambios de asignacion de variables
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('|\stoba_instancia\s*=|',' TOBA_INSTANCIA=');
		$editor->agregar_sustitucion('|\stoba_proyecto\s*=|',' TOBA_PROYECTO=');
		$editor->agregar_sustitucion('|\stoba_dir\s*=|',' TOBA_DIR=');
		$editor->agregar_sustitucion('|\$toba_dir|','$TOBA_DIR');		
		$editor->procesar_archivos($archivos_php);
		$editor->procesar_archivos($archivos_sh);
		$editor->procesar_archivos($archivos_bat);
		
		//Proceso las lecturas de variables en $_SERVER
		$editor2 = new toba_editor_archivos();
		$editor2->agregar_sustitucion('|$_SERVER[\'toba_proyecto\']|', '$_SERVER[\'TOBA_PROYECTO\']');
		$editor2->agregar_sustitucion('|$_SERVER[\'toba_instancia\']|', '$_SERVER[\'TOBA_INSTANCIA\']');
		$editor2->procesar_archivos($archivos_php);
	}
}
?>
