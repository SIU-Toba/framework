<?php

/**
 * API para administrar tablas paralelas de logs
 * @package Fuentes 
 */
class toba_auditoria_tablas_postgres
{
	protected $fuente = NULL;
	protected $tablas = NULL;
	protected $schema_logs;
	protected $schema_origen;
	protected $schema_toba;
	protected $prefijo = 'logs_';
	protected $tablas_toba = array('apex_usuario', 'apex_usuario_grupo_acc', 'apex_usuario_grupo_acc_item', 'apex_usuario_grupo_acc_miembros', 'apex_usuario_perfil_datos',
									'apex_usuario_perfil_datos_dims', 'apex_usuario_proyecto', 'apex_usuario_proyecto_perfil_datos',
									'apex_permiso_grupo_acc', 'apex_grupo_acc_restriccion_funcional');
	
	/**
	 * @var toba_db_postgres7
	 */
	protected $conexion;
		
	function __construct(toba_db $conexion, $schema_origen='public', $schema_logs='public_auditoria', $schema_toba=null) 
	{
		$this->conexion = $conexion;
		$this->schema_origen = $schema_origen;
		$this->schema_logs = $schema_logs;
		$this->schema_toba = $schema_toba;
	}
	
	static function get_campos_propios()
	{
		return array('auditoria_fecha', 'auditoria_usuario', 'auditoria_operacion', 'operacion_nombre', 'auditoria_id_solicitud');	
	}
	
	function agregar_tablas($prefijo=null) 
	{
		$where = '';
		if (isset($prefijo)) {
			$prefijo_sano = $this->conexion->quote("$prefijo%");
			$where .= "	AND (tablename LIKE $prefijo_sano)";
		}
		$schema = $this->conexion->quote($this->schema_origen);
		$sql = "	
				SELECT tablename FROM pg_tables 
		        WHERE 
		        		tablename NOT LIKE 'pg_%'
		        	AND tablename NOT LIKE 'sql_%'
					AND schemaname = $schema
					$where
				ORDER BY UPPER(tablename);
		";
		 $aux = $this->conexion->consultar($sql);
		 foreach ($aux as $t) {
		 	$this->tablas[] = $t['tablename'];
		 }
	}
	
	function agregar_tabla($nombre)
	{
		$this->tablas[] = $nombre;
	}

	function set_esquema_origen($esquema)
	{
		$this->schema_origen = $esquema;
	}
	function set_esquema_toba($esquema)
	{
		$this->schema_toba = $esquema;
	}	
	
	function set_esquema_logs($esquema)
	{
		$this->schema_logs = $esquema;
	}	
		
	function reset_tablas()
	{
		unset($this->tablas);
		$this->tablas = array();
	}
	
	//-----------------------------------------------------------------------------------------------------------------------//
	function crear() 
	{ 
		if (! isset($this->tablas)) {
			throw new toba_error('No se recuperaron tablas para el esquema de auditoria');
		}
		$this->crear_lenguaje();
		$this->crear_schema();
				
		//-- Schema de negocio
		$this->crear_funciones($this->schema_origen);
		foreach ($this->tablas as $t) {
			$this->crear_tabla($t, $this->schema_origen);
		}		
		$this->crear_sp($this->tablas, $this->schema_origen);
		$this->crear_triggers($this->tablas, $this->schema_origen);
			    
		//-- Schema de toba
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {
			$this->crear_funciones($this->schema_toba);
			foreach ($this->tablas_toba as $t) {
				$this->crear_tabla($t, $this->schema_toba);
			}			
			$this->crear_sp($this->tablas_toba, $this->schema_toba);
			$this->crear_triggers($this->tablas_toba, $this->schema_toba);
		}
		return true;
	}	
	
	function migrar()
	{
		$this->crear_funciones($this->schema_origen);
		foreach ($this->tablas as $t) {
			$this->migrar_tabla($t, $this->schema_origen);
		}
		
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {
			$this->crear_funciones($this->schema_toba);
			foreach ($this->tablas_toba as $t) {
				$this->migrar_tabla($t, $this->schema_toba);
			}
		}		
		$this->regenerar();
	}	
	
	function regenerar()
	{
		$this->eliminar_triggers($this->tablas, $this->schema_origen);
		$this->crear_sp($this->tablas, $this->schema_origen);
		$this->crear_triggers($this->tablas, $this->schema_origen);
		
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {
			$this->eliminar_triggers($this->tablas_toba, $this->schema_toba);
			$this->crear_sp($this->tablas_toba, $this->schema_toba);
			$this->crear_triggers($this->tablas_toba, $this->schema_toba);
		}
	}
	
