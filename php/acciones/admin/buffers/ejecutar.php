<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();

		eval($this->zona->editable_info["cuerpo"]);
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
?>