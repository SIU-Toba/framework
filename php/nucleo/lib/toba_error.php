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
	protected $mensaje_solo_debug = '';
	protected $titulo_ventana;

	function __construct($mensaje_visible, $mensaje_solo_debug='', $titulo_ventana = null)
	{
		$this->mensaje_solo_debug = $mensaje_solo_debug;
		$this->titulo_ventana = $titulo_ventana;
		parent::__construct($mensaje_visible);
	}

	function get_mensaje()
	{
		return parent::getMessage();
	}

	function get_mensaje_log()
	{
		if (isset($this->mensaje_solo_debug)) {
			return $this->get_mensaje().' '.$this->mensaje_solo_debug;
		} else {
			return $this->get_mensaje();
		}
	}
	
	function get_titulo_ventana()
	{
		return $this->titulo_ventana;
	}
}



/**
* Excepción ejecutando SQL contra la DB
*/
class toba_error_db extends toba_error
{
	protected $codigo;
	protected $info_error;				//Arreglo que mantiene SQLSTATE, codigo especifico del motor, mensaje especifico del motor.
	protected $sql;
	protected $parser_errores;
	protected $es_comando;

	function __construct($info, $sql=null, $parser_errores=null, $es_comando=false)
	{
		$this->es_comando = $es_comando;
		$this->parser_errores = $parser_errores;
		$this->set_sql_ejecutado($sql);
		//- No se crea un arreglo cuando falla la conexion a la base ya que son propiedades del objeto conexion.
		if (is_array($info->errorInfo)){
			$this->set_info_error($info->errorInfo);
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
			} else {
				//-- Primero se intenta parsear el mensaje para mostrar un mensaje más contextual
				if ($this->es_comando && isset($this->parser_errores)) {
					$mensaje = $this->parser_errores->parsear($sql, $this->info_error[0], $info->getMessage());
				}
				//-- Si no hay parser, o el mismo no puede deducir nada se intenta mostrar un mensaje generico
				if (! isset($mensaje)) {
					$datos_error = array();
					if (toba_proyecto::hay_instancia()) {	//El error pudo haber surgido desde la misma creacion de la instancia del proyecto (ciclo infinito)
						$datos_error = toba::proyecto()->get_mensaje_proyecto($this->get_sqlstate());
						if (!$datos_error || !is_array($datos_error)) {
							$datos_error = toba::proyecto()->get_mensaje_toba($this->get_sqlstate());
						}
					}
					if (!is_array($datos_error) || empty($datos_error)) {
						$mensaje = $info->getMessage();	
					}else{
						$mensaje = $datos_error['m'];					
					}
				}
			}				
		}else{
			$mensaje = $this->get_mensaje_log();
		}
		parent::__construct($mensaje);
	}

	function set_info_error($error)
	{
		$this->info_error = $error;	
	}

	function set_mensaje_motor($mensaje)
	{
		 $this->info_error[2] = $mensaje;
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
	
	function get_mensaje_log()
	{
		if (PHP_SAPI != 'cli') {
			$mensaje  = "<p><b>SQLSTATE:</b> {$this->get_sqlstate()}</p>" .
						"<p><b>CODIGO:</b> {$this->get_codigo_motor()}</p>" .
						"<p><b>MENSAJE:</b> {$this->get_mensaje_motor()}</p>" .
						"<p><b>SQL:</b> {$this->get_sql_ejecutado()}</p>";
		}else{
			$sep = '------------------------------------------------------';
			$mensaje = "
ERROR ejecutando SQL:
 [CODIGO]: {$this->get_codigo_motor()}
 [SQLSTATE]: {$this->get_sqlstate()} 
 [MENSAJE]: {$this->get_mensaje_motor()}
 [SQL EJECUTADA]: {$this->get_sql_ejecutado()}
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
 * Excepción producida por sobrepasar el limite de intentos de autenticacion
 */
class toba_error_autenticacion_intentos extends toba_error
{

}

/**
 * Excepción producida cuando la composición de la contraseña no es valida
 */
class toba_error_pwd_conformacion_invalida extends toba_error
{

}

/**
 * Excepción producida por sobrepasar el limite de dias de validez de la contraseña 
 */
class toba_error_login_contrasenia_vencida extends toba_error
{

}

/**
 * Excepción producida por un error en la autorizacion del usuario
 */
class toba_error_autorizacion extends toba_error
{

}


/**
 * Excepción producida por un error de seguridad
 */
class toba_error_seguridad extends toba_error
{
	function __construct($mensaje)
	{
		parent::__construct('Error Interno', $mensaje);
	}
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
 * Excepción producida en una comunicación web service  
 */
class toba_error_comunicacion extends toba_error
{
	function __construct($mensaje, $opciones, $headers)
	{
		if (PHP_SAPI != 'cli') {		
			$extra = "<ul><li>Opciones: <pre>".print_r($opciones, true)."</pre>";
			$extra .= "<li>Headers Respuesta: <pre>".print_r($headers, true)."</pre></ul>";
		} else {
			$extra = "\nOpciones: ".print_r($opciones, true);			
		}
		parent::__construct($mensaje, $extra);
	}	
}


/**
 * Excepción para recargar una solicitud
 * es necesario que la operación donde se ejecute esta excepcion este marcado como "recar
 */
class toba_reset_nucleo extends Exception
{
	private $item = null;

	function __construct($mensaje=null, $id_item=null)
	{
		parent::__construct($mensaje);
		if (isset($id_item)) {
			$this->item = array(toba::proyecto()->get_id(), $id_item);
		}
	}
	
	function set_item($item)
	{
		$this->item = $item;
	}
	
	function get_item()
	{
		return $this->item;
	}
}

class toba_error_servicio_web extends toba_error
{
	protected $codigo;
	
	function __construct($mensaje_visible, $codigo_error=null, $mensaje_solo_debug='')
	{
		$this->codigo = (string) $codigo_error;
		parent::__construct($mensaje_visible, $mensaje_solo_debug);
		
	}
	
	function get_codigo() 
	{
		return $this->codigo;	
	}
}

/**
 * Excepción producto de un error durante la verificación de la firma digital
 */
class toba_error_firma_digital extends toba_error
{

}

?>
