<?
/*
	Esta clase obtiene los mensajes guardados en la DB
*/
class mensaje
{
	function get($indice, $parametros=null)
	//Obtiene un MENSAJE
	//Escala del proyecto actual al TOBA si no lo encuentra en el primero
	{
		if($mensaje = self::get_proyecto($indice, $parametros)){
			return $mensaje;
		}else{
			return self::get_toba($indice, $parametros);
		}
	}
	//-----------------------------------------------------

	function get_toba($indice, $parametros=null)
	//Obtiene un mensaje GLOBAL del proyecto toba
	//Esto es para errores genericos del motor, etc
	{
		$datos = info_proyecto::get_mensaje_toba($indice);
		if(!is_array($datos)){
			throw new excepcion_toba_def("El mensaje $indice no EXISTE.");
		}else{
			if(trim($datos[0]['m'])==""){
				throw new excepcion_toba_def("El mensaje $indice, existe pero está vacío.");
			}else{
				$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
			}
		}
		return $mensaje;		
	}
	//-----------------------------------------------------
	
	function get_proyecto($indice, $parametros=null)
	//Obtiene un mensaje GLOBAL del proyecto
	{
		$datos = info_proyecto::get_mensaje_proyecto($indice);
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
	//-----------------------------------------------------

	function get_objeto($objeto, $indice, $parametros=null)
	//Obtiene el mensaje asociado a un OBJETO
	{
		$datos = info_proyecto::get_mensaje_objeto($objeto, $indice);
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
	//-----------------------------------------------------

	function parsear_parametros($mensaje, $parametros)
	//Si el mensaje fue definido con comodines (%numero%)
	//Estos pueden ser reemplazados por valores provistos en la llamada
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
	//-----------------------------------------------------
}
?>