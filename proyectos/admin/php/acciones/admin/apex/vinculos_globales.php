<?
	//-[1]- Cargo la lista de Objetos
	$lista = $this->cargar_objeto("objeto_lista",0);
	if($lista > -1){
		//-[2]- Cargo el ABM que permite asociar objetos
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			//$this->zona->info();
			$cargar_ef = array( "origen_item_proyecto"=>$this->hilo->obtener_proyecto(),
								"origen_item"=>"/vinculos",
								"indice"=>'0',
								"origen_objeto_proyecto"=>'admin',
								"origen_objeto"=>"0",
								"destino_objeto_proyecto"=>'admin',
								"destino_objeto"=>"0");
			$this->objetos[$abms]->cargar_estado_ef($cargar_ef);
			$this->objetos[$abms]->procesar();
			$this->objetos[$lista]->cargar_datos(array("(origen_item_proyecto = '".$this->hilo->obtener_proyecto()."') ",
														"(origen_item = '/vinculos') " ) );
			$this->objetos[$lista]->obtener_html();
			$this->objetos[$abms]->obtener_html();
			//$this->objetos[$abms]->info_estado();		
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM");
		}
	}else{
		echo ei_mensaje("No fue posible instanciar el LISTADO");
	}
?>