<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		if ($this->info_objetos > 0) {
			$objeto = current($this->info_objetos);
		    $ci = $this->cargar_objeto($objeto['clase'],0); 
		    if($ci > -1){
				$this->objetos[$ci]->set_datos($this->zona->editable_info);
				$this->objetos[$ci]->procesar_eventos();
				$this->objetos[$ci]->generar_interface_grafica();	
		    } else { 
		        echo ei_mensaje("No fue posible instanciar el CONTROLARDO de INTERFACE"); 
		    } 
		} else { 
			echo ei_mensaje("Necesita asociar un objeto CI al ítem."); 
	    }
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
	
	//________________________________________________________________________________________	
	
	
?>
