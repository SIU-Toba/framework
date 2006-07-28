<?
define('toba_db_fetch_asoc', PDO::FETCH_ASSOC);
define('toba_db_fetch_num', PDO::FETCH_NUM);

//Separador de campos en sentecias extraidas con SQL
define("apex_sql_separador","%%");			//Separador utilizado para diferenciar campos de valores compuestos
//Comodines concatenadores de SQL
define("apex_sql_where","%w%");
define("apex_sql_from","%f%");

/**
*	Representa una conexión a la base de datos 
*/
class db
{
	protected $conexion;			//Conexion ADOdb
	protected $motor;
	protected $profile;
	protected $usuario;
	protected $clave;
	protected $base;
	protected $debug = false;
	protected $debug_sql_id = 0;
	
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->profile  = $profile;
		$this->usuario  = $usuario;
		$this->clave    = $clave;
		$this->base     = $base;
	}

	function destruir()
	{
		$this->conexion = null;	
	}	
	
	/**
	*	Crea una conexion a la base
	*/
	function conectar()
	{
		if(!isset($this->conexion)) {
			try {
				$opciones =	array(PDO::ATTR_PERSISTENT => false);
				$this->conexion = new PDO($this->get_dsn(), $this->usuario, $this->clave, $opciones);
				$this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
	   			throw new excepcion_toba("No es posible realizar la conexión a la base: ". $e->getMessage());
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
		logger::instancia()->debug("***SQL[$id] : $sql");
	}

	//------------------------------------------------------------------------
	//-- Primitivas BASICAS
	//------------------------------------------------------------------------
	
	/**
	*	Ejecuta un comando sql o un conjunto de ellos
	*	@param mixed $sql Comando o arreglo de comandos
	*	@throws excepcion_toba en caso de que algun comando falle	
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
					throw new excepcion_toba("ERROR ejecutando SQL. ".
											"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
											"-- SQL ejecutado: [" . $sql[$id] . "].");
				}
			}
		} else {
			try {
				$afectados += $this->conexion->exec($sql);
				if ($this->debug) $this->log_debug($sql);
			} catch (PDOException $e) {
				throw new excepcion_toba("ERROR ejecutando SQL. ".
										"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
										"-- SQL ejecutado: [" . $sql . "].");
			}
		}
		return $afectados;
	}
	
	/**
	*	Ejecuta una consulta sql
	*	@param string $sql Consulta
	*	@param string $ado Modo Fecth de ADO, por defecto asociativo
	*	@param boolean $obligatorio Si la consulta no retorna datos lanza una excepcion
	*	@return array Resultado de la consulta en formato fila-columna
	*	@throws excepcion_toba en caso de error
	*/	
	function consultar($sql, $tipo_fetch=toba_db_fetch_asoc)
	{
		try {
			$statement = $this->conexion->query($sql);
			if ($this->debug) $this->log_debug($sql);
			return $statement->fetchAll($tipo_fetch);
		} catch (PDOException $e) {
			throw new excepcion_toba("ERROR ejecutando SQL. " .
									"-- Mensaje MOTOR: [" . $e->getMessage() . "]".
									"-- SQL ejecutado: [" . $sql . "].");
		}
	}

	function abrir_transaccion()
	{
		$this->conexion->beginTransaction();
		logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$this->conexion->rollBack();
		logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$this->conexion->commit();
		logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
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
			$this->ejecutar($sql);
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
			throw new excepcion_toba("Error al ejecutar comandos. El archivo '$archivo' no existe");
		}
		$str = file_get_contents($archivo);
		//if( trim($str) != '' ) {	//Esto estaba asi porque la ejecusion de algo vacio falla.
		return $this->ejecutar($str);
		//}
	}

	//------------------------------------------------------------------------

	function get_dsn()
	{
		throw new excepcion_toba("No implementado para el motor: $this->motor");
	}

	function recuperar_secuencia()
	{
		throw new excepcion_toba("No implementado para el motor: $this->motor");
	}

	function retrazar_constraints()
	{
		throw new excepcion_toba("No implementado para el motor: $this->motor");
	}

	//------------------------------------------------------------------------
	//-- INSPECCION del MODELO de DATOS
	//------------------------------------------------------------------------
	
	function get_definicion_columnas($tabla)
	{
		throw new excepcion_toba("No implementado para el motor: $this->motor");
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
		'YEAR' => 'D', // mysql
		'DATE' => 'D',
		'D' => 'D',
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
		'SQLDATE' => 'D', 
		'SQLVCHAR' => 'C', 
		'SQLCHAR' => 'C', 
		'SQLDTIME' => 'T', 
		'SQLINTERVAL' => 'N', 
		'SQLBYTES' => 'B', 
		'SQLTEXT' => 'X' 
		);
		if(isset($typeMap[$tipo])) 
			return $typeMap[$tipo];
		return null;
	}

	//-----------------------------------------------------------------------------------
	//-- GENERACION de MENSAJES de ERROR (Esto necesita adecuacion al esquema actual)
	//-----------------------------------------------------------------------------------

	/**
	*	Mapea el error de la base al modulo de mensajes del toba
	*/
	function obtener_error_toba($codigo, $descripcion)
	{
		throw new excepcion_toba("No implementado para el motor: $this->motor");
	}
}
?>