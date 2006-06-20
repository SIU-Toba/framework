<?
	if($solicitud=$this->hilo->obtener_parametro("solicitud")){
		ei_cronometro_solicitud($solicitud);
	}else{
		echo ei_mensaje("No se especifico el ID de la SOLICITUD");
	}
?>