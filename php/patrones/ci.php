<?php
	if ($this->info_objetos > 0) {
		$cis = array();
		$i = 0;

		//Construye los objetos ci y el cn
		foreach ($this->info_objetos as $objeto) {
			if ($objeto['clase'] != 'objeto_cn') {
				$cis[] = $this->cargar_objeto($objeto['clase'],$i); 
				$i++;
			} else {
				$cn = $this->cargar_objeto($objeto['clase'],0); 
			}
		}
		
		//Asigna el cn a los cis
		foreach ($cis as $ci) {
		    if($ci > -1){ 
				if (isset($cn)) {
					$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
				}
		    	$this->objetos[$ci]->procesar_eventos();
				$this->objetos[$ci]->generar_interface_grafica();	

		    } else { 
		        echo ei_mensaje("No fue posible instanciar el CONTROLADOR de INTERFACE"); 
		    } 
		}
	} else { 
		echo ei_mensaje("Necesita asociar un objeto CI al ítem."); 
    }
?>