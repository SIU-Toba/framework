<?
   	
	//Esto se puede llamar en el medio del proceso de una operacion
	$this->hilo->desactivar_reciclado();
   	
   	$parametros = $this->hilo->obtener_parametros();
///*
	$objeto = current($this->info_objetos);
    $ci = $this->cargar_objeto($objeto['clase'],0); 
    if($ci > -1){ 
		$this->objetos[$ci]->procesar_eventos();
		$this->objetos[$ci]->set_item($parametros['proyecto'],$parametros['item']);
		$this->objetos[$ci]->generar_interface_grafica();	
    } else { 
        echo ei_mensaje("No fue posible instanciar el CONTROLARDO de INTERFACE"); 
    } 	
//*/
	
	/*
	require_once("api/estructura_item.php");
	$elemento = new estructura_item($parametros['proyecto'],$parametros['item']);
	$elemento->generar_html();
	*/
?>