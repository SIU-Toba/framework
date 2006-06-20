<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		echo "Esta no tiene sentido si la exportacion es por componente";		

	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}

?>