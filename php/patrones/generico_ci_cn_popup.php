<?
	
	//No toma en cuenta el request actual en el sistema de reciclaje
	//global (buffers y propiedades de CN mantenidas en la sesion)
	$this->hilo->desactivar_reciclado();

	$ci = $this->cargar_objeto($this->info["item_parametro_a"] , 0);
	$cn = $this->cargar_objeto($this->info["item_parametro_b"], 0);

	$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
	$this->objetos[$ci]->procesar();
	$this->objetos[$ci]->obtener_html();			

?>