<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
 	enter();
	$hoja0 = $this->cargar_objeto("objeto_hoja",0);
	if($hoja0 > -1){
		if($this->objetos[$hoja0]->cargar_datos()===true){
			$this->objetos[$hoja0]->obtener_html();
		}else{
			echo ei_mensaje("La consulta no devolvio datos");
		}
		//$this->objetos[$abms]->info();
		//dump_session();
	}

		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>