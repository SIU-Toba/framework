<?php

/**
 * Reimplementación de la clase @toba_auditoria_tablas_postgres que nos permite
 * ejecutar el proceso de sincronización del esquema de auditoria en el
 * instalador.
 *
 * Se hace de esta forma ya que dicha clase dependía del entorno toba y en el
 * instalador se dispone de un entorno reducido que no la contemplaba.
 *
 * Nota: se requieren ajustes a la clase @db_manager del instalador.
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
	 * @var pdo
	 */
	protected $conexion;

	function __construct($conexion, $schema_origen='diaguita', $schema_logs='diaguita_auditoria', $schema_toba=null)
	{
		$this->conexion = $conexion;
		$this->schema_origen = $schema_origen;
		$this->schema_logs = $schema_logs;
		$this->schema_toba = $schema_toba;
	}

	/**
	 * Genera el esquema de auditoria para la base de negocios.
	 *
	 * Nota: si existe el esquema, lo actualiza migrando los datos.
	 */
	function generar($tablas, $prefijo_tablas)
	{
		if (empty($tablas)) {
			$this->agregar_tablas($prefijo_tablas);
		} else {
			foreach($tablas as $tabla) {
				$this->agregar_tabla($tabla);
			}
		}
		if (! $this->existe()) {
			$this->crear();
		} else {
			$this->migrar();
		}

		//--- Datos anteriores
		$archivo_datos = $_SESSION['path_instalacion'] . "/aplicacion/sql/datos_auditoria.sql";
		if (file_exists($archivo_datos)) {
			db_manager::ejecutar_archivo($this->conexion, $archivo_datos);
		}
		// creo que solo se usa si se hace auditoria del esquema toba
		//$this->proyecto->generar_roles_db();

	}

	static function get_campos_propios()
	{
		return array('auditoria_fecha', 'auditoria_usuario', 'auditoria_operacion', 'operacion_nombre', 'auditoria_id_solicitud');
	}

	function agregar_tablas($prefijo=null)
	{
		$where = '';
		if (isset($prefijo)) {
			$prefijo_sano = db_manager::quote($this->conexion, "$prefijo%");
			$where .= "	AND (tablename LIKE $prefijo_sano)";
		}
		$schema = db_manager::quote($this->conexion, $this->schema_origen);
		$sql = "
				SELECT tablename FROM pg_tables
		        WHERE
		        		tablename NOT LIKE 'pg_%'
		        	AND tablename NOT LIKE 'sql_%'
					AND schemaname = $schema
					$where
				ORDER BY UPPER(tablename);
		";
		 $aux = db_manager::consultar($this->conexion, $sql);
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

	function crear()
	{
		if (! isset($this->tablas)) {
			throw new inst_error('No se recuperaron tablas para el esquema de auditoria');
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
		if (isset($this->schema_toba) && db_manager::existe_schema($this->conexion, $this->schema_toba)) {
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
			$nombre = $this->prefijo.$t;
			if (! db_manager::existe_tabla($this->conexion, $this->schema_logs, $nombre)) {
				$this->crear_tabla($t, $this->schema_origen);
			} else {
				$this->actualizar_tabla($t, $this->schema_origen);
			}
		}

		if (isset($this->schema_toba) && db_manager::existe_schema($this->conexion, $this->schema_toba)) {
			$this->crear_funciones($this->schema_toba);
			foreach ($this->tablas_toba as $t) {
				$nombre = $this->prefijo.$t;
				if (! db_manager::existe_tabla($this->conexion, $this->schema_logs, $nombre)) {
					$this->crear_tabla($t, $this->schema_toba);
				} else {
					$this->actualizar_tabla($t, $this->schema_toba);
				}
			}
		}
		$this->regenerar();
	}

	function regenerar()
	{
		$this->eliminar_triggers($this->tablas, $this->schema_origen);
		$this->crear_sp($this->tablas, $this->schema_origen);
		$this->crear_triggers($this->tablas, $this->schema_origen);

		if (isset($this->schema_toba) && db_manager::existe_schema($this->conexion, $this->schema_toba)) {
			$this->eliminar_triggers($this->tablas_toba, $this->schema_toba);
			$this->crear_sp($this->tablas_toba, $this->schema_toba);
			$this->crear_triggers($this->tablas_toba, $this->schema_toba);
		}
	}

	function purgar($lapso_tiempo = 0)
	{
		foreach($this->tablas as $t) {
			$nombre = $this->prefijo. $t;
			if (db_manager::existe_tabla($this->conexion, $this->schema_logs, $nombre)) {
				$this->purgar_datos($nombre, $lapso_tiempo);
			}
		}
	}

	function eliminar()
	{
		$this->eliminar_triggers($this->tablas, $this->schema_origen);
		if (isset($this->schema_toba) && db_manager::existe_schema($this->conexion, $this->schema_toba)) {
			$this->eliminar_triggers($this->tablas_toba, $this->schema_toba);
		}
		$this->eliminar_schema();
		return true;
	}

	/**
	 * True en caso de existir el esquema con los logs nuevos.
	 * False en caso contrario.
	 */
	function existe()
	{
		$schema = db_manager::quote($this->conexion, $this->schema_logs);
		$sql = "
			SELECT nspname
			FROM pg_catalog.pg_namespace
			WHERE nspname = $schema;
		";
		$r = db_manager::consultar($this->conexion, $sql);
		if (count($r) > 0)
			return true;
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

	protected function crear_lenguaje()
	{
		$sql = "SELECT lanname FROM pg_language WHERE lanname='plpgsql'";
		$rs = db_manager::consultar($this->conexion, $sql);
		if (empty($rs)) {
			$sql = 'CREATE LANGUAGE plpgsql';
			db_manager::ejecutar($this->conexion, $sql);
		}
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
		db_manager::ejecutar($this->conexion, $sql);
	}

	protected function crear_schema()
	{
		$sql = "CREATE SCHEMA {$this->schema_logs}; ";
		db_manager::ejecutar($this->conexion, $sql);
	}


	protected function crear_tabla($t, $schema)
	{
	   $campos = db_manager::get_definicion_columnas($this->conexion, $t, $schema);
	   $sql = "CREATE TABLE {$this->schema_logs}.{$this->prefijo}{$t}(\n";
	   $sql .= "auditoria_usuario varchar(30),
	   			auditoria_fecha timestamp,
	   			auditoria_operacion char(1),
	   			auditoria_id_solicitud integer,
	   	";
		foreach ($campos as $campo => $def) {
			if ($def['tipo_sql'] != 'bytea') {
				$sql .= $def['nombre'] .  " " . $def['tipo_sql'] . ",\n";
			}
		}
		$sql = substr($sql, 0, -2);
		$sql .= ");\n";
		db_manager::ejecutar($this->conexion, $sql);
	}

	protected function actualizar_tabla($origen, $schema)
	{
		$destino = $this->prefijo.$origen;
		$negocio = db_manager::get_definicion_columnas($this->conexion, $origen, $schema);
		$negocio[] = array(
    			'nombre' => 'auditoria_usuario',
			    'tipo_sql' => 'character varying(30)',
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
		$logs = db_manager::get_definicion_columnas($this->conexion, $destino, $this->schema_logs);

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
				db_manager::ejecutar($this->conexion, $sql);
			}
		}

	}

	protected function crear_sp($tablas, $schema)
	{
		$sql = "";
		foreach ($tablas as $t) {
			$campos = db_manager::get_definicion_columnas($this->conexion, $t, $schema);
			//se generan los stored procedures
			$sql .= "CREATE OR REPLACE FUNCTION {$this->schema_logs}.sp_$t() RETURNS trigger AS\n";
			$sql .= "'
				DECLARE
					schema_temp varchar;
					rtabla_usr RECORD;
					rusuario RECORD;
					vusuario VARCHAR(30);
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
			$sql .= "auditoria_usuario, auditoria_fecha, auditoria_operacion, auditoria_id_solicitud) VALUES (";
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
			$sql .= "auditoria_usuario, auditoria_fecha, auditoria_operacion, auditoria_id_solicitud) VALUES (";
			foreach ($campos as $campo => $def) {
				if ($def['tipo_sql'] != 'bytea') {
					$sql .= "OLD." . $def['nombre'] .  ", ";
				}
			}
			$sql .= "vusuario, vestampilla, voperacion, vid_solicitud);
					END IF;
					RETURN NULL;
				END;
			' LANGUAGE 'plpgsql';\n\n
			";
		}
		db_manager::ejecutar($this->conexion, $sql);
	}

	protected function crear_triggers($tablas, $schema)
	{
		$sql = "";
		foreach ($tablas as $t)
	        $sql .= "
	        	CREATE TRIGGER tauditoria_$t AFTER INSERT OR UPDATE OR DELETE
					ON $schema." . $t . " FOR EACH ROW
					EXECUTE PROCEDURE {$this->schema_logs}.sp_$t();
			";
		db_manager::ejecutar($this->conexion, $sql);
	}



	//------- FUNCIONES DE ELIMINACION DE LOGS -------------------------------------
	protected function eliminar_triggers($tablas, $schema)
	{
		$sql = "";
		foreach ($tablas as $t) {
	       $sql .= "DROP TRIGGER IF EXISTS tauditoria_$t ON $schema.$t;\n\n";
		}
		db_manager::ejecutar($this->conexion, $sql);
	}

	protected function eliminar_schema()
	{
		$sql = "DROP SCHEMA {$this->schema_logs} CASCADE;\n\n";
		db_manager::ejecutar($this->conexion, $sql);
	}

	protected function eliminar_sp()
	{
		$sql = '';
		foreach ($this->tablas as $t) {
			$sql .= "DROP FUNCTION IF EXISTS {$this->schema_logs}.sp_$t();\n";
		}
		db_manager::ejecutar($this->conexion, $sql);
	}

	//-------- CONSULTAS

	function get_datos($tabla, $filtro = array())
	{
		$where = 'true';
		$order = '';

		$filtro_valores = db_manager::quote($this->conexion, $filtro);
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
		$datos = db_manager::consultar($this->conexion, $sql);
		/*foreach ($datos as $clave => $valor) {
			$datos[$clave]['log_fecha'] = procesar_timestamp($valor['log_fecha']);
			$datos[$clave]['log_operacion'] = procesar_operacion($valor['log_operacion']);
		}*/
		return $datos;
	}

	function get_campos_claves($tabla)
	{
		$tabla = substr($tabla, strlen($this->prefijo));
		$schema = $this->schema_origen;
		if (! db_manager::existe_tabla($this->conexion, $this->schema_origen, $tabla)
			&& isset($this->schema_toba) && db_manager::existe_schema($this->conexion, $this->schema_toba)) {
			$schema = $this->schema_toba;
		}
		$cols = db_manager::get_definicion_columnas($this->conexion, $tabla, $schema);
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
		$tiempo = db_manager::quote($this->conexion, $lapso_tiempo . ' months' );
		$sql = 'DELETE FROM '. $this->schema_logs . ".$tabla WHERE auditoria_fecha::timestamp  < (now() - interval $tiempo);";
		db_manager::ejecutar($this->conexion, $sql);
	}
}

?>
