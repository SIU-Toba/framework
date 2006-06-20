<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		

		$lista = $this->cargar_objeto("objeto_cuadro",0);
		if($lista > -1){
			$abms = $this->cargar_objeto("objeto_mt_abms",0);
			if($abms > -1){
				$where[] = "usuario_destino is null";
				$where[] = "proyecto = '". $editable[0] ."'";
				$this->objetos[$abms]->procesar();
				$this->objetos[$lista]->cargar_datos();
				enter();
				$this->objetos[$lista]->obtener_html();
				$this->objetos[$abms]->obtener_html();
				//$this->objetos[$abms]->info_estado();		
				//$this->vinculador->info();
			}else{
				echo ei_mensaje("No fue posible instanciar el ABM de Notas");
			}
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}


		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>