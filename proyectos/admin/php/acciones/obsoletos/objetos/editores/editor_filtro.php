<?
	if($editable = $this->zona->get_editable()){
		//$this->obtener_info_objetos();
		//-[1]- Cargo el ABM que actua sobre la cabecera
		$abm_cabecera = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm_cabecera > -1){
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$clave_ef = array("objeto_filtro"=>$editable[1]);
			$this->objetos[$abm_cabecera]->cargar_estado_ef($clave_ef);
			$this->objetos[$abm_cabecera]->procesar();
			$this->objetos[$abm_cabecera]->obtener_html();
			//Si existe un registro en la CABECERA, creo la interface para manejar el DETALLE
			$listado = $this->cargar_objeto("objeto_cuadro",0);
			if($listado > -1){
				$where = array("(objeto_filtro_proyecto = '".$editable[0]."')",
								"(objeto_filtro = '".$editable[1]."')");
				$this->objetos[$listado]->cargar_datos($where);
				$this->objetos[$listado]->obtener_html();		
			}else{
				echo ei_mensaje("No fue posible instanciar el objeto LISTA","error");
			}
			$this->zona->obtener_html_barra_inferior();
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM principal");
		}
	}else{
		echo ei_mensaje("No se especifico el Identificador del OBJETO");
	}
	//dump_session();
	//$this->hilo->dump_memoria();
?>