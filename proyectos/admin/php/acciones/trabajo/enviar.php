<? 
    if($editable = $this->zona->obtener_editable_propagado()){ 
    //--> Estoy navegando la ZONA con un editable... 
        $this->zona->cargar_editable();//Cargo el editable de la zona 
        $this->zona->obtener_html_barra_superior(); 

        $abms = $this->cargar_objeto("objeto_mt_abms",0); 
        if($abms > -1)
        { 
			$this->objetos[$abms]->procesar(); 
			$this->objetos[$abms]->obtener_html(); 
        } 
        $this->zona->obtener_html_barra_inferior(); 
    }else{ 
        echo ei_mensaje("No se especifico que EDITABLE utilizar"); 
    } 
?> 
