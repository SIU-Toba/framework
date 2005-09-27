<?
require_once("3ros/adodb464/adodb.inc.php");

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
		$this->conexion = ADONewConnection($this->motor);
		$status = @$this->conexion->NConnect($this->profile, $this->usuario, $this->clave, $this->base);
		if(!$status){
			throw new excepcion_toba("No es posible realizar la conexion");
		}
	}
	
	function destruir()
	{
		$this->conexion->close();	
	}

	function ejecutar($sql)
	{
		if(is_array($sql)){
			for($a = 0; $a < count($sql);$a++){
				if ( $this->conexion->execute($sql[$a]) === false ){
					throw new excepcion_toba("ERROR ejecutando SQL. ".
											"-- Mensaje MOTOR: (" . $this->conexion->ErrorMsg() . ")".
											"-- SQL ejecutado: (" . $sql[$a] . ").");
				}
			}
		}else{
			if ( $this->conexion->execute($sql) === false ){
				throw new excepcion_toba("ERROR ejecutando SQL. ".
										"-- Mensaje MOTOR: (" . $this->conexion->ErrorMsg() . ")".
										"-- SQL ejecutado: (" . $sql . ").");
			}
		}
	}

	//-------------------------------------------------------------------------------------

	//Tengo que responder a las llamadas de AOD
	function __call($nombre, $argumentos)
	{
		echo "me llamaron $nombre";
	}


	function consultar($sql, $ado=null, $obligatorio=false)
	//Dispara una execpcion si algo salio mal
	{
		if($fuente==null){
			if( defined("fuente_datos_defecto") ){
				$fuente = fuente_datos_defecto;
			}else{
				$fuente = "instancia";
			}
		}
		global $db, $ADODB_FETCH_MODE;	
		if(isset($ado)){
			$ADODB_FETCH_MODE = $ado;
		}else{
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		if(!isset($db[$fuente])){
			throw new excepcion_toba("La fuente de datos no se encuentra disponible. " );
		}
		$rs = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("No se genero el Recordset. " . $db[$fuente][apex_db_con]->ErrorMsg() );
		}elseif($rs->EOF){
			if($obligatorio){
				throw new excepcion_toba("La consulta no devolvio datos.");
			}else{
				return null;
			}
		}else{
			return $rs->getArray();
		}
	}
//-------------------------------------------------------------------------------------

	function recuperar_secuencia($secuencia, $fuente="instancia")
	{
		$sql = "SELECT currval('$secuencia') as seq;";
		$datos = consultar_fuente($sql,$fuente);
		return $datos[0]['seq'];		
	}
//-------------------------------------------------------------------------------------

	function ejecutar_transaccion($sentencias_sql, $fuente="instancia")
/*
 	@@acceso: actividad
	@@desc: Ejecuta un ARRAY de sentencias SQL como una transaccion.
*/
	{
		global $db;
		$sentencia_actual = 1;
		$db[$fuente][apex_db_con]->Execute("BEGIN TRANSACTION");
		foreach( $sentencias_sql as $sql )
		{
			if( $db[$fuente][apex_db_con]->Execute($sql) === false){
				$mensaje = "Ha ocurrido un error en la sentencia: $sentencia_actual -- ( " . 
							$db[$fuente][apex_db_con]->ErrorMsg() . " )";
				$db[$fuente][apex_db_con]->Execute("ROLLBACK TRANSACTION");
				return array(0, $mensaje);
			}
			$sentencia_actual++;
		}
		$db[$fuente][apex_db_con]->Execute("COMMIT TRANSACTION");
		$mensaje = "La transaccion se ha realizado satisfactoriamente";
		return array(1, $mensaje);
	}
//-------------------------------------------------------------------------------------
	
	function abrir_transaccion($fuente=null)
	{
		global $db;
		if($fuente==null){
			if( defined("fuente_datos_defecto") ){
				$fuente = fuente_datos_defecto;
			}else{
				$fuente = "instancia";
			}
		}
		$sql = "BEGIN TRANSACTION";
		$status = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$status){
			throw new excepcion_toba ("No es posible ABRIR la TRANSACCION ( " .$db[$fuente][apex_db_con]->ErrorMsg()." )","error");
		}
		toba::get_logger()->debug("************ ABRIR transaccion ****************"); 
	}
	
	function abortar_transaccion($fuente=null)
	{
		global $db;
		if($fuente==null){
			if( defined("fuente_datos_defecto") ){
				$fuente = fuente_datos_defecto;
			}else{
				$fuente = "instancia";
			}
		}
		$sql = "ROLLBACK TRANSACTION";
		$status = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$status){
			throw new excepcion_toba ("No es posible ABORTAR la TRANSACCION ( " .$db[$fuente][apex_db_con]->ErrorMsg()." )","error");
		}
		toba::get_logger()->debug("************ ABORTAR transaccion ****************"); 
	}
	
	function cerrar_transaccion($fuente=null)
	{
		global $db;
		if($fuente==null){
			if( defined("fuente_datos_defecto") ){
				$fuente = fuente_datos_defecto;
			}else{
				$fuente = "instancia";
			}
		}
		$sql = "COMMIT TRANSACTION";
		$status = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$status){
			throw new excepcion_toba ("No es posible ABORTAR la TRANSACCION ( " .$db[$fuente][apex_db_con]->ErrorMsg()." )","error");
		}
		toba::get_logger()->debug("************ CERRAR transaccion ****************"); 
	}

//-------------------------------------------------------------------------------------

	function ejecutar_archivo($archivo)
	//Esta función ejecuta una serie de comandos sql dados en un archivo, contra la BD dada.
	{
		$sql = "/".$sql;
		$str = file_get_contents(dirname(__FILE__).$sql);

		if ($db[$this->id][apex_db_con]->Execute($str) == false) {
			throw new excepcion_toba("FUENTE_TOBA: Imposible ejecutar comando '{$str}' : ".$db[$this->id][apex_db_con]->ErrorMsg());
		}

	}
	//---------------------------------------------------------------------------------------
	
	function obtener_error_toba($codigo, $descripcion)
	//Esta funcion mapea el error de la base al modulo de mensajes del toba
	{
		return array();		
	}

	function get_tipo_datos_generico($tipo)
	/*
		Adaptado de ADOdb.
	*/
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
}
//------------------------------------------------------------------------

?>