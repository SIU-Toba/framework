<?php
define('toba_db_fetch_asoc', PDO::FETCH_ASSOC);
define('toba_db_fetch_num', PDO::FETCH_NUM);

//Separador de campos en sentecias extraidas con SQL
define("apex_sql_separador","%%");			//Separador utilizado para diferenciar campos de valores compuestos
//Comodines concatenadores de SQL
define("apex_sql_where","%w%");
define("apex_sql_from","%f%");

/**
* Representa una conexión a la base de datos. Permite ejecutar comandos y consultas SQL
* En forma predeterminada utiliza los drivers PDO que tiene php desde la versión 5.1
* @package Fuentes
*/
class toba_db
{
	protected $conexion;	//Recurso
	protected $motor;
	protected $profile;
	protected $usuario;
	protected $clave;
	protected $base;
	protected $puerto;
	protected $debug = false;
	protected $debug_sql_id = 0;
	
	function __construct($profile, $usuario, $clave, $base, $puerto=null)
	{
		$this->profile  = $profile;
		$this->usuario  = $usuario;
		$this->clave    = $clave;
		$this->base     = $base;
		$this->puerto = $puerto;
	}

	function destruir()
	{
		$this->conexion = null;	
	}	
	
	/**
	*	Crea una conexion a la base
	*	@throws toba_error_db en caso de error
	*/
	function conectar()
	{
		if(!isset($this->conexion)) {
			try {
				$opciones = array();
				//--- Comentado por que da warning en php 5.2
				//$opciones =	array(PDO::ATTR_PERSISTENT => false);
				$this->conexion = new PDO($this->get_dsn(), $this->usuario, $this->clave, $opciones);
				$this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
	   			throw new toba_error_db("No es posible realizar la conexión a la base: ". $e->getMessage(), $e->getCode());
			}
		}
	}		
	
	function get_parametros()
	{
		$parametros['HOST'] = $this->profile;
		$parametros['USUARIO'] = $this->usuario;
		$parametros['CLAVE'] = $this->clave;
		$parametros['BASE'] = $this->base;
		return $parametros;
	}

	/**
	 * Cuando la conexión esta en modo debug se imprime cada consulta/comando realizado
	 */
	function set_modo_debug($debug=true)
	{
		$this->debug = $debug;
	}
	
	function log_debug($sql)
	{
		$id = $this->debug_sql_id++;
		toba_logger::instancia()->debug("***SQL[$id] : $sql");
	}

	//------------------------------------------------------------------------
	//-- Primitivas BASICAS
	//------------------------------------------------------------------------
	
	/**
	 * Convierte un string a una representación segura para el motor. Evita
	 * la inyección de código malicioso dentro de la sentencia SQL
	 */
	function quote($texto)
	{
		return $this->conexion->quote($texto);
	}
	
	/**
	*	Ejecuta un comando sql o un conjunto de ellos
	*	@param mixed $sql Comando o arreglo de comandos
	*	@throws toba_error_db en caso de que algun comando falle	
	*/
	function ejecutar($sql)
	{
		$afectados = 0;
		if (is_array($sql)) {
			foreach(array_keys($sql) as $id) {
				try {
					$afectados += $this->conexion->exec($sql[$id]);
					if ($this->debug) $this->log_debug($sql[$id]);
				} catch (PDOException $e) {
					throw new toba_error_db("ERROR ejecutando SQL. ".
											"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
											"-- SQL ejecutado: [" . $sql[$id] . "].", $e->getCode() );
				}
			}
		} else {
			try {
				$afectados += $this->conexion->exec($sql);
				if ($this->debug) $this->log_debug($sql);
			} catch (PDOException $e) {
				if (strlen($sql) > 10000) {
					$sql = substr($sql, 0, 10000)."\n\n.... CORTADO POR EXCEDER EL LIMITE";
				}
				throw new toba_error_db("ERROR ejecutando SQL. ".
										"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
										"-- SQL ejecutado: [" . $sql . "].", $e->getCode() );
										
			}
		}
		return $afectados;
	}

