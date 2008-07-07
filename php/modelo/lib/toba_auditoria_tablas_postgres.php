<?php

/**
 * API para administrar tablas paralelas de logs
 */
class toba_auditoria_tablas_postgres
{
	protected $fuente = NULL;
	protected $tablas = NULL;
	protected $schema_logs = 'auditoria';
	protected $schema_origen = 'public';
	protected $prefijo = 'logs_';
		
	function __construct(toba_db $conexion) 
	{
		$this->conexion = $conexion;
	}
	
	static function get_campos_propios()
	{
		return array('auditoria_fecha', 'auditoria_usuario', 'auditoria_operacion', 'operacion_nombre');	
	}
	
	function agregar_tablas($prefijo=null) 
	{
		$where = '';
		if (isset($prefijo)) {
			$where .= "	AND (tablename LIKE '$prefijo%')";
		}
		$sql = "	
				SELECT tablename FROM pg_tables 
		        WHERE 
		        		tablename NOT LIKE 'pg_%'
		        	AND tablename NOT LIKE 'sql_%'
					AND schemaname = '{$this->schema_origen}'
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
	
	function set_esquema_logs($esquema)
	{
		$this->schema_logs = $esquema;
	}	
	
	function crear() 
	{ 
		$this->crear_lenguaje();
		$this->crear_funciones();
		$this->crear_schema();
		$this->crear_tablas();
		$this->crear_sp();
		$this->crear_triggers();
		return true;
	}	
	
	function eliminar() 
	{			
		$this->eliminar_triggers();
		$this->eliminar_schema();
		return true;
	}	
	
	/**
	 * True en caso de existir el esquema con los logs nuevos.
	 * False en caso contrario.
	 */	
	function existe() 
	{
		$sql = "		
			SELECT nspname
			FROM pg_catalog.pg_namespace
			WHERE nspname = '{$this->schema_logs}';
		";
		$r = $this->conexion->consultar($sql);
		if (count($r) > 0)
			return true;
		return false;
	}	
	
	
	
	//------- FUNCIONES DE CREACION DE LOGS -------------------------------------
	
	protected function crear_lenguaje()
	{
		$sql = "SELECT lanname FROM pg_language WHERE lanname='plpgsql'";
		$rs = $this->conexion->consultar($sql);
		if (empty($rs)) {
			$sql = 'CREATE LANGUAGE plpgsql';
			$this->conexion->ejecutar($sql);
		}
	}
	
	protected function crear_funciones() 
	{
		$sql = "
			CREATE OR REPLACE FUNCTION {$this->schema_origen}.recuperar_schema_temp ()
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
	
	protected function crear_schema() 
	{
		$sql = "CREATE SCHEMA {$this->schema_logs}; ";
		$this->conexion->ejecutar($sql);
	}
	
	protected function crear_tablas() 
	{
		$conexion = $this->conexion;
		$sql = '';
	    foreach ($this->tablas as $t) {
		   $campos = $conexion->get_definicion_columnas($t, $this->schema_origen);
		   $sql .= "CREATE TABLE {$this->schema_logs}.{$this->prefijo}{$t}(\n";
		   $sql .= "auditoria_usuario varchar(30), 
		   			auditoria_fecha timestamp, 
		   			auditoria_operacion char(1),
		   	"; 		   
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= $def['nombre'] .  " " . $def['tipo_sql'] . ",\n";
				}
			}
			$sql = substr($sql, 0, -2);
			$sql .= ");\n";
	    }
		$this->conexion->ejecutar($sql);
	}
	
