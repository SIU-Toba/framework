<?

	$mt = $this->cargar_objeto("objeto_mt",0);
    if($mt > -1){ 
        $this->objetos[$mt]->procesar(); 
        $this->objetos[$mt]->obtener_html(); 
    }else{
        echo ei_mensaje("No fue posible instanciar el MT");
    }

##############################################################################
?>