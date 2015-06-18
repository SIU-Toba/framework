<?php
/**
 * Driver de conexión con postgres
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_postgres7 extends toba_db
{
	protected $cache_metadatos = array(); //Guarda un cache de los metadatos de cada tabla
	protected $schema;
	protected $transaccion_abierta = false;
	
	
	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "postgres7";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
		//$this->setear_datestyle_iso();
	}

	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		return "pgsql:host=$this->profile;dbname=$this->base;$puerto";	
	}
	
	function get_parametros()
	{
		$parametros = parent::get_parametros();
		$parametros['schema'] = $this->schema;
		return $parametros;
	}
	/**
	 * Determina que schema se utilizará por defecto para la ejecución de consultas, comandos y consulta de metadatos
	 * @param string $schema
	 * @param boolean $ejecutar
	 */
	function set_schema($schema, $ejecutar = true, $fallback_en_public=false)
	{
		$this->schema = $schema;
		$sql = "SET search_path TO $schema";
		if ($fallback_en_public) {
			$sql .= ', public';
		}
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}
	
	function get_schema()
	{
		if (isset($this->schema)) {
			return $this->schema;
		}
	}

	function set_encoding($encoding, $ejecutar = true)
	{
		$sql = "SET CLIENT_ENCODING TO '$encoding';";
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);		
	}

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
		/*$sql = "SELECT lanname FROM pg_language WHERE lanname='plpgsql'";
		$rs = $this->consultar($sql);
		if (empty($rs)) {
			$sql = 'CREATE LANGUAGE plpgsql';
			$this->ejecutar($sql);
		}*/
	}
	
	/**
	*	Recupera el valor actual de una secuencia
	*	@param string $secuencia Nombre de la secuencia
	*	@return string Siguiente numero de la secuencia
	*/	
	function recuperar_secuencia($secuencia, $ejecutar=true)
	{
		$sql = "SELECT currval('$secuencia') as seq;";
		if (! $ejecutar) { return $sql; }
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}

	function recuperar_nuevo_valor_secuencia($secuencia, $ejecutar = true)
	{
		$sql = "SELECT nextval('$secuencia') as seq;";
		if (! $ejecutar) { return $sql; }
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}
		
	function retrazar_constraints($retrazar = true)
	{
		$tipo = $retrazar ? 'DEFERRED' : 'IMMEDIATE';
		$this->ejecutar("SET CONSTRAINTS ALL $tipo;");
		toba_logger::instancia()->debug("************ Se cambia el chequeo de constraints ($tipo) ****************", 'toba');		
	}
	

	function abrir_transaccion()
	{
		$sql = 'BEGIN TRANSACTION;';
		$this->ejecutar($sql);
		$this->transaccion_abierta = true;
		toba_logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK TRANSACTION;';
		$this->ejecutar($sql);
		$this->transaccion_abierta = false;
		toba_logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$sql = "COMMIT TRANSACTION;";
		$this->ejecutar($sql);
		$this->transaccion_abierta = false;
		toba_logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}

	/**
	 * @return boolean Devuelve true si hay una transacción abierta y false en caso contrario
	 */
	function transaccion_abierta()
	{
		return $this->transaccion_abierta;
	}

	function agregar_savepoint($nombre)
	{
		$sql = "SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		toba_logger::instancia()->debug("************ agregar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
	}

	function abortar_savepoint($nombre)
	{
		$sql = "ROLLBACK TO SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		toba_logger::instancia()->debug("************ abortar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
	}

	function liberar_savepoint($nombre)
	{
		$sql = "RELEASE SAVEPOINT $nombre;";
		$this->ejecutar($sql);
		toba_logger::instancia()->debug("************ liberar savepoint $nombre ($this->base@$this->profile) ****************", 'toba');
	}

	/**
	*	Insert de datos desde un arreglo hacia una tabla. Requiere la extension original pgsql.
	*	@param string $tabla Nombre de la tabla en la que se insertarán los datos
	*	@param array $datos Los datos a insertar: cada elemento del arreglo será un registro en la tabla.
	*	@param string $delimitador Separador de datos de cada fila.
	*	@param string $valor_nulo Cadena que se utlilizará como valor nulo.
	*	@return boolean Retorn TRUE en caso de éxito o FALSE en caso de error.
	*/
	function insert_masivo($tabla,$datos,$delimitador="\t",$valor_nulo="\\N") {
		$dbconn = $this->get_pg_connect_nativo();
		$salida = pg_copy_from($dbconn,$tabla,$datos,$delimitador,$valor_nulo);
		if (!$salida) {
			$mensaje = pg_last_error($dbconn);
			pg_close($dbconn);
			toba::logger()->error($mensaje);
			throw new toba_error($mensaje);
		}
		pg_close($dbconn);
		return $salida;
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
	
	function borrar_schema($schema, $ejecutar = true)
	{
		$sql = "DROP SCHEMA $schema CASCADE;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}		

	function borrar_schema_si_existe($schema)
	{
		if ($this->existe_schema($schema)) {
			$this->borrar_schema($schema);
		}
	}

	function crear_schema($schema, $ejecutar = true) 
	{
		$sql = "CREATE SCHEMA $schema;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}	
	
	function renombrar_schema($actual, $nuevo, $ejecutar = true) 
	{
		$sql = "ALTER SCHEMA $actual RENAME TO $nuevo;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}		

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
	
	function existe_lenguaje($lang)
	{
		$lang = $this->quote($lang);
		$sql = "SELECT COUNT(*) FROM pg_language WHERE lanname=$lang;";
		$res = $this->consultar($sql);
		return $res[0]['count'] > 0;
	}

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
	
	function get_usuario_actual()
	{
		$sql = 'SELECT CURRENT_USER AS usuario;';
		$datos = toba::db()->consultar_fila($sql);
		return $datos['usuario'];
	}
	
	function get_rol_actual()
	{
		$sql = 'SHOW ROLE;';
		$datos = $this->consultar_fila($sql);
		return $datos['role'];
	}	
	
	function set_rol($rol, $ejecutar = true)
	{
		$sql = "SET ROLE $rol;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}
	
	function existe_rol($rol) 
	{
		$datos = $this->listar_roles($rol);
		return !empty($datos);
	}
	
	function listar_roles($rol = null)
	{
		$sql = 'SELECT rolname FROM pg_roles ';
		if (! is_null($rol)) {
			$rol = $this->quote($rol);
			$sql .= " WHERE rolname = $rol;";
		}
		return $this->consultar($sql);
	}	
	
	function crear_rol($rol, $ejecutar=true)
	{
		$sql = "CREATE ROLE $rol NOINHERIT;";
		if (! $ejecutar) { return $sql; }		
		return $this->ejecutar($sql);		
	}
	
	function crear_usuario($rol, $password, $ejecutar = true)
	{
		$password = $this->quote($password);
		$sql = "CREATE ROLE $rol NOINHERIT LOGIN PASSWORD $password;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}	
	
	function borrar_rol($rol, $ejecutar = true)
	{
		$sql = "DROP ROLE IF EXISTS $rol;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}
	
	function grant_rol($usuario, $rol, $ejecutar = true)
	{
		$sql = "GRANT $rol TO $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}	

	function grant_schema($usuario, $schema, $permisos = 'USAGE', $ejecutar = true)
	{
		$sql = "GRANT $permisos ON SCHEMA \"$schema\" TO $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}	
	
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
	
	function revoke_rol($usuario, $rol, $ejecutar = true)
	{
		$sql = "REVOKE $rol FROM $usuario;";
		if (! $ejecutar) { return $sql; }
		return $this->ejecutar($sql);
	}

	/**
	 *	Da permisos especificos a todas las tablas de un esquema dado
	 **/
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
	 *	Da permisos especificos a todas las tablas de un esquema dado
	 **/
	function grant_tablas($usuario, $schema, $tablas, $privilegios ='ALL PRIVILEGES', $ejecutar = true)
	{
		$sql = array();
		foreach ($tablas as $tabla) {
			$sql[] = "GRANT $privilegios ON $schema.$tabla TO $usuario;";
		}
		if (! $ejecutar) { return $sql; }
		$this->ejecutar($sql);
	}	

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
		
	function get_lista_tablas_y_vistas()
	{
		$esquema = null;
		if (isset($this->schema)) {
			$esquema = $this->schema;
		}		
		return $this->get_lista_tablas_bd(true, $esquema);	
	}
	
	
	function get_lista_tablas($incluir_vistas=false, $esquema=null)
	{
		if (is_null($esquema) && isset($this->schema)) {
			$esquema = $this->schema;
		}
		return $this->get_lista_tablas_bd($incluir_vistas, $esquema);
	}
		
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
	
	function get_lista_secuencias($esquema=null)
	{
		$where = '';
		if (! is_null($esquema)) {
			$esquema = $this->quote($esquema);
			$where .= " AND n.nspname = $esquema" ;			
		} elseif (isset($this->schema)) {
			$esquema = $this->quote($this->schema);
			$where .= " AND n.nspname = $esquema" ;			
		}
		$sql = "
			SELECT 
				c.relname as tabla,
				a.attname as campo,
				replace( substring(adef.adsrc,'''[^'']*'''), '''', '' ) as nombre
			FROM
				pg_catalog.pg_attribute a 
					LEFT JOIN pg_catalog.pg_attrdef adef ON a.attrelid=adef.adrelid AND a.attnum=adef.adnum
					LEFT JOIN pg_catalog.pg_type t ON a.atttypid=t.oid
					LEFT JOIN pg_catalog.pg_class c ON a.attrelid=c.oid
					LEFT JOIN pg_catalog.pg_namespace as n ON c.relnamespace = n.oid
			WHERE
			 	adsrc like '%nextval%'
			 	AND a.attnum > 0 AND NOT a.attisdropped
			 	$where
			ORDER BY a.attname;
		";
		return $this->consultar($sql);
	}
	
	/**
	*	Busca la definicion de un TABLA. Cachea los resultados por un pedido de pagina
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
		$sql = "SELECT  a.attname as 			nombre,
						t.typname as 			tipo,
						a.attlen as 			tipo_longitud,
						a.atttypmod as 			longitud,
						format_type(a.atttypid, a.atttypmod) as tipo_sql,
						a.attnotnull as 		not_null,
						a.atthasdef as 			tiene_predeterminado,
						d.adsrc as 				valor_predeterminado,
						'' as					secuencia,
						fc.relname				as fk_tabla,
						fa.attname				as fk_campo,						
						a.attnum as 			orden,
						c.relname as			tabla,
						EXISTS (SELECT 
									indisprimary 
								FROM 
									pg_index i INNER JOIN pg_class ic ON ic.oid = i.indexrelid AND indisprimary = TRUE 
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
									pg_index i INNER JOIN pg_class ic ON ic.oid = i.indexrelid AND indisunique = TRUE 
								WHERE 
									( 	a.attrelid = i.indrelid 
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
							LEFT OUTER JOIN (pg_constraint const 
												INNER JOIN pg_class fc ON fc.oid = const.confrelid
												INNER JOIN pg_attribute fa ON (fa.attrelid = const.confrelid AND fa.attnum = const.confkey[1]
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
			throw new toba_error("La tabla '$tabla' no existe");	
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
			//Secuencias
			if($columnas[$id]['tiene_predeterminado']){
				$match = array();
				if(preg_match("&nextval.*?(\'|\")(.*?[.]|)(.*)(\'|\")&",$columnas[$id]['valor_predeterminado'],$match)){
					$columnas[$id]['secuencia'] = $match[3];
				}			
			}
		}
		$this->cache_metadatos[$tabla] = array_values($columnas);
		return $this->cache_metadatos[$tabla];
	}

	function get_semantica_valor_defecto()
	{
		return 'DEFAULT';
	}

	/**
	 * Devuelve el nombre de la columna que es una secuencia en la tabla $tabla del schema $schema.
	 * Si no se especifica el schema se utiliza el que tiene por defecto la base
	 * @return string nombre de la columna si la tabla tiene secuencia. Sino devuelve null
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
		$comando = "pg_dump  -a -i -d -t $schema.$tabla -h $host -U $usuario $bd";
		$tabla = array();

		if (!is_null($pass)) {
			putenv("PGPASSWORD=$pass");
		}
		
		exec($comando, $tabla, $exito);
		if ($exito > 0) {
			throw new toba_error("Error ejecutando pg_dump. Comando ejecutado: $comando");
		}

		$this->pgdump_limpiar($tabla);
		return $tabla;
	}

	protected function pgdump_limpiar(&$array)
	{
		$borrando = true;

		foreach ($array as $key => $elem) {
			if (comienza_con($elem, 'INSERT')) {
				continue;
			}
			unset($array[$key]);
		}
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
}
?>
