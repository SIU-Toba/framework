<?
	if($editable = $this->zona->obtener_editable_propagado()){
		
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		//$this->obtener_info_objetos();


		$cn = $this->cargar_objeto("objeto_cn_t", 0);
		$ci = $this->cargar_objeto("objeto_ci_me_tab", 0);

		$this->objetos[$cn]->cargar_datos($editable);
		
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

		$this->zona->obtener_html_barra_inferior();

	}else{
		echo ei_mensaje("No es posible identificar el plan a editar");
	}
?>