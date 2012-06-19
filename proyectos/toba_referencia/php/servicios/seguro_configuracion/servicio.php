<?php

class servicio extends toba_servicio_web
{
	
	/**
	 * @param array $mensaje
	 * @return string 
	 */
	function op__eco(toba_servicio_web_mensaje $mensaje)
	{
		$array = $mensaje->get_array();
		$dependencia = $this->get_id_cliente('dependencia');
		if (! isset($dependencia)) {
			$dependencia = "No presente";
		}		
		$dependencia = xml_encode($dependencia);
		$payload = array("Clave: {$array['clave']}. Valor: {$array['valor']}. Dependencia: $dependencia");
		return new toba_servicio_web_mensaje($payload);
	}

}

?>