	protected function crear_sp() 
	{
		$sql = "";
		$conexion = $this->conexion;		
		foreach ($this->tablas as $t) {
			$campos = $conexion->get_definicion_columnas($t, $this->schema_origen);			
			//se generan los stored procedures		   
			$sql .= "CREATE OR REPLACE FUNCTION {$this->schema_logs}.sp_$t() RETURNS trigger AS\n";
			$sql .= "'
				DECLARE
					schema_temp varchar;
					rtabla_usr RECORD;
					rusuario RECORD;
					vusuario VARCHAR(30);
					voperacion varchar;
					vestampilla timestamp;
				BEGIN
					vestampilla := current_timestamp;
					SELECT INTO schema_temp {$this->schema_origen}.recuperar_schema_temp();
					SELECT INTO rtabla_usr * FROM pg_tables WHERE tablename = ''tt_usuario'' AND schemaname = schema_temp;
					IF FOUND THEN
						SELECT INTO rusuario usuario FROM tt_usuario;
						IF FOUND THEN
							vusuario := rusuario.usuario;
						ELSE
							vusuario := user;
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
			$sql .= "auditoria_usuario, auditoria_fecha, auditoria_operacion) VALUES (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= "NEW.{$def['nombre']}, ";
				}
			} 		
			$sql .= "vusuario, vestampilla, voperacion);
					ELSIF TG_OP = ''DELETE'' THEN
						voperacion := ''D'';
						INSERT INTO {$this->schema_logs}.$t (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= $def['nombre'] .  ", ";	
				}
			}		
			$sql .= "auditoria_usuario, auditoria_fecha, auditoria_operacion) VALUES (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= "OLD." . $def['nombre'] .  ", ";
				}
			}
			$sql .= "vusuario, vestampilla, voperacion);
					END IF;
					RETURN NULL;
				END;
			' LANGUAGE 'plpgsql';\n\n
			";
		}	
		$conexion->ejecutar($sql);
	}
	
	protected function crear_triggers() 
	{
		$sql = "";
		foreach ($this->tablas as $t)
	        $sql .= "
	        	CREATE TRIGGER tauditoria_$t AFTER INSERT OR UPDATE OR DELETE
					ON {$this->schema_origen}." . $t . " FOR EACH ROW 
					EXECUTE PROCEDURE {$this->schema_logs}.sp_$t();
			";
		$this->conexion->ejecutar($sql);
	}
	

	
	//------- FUNCIONES DE ELIMINACION DE LOGS -------------------------------------
	protected function eliminar_triggers() 
	{
		$sql = "";
		foreach ($this->tablas as $t) {
	       $sql .= "DROP TRIGGER IF EXISTS tauditoria_$t ON {$this->schema_origen}.$t;\n\n";
		}
		$this->conexion->ejecutar($sql);
	}
	
	protected function eliminar_schema() 
	{
		$sql = "DROP SCHEMA {$this->schema_logs} CASCADE;\n\n";
		$this->conexion->ejecutar($sql);
	}

	//-------- CONSULTAS 
	
	function get_datos($tabla, $filtro = array())
	{
		$where = 'true';
		$order = '';
		if (isset($filtro['desde_fecha'])) {
			$where .= " AND aud.auditoria_fecha::date >= '{$filtro['fecha_desde']}'";
		}
		if (isset($filtro['hasta_fecha'])) {
			$where .= " AND aud.auditoria_fecha::date <= '{$filtro['fecha_hasta']}'";
		}
		if (isset($filtro['usuario'])) {
			$where .= " AND aud.auditoria_usuario = '{$filtro['usuario']}'";
		}
		if (isset($filtro['operacion'])) {
			$where .= " AND aud.auditoria_operacion = '{$filtro['operacion']}'";
		}
		if (isset($filtro['campo']) && isset($filtro['valor'])) {
			$filtro['valor'] = quote($filtro['valor']);
			$where .= " AND aud.{$filtro['campo']} = {$filtro['valor']}";
		}		
		if (isset($filtro['ordenar']) && $filtro['ordenar'] == 'clave') {
			$claves = $this->get_campos_claves($tabla);
			$order = implode(', ', $claves).', ';
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
		/*foreach ($datos as $clave => $valor) {
			$datos[$clave]['log_fecha'] = procesar_timestamp($valor['log_fecha']);
			$datos[$clave]['log_operacion'] = procesar_operacion($valor['log_operacion']);
		}*/
		return $datos;
	}
	
	function get_campos_claves($tabla)
	{
		$cols = $this->conexion->get_definicion_columnas($tabla, $this->schema_origen);
		$pks = array();
		foreach ($cols as $col) {
			if ($col['pk']) {
				$pks[] = $col['nombre'];
			}
		}
		return $pks;		
	}

		
}

?>