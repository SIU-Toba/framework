<?
/*
	ATENCION:  

		Parametro 1: Clase CI
		Parametro 2: Clase CN
*/
	$ci = $this->cargar_objeto($this->info["item_parametro_a"] , 0);
	$cn = $this->cargar_objeto($this->info["item_parametro_b"], 0);

	$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
	$this->objetos[$ci]->procesar_eventos();
	$this->objetos[$ci]->generar_interface_grafica();			
	//$this->objetos[$cn]->debug();
?>