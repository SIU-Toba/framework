<?
	
	//-- Cargo los OBJETOS --
	
	$cuadro = $this->cargar_objeto("objeto_cuadro",0);
	if($cuadro < 0){
		echo ei_mensaje("No fue posible cargar la INTERFACE");
	}
	$mt = $this->cargar_objeto("objeto_mt_mds",0);
	if($mt < 0){
		echo ei_mensaje("No fue posible cargar la INTERFACE");
	}

	//-- Armo el comportamiento
	$this->objetos[$mt]->procesar();
	$this->objetos[$cuadro]->cargar_datos();
	enter();
	$this->objetos[$cuadro]->obtener_html();
	$this->objetos[$mt]->obtener_html();

	
?>
