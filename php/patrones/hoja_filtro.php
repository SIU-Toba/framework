<?
	$filtro = $this->cargar_objeto("objeto_filtro",0);
	if($filtro > -1){
		enter();
		$this->objetos[$filtro]->obtener_interface_vertical();
		//$this->objetos[$filtro]->info();
		$hoja =	$this->cargar_objeto("objeto_hoja",0);
		if($hoja > -1){
			//$this->objetos[$hoja]->info();
			//Si la HOJA tiene dimensiones asociadas hay que informarlas en el FILTRO.
/*
			if( $dimensiones = $this->objetos[$hoja]->obtener_dimensiones_asociadas() ){
				//ei_arbol($dimensiones,"DIMENSIONES");
				$this->objetos[$filtro]->acoplar_dimensiones($dimensiones);
			}
*/
            //Si el filtro pasa sus controles...
			$temp = $this->objetos[$filtro]->validar_estado();
    		if($temp[0])
	  		{
				$where = $this->objetos[$filtro]->obtener_where();
				$from = $this->objetos[$filtro]->obtener_from();
				//echo ei_mensaje($where);
				//ei_arbol($from, "FROM");
				if($this->objetos[$hoja]->cargar_datos($where,$from)){
					enter();
					$this->objetos[$hoja]->obtener_html();
				}else{
					$this->objetos[$hoja]->mostrar_info_proceso();
				}
    		}else{
				$this->objetos[$filtro]->mostrar_info_proceso();
   	   		}
		}else{
			echo ei_mensaje("No se pudo crear la hoja de datos");
		}
	}else{
		echo ei_mensaje("No se pudo crear el filtro");
	}		
?>