<?
/*
	ATENCION:  

		Parametro 1: Clase CI
		Parametro 2: Clase CN
*/
	$ci = $this->cargar_objeto($this->info["item_parametro_a"] , 0);
	$cn = $this->cargar_objeto($this->info["item_parametro_b"], 0);

	$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
	$this->objetos[$ci]->procesar();
	$this->objetos[$ci]->obtener_html();			
	//$this->objetos[$cn]->debug();
?>