<?
	if($editable = $this->zona->obtener_editable_propagado()){
		//$this->obtener_info_objetos();
		//-[1]- Cargo el ABM que actua sobre la cabecera
		$abm_cabecera = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm_cabecera > -1){
			$this->zona->cargar_editable();//Cargo el editable de la zona
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$clave_ef = array("objeto_mapa"=>$editable[1]);
			$this->objetos[$abm_cabecera]->cargar_estado_ef($clave_ef);
			$this->objetos[$abm_cabecera]->procesar($editable);
			$this->objetos[$abm_cabecera]->obtener_html();
			//Si existe un registro en la CABECERA, creo la interface para manejar el DETALLE
			$etapa = $this->objetos[$abm_cabecera]->obtener_etapa();
			//$this->objetos[$abm_cabecera]->info_estado();
			$this->zona->obtener_html_barra_inferior();
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM principal");
		}
	}
	//dump_session();
	//$this->hilo->dump_memoria();
?>