	/**
	*	Crea una PDO_STATEMENT y lo ejecuta.
	*	@param string $sql Consulta
	*	@param mixed $parametros Arreglo de parametros para el statement. Si el SQL poseia
	*			marcadores de tipo '?', hay que pasar un array posicional, si poseia marcadores
	*			de tipo 'nombre:', hay que pasar un array asociativo.
	*	@throws toba_error_db en caso de que algun comando falle	
	*/
	function sentencia($sql, $parametros=null)
	{
		$afectados = 0;
		try {
			$stm = $this->conexion->prepare($sql);
			$stm->execute($parametros);
			$afectados += $stm->rowCount();
			if ($this->debug) $this->log_debug($sql);
		} catch (PDOException $e) {
			if (strlen($sql) > 10000) {
				$sql = substr($sql, 0, 10000)."\n\n.... CORTADO POR EXCEDER EL LIMITE";
			}
			throw new toba_error_db("ERROR ejecutando SQL. ".
									"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
									"-- SQL ejecutado: [" . $sql . "].", $e->getCode() );
									
		}
		return $afectados;
	}

	/**
	*	Ejecuta una consulta sql
	*	@param string $sql Consulta
	*	@param string $tipo_fetch Modo Fetch de ADO, por defecto toba_db_fetch_asoc
	*	@return array Resultado de la consulta en formato recordset (filas x columnas), 
	* 				un arreglo vacio en caso que la consulta no retorne datos, usar if (empty($resultado)) para chequearlo
	*	@throws toba_error_db en caso de error
	*/	
	function consultar($sql, $tipo_fetch=toba_db_fetch_asoc)
	{
		if (! isset($tipo_fetch)) {
			$tipo_fetch=toba_db_fetch_asoc;	
		}
		try {
			$statement = $this->conexion->query($sql);
			if ($this->debug) $this->log_debug($sql);
			return $statement->fetchAll($tipo_fetch);
		} catch (PDOException $e) {
			throw new toba_error_db("ERROR ejecutando SQL. " .
									"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
									"-- SQL ejecutado: [" . $sql . "].", $e->getCode() );
		}
	}
	
	/**
	*	Ejecuta una consulta sql y retorna la primer fila del resultado.
	* 	Es útil cuando se sabe de antemano que el resultado es una única fila
	* 	
	*	@param string $sql Consulta SQL
	*	@param string $tipo_fetch Modo Fetch de ADO, por defecto toba_db_fetch_asoc
	*	@return array Arreglo asociativo columna=>valor, falso en caso de resultado vacio
	*	@throws toba_error_db en caso de error
	*/		
	function consultar_fila($sql, $tipo_fetch=toba_db_fetch_asoc)
	{
		if (! isset($tipo_fetch)) {
			$tipo_fetch=toba_db_fetch_asoc;	
		}		
		try {
			$statement = $this->conexion->query($sql);
			if ($this->debug) $this->log_debug($sql);
			return $statement->fetch($tipo_fetch);
		} catch (PDOException $e) {
			throw new toba_error_db("ERROR ejecutando SQL. " .
									"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
									"-- SQL ejecutado: [" . $sql . "].", $e->getCode() );
		}		
	}

	function abrir_transaccion()
	{
		$this->conexion->beginTransaction();
		toba_logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$this->conexion->rollBack();
		toba_logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$this->conexion->commit();
		toba_logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}

	/**
	*	Ejecuta un conjunto de comandos dentro de una transacción
	*	En caso de error en algún comando la aborta
	*	@param array $sentencias Conjunto de comandos sql
	*/
	function ejecutar_transaccion($sentencias_sql)
	{
		$sentencia_actual = 1;
		$this->abrir_transaccion();
		try {
			$this->ejecutar($sentencias_sql);
		} catch (exception_toba $e) {
			$this->abortar_transaccion();
			throw $e;
		}
		$this->cerrar_transaccion();
	}

	/**
	*	Ejecuta los comandos disponibles en un archivo
	*	@param string $archivo Path absoluto del archivo
	*/
	function ejecutar_archivo($archivo)
	//Esta función ejecuta una serie de comandos sql dados en un archivo, contra la BD dada.
	{
		if (!file_exists($archivo)) {
			throw new toba_error("Error al ejecutar comandos. El archivo '$archivo' no existe");
		}
		$str = file_get_contents($archivo);
		//if( trim($str) != '' ) {	//Esto estaba asi porque la ejecusion de algo vacio falla.
		return $this->ejecutar($str);
		//}
	}

	//------------------------------------------------------------------------

	function get_dsn()
	{
		throw new toba_error("No implementado para el motor: $this->motor");
	}

