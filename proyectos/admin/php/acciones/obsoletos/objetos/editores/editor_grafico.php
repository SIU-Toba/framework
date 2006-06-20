<?
	if($editable = $this->zona->obtener_editable_propagado()){
		//$this->obtener_info_objetos();
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->zona->cargar_editable();//Cargo el editable de la zona
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$clave_ef = array("objeto_grafico"=>$editable[1]);
			$this->objetos[$abms]->cargar_estado_ef($clave_ef);
			$this->objetos[$abms]->procesar($editable);
			$this->objetos[$abms]->obtener_html();
			//$this->objetos[$abms]->info_estado();
			//$this->objetos[$abms]->info_estado_ef();
			//dump_session();
			$this->zona->obtener_html_barra_inferior();
		}
	}else{
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar();
			$this->objetos[$abms]->obtener_html();
		}
	}
?>