<?
	
	if($clase = $this->info["item_parametro_a"]){
		$cuadro = $this->cargar_objeto($clase,0);
		if($cuadro > -1)
		{
			$this->objetos[$cuadro]->cargar_datos();
			enter();
			$this->objetos[$cuadro]->obtener_html();
			enter();
		}else{
			echo ei_mensaje("No se pudo crear la lista");
		}
	}else{
		echo ei_mensaje("Especifique la clase que desea cargar en el PARAMETRO 1");
	}

?>