<?
	$cuadro = $this->cargar_objeto("objeto_cuadro",0);
	if($cuadro > -1){
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar();
			$where[] = "( proyecto = '".$this->hilo->obtener_proyecto()."')";
			$this->objetos[$cuadro]->cargar_datos($where);
			$this->objetos[$abms]->obtener_html();
			$this->objetos[$cuadro]->obtener_html();
			//$this->objetos[$abms]->info_estado();		
			//$this->vinculador->info();
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM");
		}
	}else{
		echo ei_mensaje("No fue posible instanciar el LISTADO");
	}
?>