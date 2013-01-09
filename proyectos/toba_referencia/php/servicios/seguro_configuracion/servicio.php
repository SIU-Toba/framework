<?php

class servicio extends toba_servicio_web
{
	
	/**
	 * @param array $mensaje
	 * @return string 
	 */
	function op__test(toba_servicio_web_mensaje $mensaje)
	{
		$array = $mensaje->get_array();
		$id = $this->get_id_cliente();
		if (isset($id)) {
			$id = "No presente";
		} else {
			$id = var_export($id);
		}	
		$id = xml_encode($id);
		$payload = array("Clave: {$array['clave']}. Valor: {$array['valor']}. ID: $id");
		return new toba_servicio_web_mensaje($payload);
	}

}

?>