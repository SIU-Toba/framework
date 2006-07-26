<?
	if($editable = $this->zona->get_editable()){
		//$this->obtener_info_objetos();
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$clave_ef = array("objeto"=>$editable[1]);
			$this->objetos[$abms]->cargar_estado_ef($clave_ef);
			$this->objetos[$abms]->procesar($editable);
			$this->objetos[$abms]->obtener_html();
			$this->zona->obtener_html_barra_inferior();
			//$this->objetos[$abms]->info();
			//dump_session();
		}
	}else{
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar();
			$this->objetos[$abms]->obtener_html();
		}
	}
?>