<?php 
class serv_eco extends toba_servicio_web
{
	
	/** 
	 * Operación de eco
	 * @param string $mensaje El mensaje a repetir
	 * @return string $salida Mensaje repetido
	 */	
	function op__eco($mensaje) {
	    $salida = new WSMessage($mensaje->str);
	    return $salida;
	}
	
}

?>