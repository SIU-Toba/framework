<?
/*
	Esta clase obtiene los mensajes guardados en la DB
*/
class mensaje
{
	function get($indice, $parametros=null)
	//Obtiene un mensaje GLOBAL del proyecto
	{
		$hilo = toba::get_hilo();
		$proyecto_actual = $hilo->obtener_proyecto();
		$sql = "SELECT mensaje_customizable as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = '$proyecto_actual';";
		$datos = consultar_fuente($sql);
		if(!is_array($datos)){
			$mensaje = "El mensaje solicitado no EXISTE";
		}else{
			$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
		}
		return $mensaje;
	}
	//-----------------------------------------------------
	
	function get_objeto($objeto, $indice, $parametros)
	//Obtiene el mensaje asociado a un OBJETO
	{
		$hilo = toba::get_hilo();
		$proyecto_actual = $hilo->obtener_proyecto();
		$sql = "SELECT mensaje_customizable as m
				FROM apex_objeto_msg 
				WHERE indice = '$indice'
				AND objeto_proyecto = '$proyecto_actual'
				AND objeto = '$objeto';";
		$datos = consultar_fuente($sql);
		if(!is_array($datos)){
			//Retorna null para que siga la busqueda al GLOBAL
			$mensaje = null;
		}else{
			$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
		}
		return $mensaje;		
	}
	//-----------------------------------------------------

	function get_toba()
	//Obtiene un mensaje GLOBAL del proyecto toba
	//Esto es para errores genericos del motor, etc
	{
		$hilo = toba::get_hilo();
		$proyecto_actual = $hilo->obtener_proyecto();
		$sql = "SELECT mensaje_customizable as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = 'toba';";
		$datos = consultar_fuente($sql);
		if(!is_array($datos)){
			$mensaje = "El mensaje solicitado no EXISTE";
		}else{
			$mensaje = self::parsear_parametros($datos[0]['m'], $parametros);
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
		}else{
			//No hay parametros: elimino los comodines.
			$mensaje = ereg_replace("%[^ 	]*%","",$mensaje);
		}
		return $mensaje;
	}
	//-----------------------------------------------------
}

?>