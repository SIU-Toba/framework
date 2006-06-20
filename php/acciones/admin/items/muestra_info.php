<? 
    $cuadro = $this->cargar_objeto("objeto_cuadro_reg",0); 
    if($cuadro > -1) 
    { 
        if (($item = $this->hilo->obtener_parametro("item")) && ($proyecto = $this->hilo->obtener_parametro("proyecto"))){
			$where[] = "item = '$item' and item_proyecto='$proyecto'";
    	    $this->objetos[$cuadro]->cargar_datos($where);
			enter();
    	    $this->objetos[$cuadro]->obtener_html(false);
	        enter();
        }else{
			echo ei_mensaje("No se recibió el ITEM sobre el cual se desea ver la ayuda");
		}
    }else{ 
        echo ei_mensaje("No se pudo cargar la ayuda del ITEM"); 
    } 
?> 