	function purgar($lapso_tiempo = 0)
	{
		foreach($this->tablas as $t) {
			$nombre = $this->prefijo. $t;
			if ($this->conexion->existe_tabla($this->schema_logs, $nombre)) {
				$this->purgar_datos($nombre, $lapso_tiempo);
			}
		}
	}
	
	function eliminar() 
	{			
		$this->eliminar_triggers($this->tablas, $this->schema_origen);
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {
			$this->eliminar_triggers($this->tablas_toba, $this->schema_toba);
		}
		$this->eliminar_schema();
		return true;
	}	
		
	function desactivar_triggers_auditoria()
	{
		$this->set_estado_activacion_triggers($this->tablas, $this->schema_origen, false);		
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {		
			$this->set_estado_activacion_triggers($this->tablas_toba, $this->schema_toba, false);
		}
	}
	
	function activar_triggers_auditoria()
	{
		$this->set_estado_activacion_triggers($this->tablas, $this->schema_origen, true);		
		if (isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {		
			$this->set_estado_activacion_triggers($this->tablas_toba, $this->schema_toba, true);
		}
	}
	
	function get_triggers_activos()
	{
		//Busco todos los triggers del esquema, que sean de auditoria y se disparen ahi mismo.
		$triguers = $this->conexion->get_triggers_schema($this->schema_origen, 'tauditoria_', 'O');
		return $triguers;
	}
	
	//---------------------------------------------------------------------------------------------------------//
	/**
	 * True en caso de existir el esquema con los logs nuevos.
	 * False en caso contrario.
	 */	
	function existe() 
	{
		$schema = $this->conexion->quote($this->schema_logs);
		$sql = "
			SELECT nspname
			FROM pg_catalog.pg_namespace
			WHERE nspname = $schema;
		";
		$r = $this->conexion->consultar($sql);
		if (! empty($r)) {
			return true;
		}
		return false;
	}	
	
        function activar() {
            if (! isset($this->tablas)) {
                $this->agregar_tablas();
            }
            $this->crear_sp($this->tablas, $this->schema_origen);
            $this->crear_triggers($this->tablas, $this->schema_origen);
        }

        function desactivar() {
            if (! isset($this->tablas)) {
                $this->agregar_tablas();
            }
            $this->eliminar_triggers($this->tablas, $this->schema_origen);
        }
        
	
	
	//------- FUNCIONES DE CREACION DE LOGS -------------------------------------

	protected function crear_schema() 
	{
		$this->conexion->crear_schema($this->schema_logs);
	}
	
	protected function crear_lenguaje()
	{
		$this->conexion->crear_lenguaje_procedural();
	}
	
	protected function crear_funciones($schema) 
	{
		$sql = "
			CREATE OR REPLACE FUNCTION $schema.recuperar_schema_temp ()
			RETURNS varchar AS
			'
			DECLARE
			   schemas varchar;
			   pos_inicial int4;
			   pos_final int4;
			   schema_temp varchar;
			BEGIN
			   schema_temp := '''';
			   SELECT INTO schemas current_schemas(true);
			   SELECT INTO pos_inicial strpos(schemas, ''pg_temp'');
			   IF (pos_inicial > 0) THEN
			      SELECT INTO pos_final strpos(schemas, '','');
			      SELECT INTO schema_temp substr(schemas, pos_inicial, pos_final - pos_inicial);
			   END IF;
			   RETURN schema_temp;
			END;
			' LANGUAGE 'plpgsql';	
		";
		$this->conexion->ejecutar($sql);
	}

	protected function migrar_tabla($t, $schema)
	{
		$nombre = $this->prefijo.$t;
		if (! $this->conexion->existe_tabla($this->schema_logs, $nombre)) {
			$this->crear_tabla($t, $schema);
		} else {
			$this->actualizar_tabla($t, $schema);
		}
	}
	
	protected function crear_tabla($t, $schema)
	{
	   $campos = $this->conexion->get_definicion_columnas($t, $schema);
	   $sql = "CREATE TABLE {$this->schema_logs}.{$this->prefijo}{$t}(\n";
	   $sql .= 'auditoria_usuario varchar(60), 
	   			auditoria_fecha timestamp, 
	   			auditoria_operacion char(1),
	   			auditoria_id_solicitud integer,
	   	'; 		   
		foreach ($campos as $def) {
			if ($def['tipo_sql'] != 'bytea') {
				$sql .= $def['nombre'] .  ' ' . $def['tipo_sql'] . ",\n";
			}
		}
		$sql = substr($sql, 0, -2);
		$sql .= ");\n";	
		$this->conexion->ejecutar($sql);
	}
		
	protected function crear_sp($tablas, $schema) 
	{
		$sql = "";
		$conexion = $this->conexion;		
		foreach ($tablas as $t) {
			$campos = $conexion->get_definicion_columnas($t, $schema);			
			//se generan los stored procedures		   
			$sql .= "CREATE OR REPLACE FUNCTION {$this->schema_logs}.sp_$t() RETURNS trigger AS\n";
			$sql .= "'
				DECLARE
					schema_temp varchar;
					rtabla_usr RECORD;
					rusuario RECORD;
					vusuario VARCHAR(60);
					voperacion varchar;
					vid_solicitud integer;
					vestampilla timestamp;
				BEGIN
					vestampilla := current_timestamp;
					SELECT INTO schema_temp $schema.recuperar_schema_temp();
					SELECT INTO rtabla_usr * FROM pg_tables WHERE tablename = ''tt_usuario'' AND schemaname = schema_temp;
					IF FOUND THEN
						SELECT INTO rusuario usuario, id_solicitud FROM tt_usuario;
						IF FOUND THEN
							vusuario := rusuario.usuario;
							vid_solicitud := rusuario.id_solicitud;
						ELSE
							vusuario := user;
							vid_solicitud := 0;
						END IF;
					ELSE
						vusuario := user;
					END IF;
					IF (TG_OP = ''INSERT'') OR (TG_OP = ''UPDATE'') THEN
						IF (TG_OP = ''INSERT'') THEN
							voperacion := ''I'';  
						ELSE
							voperacion := ''U'';
						END IF;
			";
			$sql .= "	INSERT INTO {$this->schema_logs}.{$this->prefijo}$t (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= $def['nombre'] .  ", "; 
				}				
			}
			$sql .= 'auditoria_usuario, auditoria_fecha, auditoria_operacion, auditoria_id_solicitud) VALUES (';
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= "NEW.{$def['nombre']}, ";
				}
			} 		
			$sql .= "vusuario, vestampilla, voperacion, vid_solicitud);
					ELSIF TG_OP = ''DELETE'' THEN
						voperacion := ''D'';
						INSERT INTO {$this->schema_logs}.{$this->prefijo}$t (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= $def['nombre'] .  ", ";	
				}
			}		
			$sql .= 'auditoria_usuario, auditoria_fecha, auditoria_operacion, auditoria_id_solicitud) VALUES (';
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= "OLD." . $def['nombre'] .  ", ";
				}
			}
			$sql .= "vusuario, vestampilla, voperacion, vid_solicitud);
					END IF;
					RETURN NULL;
				END;
			' LANGUAGE 'plpgsql';\n\n";
		}	
		$conexion->ejecutar($sql);
	}
	
	protected function crear_triggers($tablas, $schema) 
	{
		$sql = '';
		foreach ($tablas as $t) {
				$sql .= " CREATE TRIGGER tauditoria_$t AFTER INSERT OR UPDATE OR DELETE
					ON $schema." . $t . " FOR EACH ROW 
					EXECUTE PROCEDURE {$this->schema_logs}.sp_$t(); \n";
		}
		$this->conexion->ejecutar($sql);
	}
	
	protected function actualizar_tabla($origen, $schema)
	{
		$destino = $this->prefijo.$origen;
		$negocio = $this->conexion->get_definicion_columnas($origen, $schema);
		$negocio[] = array(                                                                
    			'nombre' => 'auditoria_usuario',                                                   
			    'tipo_sql' => 'character varying(60)',                                             
			  );
		$negocio[] = array(                                                                
    			'nombre' => 'auditoria_fecha',                                                   
			    'tipo_sql' => 'timestamp without time zone',                                             
			  );
		$negocio[] = array(                                                                
    			'nombre' => 'auditoria_operacion',                                                   
			    'tipo_sql' => 'character(1)',                                             
			  );		
		$negocio[] = array(                                                                
    			'nombre' => 'auditoria_id_solicitud',                                                   
			    'tipo_sql' => 'integer',                                             
			  );			  	  
		$logs = $this->conexion->get_definicion_columnas($destino, $this->schema_logs);				
		foreach ($negocio as $campo_negocio) {
			$existe = false;
			foreach ($logs as $campo_log) {
				if ($campo_negocio['nombre'] == $campo_log['nombre']) {
					$existe = true;
					break;
				}
			}
			if (! $existe && $campo_negocio['tipo_sql'] != 'bytea') {
				$sql = "ALTER TABLE {$this->schema_logs}.$destino ADD COLUMN {$campo_negocio['nombre']} {$campo_negocio['tipo_sql']}";
				$this->conexion->ejecutar($sql);				
			}
		}		
	}
	
	protected function set_estado_activacion_triggers($tablas, $schema, $estado)
	{
		$sql = array();
		$estado_final = ($estado) ? 'ENABLE' : 'DISABLE';
		foreach($tablas as $tabla) {
			$nombre_trigger = 'tauditoria_'. $tabla;
			$sql[] = " ALTER TABLE $schema.$tabla $estado_final TRIGGER $nombre_trigger; ";			
		}
		$this->conexion->ejecutar($sql);
	}
	
	//------- FUNCIONES DE ELIMINACION DE LOGS -------------------------------------	
	protected function eliminar_triggers($tablas, $schema) 
	{
		$sql = '';
		foreach ($tablas as $t) {
			$sql .= "DROP TRIGGER IF EXISTS tauditoria_$t ON $schema.$t;\n\n";
		}
		$this->conexion->ejecutar($sql);
	}
	
	protected function eliminar_schema() 
	{
		$sql = "DROP SCHEMA {$this->schema_logs} CASCADE;\n\n";
		$this->conexion->ejecutar($sql);
	}
	
	protected function eliminar_sp()
	{
		$sql = '';
		foreach ($this->tablas as $t) {
			$sql .= "DROP FUNCTION IF EXISTS {$this->schema_logs}.sp_$t();\n";
		}
		$this->conexion->ejecutar($sql);
	}	

	//-------- CONSULTAS 
	
	function get_datos($tabla, $filtro = array())
	{
		$where = 'true';
		$order = '';
		
		$filtro_valores = $this->conexion->quote($filtro);
		if (isset($filtro['fecha_desde'])) {
			$where .= " AND aud.auditoria_fecha::date >= {$filtro_valores['fecha_desde']}";
		}
		if (isset($filtro['fecha_hasta'])) {
			$where .= " AND aud.auditoria_fecha::date <= {$filtro_valores['fecha_hasta']}";
		}
		if (isset($filtro['usuario'])) {
			$where .= " AND aud.auditoria_usuario = {$filtro_valores['usuario']}";
		}
		if (isset($filtro['operacion'])) {
			$where .= " AND aud.auditoria_operacion = {$filtro_valores['operacion']}";
		}
			if (isset($filtro['auditoria_id_solicitud'])) {
			$where .= " AND aud.auditoria_id_solicitud = {$filtro_valores['auditoria_id_solicitud']}";
		}		
		if (isset($filtro['ordenar']) && $filtro['ordenar'] == 'clave') {
			$claves = $this->get_campos_claves($tabla);
			$order = implode(', ', $claves).', ';
		}
		if (isset($filtro['campo']) && isset($filtro['valor'])) {
			$resultado = null;
			if (preg_match('/^\w*$/i', $filtro['campo'], $resultado)) {
				$where .= " AND aud.{$filtro['campo']} = {$filtro_valores['valor']}";
			}
		}						
	
		$sql = "SELECT 
					aud.*,
					CASE 
						WHEN aud.auditoria_operacion = 'U' THEN 'Modificación'
						WHEN aud.auditoria_operacion = 'I' THEN 'Alta'
						WHEN aud.auditoria_operacion = 'D' THEN 'Baja'
					END as operacion_nombre
				FROM
					{$this->schema_logs}.$tabla as aud
				WHERE
					$where
				ORDER BY $order aud.auditoria_fecha
		";
		$datos = $this->conexion->consultar($sql);
		return $datos;
	}
	
	function get_campos_claves($tabla)
	{
		$tabla = substr($tabla, strlen($this->prefijo));
		$schema = $this->schema_origen;
		if (! $this->conexion->existe_tabla($this->schema_origen, $tabla) 
			&& isset($this->schema_toba) && $this->conexion->existe_schema($this->schema_toba)) {
			$schema = $this->schema_toba;
		}
		$cols = $this->conexion->get_definicion_columnas($tabla, $schema);
		$pks = array();
		foreach ($cols as $col) {
			if ($col['pk']) {
				$pks[] = $col['nombre'];
			}
		}
		return $pks;
	}

	function purgar_datos($tabla, $lapso_tiempo)
	{
		$tiempo = $this->conexion->quote($lapso_tiempo . ' months' );
		$sql = 'DELETE FROM '. $this->schema_logs . ".$tabla WHERE auditoria_fecha::timestamp  < (now() - interval $tiempo);";
		$this->conexion->ejecutar($sql);
	}
	
	function migrar_estructura_campos_toba_2_4()
	{
		foreach ($this->tablas as $t) {		
			$nombre = $this->prefijo.$t;
			if ($this->conexion->existe_tabla($this->schema_logs, $nombre)) {
				$sql = "ALTER TABLE {$this->schema_logs}.$nombre ALTER auditoria_usuario TYPE character varying(60);";
				$this->conexion->ejecutar($sql);				
			}			
		}
	}
}
?>
