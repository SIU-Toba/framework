<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		//$this->info();
		//$this->obtener_info_objetos();
		$cuadro = $this->cargar_objeto("objeto_cuadro",0);
		if ($cuadro > -1) {
			$this->zona->cargar_editable();//Cargo el editable de la zona
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$this->objetos[$cuadro]->obtener_html();
			$this->zona->obtener_html_barra_inferior();
		}else{
			echo ei_mensaje("No fue posible instanciar el CUADRO (1)");
		}	
	}
?>