<?
	if($editable = $this->zona->get_editable()){
	//--> Estoy navegando la ZONA con un editable...
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$ef["item_proyecto"] = $editable[0];
			$ef["item"] = $editable[1];
			$this->objetos[$abms]->cargar_estado_ef($ef);			
			$this->objetos[$abms]->procesar($editable);
			//ei_arbol($this->objetos[$abms]->obtener_datos());
			
			$this->zona->obtener_html_barra_superior();
			$this->objetos[$abms]->obtener_html();
			$this->zona->obtener_html_barra_inferior();
			//$this->objetos[$abms]->info_estado();		
 		}
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}

?>
