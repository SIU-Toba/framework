<?php

class servicio extends toba_servicio_web
{
	/**
	 * @param array $mensaje
	 * @return string 
	 */
	function op__eco(toba_servicio_web_mensaje $mensaje, $headers)
	{
		$array = $mensaje->get_array();
		if (isset($headers['dependencia'])) {
			$dependencia = $headers['dependencia'];
		} else {
			$dependencia = "No presente";
		}		
		$payload = array("Clave: {$array['clave']}. Valor: {$array['valor']}. Dependencia: $dependencia");
		return new toba_servicio_web_mensaje($payload);
	}

}

?>