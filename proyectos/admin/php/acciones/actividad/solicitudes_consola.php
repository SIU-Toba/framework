<? 
	$solicitud = $this->hilo->obtener_parametro("solicitud");

    if($clase = $this->info["item_parametro_a"]){ 
        $cuadro = $this->cargar_objeto($clase,0); 
        if($cuadro > -1) 
        { 
			$where[] = "solicitud = $solicitud";
            $this->objetos[$cuadro]->cargar_datos($where); 
            enter(); 
            $this->objetos[$cuadro]->obtener_html(); 
            enter(); 
        }else{ 
            echo ei_mensaje("No se pudo crear la lista"); 
        }
    }else{
        echo ei_mensaje("Especifique la clase que desea cargar en el PARAMETRO 1"); 
    }
	
	ei_cronometro_solicitud($solicitud,"80%");
	
	//Observacion
	$cuadro = $this->cargar_objeto("objeto_cuadro_reg",0); 
    if($cuadro > -1)
    { 
		$where[] = "solicitud = $solicitud";
    	$this->objetos[$cuadro]->cargar_datos($where); 
       	enter();
		$this->objetos[$cuadro]->obtener_html(); 
    }else{ 
    	echo ei_mensaje("No se pueden mostrar las observaciones"); 
    }
	
	//Observacion de objetos
	$cuadro = $this->cargar_objeto("objeto_cuadro_reg",1); 
    if($cuadro > -1) 
    { 
		$where[] = "solicitud = $solicitud";
    	$this->objetos[$cuadro]->cargar_datos($where); 
     	$this->objetos[$cuadro]->obtener_html(); 
    }else{ 
    	echo ei_mensaje("No se pueden mostrar las observaciones de objetos"); 
    }
?>