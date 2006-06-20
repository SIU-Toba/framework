<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		//$this->info();
		//$this->obtener_info_objetos();
		$abm = $this->cargar_objeto("objeto_mt_abms",0);
		$cuadro = $this->cargar_objeto("objeto_cuadro",0);
		if ($cuadro > -1) {
			if($abm > -1){
				$this->zona->cargar_editable();//Cargo el editable de la zona
				$this->zona->obtener_html_barra_superior();
				$where[] = "proyecto = '". $editable[0] . "'";
				enter();
				$this->objetos[$abm]->cargar_estado_ef(array("proyecto"=>$editable[0]));
				$this->objetos[$abm]->procesar();
				$this->objetos[$cuadro]->cargar_datos($where);
				//$this->zona->info();
				$this->objetos[$cuadro]->obtener_html();
				$this->objetos[$abm]->obtener_html();
				$this->zona->obtener_html_barra_inferior();
			}else{
				echo ei_mensaje("No fue posible instanciar el ABM (1)");
			}
		}else{
				echo ei_mensaje("No fue posible instanciar el CUADRO (1)");
		}	
	}else{ //No estoy navegando un proyecto
		{
			echo ei_mensaje("ATENCION: no se especifico un PROYECTO");
		}	
	}
?>