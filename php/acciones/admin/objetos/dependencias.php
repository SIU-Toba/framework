<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		

		$cuadro = $this->cargar_objeto("objeto_cuadro",0);
		if($cuadro > -1){
			//-[2]- Cargo el ABM que permite asociar objetos
			$abms = $this->cargar_objeto("objeto_mt_abms",0);
			if($abms > -1){
				$cargar_ef = array("objeto_consumidor"=>$editable[1]);
				$this->objetos[$abms]->cargar_estado_ef($cargar_ef);
				$this->objetos[$abms]->procesar();

				$this->objetos[$cuadro]->cargar_datos(array("(d.objeto_consumidor = '".$editable[1]."') ",
															"(d.proyecto = '".$editable[0]."') " ) );

				$cuadro_info = $this->cargar_objeto("objeto_cuadro",1);
				if($cuadro_info > -1){
					$where_clase[] = "clase_consumidora_proyecto = '".$this->zona->editable_info['clase_proyecto']."'";
					$where_clase[] = "clase_consumidora = '".$this->zona->editable_info['clase']."'";
					$this->objetos[$cuadro_info]->cargar_datos($where_clase);
					enter();
					$this->objetos[$cuadro_info]->obtener_html();
					enter();
				}
				$this->objetos[$cuadro]->obtener_html();
				$this->objetos[$abms]->obtener_html();

				//$this->objetos[$abms]->info_estado();		

			}else{
				echo ei_mensaje("No fue posible instanciar el ABM");
			}
		}else{
			echo ei_mensaje("No fue posible instanciar el CUADRO");
		}

		

		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>