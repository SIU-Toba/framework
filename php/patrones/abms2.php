<?
	$lista = $this->cargar_objeto("objeto_lista",0);
	if($lista > -1){
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar();
			$this->objetos[$lista]->cargar_datos();
			$this->objetos[$lista]->obtener_html();
			$this->objetos[$abms]->obtener_html();
			//$this->objetos[$abms]->info_estado();		
			//$this->vinculador->info();
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM");
		}
	}else{
		echo ei_mensaje("No fue posible instanciar el LISTADO");
	}
?>