	function recuperar_secuencia($secuencia)
	{
		throw new toba_error("No implementado para el motor: $this->motor");
	}

	function retrazar_constraints()
	{
		throw new toba_error("No implementado para el motor: $this->motor");
	}

	//------------------------------------------------------------------------
	//-- INSPECCION del MODELO de DATOS
	//------------------------------------------------------------------------
	
	function get_definicion_columnas($tabla)
	{
		throw new toba_error("No implementado para el motor: $this->motor");
	}
	
	/**
	*	Mapea un tipo de datos especifico de un motor a uno generico de toba
	*	Adaptado de ADOdb
	*/
	function get_tipo_datos_generico($tipo)
	{
		$tipo=strtoupper($tipo);
	static $typeMap = array(
		'VARCHAR' => 'C',
		'VARCHAR2' => 'C',
		'CHAR' => 'C',
		'C' => 'C',
		'STRING' => 'C',
		'NCHAR' => 'C',
		'NVARCHAR' => 'C',
		'VARYING' => 'C',
		'BPCHAR' => 'C',
		'CHARACTER' => 'C',
		'INTERVAL' => 'C',  # Postgres
		##
		'LONGCHAR' => 'X',
		'TEXT' => 'X',
		'NTEXT' => 'X',
		'M' => 'X',
		'X' => 'X',
		'CLOB' => 'X',
		'NCLOB' => 'X',
		'LVARCHAR' => 'X',
		##
		'BLOB' => 'B',
		'IMAGE' => 'B',
		'BINARY' => 'B',
		'VARBINARY' => 'B',
		'LONGBINARY' => 'B',
		'B' => 'B',
		##
		'YEAR' => 'F', // mysql
		'DATE' => 'F',
		'D' => 'F',
		##
		'TIME' => 'T',
		'TIMESTAMP' => 'T',
		'DATETIME' => 'T',
		'TIMESTAMPTZ' => 'T',
		'T' => 'T',
		##
		'BOOL' => 'L',
		'BOOLEAN' => 'L', 
		'BIT' => 'L',
		'L' => 'L',
		# SERIAL... se tratan como enteros#
		'COUNTER' => 'E',
		'E' => 'E',
		'SERIAL' => 'E', // ifx
		'INT IDENTITY' => 'E',
		##
		'INT' => 'E',
		'INT2' => 'E',
		'INT4' => 'E',
		'INT8' => 'E',
		'INTEGER' => 'E',
		'INTEGER UNSIGNED' => 'E',
		'SHORT' => 'E',
		'TINYINT' => 'E',
		'SMALLINT' => 'E',
		'E' => 'E',
		##
		'LONG' => 'N', // interbase is numeric, oci8 is blob
		'BIGINT' => 'N', // this is bigger than PHP 32-bit integers
		'DECIMAL' => 'N',
		'DEC' => 'N',
		'REAL' => 'N',
		'DOUBLE' => 'N',
		'DOUBLE PRECISION' => 'N',
		'SMALLFLOAT' => 'N',
		'FLOAT' => 'N',
		'FLOAT8' => 'N',
		'NUMBER' => 'N',
		'NUM' => 'N',
		'NUMERIC' => 'N',
		'MONEY' => 'N',
		
		## informix 9.2
		'SQLINT' => 'E', 
		'SQLSERIAL' => 'E', 
		'SQLSMINT' => 'E', 
		'SQLSMFLOAT' => 'N', 
		'SQLFLOAT' => 'N', 
		'SQLMONEY' => 'N', 
		'SQLDECIMAL' => 'N', 
		'SQLDATE' => 'F', 
		'SQLVCHAR' => 'C', 
		'SQLCHAR' => 'C', 
		'SQLDTIME' => 'T', 
		'SQLINTERVAL' => 'N', 
		'SQLBYTES' => 'B', 
		'SQLTEXT' => 'X' 
		);
		if(isset($typeMap[$tipo])) 
			return $typeMap[$tipo];
		return 'Z';
	}

