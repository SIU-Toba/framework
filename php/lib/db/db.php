<?
require_once("3ros/adodb464/adodb.inc.php");
define('apex_db_asociativo', ADODB_FETCH_ASSOC);
define('apex_db_numerico', ADODB_FETCH_NUM);

//Separador de campos en sentecias extraidas con SQL
define("apex_sql_separador","%%");			//Separador utilizado para diferenciar campos de valores compuestos
//Comodines concatenadores de SQL
define("apex_sql_where","%w%");
define("apex_sql_from","%f%");

/**
*	Representa una conexión a la base de datos utilizando ADODb
*/
class db
{
	protected $conexion;			//Conexion ADOdb
	protected $motor;
	protected $profile;
	protected $usuario;
	protected $clave;
	protected $base;
	
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->profile  = $profile;
		$this->usuario  = $usuario;
		$this->clave    = $clave;
		$this->base     = $base;
	}

	function destruir()
	{
		$this->conexion->close();	
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
	*	Realiza la conexión a la BD utilizando ADOdb
	*	La creación de la conexión se encierra entre las llamas pre_conectar y post_conectar
	*/
	function conectar()
	{
		$this->conexion = ADONewConnection($this->motor);
		$status = $this->conexion->NConnect($this->profile, $this->usuario, $this->clave, $this->base);
		if(!$status){
			$error = $this->conexion->ErrorMsg();
			throw new excepcion_toba("No es posible realizar la conexión a la base. $error");
		}
		return $this->conexion;
	}
	
	/**
	 * Cuando la conexión esta en modo debug se imprime cada consulta/comando realizado
	 */
	function set_modo_debug($debug=true)
	{
		$this->conexion->debug = $debug;
	}
	
	//-------------------------------------------------------------------------------------	

	/**
	*	Ejecuta un comando sql o un conjunto de ellos
	*	@param mixed $sql Comando o arreglo de comandos
	*	@throws excepcion_toba en caso de que algun comando falle	
	*/
	function ejecutar($sql)
	{
		$afectados = 0;
		if (is_array($sql)) {
			for($a = 0; $a < count($sql);$a++){
				if ( $this->conexion->execute($sql[$a]) === false ) {
					throw new excepcion_toba("ERROR ejecutando SQL. ".
											"-- Mensaje MOTOR: [" . $this->conexion->ErrorMsg() . "]".
											"-- SQL ejecutado: [" . $sql[$a] . "].");
				}
				$afectados += $this->conexion->Affected_Rows();
			}
		} else {
			if ( $this->conexion->execute($sql) === false ) {
				throw new excepcion_toba("ERROR ejecutando SQL. ".
										"-- Mensaje MOTOR: [" . $this->conexion->ErrorMsg() . "]".
										"-- SQL ejecutado: [" . $sql . "].");
			}
			$afectados += $this->conexion->Affected_Rows();			
		}
		return $afectados;
	}
	
	//-------------------------------------------------------------------------------------
	/**
	*	Ejecuta una consulta sql
	*	@param string $sql Consulta
	*	@param string $ado Modo Fecth de ADO, por defecto asociativo
	*	@param boolean $obligatorio Si la consulta no retorna datos lanza una excepcion
	*	@return array Resultado de la consulta en formato fila-columna
	*	@throws excepcion_toba en caso de error
	*/	
	function consultar($sql, $tipo_indice=null, $obligatorio=false, $compatibilidad=false)
	{
		global $ADODB_FETCH_MODE;
		if(isset($tipo_indice)){
			$ADODB_FETCH_MODE = $tipo_indice;
		}else{
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		$rs = $this->conexion->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("No se genero el Recordset. " . $this->conexion->ErrorMsg() . " -- " . $sql);
		}elseif($rs->EOF){
			if($obligatorio){
				throw new excepcion_toba("La consulta no devolvio datos.");
			}else{
				if(!$compatibilidad) return array();
				return null;
			}
		}else{
			return $rs->getArray();
		}
	}
//-------------------------------------------------------------------------------------

	/**
	*	Recupera el valor actual de una secuencia
	*	@param string $secuencia Nombre de la secuencia
	*	@return string Siguiente numero de la secuencia
	*/	
	function recuperar_secuencia($secuencia)
	{
		$sql = "SELECT currval('$secuencia') as seq;";
		$datos = $this->consultar($sql);
		return $datos[0]['seq'];
	}
//-------------------------------------------------------------------------------------

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
	//-------------------------------------------------------------------------------------
	
	function abrir_transaccion()
	{
		$sql = 'BEGIN TRANSACTION';
		$this->ejecutar($sql);
		logger::instancia()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK TRANSACTION';
		$this->ejecutar($sql);		
		logger::instancia()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$sql = "COMMIT TRANSACTION";
		$this->ejecutar($sql);		
		logger::instancia()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}

//-------------------------------------------------------------------------------------

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
	
	/**
	*	Mapea el error de la base al modulo de mensajes del toba
	*/
	function obtener_error_toba($codigo, $descripcion)
	{
		return array();		
	}

	/**
	*	Busca la definicion de columnas de la base
	*	ATENCION: Utiliza ADOdb
	*/
	function obtener_definicion_columnas($tabla)
	{
	}
}
?>