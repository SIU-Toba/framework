<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		

		$ci = $this->cargar_objeto("objeto_ci", 0);
		$cn = $this->cargar_objeto("objeto_cn", 0);
	
		$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
		$this->objetos[$ci]->procesar();
		$this->objetos[$ci]->obtener_html();

		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>