	/**
	 * Dada una tabla retorna la SQL de carga de la tabla y sus campos cosméticos
	 * @param string $tabla
	 * @return array(sql, clave)
	 */
	function get_sql_carga_tabla($tabla)
	{
		$columnas = $this->get_definicion_columnas($tabla);
		$claves = array();
		$select = array();
		$alias = sql_get_alias($tabla);		
		$from = array();
		$aliases = array($alias);
		$where = array();
		$left = array();
		$campo_descripcion = null;
		foreach ($columnas as $columna) {
			if ($columna['pk']) {
				$claves[] = $columna['nombre'];	
			}
			//-- Si es clave o no es una referencia se trae el dato puro
			if ($columna['pk']  || !$columna['fk_tabla']) {
				$select[] = $alias.'.'.$columna['nombre'];
				//-- Aprovecha para detectar el candidato a campo 'descripcion'
				if (! isset($campo_descripcion) || $this->es_campo_descripcion($columna)) {
					$campo_descripcion = $columna['nombre'];
				}				
			} else {
				//--- Es una referencia, hay que hacer joins
				$externo = $this->get_opciones_sql_campo_externo($columna);
				$alias_externo = sql_get_alias( $externo['tabla']);
				if (in_array($alias_externo, $aliases)) {
					$alias_externo = $externo['tabla']; //En caso de existir el alias, usa el nombre de la tabla
				}
				$aliases[] = $alias_externo;				
				$select[] = $alias_externo.'.'.$externo['descripcion'].' as '.$columna['nombre'];				
				$ext_where = $alias.'.'.$columna['nombre'].' = '.$alias_externo.'.'.$externo['clave'];
				$ext_from = $externo['tabla'].' as '.$alias_externo;
				if ($columna['not_null']) {
					//-- Si es NOT NULL, se hace un INNER join
					$from[] = $ext_from;
					$where[] = $ext_where;
				} else {
					//-- Si es NULL, se hace un LEFT OUTER join
					$left[] = "$ext_from ON ($ext_where)";
				}
			}
		}
		$from = array_unique($from);
		$sql = "SELECT\n\t".implode(",\n\t", $select)."\n";
		$sql .= "FROM\n\t$tabla as $alias";
		if (!empty($left)) {
			$texto_left = "\tLEFT OUTER JOIN ";
			$sql .= $texto_left.implode("\n$texto_left",$left)."\n";
		}
		if (!empty($from)) {
			$sql .= ",\n\t".implode(",\n\t",$from)."\n";			
		}
		if (!empty($where)) {
			$sql .= "WHERE\n\t".implode(",\n\t",$where)."\n";
		}
		return array($sql, implode(',',$claves), $campo_descripcion);
	}
	
	/**
	 * Determina la sql,clave y desc de un campo externo de una tabla
	 * Remonta N-niveles de indireccion de FKs
	 */
	function get_opciones_sql_campo_externo($campo)
	{
		//--- Busca cual es el campo descripcion de la tabla destino
		while (isset($campo['fk_tabla'])) {
			$tabla = $campo['fk_tabla'];
			$clave = $campo['fk_campo'];
			$descripcion = $campo['fk_campo'];
			//-- Busca cual es el campo descripción más 'acorde' en la tabla actual
			$campos_tabla_externa = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);
			$encontrado = false;			
			foreach ($campos_tabla_externa as $campo_tabla_ext) {
				//---Detecta cual es la clave para seguir ejecutando el script
				if ($campo_tabla_ext['nombre'] == $clave) {
					$campo = $campo_tabla_ext;
				}
				if (! $encontrado && $this->es_campo_descripcion($campo_tabla_ext)) {
					$descripcion = $campo_tabla_ext['nombre'];
					$encontrado = true;
				}
			}
			$sql = "SELECT $clave, $descripcion FROM $tabla";
		}
		return array('sql'=>$sql, 'tabla'=>$tabla, 'clave'=>$clave, 'descripcion'=>$descripcion);
	}	
	
	/**
	 * Determina si la definición de un campo de una tabla es un campo descripción
	 * @param array $campo Definicion de un campo
	 */
	protected function es_campo_descripcion($campo)
	{
		return !$campo['pk'] && $campo['tipo'] == 'C';
	}
	
	//-----------------------------------------------------------------------------------
	//-- GENERACION de MENSAJES de ERROR (Esto necesita adecuacion al esquema actual)
	//-----------------------------------------------------------------------------------

	/**
	*	Mapea el error de la base al modulo de mensajes del toba
	*/
	function get_error_toba($codigo, $descripcion)
	{
		throw new toba_error("No implementado para el motor: $this->motor");
	}
}
?>