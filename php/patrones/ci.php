<? 
    $ci = $this->cargar_objeto("objeto_ci_me",0); 
    if($ci > -1){ 
        $this->objetos[$ci]->procesar(); 
        $this->objetos[$ci]->obtener_html(); 
    }else{ 
        echo ei_mensaje("No fue posible instanciar el CONTROLARDO de INTERFACE"); 
    } 
?>