<?php

/**
 * Obtiene los mensajes del proyecto definidos en el editor, útiles para evitar fijar los mensajes del usuario en el código
 * Consumir como: toba::mensajes()->metodo
 * @package Fuentes
 */
class toba_mensajes
{
	/**
	 * Obtiene un mensaje global del proyecto, si no lo encuentra escala buscando el mensaje en el mismo framework
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 */
	static function get($indice, $parametros=null)
	{
		if($mensaje = self::get_proyecto($indice, $parametros)){
			return $mensaje;
		}else{
			return self::get_toba($indice, $parametros);
		}
	}

	/**
	 * Obtiene un mensaje global del framework
	 * Esto es para errores genericos del motor, etc
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 */
	static function get_toba($indice, $parametros=null)
	{
		$datos = toba_proyecto::get_mensaje_toba($indice);
		if(!is_array($datos)){
			throw new toba_error_def("El mensaje $indice no EXISTE.");
		}else{
			if(trim($datos[0]['m'])==""){
				throw new toba_error_def("El mensaje $indice, existe pero está vacío.");
			}else{
				$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
			}
		}
		return $mensaje;		
	}

	/**
	 * Obtiene un mensaje global del proyecto
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 */
	static function get_proyecto($indice, $parametros=null)
	{
		$datos = toba_proyecto::get_mensaje_proyecto($indice);
		if(!is_array($datos)){
			$mensaje = null;
		}else{
			if(trim($datos[0]['m'])==""){
				$mensaje = null;	
			}else{
				$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
			}
		}
		return $mensaje;
	}

	/**
	 * Retorna un mensaje asociado a un componente específico
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 */
	static function get_componente($objeto, $indice, $parametros=null)
	{
		$datos = toba_proyecto::get_mensaje_objeto($objeto, $indice);
		if(!is_array($datos)){
			//Retorna null para que siga la busqueda al GLOBAL
			$mensaje = null;
		}else{
			if(trim($datos[0]['m'])==""){
				$mensaje = null;	
			}else{
				$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
			}
		}
		return $mensaje;		
	}

	/**
	 * Si el mensaje fue definido con comodines (%numero%)
	 * Estos pueden ser reemplazados por valores provistos en la llamada
	 */
	static function parsear_parametros($mensaje, $parametros)
	{
		if(is_array($parametros)){
			//Si se enviaron parametros los pongo en el
			//lugar de los comodines
			for($a=0;$a<count($parametros);$a++){
				$mensaje = ereg_replace("%".($a+1)."%", $parametros[$a], $mensaje);
			}
			//Por si todavia quedan comodines
			$mensaje = ereg_replace("%[^ 	]*%","",$mensaje);
		}else{
			//No hay parametros: elimino los comodines.
			$mensaje = ereg_replace("%[^ 	]*%","",$mensaje);
		}
		return $mensaje;
	}
}
?>