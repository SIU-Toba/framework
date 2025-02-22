<?php
/**
 * Driver de conexi�n con postgres
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_postgres7 extends toba_db
{
	protected $cache_metadatos = array();		 //Guarda un cache de los metadatos de cada tabla
	protected $schema;
	protected $transaccion_abierta = false;

	/**
	 * Constructor de la clase
	 * @param mixed $profile	Host de la conexion
	 * @param mixed $usuario	Usuario de la conexion
	 * @param mixed $clave		Clave del usuario de conexion
	 * @param mixed $base		Base a la cual conectar
	 * @param mixed $puerto	Puerto del motor
	 * @param mixed $server	Nombre de la instancia del motor
	 * @param mixed $sslmode	Tipo de conexion SSL
	 * @param mixed $cert_path	Path al certificado cliente
	 * @param mixed $key_path	Path a la clave del certificado cliente
	 * @param mixed $crl_path	Path a la lista de revocacion de certificados
	 * @param mixed $cacert_path	Path al certificado de la CA
	 */
	function __construct($profile, $usuario, $clave, $base, $puerto, $server='', $sslmode='', $cert_path='', $key_path='', $crl_path='', $cacert_path='')
	{
		$this->motor = "postgres7";
		parent::__construct($profile, $usuario, $clave, $base, $puerto, $server, $sslmode, $cert_path, $key_path, $crl_path, $cacert_path);
		//$this->setear_datestyle_iso();
	}

	/**
	 * Retorna el string de conexion para el motor
	 * @return string
	 */
	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		$ssl = $certs = '';
		if ($this->sslmode != '') {
			$ssl =  "sslmode='{$this->sslmode}'";
			$certs = $this->get_config_certificados();
			$certs = (! empty($certs)) ? $this->dsn_parameters_implode(';', $certs) : '';
		}
		return "pgsql:host=$this->profile;dbname=$this->base;$puerto;$ssl;$certs";
	}

	/**
	 * Retorna los parametros de la conexion
	 * @return array
	 */
	function get_parametros()
	{
		$parametros = parent::get_parametros();
		$parametros['schema'] = $this->schema;
		return $parametros;
	}

	/**
	 * Retorna la configuracion de los certificados para una conexion SSL
	 * @return array
	 */
	function get_config_certificados()
	{
		$certs = array();
		if (! is_null($this->key_path)) {
			$certs['sslkey']    = $this->key_path;
		}
		if (! is_null($this->cert_path)) {
			$certs['sslcert'] = $this->cert_path;
		}
		if (! is_null($this->cacert_path)) {
			$certs['sslrootcert'] =$this->cacert_path;
		}
		if (! is_null($this->crl_path)) {
			$certs['sslcrl'] = $this->crl_path;
		}
		return $certs;
	}

	/**
	* Determina que schema se utilizar� por defecto para la ejecuci�n de consultas, comandos y consulta de metadatos
	* @param string $schema
	* @param boolean $ejecutar
	* @param boolean $fallback_en_public
	* @return mixed
	*/
	function set_schema($schema, $ejecutar = true, $fallback_en_public=false)
	{
		$this->schema = $schema;
		$sql = "SET search_path TO $schema";
		if ($fallback_en_public) {
			$sql .= ', public';
		}
		$sql .= ';';
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 * Devuelve el nombre del Schema actual
	 * @return mixed
	 */
	function get_schema()
	{
		if (isset($this->schema)) {
			return $this->schema;
		}
	}

	/**
	 * Fija el encoding del cliente para la conexion
	 * @param string $encoding	Identificador del encoding a usar
	 * @param boolean $ejecutar	Indica si la sentencia debe ser ejecutada o simplemente generada
	 * @return string			Sentencia generada para el encoding especificado
	 */
	function set_encoding($encoding, $ejecutar = true)
	{
		$sql = "SET CLIENT_ENCODING TO '$encoding';";
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 * Fija el formato de fecha a ISO
	 */
	function set_datestyle_iso()
	{
		$sql = "SET datestyle TO 'iso';";
		$this->ejecutar($sql);
	}

	/**
	 *  Crea el lenguaje plpgsql unicamente si el mismo aun no existe para la base de datos.
	 */
	function crear_lenguaje_procedural()
	{
		if (! $this->existe_lenguaje('plpgsql')) {
			$this->crear_lenguaje('plpgsql');
		}
	}

	/**
	*  Recupera el valor actual de una secuencia
	* @param string $secuencia Nombre de la secuencia
	* @param boolean ejecutar   Indica si la sentencia debe ser ejecutada o solo generada
	* @return mixed Actual numero de la secuencia o sentencia generada
	*/
	function recuperar_secuencia($secuencia, $ejecutar=true)
	{
		$sql = "SELECT currval('$secuencia') as seq;";
		if (! $ejecutar) { return $sql; }
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}

	/**
	 * Recupera el proximo valor de la secuencia
	 * @param string $secuencia
	 * @param boolean ejecutar   Indica si la sentencia debe ser ejecutada o solo generada
	 * @return mixed Siguiente numero de la secuencia o sentencia generada
	 */
	function recuperar_nuevo_valor_secuencia($secuencia, $ejecutar = true)
	{
		$sql = "SELECT nextval('$secuencia') as seq;";
		if (! $ejecutar) { return $sql; }
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}

	/**
	 * Retraza o activa el chequeo de constraints
	 * @deprecated since version 3.0.1
	 * @see retrasar_constraints()
	 * @param boolean $retrazar
	 */
	function retrazar_constraints($retrazar = true)
	{
		$this->retrasar_constraints($retrazar);
	}

	/**
	 * Retraza o activa el chequeo de constraints
	 * @param boolean $retrasar
	 */
	function retrasar_constraints($retrasar = true)
	{
		$tipo = $retrasar ? 'DEFERRED' : 'IMMEDIATE';
		$this->ejecutar("SET CONSTRAINTS ALL $tipo;");
		//toba_logger::instancia()->debug("************ Se cambia el chequeo de constraints ($tipo) ****************", 'toba');
		$this->log("************ Se cambia el chequeo de constraints ($tipo) ****************", 'debug', 'toba');
	}

	/**
	 * Abre una transaccion en la conexion
	 */
	function abrir_transaccion()
	{
		$sql = 'BEGIN TRANSACTION;';
		$this->ejecutar($sql);
		$this->transaccion_abierta = true;
		//toba_logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Aborta una transaccion en la conexion
	 */
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK TRANSACTION;';
		$this->ejecutar($sql);
		$this->transaccion_abierta = false;
		//toba_logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Realiza el commit de la transaccion
	 */
	function cerrar_transaccion()
	{
		$sql = "COMMIT TRANSACTION;";
		$this->ejecutar($sql);
		$this->transaccion_abierta = false;
		//toba_logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Devuelve true si hay una transacci�n abierta y false en caso contrario
	 * @return boolean
	 */
	function transaccion_abierta()
	{
		return $this->transaccion_abierta;
	}

	/**
	 * Genera un savepoint dentro de la trasaccion
	 * @param string $nombre
	 */
	function agregar_savepoint($nombre)
	{
		$sql = "SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		//toba_logger::instancia()->debug("************ agregar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ agregar savepoint $nombre ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Aborta un savepoint y deja el estado anterior en la transaccion
	 * @param string $nombre
	 */
	function abortar_savepoint($nombre)
	{
		$sql = "ROLLBACK TO SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		//toba_logger::instancia()->debug("************ abortar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ abortar savepoint $nombre ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	 * Libera un savepoint
	 * @param string $nombre
	 */
	function liberar_savepoint($nombre)
	{
		$sql = "RELEASE SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		//toba_logger::instancia()->debug("************ liberar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
		$this->log("************ liberar savepoint $nombre ($this->base@$this->profile) ****************", 'debug', 'toba');
	}

	/**
	* Insert de datos desde un arreglo hacia una tabla. Ahora usa pdo!
	* @param string $tabla Nombre de la tabla en la que se insertar�n los datos
	* @param array $datos Los datos a insertar: cada elemento del arreglo ser� un registro en la tabla.
	* @param string $delimitador Separador de datos de cada fila.
	* @param string $valor_nulo Cadena que se utlilizar� como valor nulo.
	* @return boolean Retorn TRUE en caso de �xito o FALSE en caso de error.
	*/
	function insert_masivo($tabla,$datos,$delimitador="\t",$valor_nulo="\\N") {
                $funciono = true;
                // se genera el sql equivalente para los logs
                $sql = "-- COPY $tabla FROM stdin de ".count($datos)." filas.";
                $sql = $this->pegar_estampilla_log($sql);
                try {
                   if ($this->registrar_ejecucion) {
                     $this->registro_ejecucion[] = $sql;
                   }
                   if (! $this->desactivar_ejecucion) {
                      if ($this->debug) $this->log_debug_inicio($sql);
                      $funciono = $this->conexion->pgsqlCopyFromArray($tabla,$datos,$delimitador,$valor_nulo);
                      if ($this->debug) $this->log_debug_fin();
                   }
                } catch (PDOException $e) {
                   $this->log($e->getMessage(), 'error');
                   $ee = new toba_error_db($e, $sql);
                   throw $ee;
                }
                return $funciono;
	}

	/**
	 * Retorna un recurso pg_connect usando los mismos parametros que PDO
	 * @return resource
	 */
	function get_pg_connect_nativo()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		$host = "host={$this->profile}";
		$base = "dbname={$this->base}";
		$usuario = "user={$this->usuario}";
		$clave = "password={$this->clave}";
		$conn_string = "$host $puerto $base $usuario $clave";
		$dbconn = pg_connect($conn_string);
		if (isset($this->schema)) {
			$sql = "SET search_path TO {$this->schema};";
			pg_query($dbconn, $sql);
		}
		return $dbconn;
	}

	//------------------------------------------------------------------------
	//-- SCHEMAS Postgres
	//------------------------------------------------------------------------
	/**
	 * Determina si existe un schema con el nombre indicado
	 * @param mixed $esquema
	 * @return boolean
	 */
	function existe_schema($esquema)
	{
		$esquema = $this->quote($esquema);
		$sql = "SELECT
					count(*) as cant
				FROM
					information_schema.schemata
				WHERE
					schema_name = $esquema;";
		$rs = $this->consultar_fila($sql);
		return $rs['cant'] > 0;
	}

	/**
	 * Elimina un schema de la base
	 * @param mixed $schema		Nombre del schema
	 * @param boolean $ejecutar		Indica si la sentencia se debe ejecutar o solo generarse
	 * @return mixed				Estado de la ejecucion o sentencia generada
	 */
	function borrar_schema($schema, $ejecutar = true)
	{
		$sql = "DROP SCHEMA $schema CASCADE;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Elimina un schema solo si existe
	 * @param mixed $schema
	 */
	function borrar_schema_si_existe($schema)
	{
		if ($this->existe_schema($schema)) {
			$this->borrar_schema($schema);
		}
	}

	/**
	 * Crea un schema con un identificador indicado
	 * @param mixed $schema
	 * @param boolean $ejecutar		Indica si la sentencia se debe ejecutar o solo generarse
	 * @return mixed				Estado de la ejecucion o sentencia generada
	 */
	function crear_schema($schema, $ejecutar = true)
	{
		$sql = "CREATE SCHEMA $schema;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Renombra un schema de su identificador actual al nuevo
	 * @param mixed $actual
	 * @param mixed $nuevo
	 * @param boolean $ejecutar		Indica si la sentencia se debe ejecutar o solo generarse
	 * @return mixed				Estado de la ejecucion o sentencia generada
	 */
	function renombrar_schema($actual, $nuevo, $ejecutar = true)
	{
		$sql = "ALTER SCHEMA $actual RENAME TO $nuevo;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Devuelve la lista de schemas disponibles en la base
	 * @return array
	 */
	function get_lista_schemas_disponibles()
	{
		$sql = 'SELECT n.nspname AS esquema
				FROM pg_catalog.pg_namespace n
				WHERE   (n.nspname !~ \'^pg_\')
					AND  (n.nspname <> \'information_schema\')
					AND  (n.nspname !~ \'_backup$\')
					AND  (n.nspname !~ \'_logs$\')
				ORDER BY 1;';
		return $this->consultar($sql);
	}

	/**
	 * Determina si existe el lenguaje procedural indicado o no
	 * @param string $lang
	 * @return boolean
	 */
	function existe_lenguaje($lang)
	{
		$lang = $this->quote($lang);
		$sql = "SELECT COUNT(*) FROM pg_language WHERE lanname=$lang;";
		$res = $this->consultar($sql);
		return $res[0]['count'] > 0;
	}

	/**
	 * Crea el lenguaje procedural indicado
	 * @param string $lang Identificador del lenguaje procedural
	 * @return mixed	Resultado de la ejecucion
	 */
	function crear_lenguaje($lang)
	{
		$sql = "CREATE LANGUAGE $lang;";
		return $this->ejecutar($sql);
	}

	/**
	 * Clona el schema actual en $nuevo_schema. FUNCIONA EN POSTGRES >= 8.3
	 * @param string $actual el nombre del schema a clonar
	 * @param string $nuevo el nombre del nuevo schema
	 */
	public function clonar_schema($actual, $nuevo)
	{
		if (!$this->existe_lenguaje('plpgsql')) {
			$this->crear_lenguaje('plpgsql');
		}

		$sql = "
			CREATE OR REPLACE FUNCTION clone_schema(source_schema text, dest_schema text) RETURNS void AS
			\$BODY$
			DECLARE
			  objeto text;
			  buffer text;
			BEGIN
				EXECUTE 'CREATE SCHEMA ' || dest_schema ;

				FOR objeto IN
					SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = source_schema
				LOOP
					buffer := dest_schema || '.' || objeto;
					EXECUTE 'CREATE SEQUENCE ' || buffer;
					BEGIN
						--EXECUTE 'SELECT setval(' || quote_literal(buffer) ||', (SELECT nextval(' || quote_literal(source_schema ||'.'|| objeto) || '))' || ')';
						EXECUTE 'SELECT setval(' || quote_literal(buffer) ||', (SELECT last_value FROM ' || source_schema ||'.'|| objeto || ')' || ')';
					EXCEPTION WHEN OTHERS THEN
					END;
				END LOOP;

				FOR objeto IN
					SELECT table_name::text FROM information_schema.tables WHERE table_schema = source_schema
				LOOP
					buffer := dest_schema || '.' || objeto;
					EXECUTE 'CREATE TABLE ' || buffer || ' (LIKE ' || source_schema || '.' || objeto || ' INCLUDING CONSTRAINTS INCLUDING INDEXES INCLUDING DEFAULTS)';
					EXECUTE 'INSERT INTO ' || buffer || '(SELECT * FROM ' || source_schema || '.' || objeto || ')';
				END LOOP;

			END;
			\$BODY$
			LANGUAGE plpgsql;
		";

		$this->ejecutar($sql);

		$actual = $this->quote($actual);
		$nuevo = $this->quote($nuevo);
		$sql = "SELECT clone_schema($actual, $nuevo);";
		$this->ejecutar($sql);
	}

	//---------------------------------------------------------------------
	//-- PERMISOS
	//---------------------------------------------------------------------
	/**
	 * Devuelve el identificador del usuario actualmente conectado
	 * @return string
	 */
	function get_usuario_actual()
	{
		$sql = 'SELECT CURRENT_USER AS usuario;';
		$datos = toba::db()->consultar_fila($sql);
		return $datos['usuario'];
	}

	/**
	 * Devuelve el identificador del rol actual
	 * @return string
	 */
	function get_rol_actual()
	{
		$sql = 'SHOW ROLE;';
		$datos = $this->consultar_fila($sql);
		return $datos['role'];
	}

	/**
	 * Indica cual sera el rol activo
	 * @param string $rol
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function set_rol($rol, $ejecutar = true)
	{
		$sql = "SET ROLE $rol;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Determina si existe el rol en el motor
	 * @param string $rol
	 * @return boolean
	 */
	function existe_rol($rol)
	{
		$datos = $this->listar_roles($rol);
		return !empty($datos);
	}

	/**
	 * Devuelve una lista de los roles disponibles
	 * @param string $rol Identificador de rol [Opcional]
	 * @return array
	 */
	function listar_roles($rol = null)
	{
		$sql = 'SELECT rolname FROM pg_roles ';
		if (! is_null($rol)) {
			$rol = $this->quote($rol);
			$sql .= " WHERE rolname = $rol;";
		}
		return $this->consultar($sql);
	}

	/**
	 * Crea un rol en el motor
	 * @param string $rol
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function crear_rol($rol, $ejecutar=true)
	{
		$sql = "CREATE ROLE $rol NOINHERIT;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Crea un usuario en el motor
	 * @param string $rol
	 * @param string $password
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function crear_usuario($rol, $password, $ejecutar = true)
	{
		$password = $this->quote($password);
		$sql = "CREATE ROLE $rol NOINHERIT LOGIN PASSWORD $password;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Elimina un rol del motor
	 * @param string $rol
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function borrar_rol($rol, $ejecutar = true)
	{
		$sql = "DROP ROLE IF EXISTS $rol;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Asigna un rol especifico a un usuario
	 * @param string $usuario
	 * @param string $rol
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function grant_rol($usuario, $rol, $ejecutar = true)
	{
		$sql = "GRANT $rol TO $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Le asigna permisos a un usuario sobre un schema
	 * @param string $usuario	Identificador del usuario
	 * @param string $schema	Identificador del schema
	 * @param string $permisos	Lista de permisos separada por coma
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function grant_schema($usuario, $schema, $permisos = 'USAGE', $ejecutar = true)
	{
		$sql = "GRANT $permisos ON SCHEMA \"$schema\" TO $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Quita los permisos de un usuario sobre un schema
	 * @param string $usuario	Identificador del usuario
	 * @param string $schema	Identificador del schema
	 * @param string $permisos	Lista de permisos separada por coma
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function revoke_schema($usuario, $schema, $permisos = 'ALL PRIVILEGES', $ejecutar = true)
	{
		$sql = array();
		//--Revoka las tablas dentro
		$sql_rs = "SELECT
					relname
				FROM pg_class c
					JOIN pg_namespace ns ON (c.relnamespace = ns.oid)
				WHERE
						relkind in ('r','v','S')
					AND nspname = '$schema' ;";
		$rs = $this->consultar($sql_rs);
		foreach ($rs as $tabla) {
			$sql[] = "REVOKE $permisos ON $schema.{$tabla['relname']} FROM $usuario CASCADE;";
		}

		$sql[] = "REVOKE $permisos ON SCHEMA \"$schema\" FROM $usuario;";
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 * Quita permisos a un usuario sobre un conjunto de tablas
	 * @param string $usuario	Identificador del usuario
	 * @param string $schema	Identificador del schema
	 * @param array  $tablas		Lista de tablas
	 * @param string $permisos	Lista de permisos separada por coma
	 * @param boolean $ejecutar
	 * @return mixed
	 */

	function revoke_tablas($usuario, $schema, $tablas, $permisos = 'ALL PRIVILEGES', $ejecutar=true)
	{
		$sql = array();
		foreach ($tablas as $tabla) {
			$sql[] = "REVOKE $permisos ON $schema.{$tabla} FROM $usuario CASCADE;";
		}
		if (! $ejecutar) {
			return $sql;
		}
		$this->ejecutar($sql);
	}

	/**
	 * Le quita un rol a un usuario especifico
	 * @param string $usuario
	 * @param string $rol
	 * @param boolena $ejecutar
	 * @return mixed
	 */
	function revoke_rol($usuario, $rol, $ejecutar = true)
	{
		$sql = "REVOKE $rol FROM $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 * Da permisos especificos a todas las tablas de un esquema dado
	 * @param string $usuario	Identificador de usuario
	 * @param string $schema	Identificador de schema
	 * @param string $privilegios	Lista de permisos separada por coma
	 */
	function grant_tablas_schema($usuario, $schema, $privilegios ='ALL PRIVILEGES')
	{
		$sql = "SELECT
					relname
				FROM pg_class c
					JOIN pg_namespace ns ON (c.relnamespace = ns.oid)
				WHERE
						relkind in ('r','v','S')
					AND nspname = ".$this->quote($schema);
		$tablas = $this->consultar($sql);
		$this->grant_tablas($usuario, $schema, aplanar_matriz($tablas, 'relname'), $privilegios);
	}

	/**
	 * Da permisos especificos a todas las tablas de un esquema dado
	 * @param string $usuario	Identificador de usuario
	 * @param string $schema	Identificador de schema
	 * @param array  $tablas		Lista de tablas
	 * @param string $privilegios	Lista de permisos separada por coma	 *
	 * @param boolean $ejecutar
	 * @return mixed
	 */
	function grant_tablas($usuario, $schema, $tablas, $privilegios ='ALL PRIVILEGES', $ejecutar = true)
	{
		$sql = array();
		foreach ($tablas as $tabla) {
			$sql[] = "GRANT $privilegios ON $schema.$tabla TO $usuario;";
		}
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 *  Asigna permisos a un usuario sobre las funciones
	 * @param string $usuario	Identificador de usuario
	 * @param string $schema	Identificador de schema
	 * @param string $privilegios	Lista de permisos separada por coma
	 * @param boolean $ejecutar
	 * @return string
	 */
	function grant_sp_schema($usuario, $schema,  $privilegios = 'ALL PRIVILEGES', $ejecutar = true)
	{
		$stored_procedures = $this->get_sp_schema($schema);				//Busco todos los stored procedures del schema
		$sql = "GRANT $privilegios ON FUNCTION ";
		foreach ($stored_procedures as $sp) {												//Los agrego separados por coma para usar 1 sola SQL
			$sql .= " $schema.$sp(), ";
		}
		$sql = substr($sql, 0, -2) . " TO $usuario;";											//Agrego el rol/usuario beneficiario
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 *  Quita permisos a un usuario sobre las funciones
	 * @param string $usuario	Identificador de usuario
	 * @param string $schema	Identificador de schema
	 * @param string $privilegios	Lista de permisos separada por coma
	 * @param boolean $ejecutar
	 * @return string
	 */
	function revoke_sp_schema($usuario, $schema,  $privilegios = 'ALL PRIVILEGES', $ejecutar = true)
	{
		$stored_procedures = $this->get_sp_schema($schema);				//Busco todos los stored procedures del schema
		$sql = "REVOKE $privilegios ON FUNCTION ";
		foreach ($stored_procedures as $sp) {
			$sql .= "$schema.$sp(), ";																	//Los agrego separados por coma para usar 1 sola SQL
		}
		$sql = substr($sql, 0 , -2) .  " FROM $usuario;";
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}

	/**
	 * Devuelve una lista de funciones en un schema dado
	 * @param sting $schema
	 * @return array
	 */
	function get_sp_schema($schema)
	{
		$sql = "SELECT
											proname
						 FROM pg_proc p
							JOIN pg_namespace ns ON (p.pronamespace = ns.oid)
						WHERE
							nspname =  ".$this->quote($schema);
		$stored_proc = $this->consultar($sql);
		return aplanar_matriz($stored_proc, 'proname');								//Devuelvo la matriz sin el subindice de nombre de columna
	}

	//------------------------------------------------------------------------
	//-- INSPECCION del MODELO de DATOS
	//------------------------------------------------------------------------
	/**
	 * Devuelve la lista de tablas y vistas para el schema actual
	 * @return array
	 */
	function get_lista_tablas_y_vistas()
	{
		$esquema = null;
		if (isset($this->schema)) {
			$esquema = $this->schema;
		}
		return $this->get_lista_tablas_bd(true, $esquema);
	}

	/**
	 * Devuelve una lista de tablas segun schema (default actual), puede incluir vistas
	 * @param boolean $incluir_vistas
	 * @param string $esquema
	 * @return array
	 */
	function get_lista_tablas($incluir_vistas=false, $esquema=null)
	{
		if (is_null($esquema) && isset($this->schema)) {
			$esquema = $this->schema;
		}
		return $this->get_lista_tablas_bd($incluir_vistas, $esquema);
	}

	/**
	 * Devuelve una lista de tablas segun schemas, puede incluir vistas
	 * @param boolean $incluir_vistas
	 * @param string $esquema
	 * @return array
	 */
	function get_lista_tablas_bd($incluir_vistas=false, $esquema=null)
	{
		$sql_esquema = '';
		if (! is_null($esquema)) {
			$esquema = $this->quote($esquema);
			if (is_array($esquema)) {
				$sql_esquema .= " AND schemaname IN (" .implode(',' , $esquema) .")" ;
			} else {
				$sql_esquema .= " AND schemaname= $esquema " ;
			}
		}
		$sql = "SELECT tablename as nombre,
					  schemaname as esquema
				FROM
					pg_tables
				WHERE
						tablename NOT LIKE 'pg_%'
					AND tablename NOT LIKE 'sql_%'
					AND schemaname NOT LIKE 'pg_temp_%'
					AND schemaname != 'information_schema'
					$sql_esquema
		";
		if ($incluir_vistas) {
			$sql .= "
				UNION
				SELECT viewname as nombre,
						schemaname as esquema
				FROM
					pg_views
				WHERE
						viewname NOT LIKE 'pg_%'
					AND viewname NOT LIKE 'sql_%'
					AND schemaname NOT LIKE 'pg_temp_%'
					AND schemaname != 'information_schema'
					$sql_esquema
			";
		}
		$sql .= 'ORDER BY nombre;';
		return $this->consultar($sql);
	}

	/**
	 * Determina si existe la tabla en el schema indicado
	 * @param string $schema
	 * @param string $tabla
	 * @return boolean
	 */
	function existe_tabla($schema, $tabla)
	{
		$tabla = $this->quote($tabla);
		$schema = $this->quote($schema);

		$sql = "SELECT
					table_name
				FROM
					information_schema.tables
				WHERE
					table_name = $tabla AND
					table_schema= $schema;";

		$rs = $this->consultar_fila($sql);
		return !empty($rs);
	}

	/**
	 * Determina si existe la columna en la tabla indicada
	 * @param string $columna
	 * @param string $tabla
	 * @return boolean
	 */
	function existe_columna($columna, $tabla)
	{
		$tabla = $this->quote($tabla);
		$sql = "
				SELECT 	a.attname as nombre
				FROM 	pg_class c,
						pg_type t,
						pg_attribute a
				WHERE c.relkind in ('r','v')
				AND c.relname= $tabla
				AND a.attname not like '....%%'
				AND a.attnum > 0
				AND a.atttypid = t.oid
				AND a.attrelid = c.oid
				ORDER BY a.attnum;
		";
		foreach ($this->consultar($sql) as $campo) {
			if ($campo['nombre'] == $columna) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna la lista de secuencias en el schema
	 * @param string $esquema
	 * @return array
	 */
    public function get_lista_secuencias(?string $esquema = null):array
    {
        //alter sequence my_sequence owned by my_table_manually.id;
        $where = '';
        if (! is_null($esquema)) {
            $esquema = $this->quote($esquema);
            $where .= " AND n.nspname = $esquema" ;
        } elseif (isset($this->schema)) {
            $esquema = $this->quote($this->schema);
            $where .= " AND n.nspname = $esquema" ;
        }
        $sql = '
			SELECT
				c.relname as tabla,
				a.attname as campo,
                t.typname as tipo,
                substring(pg_get_expr(d.adbin, a.attrelid) from  \'nextval%#"[a-z_.]+#"%\' for \'#\' ) as secuencia, 
				substring(pg_get_serial_sequence( n.nspname || \'.\' || c.relname, a.attname),  n.nspname || \'\.(.*)\' )  as nombre     --- Elimina el nombre del schema
			FROM
                 pg_catalog.pg_attribute a
            JOIN pg_catalog.pg_attrdef adef ON a.attrelid=adef.adrelid AND a.attnum=adef.adnum
            JOIN pg_catalog.pg_type t ON a.atttypid=t.oid
            JOIN pg_catalog.pg_class c ON a.attrelid=c.oid
            JOIN pg_catalog.pg_namespace as n ON c.relnamespace = n.oid
            JOIN pg_catalog.pg_attrdef d ON ( d.adrelid = a.attrelid AND d.adnum = a.attnum)
			WHERE
			 	(
                    pg_get_expr(d.adbin, a.attrelid) IS NOT NULL
                OR
                    pg_get_serial_sequence( n.nspname || \'.\' || c.relname, a.attname) IS NOT NULL
                )
			 	AND a.attnum > 0 
                AND a.attnotnull = true
                AND NOT a.attisdropped '.
                $where .
            ' ORDER BY a.attname';
        return $this->consultar($sql);
    }

    /**
     * Retorna lista de secuencias usando vista de pg_catalog
     * @param string|null $esquema
     * @return array
     */
    public function get_lista_secuencias_exportacion(?string $esquema = null): array
    {
        $where = '';
        if (! is_null($esquema)) {
            $esquema = $this->quote($esquema);
            $where .= " schemaname = $esquema" ;
        } elseif (isset($this->schema)) {
            $esquema = $this->quote($this->schema);
            $where .= " schemaname = $esquema" ;
        }

        $sql = 'SELECT sequencename as nombre, coalesce(last_value, start_value) as valor FROM pg_catalog.pg_sequences WHERE '. $where;
        return $this->consultar($sql);
    }

	/**
	* Busca la definicion de un TABLA. Cachea los resultados por un pedido de pagina
	* @param string $tabla
	* @param string $esquema
	* @return array
	* @throws toba_error
	*/
	function get_definicion_columnas($tabla, $esquema=null)
	{
		$where = '';
		if (isset($esquema)) {
			$esquema = $this->quote($esquema);
			$where .= " AND n.nspname = $esquema" ;
		}
		if (isset($this->cache_metadatos[$tabla])) {
			return $this->cache_metadatos[$tabla];
		}
		$tabla_sana = $this->quote($tabla);
		//1) Busco definicion
		$sql =
            "SELECT a.attname as 			nombre,
                    t.typname as 			tipo,
                    a.attlen as 			tipo_longitud,
                    a.atttypmod as 			longitud,
                    format_type(a.atttypid, a.atttypmod) as tipo_sql,
                    a.attnotnull as 		not_null,
                    CASE
                        WHEN a.attidentity IN ('a', 'd') THEN true
                        ELSE a.atthasdef
                    END AS tiene_predeterminado,
                    pg_get_expr(d.adbin, a.attrelid) as  valor_predeterminado,
                    substring(pg_get_serial_sequence( n.nspname || '.' || c.relname, a.attname),  n.nspname || '\.(.*)' ) as secuencia, --- Fuerza el schema para poder consultar desde cualquier lugar, luego lo retira por legibilidad
                    fc.relname				as fk_tabla,
                    fa.attname				as fk_campo,
                    a.attnum as 			orden,
                    c.relname as			tabla,
                    EXISTS (SELECT
                                indisprimary
                            FROM
                                pg_index i
                                INNER JOIN pg_class ic ON ic.oid = i.indexrelid AND indisprimary = TRUE
                            WHERE
                            (	a.attrelid = i.indrelid
                                AND (i.indkey[0] = a.attnum
                                OR i.indkey[1] = a.attnum
                                OR i.indkey[2] = a.attnum
                                OR i.indkey[3] = a.attnum
                                OR i.indkey[4] = a.attnum
                                OR i.indkey[5] = a.attnum
                                OR i.indkey[6] = a.attnum
                                OR i.indkey[7] = a.attnum)
                                )
                        ) as pk,
                    EXISTS (SELECT
                                indisunique
                            FROM
                                pg_index i
                                INNER JOIN pg_class ic ON ic.oid = i.indexrelid AND indisunique = TRUE
                            WHERE
                            (   a.attrelid = i.indrelid
                                AND (i.indkey[0] = a.attnum
                                OR i.indkey[1] = a.attnum
                                OR i.indkey[2] = a.attnum
                                OR i.indkey[3] = a.attnum
                                OR i.indkey[4] = a.attnum
                                OR i.indkey[5] = a.attnum
                                OR i.indkey[6] = a.attnum
                                OR i.indkey[7] = a.attnum)
                                )
                        ) as uk
            FROM 	pg_class c,
                    pg_type t,
                    pg_namespace as n,
                    pg_attribute a
            LEFT OUTER JOIN pg_attrdef d
                            ON ( d.adrelid = a.attrelid AND d.adnum = a.attnum)
            --- Foreign Keys
            LEFT OUTER JOIN (
                            pg_constraint const
                            INNER JOIN pg_class fc ON fc.oid = const.confrelid
                            INNER JOIN pg_attribute fa ON (fa.attrelid = const.confrelid
                                                            AND fa.attnum = const.confkey[1]
                                                            AND const.confkey[2] IS NULL)
                            )
                    ON (const.conrelid = a.attrelid
                                    AND const.contype='f'
                                    AND const.conkey[1] = a.attnum
                    )
            WHERE
                    c.relkind in ('r','v')
                AND c.relname=$tabla_sana
                AND a.attname not like '....%%'
                AND a.attnum > 0
                AND a.atttypid = t.oid
                AND a.attrelid = c.oid
                AND c.relnamespace = n.oid
                    $where
            ORDER BY a.attnum;";

		$columnas = $this->consultar($sql);
		if(!$columnas){
			$this->log("La tabla '$tabla' no existe", 'error');
			throw new toba_error('Alguna tabla no existe');
		}
		//2) Normalizo VALORES
		$columnas_booleanas = array('uk','pk','not_null','tiene_predeterminado');

		foreach(array_keys($columnas) as $id)
		{
			//Estas columnas manejan string en vez de booleanos
			foreach($columnas_booleanas as $x) {
				if($columnas[$id][$x]=='t'){
					$columnas[$id][$x] = true;
				}else{
					$columnas[$id][$x] = false;
				}
			}
			//Tipo de datos generico
			$columnas[$id]['tipo'] = $this->get_tipo_datos_generico($columnas[$id]['tipo']);
			//longitudes
			//-- Si el tipo es -1 es que es 'varlena' http://www.postgresql.org/docs/7.4/static/catalog-pg-type.html
			//-- Para el caso de varchar hay que restarle 4
			if($columnas[$id]['tipo_longitud'] <= 0){
				$columnas[$id]['longitud'] = $columnas[$id]['longitud'] - 4;
			}
			//-- Si es numerico(a,b) la longitud es 327680*b+a, pero para facilitar el proceso general se usa -1
			if ($columnas[$id]['tipo'] == 'numeric') {
				$columnas[$id]['longitud'] = -1;
			}
		}
		$this->cache_metadatos[$tabla] = array_values($columnas);
		return $this->cache_metadatos[$tabla];
	}

	/**
	 * Define cual es el "valor" a enviar para disparar un default value
	 * @return string
	 */
	function get_semantica_valor_defecto()
	{
		return 'DEFAULT';
	}

	/**
	 * Devuelve el nombre de la columna que es una secuencia en la tabla $tabla del schema $schema.
	 * Si no se especifica el schema se utiliza el que tiene por defecto la base
	 * @return string nombre de la columna si la tabla tiene secuencia. Sino devuelve null
	 * @param string $tabla
	 * @param string $schema
	 */
	function get_secuencia_tabla($tabla, $schema = null)
	{

		$result = $this->get_secuencia_tablas(array($tabla), $schema);
		if (! empty($result)) {
			return $result[$tabla];
		} else {
			return null;
		}
	}

	/**
	 * Devuelve una lista de secuencias para una tabla
	 * @param string $tablas
	 * @param string $schema
	 * @return array
	 */
	function get_secuencia_tablas($tablas, $schema = null)
	{
		$secuencias = array();
		$tablas_usadas = implode ("','" , $tablas);
		if (is_null($schema)) {
			$schema = $this->get_schema();
		}

		$sql = "
			SELECT column_name, table_name
			FROM information_schema.columns
			WHERE
				table_name   IN ('$tablas_usadas')
				AND	table_schema = '$schema'
				AND ((column_default LIKE '%seq\"''::text)::regclass)') OR (column_default LIKE '%seq''::text)::regclass)')); ";

		$result = $this->consultar($sql);
		if (! empty($result)) {
			foreach($result as $valores) {
				$secuencias[$valores['table_name']] = $valores['column_name'];
			}
		}
		return $secuencias;
	}

	/**
	 *  Devuelve una lista de los triggers en el esquema, segun estado, nombre y tablas.
	 * @param string $schema	Nombre del schema
	 * @param string $nombre	Comienzo del nombre de/los triggers
	 * @param char $estado		Estado de disparo actual del trigger (O=Origen, D=Disable, A=Always, R=Replica)
	 * @param array $tablas		Tablas involucradas con los triggers
	 * @return array
	 */
	function get_triggers_schema($schema, $nombre = '', $estado = 'O', $tablas = array())
	{
		$where = array();
		$esquema = $this->quote($schema);
		$estado = $this->quote($estado);
		$sql = "  SELECT t.*,
					     c.relname as tabla,
				              n.nspname as schema
				FROM pg_trigger as t,
				            pg_class as c,
					   pg_namespace as n
				WHERE
					  t.tgrelid = c.oid
					  AND c.relnamespace = n.oid
					  AND n.nspname = $esquema
					  AND t.tgenabled = $estado ";

		if (trim($nombre) != '') {
			$sql .= ' AND t.tgname ILIKE '. $this->quote($nombre.'%');
		}
		if (! empty($tablas)) {
			$tablas = $this->quote($tablas);
			$sql  .= ' AND C.relname IN ('. implode(',', $tablas) . ')';
		}
		return $this->consultar($sql);
	}

	//-----------------------------------------------------------------------------------
	//-- UTILIDADES PG_DUMP
	//-----------------------------------------------------------------------------------

	/**
	 * Devuelve una tabla del sistema como un arreglo de INSERTS obtenida a partir
	 * del pg_dump de postgres
	 * @param string $bd	El nombre de la base
	 * @param string $schema	El schema al que pertenece la tabla
	 * @param string $tabla		La tabla a exportar
	 * @param string $host
	 * @param string $usuario
	 * @param string $pass
	 * @return array
	 */
	function pgdump_get_tabla($bd, $schema, $tabla, $host, $usuario, $pass = null)
	{
		$exito = 0;
		$comando = "pg_dump  --data-only --ignore-version --inserts  -t $schema.$tabla -h $host -U $usuario $bd";
		$tabla = array();

		if (!is_null($pass)) {
			putenv("PGPASSWORD=$pass");
		}

		exec($comando, $tabla, $exito);
		if ($exito > 0) {
			$this->log("Error ejecutando pg_dump. Comando ejecutado: $comando", 'error');
			throw new toba_error('No se pudo generar el dump');
		}

		$tabla = $this->pgdump_limpiar($tabla);
		return $tabla;
	}

	/**
	 * Quita todo lo que no sean inserts del arreglo pasado por referencia
	 * @param type $array
	 * @ignore
	 */
	protected function pgdump_limpiar(&$array)
	{
                            $tope = count($array);
                            $salida = array();
                            $i = 0;
                            while ($i < $tope && (! comienza_con($array[$i], 'INSERT'))) {          //Busca la primera linea que posee insert
                                $i++;
                             }
                            while ($i < $tope) {         //Agrega todo lo que venga despues
                                    $salida[] = $array[$i];
                                    $i++;
                            }
                            return $salida;
	}

	//-----------------------------------------------------------------------------------
	//-- AUDITORIA (se le pide una instancia de manejador a la base que ya sabe el motor)
	//-----------------------------------------------------------------------------------

	/**
	 * Devuelve una instancia del manejador de auditoria para este motor de base de datos
	 * ventana de extension en los hijos
	 * @param string $schema_modelo
	 * @param string $schema_auditoria
	 * @param string $schema_toba
	 * @return object
	 */
	function get_manejador_auditoria($schema_modelo ='public', $schema_auditoria = 'public_auditoria', $schema_toba = null)
	{
		return new toba_auditoria_tablas_postgres($this, $schema_modelo, $schema_auditoria, $schema_toba);
	}
	
	/**
	 * Retorna una ER para quitar comentarios de la SQL
	 */
	function get_separador_comentarios()
	{
		return "/\/\*([^'])*?\*\/|(?:-{2,}[^']*?\R)/im";
	}
}
?>
