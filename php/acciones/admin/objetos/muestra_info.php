<? 
    $cuadro = $this->cargar_objeto("objeto_cuadro_reg",0); 
    if($cuadro > -1) 
    { 
        if (($objeto = $this->hilo->obtener_parametro("objeto")) && ($proyecto = $this->hilo->obtener_parametro("proyecto"))){
			$where[] = "objeto = '$objeto' and objeto_proyecto='$proyecto'";
    	    $this->objetos[$cuadro]->cargar_datos($where);
			enter();
    	    $this->objetos[$cuadro]->obtener_html(false);
	        enter();
        }else{
			echo ei_mensaje("No se recibió el OBJETO sobre el cual se desea ver la ayuda");
		}
    }else{ 
        echo ei_mensaje("No se pudo cargar la ayuda del OBJETO"); 
    } 
?> 