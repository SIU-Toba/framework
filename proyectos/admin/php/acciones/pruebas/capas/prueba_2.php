<?

	$cn = $this->cargar_objeto("objeto_cn_t", 0);
	$ci = $this->cargar_objeto("objeto_ci_me_tab", 0);

	//$this->objetos[$cn]->cargar_datos($editable);
	
	$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );

	$this->objetos[$ci]->procesar();
	$this->objetos[$ci]->obtener_html();			

	//$this->objetos[$cn]->debug();
	//ei_arbol($this->objetos[$ci]->info_estado());
	//$this->objetos[$ci]->info();
	//ei_arbol($this->objetos[$ci]->info_estado());
	//$this->info_estado();
	//$this->objetos[$ci]->mostrar_memoria();
	//dump_SESSION();

?>