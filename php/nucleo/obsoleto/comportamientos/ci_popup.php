<?php
	$this->hilo->desactivar_reciclado();
	
	if ($this->info_objetos > 0) {
		$objeto = current($this->info_objetos);
	    $ci = $this->cargar_objeto($objeto['clase'],0); 
	    if($ci > -1){ 
			$this->objetos[$ci]->procesar_eventos();
			$this->objetos[$ci]->generar_interface_grafica();	
	    } else { 
	        echo ei_mensaje("No fue posible instanciar el CONTROLARDO de INTERFACE"); 
	    } 
	} else { 
		echo ei_mensaje("Necesita asociar un objeto CI al ítem."); 
    }
?>