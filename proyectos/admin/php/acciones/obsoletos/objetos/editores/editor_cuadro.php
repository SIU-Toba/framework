<?
	if($editable = $this->zona->get_editable()){
		//$this->obtener_info_objetos();
		//-[1]- Cargo el ABM que actua sobre la cabecera
		$abm_cabecera = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm_cabecera > -1){
			//$this->zona->info();
			$this->zona->obtener_html_barra_superior();
			$clave_ef = array("objeto_cuadro"=>$editable[1]);
			$this->objetos[$abm_cabecera]->cargar_estado_ef($clave_ef);
			$this->objetos[$abm_cabecera]->procesar($editable);
			$this->objetos[$abm_cabecera]->obtener_html();
			//Si existe un registro en la CABECERA, creo la interface para manejar el DETALLE
			//Atencion, creo que que aca me dio un error en tandil, esta todo OK?
			$etapa = $this->objetos[$abm_cabecera]->obtener_etapa();
			if($etapa!="SA")
			{
				//$this->objetos[$abm_cabecera]->info();
				ei_separador("COLUMNAS");
				//-[2]- Cargo la lista de ITEMs
				$listado = $this->cargar_objeto("objeto_lista",0);
				if($listado > -1){
					//-[3]- Cargo el ABM de edicion de ITEMs
					$abm_detalle = $this->cargar_objeto("objeto_mt_abms",1);
					if($abm_detalle > -1){
						$cargar_ef = array("objeto_cuadro"=>$editable[1]);
						$this->objetos[$abm_detalle]->cargar_estado_ef($cargar_ef);
						//proceso el evento antes de cargar la lista porque si es un INSERT 
						//no va a aparecer en el listado.
						$this->objetos[$abm_detalle]->procesar();
						//LISTA
						$where = array("(objeto_cuadro_proyecto = '".$editable[0]."')",
										"(objeto_cuadro = '".$editable[1]."')");
						$this->objetos[$listado]->cargar_datos($where);
						enter();
						$this->objetos[$listado]->obtener_html();		
						//FORMULARIO
						$this->objetos[$abm_detalle]->obtener_html();	
						//$this->objetos[$abm_detalle]->info_estado();
						//ei_arbol($this->objetos[$abm_detalle]->obtener_datos());
					}else{
						echo ei_mensaje("No fue posible instanciar el ABM 2");
					}
				}else{
					echo ei_mensaje("No fue posible instanciar el objeto LISTA","error");
				}
			}
			$this->zona->obtener_html_barra_inferior();

		}else{
			echo ei_mensaje("No fue posible instanciar el ABM principal");
		}
	}else{
		$abm_cabecera = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm_cabecera > -1){
			$this->objetos[$abm_cabecera]->procesar($editable);
			$this->objetos[$abm_cabecera]->obtener_html();
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM principal");
		}
	}
	//dump_session();
	//$this->hilo->dump_memoria();
?>