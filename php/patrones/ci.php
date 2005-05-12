<? 
    $ci = $this->cargar_objeto($this->info["item_parametro_a"],0); 
    if($ci > -1){ 
		$this->objetos[$ci]->procesar_eventos();
		$this->objetos[$ci]->generar_interface_grafica();	
    }else{ 
        echo ei_mensaje("No fue posible instanciar el CONTROLARDO de INTERFACE"); 
    } 
?>