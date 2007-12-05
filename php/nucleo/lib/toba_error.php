<?php
/**
 * Excepciones particulares disparadas dentro del núcleo
 * @package Centrales
 * @subpackage Errores
 */

/**
* Error interno de toba
*/
class toba_error extends Exception
{

}

/**
* Excepción ejecutando SQL contra la DB
*/
class toba_error_db extends toba_error
{
	protected $codigo;
	protected $info_error;				//Arreglo que mantiene SQLSTATE, codigo especifico del motor, mensaje especifico del motor.
	protected $sql;
	protected $mensaje_motor ='';

	function __construct($info, $sql=null)
	{
		$this->set_sql_ejecutado($sql);
		//- No se crea un arreglo cuando falla la conexion a la base ya que son propiedades del objeto conexion.
		if (is_array($info->errorInfo)){
			$this->set_info_error($info->errorInfo);
			$this->set_mensaje_motor($this->info_error[2]);	
		}else{
			 //- No va a funcionar si se cambia de motor porque se parsea el mensaje de postgres 
			$cadena = substr($info->getMessage(),0,16);
			if (strpos($cadena, 'SQLSTATE') !== false) {
				$codigo = $this->parsear_mensaje($cadena, '[', ']');
				$this->set_info_error( array($codigo, $info->getCode(), $info->getMessage()) );	
			}else{
				//- No se pudo parsear el mensaje de error, se arma uno generico.
				$this->set_info_error( array('96669', $info->getCode(), $info->getMessage()) );	
			}
		}
		if (PHP_SAPI != 'cli') {
			if (($this->get_sqlstate() == 'db_08006') || ($this->get_sqlstate() == 'db_96669')) {
				$mensaje = "No es posible realizar la conexión a la base.";
			}else{
				$datos_error = toba::proyecto()->get_mensaje_proyecto($this->get_sqlstate());
				if (!$datos_error || !is_array($datos_error)) {
					$datos_error = toba::proyecto()->get_mensaje_toba($this->get_sqlstate());
				}
				if (!is_array($datos_error) || empty($datos_error)) {
					$mensaje = $info->getMessage();	
				}else{
					$mensaje = $datos_error['m'];					
				}
			}				
		}else{
			$mensaje = $this->get_mensaje();
		}
		parent::__construct($mensaje);
	}

	function set_info_error($error)
	{
		$this->info_error = $error;	
	}

	function set_mensaje_motor($mensaje)
	{
		$this->mensaje_motor = $mensaje;
	}
	
	function set_sql_ejecutado($sql)
	{
		$this->sql = $sql;
	}
	
	function get_mensaje_motor()
	{
		return $this->info_error[2];
	}
	
	function get_sql_ejecutado()
	{
		return $this->sql;
	}
	
	function get_sqlstate()
	{
		return 'db_'.$this->info_error[0];
	}

	function get_codigo_motor()
	{
		return $this->info_error[1];
	}
	
	function get_mensaje()
	{
		if (PHP_SAPI != 'cli') {
			$mensaje  = "<p><b>SQLSTATE:</b> {$this->get_sqlstate()}</p>" .
						"<p><b>CODIGO:</b> {$this->get_codigo_motor()}</p>" .
						"<p><b>MENSAJE:</b> {$this->get_mensaje_motor()}</p>" .
						"<p><b>SQL:</b> {$this->get_sql_ejecutado()}</p>";
		}else{
			$sep = '------------------------------------------------------';
			$mensaje = "
ERROR ejecutando SQL. CODIGO: {$this->get_codigo_motor()}
$sep
     MENSAJE
$sep
{$this->get_mensaje_motor()}

$sep
     SQL EJECUTADA
$sep
{$this->get_sql_ejecutado()}

";
	
		}
		return $mensaje;
	}
	
	protected function parsear_mensaje($cadena, $inicio, $final){
        $cadena = " ".$cadena;
        $i = strpos($cadena, $inicio);
        if ($i == 0) return "";
        $i += strlen($inicio);   
        $l = strpos($cadena, $final, $i) - $i;
        return substr($cadena, $i, $l);
	}
	
}

/**
* Excepción producida por alguna interacción del usuario
*/
class toba_error_usuario extends toba_error
{

}

/**
* Excepción producida por error del la definicion en el desarrollo
*/
class toba_error_def extends toba_error
{

}

/**
 * Excepción producida cuando el usuario no tiene permitido algún derecho
 */
class toba_error_permisos extends toba_error 
{
	
}

/**
 * Excepción producida por un error en la autenticacion del usuario
 */
class toba_error_autenticacion extends toba_error
{

}

/**
 * Excepción producida por un error en la autorizacion del usuario
 */
class toba_error_autorizacion extends toba_error
{

}


class toba_error_validacion extends toba_error 
{
	protected $causante;
	
	function __construct($mensaje, $causante=null)
	{
		$this->causante = $causante;
		parent::__construct($mensaje);
	}	
	
	function get_causante()
	{
		return $this->causante;	
	}
}

/**
 * Excepción producida cuando falla la incializacion predefinida de una sesion
 */
class toba_error_ini_sesion extends toba_error
{
	
}

/**
 * Excepción para recargar una solicitud
 * es necesario que la operación donde se ejecute esta excepcion este marcado como "recar
 */
class toba_reset_nucleo extends Exception
{
	private $item = null;

	function set_item($item)
	{
		$this->item = $item;
	}
	
	function get_item()
	{
		return $this->item;
	}
}
?>