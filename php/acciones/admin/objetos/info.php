<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->zona->cargar_editable();//Cargo el editable de la zona
			$ef["objeto_proyecto"]=$editable[0];
			$ef["objeto"]=$editable[1];
			$this->objetos[$abms]->cargar_estado_ef($ef);			
			$this->objetos[$abms]->procesar($editable);
			$this->zona->obtener_html_barra_superior();
			$this->objetos[$abms]->obtener_html();
			$this->zona->obtener_html_barra_inferior();
			//$this->objetos[$abms]->info_estado();		
 		}
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}


?>